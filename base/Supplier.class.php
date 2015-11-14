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


class Supplier extends Component {

  function init() {

    $this->stone->Page->registerPage(
      array("suppliers" => array (
        // perhaps this needs a different name then "process"
        "process" => array( $this, "ProcessPage")
      )));

    $this->stone->Wizard->registerPage(
      array("Supplier_add_existing"=>array(
                               'render_raw'    => array( $this, "add_existing_render_raw"), 
                               "process"       => array( $this, "add_existing_process"))));

    $this->stone->Wizard->registerPage(
      array("Supplier_add_existing_organisation"=>array(
                               'render_raw'    => array( $this, "add_existing_organisation_render_raw"), 
                               "process"       => array( $this, "add_existing_process"))));

    $this->stone->Wizard->registerPage(
      array("Supplier_add_existing_person"=>array(
                               'render_raw'    => array( $this, "add_existing_person_render_raw"), 
                               "process"       => array( $this, "add_existing_process"))));

  }
//------------------------------------------------------------------------------
  function ProcessPage() {
    // we need propper rendering later .... 
    $this->stone->_data['content_raw'] .= "<A href='addnewperson'><button>Nieuwe Persoon</button></a>";
    $this->stone->_data['content_raw'] .= "<A href='addneworganisation'><button>Nieuwe Organisatie</button></a>";

    $this->stone->_data['content_raw'] .= "<a href='addexistingperson'><button>Bestaande Persoon</button></a>";
    $this->stone->_data['content_raw'] .= "<a href='addexistingorganisation'><button>Bestaande Organisatie</button></a>";

    $this->stone->_data['content_raw'] .= "<A href='show'><button>Toon alle klanten</button></a><br>";

    if ($this->stone->_request[1]=="addnewperson") {
    $this->stone->_data['content_raw'] .= "todo (addnewperson)<br>";
/*
      $this->stone->Wizard->initPage("Wizard_Organisation_ChooseCountry");
      $this->stone->Organisation->setDonePage("person_enter");
      $this->stone->Wizard->process();    
      $this->stone->Wizard->render();
*/
    }

    if ($this->stone->_request[1]=="addneworganisation") {

      //TODO MOVE DATA TO WIZARD, IMPLEMENT RESET
      $this->stone->Wizard->initPage("Wizard_Organisation_ChooseCountry");
      //TODO HANDOVER
      $this->stone->Organisation->setDonePage("Supplier_add_existing_organisation");
      $this->stone->Wizard->process();    
      $this->stone->Wizard->render();
    }

    if ($this->stone->_request[1]=="addexistingperson") {
      $this->stone->Wizard->initPage("Supplier_add_existing_person");
      $this->stone->Organisation->setDonePage("done");
      $this->stone->Wizard->process();    
      $this->stone->Wizard->render();
    }

    if ($this->stone->_request[1]=="addexistingorganisation") {
      $this->stone->Wizard->initPage("Supplier_add_existing_organisation");
      $this->stone->Organisation->setDonePage("done");
      $this->stone->Wizard->process();    
      $this->stone->Wizard->render();
    }


    if ($this->stone->_request[1]=="show") {
      $this->show();
    }

  }
//------------------------------------------------------------------------------
  function addPerson($personId) {
    $sth = $this->stone->pdo->prepare("INSERT INTO supplier (supplier_id) VALUES (NULL)");
    $sth->execute();
    $insertData = array();

    $insertData[':supplier_id']=$this->stone->pdo->lastInsertId();
    $insertData[':person_id']=$personId;

    $sth = $this->stone->pdo->prepare("INSERT INTO link_supplier2person (supplier_id, person_id) 
                          VALUES (:supplier_id, :person_id)");

    $sth->execute($insertData);
  }
//------------------------------------------------------------------------------
  function addOrganisation($organisationId) {

    $sth = $this->stone->pdo->prepare("INSERT INTO supplier (supplier_id) VALUES (NULL)");
    $sth->execute();
    $insertData = array();

    $insertData[':supplier_id']=$this->stone->pdo->lastInsertId();
    $insertData[':organisation_id']=$organisationId;

    $sth = $this->stone->pdo->prepare("INSERT INTO link_supplier2organisation (supplier_id, organisation_id)
                          VALUES (:supplier_id, :organisation_id)");
 
    $sth->execute($insertData);
  }
//------------------------------------------------------------------------------
  function show() {
    $sth = $this->stone->pdo->prepare("SELECT supplier_id, supplier_name
                          FROM (SELECT supplier_id,organisation_name as supplier_name
                                FROM   link_supplier2organisation
                                JOIN   organisation 
                                ON link_supplier2organisation.organisation_id = organisation.organisation_id) ALIAS_A  
                          UNION (SELECT supplier_id,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name)  as supplier_name
                                FROM   link_supplier2person
                                JOIN    person
                                ON link_supplier2person.person_id = person.person_id ) ");
    $sth->execute();
    $this->stone->_data['content_raw'] .= "<table>";
    while ($supplier = $sth->fetch()){
      $this->stone->_data['content_raw'] .= "<tr><td>".sprintf("%04d ",$supplier['supplier_id'])."</td><td>".$supplier['supplier_name']."</td></tr>";

    }
    $this->stone->_data['content_raw'] .= "</table>";
  }
//------------------------------------------------------------------------------
  function add_existing_render_raw() {
    //STUB
    //$this->stone->_data['content_raw'] .= "<h2>Organisations</h2>";
    $this->add_existing_organisation_render_raw();
    //$this->stone->_data['content_raw'] .= "<h2>Persons</h2>";
    $this->add_existing_person_render_raw();
  }
//------------------------------------------------------------------------------
  function add_existing_process() {
    $result = array();
    if (isset($_POST['organisation_id'])) {
      $this->addOrganisation($_POST['organisation_id']);
      $result['next_page'] = "done";
    }
    if (isset($_POST['person_id'])) {
      $this->addPerson($_POST['person_id']);
      $result['next_page'] = "done";
    }

    // handover wizard
    // TODO use same names, add person support
    if (isset($this->stone->Wizard->_data['organisationId'])) {
      $this->addOrganisation($this->stone->Wizard->_data['organisationId']);
      $result['next_page'] = "done";
    }





    return $result;
  }
//------------------------------------------------------------------------------
  function add_existing_organisation_render_raw() {
    $this->stone->_data['content_raw'] .= "<h2>Organisations</h2>";
    $sth = $this->stone->pdo->prepare("SELECT organisation_id, organisation_name
                                       FROM organisation
                                       WHERE organisation_id NOT IN 
                                           ( SELECT organisation_id 
                                             FROM link_supplier2organisation
                                           )");

    $sth->execute();
    $this->stone->_data['content_raw'] .= "<form method=post><table>";
    while ($supplier = $sth->fetch()){

      $this->stone->_data['content_raw'] .= "<tr><td>".$supplier['organisation_name']."</td>";
      $this->stone->_data['content_raw'] .= "<td><button name=organisation_id value=".$supplier['organisation_id'].">Toevoegen</button></td></tr>";
    }
    $this->stone->_data['content_raw'] .= "</table></form>";

  }
//------------------------------------------------------------------------------
  function add_existing_person_render_raw() {
    $this->stone->_data['content_raw'] .= "<h2>Persons</h2>";
    //TODO: also check if a person is not linked to an organisation?
    //A person might be both individual and coorperate supplier
    $sth = $this->stone->pdo->prepare("SELECT person_id ,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name) as person_name
                                       FROM person
                                       WHERE person_id NOT IN 
                                           ( SELECT organisation_id 
                                             FROM link_supplier2person
                                           )");

    $sth->execute();
    $this->stone->_data['content_raw'] .= "<form method=post><table>";
    while ($supplier = $sth->fetch()){
      $this->stone->_data['content_raw'] .= "<tr><td>".$supplier['person_name']."</td></tr>";
      $this->stone->_data['content_raw'] .= "<td><button name=organisation_id value=".$supplier['person_id'].">Toevoegen</button></td></tr>";
    }
    $this->stone->_data['content_raw'] .= "</table></form>";

  }



}
