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
      array("declareWizard_chooseproject"=>array(
                               'render_raw'    => array( $this, "declareWizard_chooseproject_render_raw"), 
                               "process"       => array( $this, "declareWizard_chooseproject_process"))));

    $this->stone->Wizard->registerPage(
      array("declareWizard_declaretime"=>array(
                               'render_raw'    => array( $this, "declareWizard_declaretime_render_raw"), 
                               "process"       => array( $this, "declareWizard_declaretime_process"))));

  $this->stone->Wizard->registerPage(
      array("declareWizard_declaretime"=>array(
                               'render_raw'    => array( $this, "declareWizard_declaretime_render_raw"), 
                               "process"       => array( $this, "declareWizard_declaretime_process"))));


  $this->stone->Wizard->registerPage(
      array("addProject_choosecustomer"=>array(
                               'render_raw'    => array( $this, "addProject_choosecustomer_render_raw"), 
                               "process"       => array( $this, "addProject_choosecustomer_process"))));

  $this->stone->Wizard->registerPage(
      array("addProject_projectInfo"=>array(
                               'render_raw'    => array( $this, "addProject_projectInfo_render_raw"), 
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

    switch ($this->stone->_request[1]) {
      case "add":
        $this->stone->Wizard->initPage("addProject_choosecustomer");
        $this->stone->Wizard->process();    
        $this->stone->Wizard->render();          
        break;
      case "declare":    
        $this->stone->Wizard->initPage("declareWizard_chooseproject");
        $this->stone->Wizard->process();    
        $this->stone->Wizard->render();          
        break;
      case "view":
        $this->view();
        break;
    }
    //if ($request[1]=="add") Project::addWizard();
    //else if ($request[1]=="declare") Project::declareWizard();
    //else Project::view();
    
  }
//------------------------------------------------------------------------------
  function addProject_choosecustomer_render_raw(){
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
    $form = "<form method=post>";
    $form .= "<table><tr><td></td><td><input type=submit name=internal value='Intern project'></td></tr>";
    $form .= "<tr><td>Kies klant</td><td>";
    $form .= "<select size=5 name=customer_id>";
    while ($customer = $sth->fetch()){
      $form .= "<OPTION VALUE=". $customer['customer_id'] . ">". sprintf("%04d ",$customer['customer_id']) . $customer['customer_name'] . "</option>";
    }
    $form .="</select></td></tr>";
    $form .="<tr><td></td><td><input type=submit value=volgende></td></tr></table>";
    $form .= "</form>";     
    return $form;
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
  function addProject_projectInfo_render_raw(){
    $form = "<form method=post>";
    $form .= "<table>";
    $form .= "<tr><td>Projectnaam</td><td><input type=text name=description_short></td></tr>";
    $form .= "<tr><td>Omschrijving</td><td><textarea name=description_long cols=65 rows=5></textarea></td></tr>";

    if (isset($this->stone->Wizard->_data['customerId'])) {
      //$form .= "project voor klant";
      $form .= "<tr><td>Kostenberekening</td><td><select name=billing_type>";
      $form .= "<option value=timed>Uurtarief</option>";
      $form .= "<option value=fixed>Vast bedrag</option>";
      $form .= "</select></td></tr>";
      $form .= "<tr><td>Bedrag (centen)</td><td><input type=number name=billing_rate></td></tr>";
    } else {
      //$form .= "intern project";
    }
    $form .= "<tr><td>Status</td><td><select name=status>";
    $form .= "<option value=planned>Gepland</option>";
    $form .= "<option value=running>Lopende</option>";
    $form .= "<option value=finished>Afgerond</option>";
    $form .= "</select></td></tr>";
    $form .="<tr><td></td><td><input type=submit value=volgende></td></tr>";
    $form .= "</table></form>";
    return $form;
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
  function declareWizard_chooseproject_render_raw(){
    $sth = $this->stone->pdo->prepare("SELECT project_id, project_description_short FROM project ");
    $sth->execute();
    $form = "<form method=post><table>";
    while ($project = $sth->fetch()){
      $form.="<tr><td>" . $project['project_id'] . "</td><td>" . $project['project_description_short'] . "</td><td><button name=declareProject value=".$project['project_id'].">Declareren</button></td></tr>";
    }
    $form.="</table></form>";
    return $form;
  }
//------------------------------------------------------------------------------
  function declareWizard_chooseproject_process(){
    $result = array();
    if (isset($_POST['declareProject'])){
      $result['next_page']="declareWizard_declaretime";
      $this->stone->Wizard->_data['projectId'] = $_POST['declareProject'];
    } 
    return $result;
  }
//------------------------------------------------------------------------------
  function declareWizard_declaretime_render_raw(){
    $form = "<form method=post><table>";
    $form .= "<tr><td>Uren</td><td><input type=number name=hours min=0 max=12></td></tr>";
    $form .= "<tr><td>Kwartier</td><td><input type=number name=quarters min=0 max=3></td></tr>";
    // make it default to 'today'
    // note: Firefox doesn't yet support 'date', Chromium does
    $form .= "<tr><td>Datum</td><td><input type=date name=date value=".date('Y-m-d') ."></td></tr>";
    $form .= "<tr><td></td><td><input name=declare type=submit value=Declareren></td></tr>";
    $form.="</table></form>";
    return $form;
  }
//------------------------------------------------------------------------------
  function declareWizard_declaretime_process(){
    $result = array();
    if (@$_POST['date'] && @$_POST['quarters']){ 
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
