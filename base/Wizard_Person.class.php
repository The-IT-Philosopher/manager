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


class Wizard_Person extends Component {

  private $_donePage = "done";
  
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }

  function init() {
    $this->stone->_wizard->registerPage(
      array("person_enter"=>array('render_raw'=> array($this, "person_enter_render_raw"), 
                               "process"   => array($this, "person_enter_process"))));
  }

  function person_enter_render_raw(){
    $form = "<form method=post>";
    $form .= "<table>";
    $form .= "<tr><td>Voornaam</td><td><input type=text name=first_name></td></tr>";
    $form .= "<tr><td>Voorletters</td><td><input type=text name=initials></td></tr>";
    $form .= "<tr><td>Tussenvoegsel</td><td><input type=text name=last_name_prefix></td></tr>";
    $form .= "<tr><td>Achternaam</td><td><input type=text name=last_name></td></tr>";
    $form .= "<tr><td>E-mail adres</td><td><input type=email name=email_address></td></tr>";
    $form .= "<tr><td></td><td><input type=submit value=volgense></td></tr>";
    $form .= "</table></form>";
    return $form;
  }

  function person_enter_process(){
    $result = array();
    if ( (strlen($_POST['first_name']) || strlen($_POST['initials'])) && strlen($_POST['last_name'])) {
      $insertData = array();
      $insertData[':person_first_name'] = $_POST['first_name'];
      $insertData[':person_initials']   = $_POST['initials'];
      $insertData[':person_last_name_prefix'] = $_POST['last_name_prefix'];
      $insertData[':person_last_name'] = $_POST['last_name'];
      $sth = $this->stone->pdo->prepare("INSERT INTO person (person_first_name, person_initials, person_last_name_prefix, person_last_name) VALUES (:person_first_name, :person_initials, :person_last_name_prefix, :person_last_name)");
      $sth->execute($insertData);
      $person_id = $this->stone->pdo->lastInsertId();
      $this->stone->_data['personId'] = $person_id;
      if (strlen($_POST['email_address'])) {
        $insertData = array();
        $insertData[":email_verification"]=sha1(mcrypt_create_iv(16), MCRYPT_DEV_URANDOM ); 
        $insertData[":email_address"]=$_POST['email_address'];
        $sth = $this->stone->pdo->prepare("INSERT INTO email (email_address,email_verification) VALUES (:email_address,:email_verification)");
        $sth->execute($insertData);
        $email_id = $this->stone->pdo->lastInsertId();
        $this->stone->_data['emailId'] = $email_id;
      }
      $result['next_page'] = $this->_donePage;
    } 
    return $result;
  }
}
