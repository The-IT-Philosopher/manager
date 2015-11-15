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


// TODO: rename so Wizard becomes a prefix
// Also ... see how to create a good structure
class KvK extends Component {
//------------------------------------------------------------------------------
  private $_donePage = "done";
  
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }
//------------------------------------------------------------------------------
  function init() {
    $this->stone->Wizard->registerPage(
      array("kvk_enter"=>array( 
                               'render_xml'=> array($this, "kvk_enter_render_xml"), 
                               "process"   => array($this, "kvk_enter_process"))));

  }
//------------------------------------------------------------------------------
  function kvk_enter_render_xml(){
    $form = new Form();
    $form->addElement(new FormInputElement("kvk","KvK Nummer", "number"));
    return $form->GenerateForm(NULL, "Voer KvK nummer in", true);
  }
//------------------------------------------------------------------------------
  function kvk_enter_process() {
    $result = array();
    if (!isset($_POST)) return $result;
    // for now, request class instance directly.
    // later, we'll query for the data properties it provides and
    // determine the class we need that way.
    $DP = $this->stone->DP_OverheidIO; 
    if ($DP==NULL) {
      $this->stone->_data['content_raw'] .= "<pre>Could not get DataProvider!!!</pre>";
      return;
    }
    if ($DP->check($_POST['kvk'])) {
      //--    

      $kvkData = $DP->getAddress();
      $this->stone->Wizard->_data['kvkData'] = $kvkData;
      
      $result['next_page']= $this->_donePage;


      
      

      $sth = $this->stone->pdo->prepare("INSERT INTO address (address_street, address_number, address_postalcode, address_city, address_country) 
                            VALUES (:address_street, :address_number, :address_postalcode, :address_city, 'NL')");
      $insertData = array();
      $insertData[':address_street']    = $kvkData['address_street'];
      $insertData[':address_number']    = $kvkData['address_number'];
      $insertData[':address_postalcode']= $kvkData['address_postalcode'];
      $insertData[':address_city']= $this->stone->Wizard->_data['kvkData']['address_city'];
      $sth->execute($insertData);
      $address_id = $this->stone->pdo->lastInsertId(); 
      $this->stone->Wizard->_data['addressId'] = $address_id; 
      $sth = $this->stone->pdo->prepare("INSERT INTO organisation (organisation_name, organisation_type, organisation_nl_kvk, organisation_country)
                            VALUES (:organisation_name, :organisation_type, :organisation_nl_kvk, 'NL')");


      $insertData = array();
      $insertData[':organisation_name'] = $kvkData['organisation_name'];
      $insertData[':organisation_type'] = $this->stone->Wizard->_data['organisationType'];
      $insertData[':organisation_nl_kvk'] = $kvkData['kvk_nummer'];
      $sth->execute($insertData);
      $organisation_id = $this->stone->pdo->lastInsertId();
      $this->stone->Wizard->_data['organisationId'] = $organisation_id;
      $sth = $this->stone->pdo->prepare ("INSERT INTO link_address2organisation (address_id, organisation_id, address_type) VALUES
                             (:address_id, :organisation_id, 'validated' )");
      $sth->execute(array(":address_id"=>$address_id, ":organisation_id" => $organisation_id));


    } else {
      // for now
      $result['error'] = "KvK did not validate, or validation error";
    }
    return $result;
  }
//------------------------------------------------------------------------------

}
