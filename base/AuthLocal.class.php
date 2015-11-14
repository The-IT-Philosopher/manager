<?php
/*
Copyright (c) 2015, André van Schoubroeck 
All rights reserved. 

Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are met: 

 * Redistributions of source code must retain the above copyright notice, 
   this list of conditions and the following disclaimer. 
 * Redistributions in binary form must reproduce the above copyright 
   notice, this list of conditions and the following disclaimer in the 
   documentation and/or other materials provided with the distribution. 
 * Neither the name of The IT Philosopher nor the names of its contributors 
   may be used to endorse or promote products derived from this software 
   without specific prior written permission. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE. 
*/

namespace Philosopher;

class AuthLocal extends Component {
  function init(){
    $this->stone->Page->registerPage(
      array("login" => array (
        "process" => array( $this, "ProcessPage")
      )));
  }

  function ProcessPage(){
    if (! $this->stone->uid ) { 
      $this->authenticate();

      if ( !$this->stone->uid ) { 
        if (isset($_POST['password']) && isset($_POST['email']) ) {        
          // We should handle login errors
          $this->stone->_data['content_raw'] .= "<P>Login error</P>"; 
        }
        $this->stone->_data['content_raw'] .= $this->loginform();
      } else {
        $this->stone->_data['content_raw'] .= "<p>Login Success</p>";
        // TODO after logging in we should process all the other components
        // as their behaviour may depend on the user being logged in
      }


    } else {
      $this->stone->_data['content_raw'] .= "<p>Already logged in</p>";
    }
    

  }

  function loginform(){
    $result  ="<form action='/login' method='post'>";
    $result .="<input name=email type=email>";
    $result .="<input name=password type=password>";
    $result .="<input type=submit></form>";
    return $result;
    break;
  }

  function authenticate(){
    if (isset($_POST['password']) && isset($_POST['email']) ) {
      $hasher = new \PasswordHash(); //TODO better manage external classes
      $sth = $this->stone->pdo->prepare("SELECT user_pbkdf2, user.user_id as user_id
                     FROM user
                     JOIN link_email2user 
                     ON link_email2user.user_id = user.user_id
                     JOIN email 
                     ON link_email2user.email_id = email.email_id
                     WHERE email_address = :email");
       $sth->execute(array(":email" => $_POST['email']));
       $loginData = $sth->fetch();

       $dbHash = $loginData['user_pbkdf2'];
       $user_id = $loginData['user_id'];
      //DEBUG
      //echo "<pre>" . var_export($loginData,true) . "</pre>";
      //DEBUG     
       $validPassword = $hasher->validate_password($_POST['password'], $dbHash);
       if ($validPassword) {
        $this->stone->setUserID($user_id);
        $sth = $this->stone->pdo->prepare("SELECT capability_name 
                            FROM capability 
                              WHERE user_id = :user_id");
        $sth->execute(array(":user_id"=> $user_id));
        $capabilities = array();
        while ($capability=$sth->fetchColumn()) $capabilities[]=$capability;
        $this->stone->setUserCapabilities($capabilities);

         $data = array();
         //$data[":session_hash"]=sha1(mcrypt_create_iv(16), MCRYPT_DEV_URANDOM);
         // even with URANDOM, it hangs, production server appears to have little entropy // is this really the case???
         $data[":session_hash"]=sha1(rand()); //TODO: something better as a session hash!
         if (strstr($_SERVER['REMOTE_ADDR'],":")) {
           // Remote address is IPv6 or IPv4 in IPv6 notation
           $data[":session_ip_start"] =inet_pton($_SERVER['REMOTE_ADDR']);
         } else {
          // Remote address is IPv4 in IPv4 notation
          // Convert to IPv6 notation
          $data[":session_ip_start"] = inet_pton("::ffff:".$_SERVER['REMOTE_ADDR']);
         }
         $data[":session_useragent"] = $_SERVER['HTTP_USER_AGENT'];
         $sth = $this->stone->pdo->prepare("INSERT INTO session (session_hash,session_ip_start,session_useragent) values (:session_hash,:session_ip_start,:session_useragent)");
         //$sth->execute($data);
         if(!$sth->execute($data)) {
           //todo: error handling
         } 
         setcookie("ItPhilManagerSession", $data[":session_hash"] , 0xFFFFFFFF ); //PHP_INT_MAX); 
         // PHP_MAX_INT causes problem on production server:
         // PHP Warning:  Expiry date cannot have a year greater than 9999 
         // and does not set cookie. I suppose using the max 32 bit value solves the problem.... until 2038
         // (This problem occurs on 64 bit PHP installations)
         $data = array();
         $data[':session_id'] = $this->stone->pdo->lastInsertId();
         $data[':user_id'] =  $user_id;
         $sth = $this->stone->pdo->prepare("INSERT INTO link_session2user (session_id,user_id) Values (:session_id,:user_id)");
         if(!$sth->execute($data)) {
           //todo: error handling
         }
      }
    }    
  }
//------------------------------------------------------------------------------
}
