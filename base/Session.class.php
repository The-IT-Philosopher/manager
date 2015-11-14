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

class Session extends Component {

  function resume() {
  if (isset($_COOKIE['ItPhilManagerSession'])) {
    $sth = $this->stone->pdo->prepare("SELECT user.user_id as user_id from user
                          JOIN link_session2user
                          ON link_session2user.user_id = user.user_id
                          JOIN session
                          ON link_session2user.session_id = session.session_id
                          WHERE session_hash = :session_hash");
    $sth->execute(array(":session_hash" => $_COOKIE['ItPhilManagerSession']));
    $user_id = $sth->fetchColumn();
    if ($user_id) {
      $this->stone->setUserID($user_id);
      $sth = $this->stone->pdo->prepare("SELECT capability_name 
                            FROM capability 
                              WHERE user_id = :user_id");
      $sth->execute(array(":user_id"=> $user_id));
      $capabilities = array();
      while ($capability=$sth->fetchColumn()) $capabilities[]=$capability;
      $this->stone->setUserCapabilities($capabilities);
      return true;
      } else { // invalid or ended session 
        setcookie(ItPhilManagerSession, "" , 1); //unsetting cookie
        return false;
      }
    } else { // no cookie set
      return false;
    }
  }

  function terminate(){
    //logout support
    setcookie(ItPhilManagerSession, "" , 1); //unsetting cookie
  }

  function start($user_id) {
    $data = array();
    $data[":session_hash"]=sha1(rand()); // for now
    if (strstr($_SERVER['REMOTE_ADDR'],":")) {
      // Remote address in in IPv6 notation
      $data[":session_ip_start"] = inet_pton($_SERVER['REMOTE_ADDR']);
    } else {
      // Remote address in IPv4 notation
      $data[":session_ip_start"] = inet_pton("::ffff:".$_SERVER['REMOTE_ADDR']);
    }
    $data[":session_useragent"] = $_SERVER['HTTP_USER_AGENT'];
    $sth = $this->stone->pdo->prepare("INSERT INTO session 
                               (session_hash,session_ip_start,session_useragent)
                  values (:session_hash,:session_ip_start,:session_useragent)");
    if(!$sth->execute($data)) {
      //ERROR
    } 
    // Set the cookie to the maximum 32 bit int value
    // to prevent year 2038 problems.
    setcookie("ItPhilManagerSession", $data[":session_hash"] , 2147483647 );
    $data = array();
    $data[':session_id'] = $this->stone->pdo->lastInsertId();
    $data[':user_id'] =  $user_id;
    $sth = $this->stone->pdo->prepare("INSERT INTO link_session2user 
                                       (session_id,user_id) 
                                VALUES (:session_id,:user_id)");
    if(!$sth->execute($data)) {
      //ERROR
    }
  }
    
  
}
