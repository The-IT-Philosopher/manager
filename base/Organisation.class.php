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


class Organisation extends Component {

//------------------------------------------------------------------------------
  private $_donePage = "done"; 
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }
//------------------------------------------------------------------------------  
  function init() {

    $this->stone->Page->registerPage(
      array("organisations" => array (
        "process" => array( $this, "Wizard_Organisation_ProcessPage")
      )));

    $this->stone->Wizard->registerPage(
      array("Wizard_Organisation_ChooseCountry"=>array(
                               'render_xml'    => array( $this, "Wizard_Organisation_ChooseCountry_render_xml"), 
                               "process"       => array( $this, "Wizard_Organisation_ChooseCountry_process"))));

    $this->stone->Wizard->registerPage(
      array("Wizard_Organisation_ChooseCountryEU"=>array(
                               'render_xml' => array( $this, "Wizard_Organisation_ChooseCountryEU_render_xml"), 
                               "process"    => array( $this, "Wizard_Organisation_ChooseCountryEU_process"))));

    $this->stone->Wizard->registerPage(
      array("Wizard_Organisation_ChooseCountryNOTEU"=>array(
                               'render_xml'    => array( $this, "Wizard_Organisation_ChooseCountryNOTEU_render_xml"),                                
                               "process"       => array( $this, "Wizard_Organisation_ChooseCountryNOTEU_process"))));

    $this->stone->Wizard->registerPage(
      array("Wizard_Organisation_ChooseOrganisationType"=>array(
                               'render_xml'    => array( $this, "Wizard_Organisation_ChooseOrganisationType_render_xml"),  
                               "process"       => array( $this, "Wizard_Organisation_ChooseOrganisationType_process"))));



    $this->stone->Wizard->registerPage(
      array("Wizard_Organisation_OrganisationName"=>array(
                               'render_xml'    => array( $this, "organisation_name_render_xml"),  
                               "process"       => array( $this, "organisation_name_process"))));

  }
//------------------------------------------------------------------------------
  function organisation_name_render_xml(){
    $form = new Form();
    $form->addElement(new FormInputElement("organisation_name","Naam organisatie"));
    $data = NULL;
    if (isset($this->stone->Wizard->_data['viesData'])){
      if (strlen($this->stone->Wizard->_data['viesData']['organisation_name'])) {
        $data['organisation_name'] = $this->stone->Wizard->_data['viesData']['organisation_name'];
      }
    }
    return $form->GenerateForm($data, "Voer naam organisatie in");
  }
//------------------------------------------------------------------------------
  function organisation_name_process(){
    $result = array();
    if (strlen(@$_POST['organisation_name'])) {

      $sth = $this->stone->pdo->prepare("INSERT INTO organisation (organisation_name, organisation_type , organisation_country)
                            VALUES (:organisation_name, :organisation_type, :organisation_country)");

      $insertData = array();
      $insertData[':organisation_name'] = $_POST['organisation_name'];
      $insertData[':organisation_type'] = $this->stone->Wizard->_data['organisationType'];
      $insertData[':organisation_country'] = $this->stone->Wizard->_data['organisationCountry'];
      $sth->execute($insertData);
      $organisation_id = $this->stone->pdo->lastInsertId();
      $this->stone->Wizard->_data['organisationId'] = $organisation_id;
      $result['next_page'] = "address_enter";


      if (isset($this->stone->Wizard->_data['viesData'])) {
        $vat_number = $this->stone->Wizard->_data['viesData']['vat_number'];
        $sth = $this->stone->pdo->prepare("UPDATE organisation 
                        SET    organisation_vat = :organisation_vat 
                        WHERE  organisation_id  = :organisation_id");
        $sth->execute(array(":organisation_vat" => $vat_number, "organisation_id" => $this->stone->Wizard->_data['organisationId']  ));
      }


    }
    return $result;
  }
//------------------------------------------------------------------------------

  function Wizard_Organisation_ProcessPage() {
    // we need propper rendering later .... 
    $this->stone->_data['content_raw'] .= "<A href='add'><button>Toevoegen</button></a><a href='show'><button>Tonen</button></a><br>";

    if ($this->stone->_request[1]=="add") {
      $this->stone->Wizard->initPage("Wizard_Organisation_ChooseCountry");
      $this->stone->Organisation->setDonePage("person_enter");
      $this->stone->Wizard->process();    
      $this->stone->Wizard->render();
    }

    if ($this->stone->_request[1]=="show") {
    $this->stone->_data['content_raw'] .= "todo<br>";
    }

  }

//------------------------------------------------------------------------------
  function Wizard_Organisation_ChooseCountry_render_xml(){
    $form = new Form();
    $form->addElement(new FormButtonElement("country","Nederland", "NL"));
    $form->addElement(new FormButtonElement("region","EU", "EU"));
    $form->addElement(new FormButtonElement("region","Buiten EU", "NOTEU"));
    return $form->GenerateForm(NULL, "Kies land", true);
  }
//------------------------------------------------------------------------------
function Wizard_Organisation_ChooseCountryEU_render_xml(){
    $sth  = $this->stone->pdo->prepare("SELECT alpha2, langNL FROM country where alpha2 IN (SELECT alpha2 from country_vies) ORDER BY langNL"); 
    $sth->execute();
    $form = new Form();
    $select = new FormInputElement("country", "Land", "select");
    $form->addElement($select);
    while ($country = $sth->fetch()) {
      $select->addOption(new FormSelectOptionElement($country['alpha2'], $country['langNL']));
    }
    return $form->GenerateForm(NULL, "Kies land", false);
  }
//------------------------------------------------------------------------------
function Wizard_Organisation_ChooseCountryNOTEU_render_xml(){
    $sth  = $this->stone->pdo->prepare("SELECT alpha2, langNL FROM country where alpha2 NOT IN (SELECT alpha2 from country_vies) ORDER BY langNL"); 
    $sth->execute();
    $form = new Form();
    $select = new FormInputElement("country", "Land", "select");
    $form->addElement($select);
    while ($country = $sth->fetch()) {
      $select->addOption(new FormSelectOptionElement($country['alpha2'], $country['langNL']));
    }
    return $form->GenerateForm(NULL, "Kies land", false);
  }

//------------------------------------------------------------------------------
  function Wizard_Organisation_ChooseOrganisationType_render_xml(){
    $form = new Form();
    $form->addElement(new FormButtonElement('organisationType', "In oprichting",'in_formation'));
    $form->addElement(new FormButtonElement('organisationType', "Vereniging (zonder kvk)","association_unregged"));
    $form->addElement(new FormButtonElement('organisationType', "Vereniging (met kvk)","association_regged"));
    $form->addElement(new FormButtonElement('organisationType', "Stichting","foundation"));
    $form->addElement(new FormButtonElement('organisationType', "Bedrijf","company"));
    $form->addElement(new FormButtonElement('organisationType', "Anders","other"));
    return $form->GenerateForm(NULL, "Kies type organisatie", true);
  }
//------------------------------------------------------------------------------
  function Wizard_Organisation_ChooseCountry_process(){
    if (!(isset($_POST['country'])||isset($_POST['region']))) return array();
    $result = array();
    if (isset($_POST['country']) && $_POST['country']=='NL') {
      $this->stone->Wizard->_data['organisationCountry'] = "NL";
      $this->stone->Wizard->_data['organisationCountryVIES'] = 1;
        $result['next_page'] = "Wizard_Organisation_ChooseOrganisationType";
    }
    if (isset($_POST['region']))  {
      if ($_POST['region'] == "EU") {
        $result['next_page'] = "Wizard_Organisation_ChooseCountryEU";
      } else {
        $result['next_page'] = "Wizard_Organisation_ChooseCountryNOTEU";
      }
    }
    return $result;
  }

  function Wizard_Organisation_ChooseCountryEU_process(){
    $result = array();
    if (isset($_POST['country'])) {
      $result["next_page"]="Wizard_Organisation_ChooseOrganisationType";
      $this->stone->Wizard->_data['organisationCountry'] = $_POST['country'];
      $this->stone->Wizard->_data['organisationCountryVIES'] = 1;
    }
    return $result;
  }

  function Wizard_Organisation_ChooseCountryNOTEU_process(){
    $result = array();
    if (isset($_POST['country'])) {
      $result["next_page"]="Wizard_Organisation_ChooseOrganisationType";
      $this->stone->Wizard->_data['organisationCountry'] = $_POST['country'];
      $this->stone->Wizard->_data['organisationCountryVIES'] = 0;
    }
    return $result;
  }

  function Wizard_Organisation_ChooseOrganisationType_process(){
    $result = array();    
    if (isset($_POST['organisationType'])) {
      $result['next_page'] ="Wizard_Organisation_OrganisationName";
      $this->stone->Wizard->_data['organisationType'] = $_POST['organisationType'];
      if ($this->stone->Wizard->_data['organisationCountry']=="NL") {
        if (! in_array( $this->stone->Wizard->_data['organisationType'], array('in_formation','association_unregged') )) {
          // Dutch organisation registered by the KvK (Dutch Chamber of Commerce)
          $Wizard_KvK = $this->stone->KvK;
          if ($Wizard_KvK) {
            $Wizard_KvK->setDonePage($this->_donePage);
            $result['next_page'] = "kvk_enter"; 
          } else {
            //$result['error'] = "KvK Wizard not available"; 
          }
        }        
      } else if ($this->stone->Wizard->_data['organisationCountryVIES']) {
        if (! in_array( $this->stone->Wizard->_data['organisationType'], array('in_formation','association_unregged', 'association_regged') )) {
          // EU Organisation might have a tax number (Companies always have one, but foundations might not)
          $Wizard_VIES = $this->stone->VIES;
          if ($Wizard_VIES) {
            // TODO : first enter name/address
            //        then invoke VIES
            $Wizard_VIES->setDonePage("Wizard_Organisation_OrganisationName");
            $result['next_page'] = "vies_enter"; 
          } else {
            //$result['error'] = "VIES Wizard not available"; 
          }
        }
      } 
    }
    return $result;
  }
}
?>
