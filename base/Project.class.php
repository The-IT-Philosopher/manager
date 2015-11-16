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


class Project extends Component {



  function init(){
    $this->stone->Page->registerPage(
      array("projects" => array (
        "process" => array( $this, "ProcessPage")
      )));

    $this->stone->Wizard->registerPage(
      array("chooseproject_decl"=>array(
                               'render_xml'    => array( $this, "chooseproject_render_xml"), 
                               "process"       => array( $this, "chooseproject_decl_process"))));

    $this->stone->Wizard->registerPage(
      array("chooseproject_view"=>array(
                               'render_xml'    => array( $this, "chooseproject_render_xml"), 
                               "process"       => array( $this, "chooseproject_view_process"))));

  $this->stone->Wizard->registerPage(
      array("showProjectDetails"=>array(
                               'render_raw'    => array( $this, "showProjectDetails_render_raw"), 
                               "process"       => array( $this, "showProjectDetails_process"))));


    $this->stone->Wizard->registerPage(
      array("declareWizard_declaretime"=>array(
                               'render_raw'    => array( $this, "declareWizard_declaretime_render_raw"), 
                               "process"       => array( $this, "declareWizard_declaretime_process"))));


    $this->stone->Wizard->registerPage(
      array("addProject_choosecustomer"=>array(
                               'render_xml'    => array( $this, "addProject_choosecustomer_render_xml"), 
                               "process"       => array( $this, "addProject_choosecustomer_process"))));

    $this->stone->Wizard->registerPage(
      array("addProject_projectInfo"=>array(
                               'render_xml'    => array( $this, "addProject_projectInfo_render_xml"), 
                               "process"       => array( $this, "addProject_projectInfo_process"))));

  }
//------------------------------------------------------------------------------
  private $_donePage = "done";
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }
//------------------------------------------------------------------------------
  function ProcessPage(){

    $this->stone->_data['content_raw'] .= "Projectenbeheer<br>"; 
    $this->stone->_data['content_raw'] .= "<a href=add><button>Nieuw Project</button></a>";
    $this->stone->_data['content_raw'] .= "<a href=declare><button>Uren declareren</button></a>";
    $this->stone->_data['content_raw'] .= "<a href=view><button>Projecten tonen</button></a>";


    // TODO I suppose we should register subpages and add a WizardPage
    switch ($this->stone->_request[1]) {
      case "add":
        $this->stone->Wizard->initPage("addProject_choosecustomer");
        $this->stone->Wizard->process();    
        $this->stone->Wizard->render();          
        break;
      case "declare":    
        $this->stone->Wizard->initPage("chooseproject_decl");
        $this->stone->Wizard->process();    
        $this->stone->Wizard->render();          
        break;
      case "view":
        //$this->view();
        $this->stone->Wizard->initPage("chooseproject_view");
        $this->stone->Wizard->process();    
        $this->stone->Wizard->render();          
        break;
    }
    //if ($request[1]=="add") Project::addWizard();
    //else if ($request[1]=="declare") Project::declareWizard();
    //else Project::view();
    
  }
//------------------------------------------------------------------------------
  function addProject_choosecustomer_render_xml(){
    $sth = $this->stone->pdo->prepare("SELECT customer_id, customer_name
                          FROM (SELECT customer_id,organisation_name as customer_name
                                FROM   link_customer2organisation
                                JOIN   organisation 
                                ON link_customer2organisation.organisation_id = organisation.organisation_id) ALIAS_A  
                          UNION (SELECT customer_id,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name)  as customer_name
                                FROM   link_customer2person
                                JOIN    person
                                ON link_customer2person.person_id = person.person_id ) ");
    $sth->execute();
    $form = new Form();
    $form->addElement(new FormButtonElement("internal",'0000 - Intern project'));


    while ($customer = $sth->fetch()){
      // in the render_raw we had a select with height 5
      // we need a better customer seleting system, searchable, etc. later anyways.
      // but for now thsi will suffice
      $form->addElement(new FormButtonElement("customer_id",sprintf("%04d - %s",$customer['customer_id'], $customer['customer_name']), $customer['customer_id']));
    }

    return $form->GenerateForm(NULL, "Kies een klant", false);
  }
//------------------------------------------------------------------------------
  function addProject_choosecustomer_process() {
    $result = array();  
    if (isset($_POST['customer_id'])) {
      $this->stone->Wizard->_data['customerId']= $_POST['customer_id'];
      $result['next_page'] = "addProject_projectInfo";
    } else if (isset($_POST['internal'])) {
      $result['next_page'] = "addProject_projectInfo";
    }
  return $result;
  }
//------------------------------------------------------------------------------
  function addProject_projectInfo_render_xml(){

    $form = new Form();
    $form->addElement(new FormInputElement("description_short", "Projectnaam"));
    $form->addElement(new FormInputElement("description_long", "Omschrijving", "textarea"));

    //$form .= "<tr><td>Projectnaam</td><td><input type=text name=description_short></td></tr>";
    //$form .= "<tr><td>Omschrijving</td><td><textarea name=description_long cols=65 rows=5></textarea></td></tr>";


    if (isset($this->stone->Wizard->_data['customerId'])) {
      //$form .= "project voor klant";
      $select = new FormInputElement("billing_type", "Kostenberekening", "select");
      $form->addElement($select);
      $select->addOption(new FormSelectOptionElement("timed", "Uurtarief"));
      $select->addOption(new FormSelectOptionElement("fixed", "Vast bedrag"));
      $form->addElement(new FormInputElement("billing_rate","Bedrag (in centen)", "number"));
    } else {

    }

    $select = new FormInputElement("status", "Status", "select");
    $form->addElement($select);
    $select->addOption(new FormSelectOptionElement("negotiation", "Voorbesprekingen"));
    $select->addOption(new FormSelectOptionElement("planned", "Gepland"));
    $select->addOption(new FormSelectOptionElement("running", "Lopend"));
    $select->addOption(new FormSelectOptionElement("finished", "Afgerond"));
    $select->addOption(new FormSelectOptionElement("cancelled", "Afgelast"));

    return $form->GenerateForm(NULL, "Voer projectgegevens in");
  }
//------------------------------------------------------------------------------
  function addProject_projectInfo_process(){
    $result=array();
    if (isset($_POST['description_short']) && strlen($_POST['description_short'])){
        //accept projects with just a short description
        //This might/will change in the future but we want
        //the manager up and running ASAP
        //Another consideration is adding project while still nagitiating details
        $insertData=array();
          $insertData[':project_description_short'] = $_POST['description_short'];
          $insertData[':project_description_long']  = $_POST['description_long'];
          $insertData[':project_status']            = $_POST['status'];

        if (isset($this->stone->Wizard->_data['customerId'])) {
          $sth = $this->stone->pdo->prepare("INSERT INTO project (project_description_short,
                                                      project_description_long,
                                                      project_billing_rate, 
                                                      project_billing_type,
                                                      project_status) VALUES
                                                     (:project_description_short,
                                                      :project_description_long,
                                                      :project_billing_rate, 
                                                      :project_billing_type,
                                                      :project_status) ");
          $insertData[':project_billing_rate']=$_POST['billing_rate'];
          $insertData[':project_billing_type']=$_POST['billing_type'];

        } else {
          $sth = $this->stone->pdo->prepare("INSERT INTO project (project_description_short,
                                                      project_description_long,
                                                      project_status) VALUES
                                                     (:project_description_short,
                                                      :project_description_long,
                                                      :project_status) ");

        }
        $sth->execute($insertData);
        $project_id = $this->stone->pdo->lastInsertId();
        if (isset($this->stone->Wizard->_data['customerId'])) {
          $sth = $this->stone->pdo->prepare("INSERT INTO link_customer2project (project_id,customer_id) values (:project_id,:customer_id)");
          $sth->execute(array(":customer_id"=>$this->stone->Wizard->_data['customerId'],":project_id"=>$project_id));
          
        }
        $result['next_page'] = $this->_donePage;
      }
    return $result;  
  }
//------------------------------------------------------------------------------
  function chooseproject_render_xml(){
    $sth = $this->stone->pdo->prepare("SELECT project_id, project_description_short FROM project ");
    $sth->execute();
    $form = new Form();
    while ($project = $sth->fetch()){
      $form->addElement(new FormButtonElement("project_id",$project['project_description_short'], $project['project_id']));
    }
  return $form->GenerateForm(NULL, "Kies project");
  }
//------------------------------------------------------------------------------
  function showProjectDetails_render_raw(){
    $result = "<p>RenderRawDetails<br>We need a new rederer soon</p>";
    $currentMonth = date("n");

    $sth = $this->stone->pdo->prepare("SELECT * FROM project WHERE  project_id = :projectId");
    $sth->execute(array(":projectId"=>$this->stone->Wizard->_data['projectId']));
    $projectInfo = $sth->fetch();
    $result .= "<h1>Projectinformatie</h1>";
    $result .= "<h2>".$projectInfo['project_description_short']."</h2>";
    $result .= "<div>".$projectInfo['project_description_long']."</div>";

    // next to show, who is the project owner, billing rate, project state
    // then perhaps something like below, hours per month or so


    $sth = $this->stone->pdo->prepare("SELECT  all_customers.customer_id, customer_name  FROM 
                                       (SELECT * FROM 
                                         (SELECT customer_id,organisation_name as customer_name
                                          FROM   link_customer2organisation
                                          JOIN   organisation 
                                          ON link_customer2organisation.organisation_id = organisation.organisation_id) as ALIAS_A
                                        UNION (SELECT customer_id,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name)  as customer_name
                                          FROM   link_customer2person
                                          JOIN    person
                                            ON link_customer2person.person_id = person.person_id ) 
                                        ) as all_customers 
                                       JOIN link_customer2project 
                                         ON link_customer2project.customer_id = all_customers.customer_id 
                                       JOIN project on project.project_id = link_customer2project.project_id
                                       WHERE project.project_id = :projectId");
// Damn it becomes a huge ugly query... it seems the (SELECT * FROM...) is required to give the result of the UNION an alias  

    $sth->execute(array(":projectId"=>$this->stone->Wizard->_data['projectId']));
    $projectCustomer = $sth->fetch();
    if ($projectCustomer) {
      $result .= "<h1>Klantinformatie</h1>";
      $result .= "<h2>".$projectCustomer['customer_name']."</h2>";
    }  
/*
  this is a nice query for somewhere else, show hours per project per month, make a place for that
    $sth = $this->stone->pdo->prepare("SELECT project_id,  project_description_short
      SUM(project_hours_hours) + 0.25 * SUM(project_hours_quarters) AS project_hours_total 
                                       FROM project_hours 
                                       JOIN project on project.project_id = project_hours.project_id 
                                       WHERE MONTH(project_hours_date) = :currentMonth GROUP BY project_id");
    $sth->execute(array(":currentMonth"=>$currentMonth));
*/
    return $result;
  }
//------------------------------------------------------------------------------
  function chooseproject_decl_process(){
    $result = array();
    if (isset($_POST['project_id'])){
      $result['next_page']="declareWizard_declaretime";
      $this->stone->Wizard->_data['projectId'] = $_POST['project_id'];
    } 
    return $result;
  }
//------------------------------------------------------------------------------
  function chooseproject_view_process(){
    $result = array();
    if (isset($_POST['project_id'])){
      $result['next_page']="showProjectDetails";
      $this->stone->Wizard->_data['projectId'] = $_POST['project_id'];
    } 
    return $result;
  }
//------------------------------------------------------------------------------
  function declareWizard_declaretime_render_raw(){
    // current form implementation cannot have parameters such as min and max yet
    // this will be postponed till the next iteration of the rendering engine 
    $form = "<form method=post><table>";
    $form .= "<tr><td>Uren</td><td><input type=number name=hours min=0 max=12></td></tr>";
    $form .= "<tr><td>Kwartier</td><td><input type=number name=quarters min=0 max=3></td></tr>";
    $form .= "<tr><td>Datum</td><td><input type=date name=date value=".date('Y-m-d') ."></td></tr>";
    $form .= "<tr><td></td><td><input name=declare type=submit value=Declareren></td></tr>";
    $form.="</table></form>";
    return $form;
  }
//------------------------------------------------------------------------------
  function declareWizard_declaretime_process(){
    $result = array();
    if (@$_POST['date']){ 
      $sth = $this->stone->pdo->prepare("INSERT INTO project_hours (project_id, project_hours_date,project_hours_hours,project_hours_quarters) VALUES (:project_id, :date,:hours,:quarters)");
      $retval = $sth->execute(array(":project_id" => $this->stone->Wizard->_data['projectId'], 
                          ":date" =>$_POST['date'] ,
                          ":hours"=>$_POST['hours'] ,
                          ":quarters"=>$_POST['quarters']));
      if ($retval===false) {
        $this->stone->_data['content_raw'] .= "<pre>DB ERROR\n".$this->stone->pdo->errorInfo().var_export($sth->errorInfo(),true)."</pre>";
      } else
        $result['next_page']=$this->_donePage;
    }
    return $result;
  }
//------------------------------------------------------------------------------
  function view(){
    $sth = $this->stone->pdo->prepare("SELECT project_id, project_description_short FROM project ");
    $sth->execute();
    $this->stone->_data['content_raw'] .= "<table>";
    while ($project = $sth->fetch()){
      $this->stone->_data['content_raw'] .="<tr><td>" . $project['project_id'] . "</td><td>" . $project['project_description_short'] . "</td></tr>";
    }
    $this->stone->_data['content_raw'] .= "</table>";
  }
}
