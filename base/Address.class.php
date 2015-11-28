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

class Address extends Component {

//------------------------------------------------------------------------------
//!! TODO create class WizardPage to put this as it occurs everywhere
  private $_donePage = "done";
  
  function setDonePage($donepage) {
      $this->_donePage=$donepage;
  }
//------------------------------------------------------------------------------
  function init(){

    $this->stone->Wizard->registerPage(
      array("address_enter"=>array(
                              'render_xml'=> array($this, "render_xml"), 
                              "process"   => array($this, "process"))));

  }
//------------------------------------------------------------------------------
  function render_xml(){
    $form = new Form();
    $form->addElement(new FormInputElement("address_street","Straat"));
    $form->addElement(new FormInputElement("address_number","Huisnummer"));
    $form->addElement(new FormInputElement("address_postalcode","Postcode"));
    $form->addElement(new FormInputElement("address_city","Stad"));
    //$form->addElement(new FormInputElement("address_province","Provincie"));
    //$form->addElement(new FormInputElement("address_country","Land")); //!! ISO CODE, DROPDOWN
    return $form->GenerateForm(NULL, "Voer adresgegevens in");
  }
//------------------------------------------------------------------------------
  function process(){
    $result = array();
    if (strlen(@$_POST['address_street']) &&
        strlen(@$_POST['address_number']) &&
        strlen(@$_POST['address_postalcode']) &&
        strlen(@$_POST['address_city']) ){
      // For now -- unverified data
      // TODO add postcode integration
      $sth = $this->stone->pdo->prepare("INSERT INTO address (address_street, address_number, address_postalcode, address_city, address_country) 
                              VALUES (:address_street, :address_number, :address_postalcode, :address_city, :address_country)");
      $insertData = array();
      $insertData[':address_street']    = $_POST['address_street'];
      $insertData[':address_number']    = $_POST['address_number'];
      $insertData[':address_postalcode']= $_POST['address_postalcode'];
      $insertData[':address_city']      = $_POST['address_city'];
      if (strlen(@$this->stone->Wizard->_data['organisationCountry'])) {
        $insertData[':address_country']   = $this->stone->Wizard->_data['organisationCountry'];
      } else {
        $insertData[':address_country']   = "ZA";//$_POST['address_country']; //TODO
      }
       

      $sth->execute($insertData);
      $address_id = $this->stone->pdo->lastInsertId(); 
      $this->stone->Wizard->_data['addressId'] = $address_id; 
      $result['next_page'] = $this->_donePage;

      if (isset($this->stone->Wizard->_data['organisationId'])) {
        $organisation_id = $this->stone->Wizard->_data['organisationId'];
        $sth = $this->stone->pdo->prepare ("INSERT INTO link_address2organisation (address_id, organisation_id, address_type) VALUES
                               (:address_id, :organisation_id, 'general' )");
        $sth->execute(array(":address_id"=>$address_id, ":organisation_id" => $organisation_id));
      } else if (isset($this->stone->Wizard->_data['personId'])) {
        $person_id = $this->stone->Wizard->_data['personId'];
        $sth = $this->stone->pdo->prepare ("INSERT INTO link_address2person (address_id, person_id, address_type) VALUES
                               (:address_id, :person_id, 'general' )");
        $sth->execute(array(":address_id"=>$address_id, ":person_id" => $person_id));
      }


    }
    return $result;
  }
//------------------------------------------------------------------------------
}
