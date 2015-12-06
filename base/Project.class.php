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
      array("chooseproject_invoice"=>array(
                               'render_xml'    => array( $this, "chooseproject_render_xml"), 
                               "process"       => array( $this, "chooseproject_invoice_process"))));


    $this->stone->Wizard->registerPage(
      array("invoiceWizard_verifyInvoice"=>array(
                               'render_raw'    => array( $this, "invoiceWizard_verifyInvoice_render_raw"), 
                               "process"       => array( $this, "invoiceWizard_verifyInvoice_process"))));

//


    $this->stone->Wizard->registerPage(
      array("invoiceWizard_chooseMonth"=>array(
                               'render_xml'    => array( $this, "invoiceWizard_chooseMonth_render_xml"), 
                               "process"       => array( $this, "invoiceWizard_chooseMonth_process"))));



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
    $this->stone->_data['content_raw'] .= "<a href=invoice><button>Facturatie</button></a>";


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
      case "invoice":
        //$this->view();
        $this->stone->Wizard->initPage("chooseproject_invoice");
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
  function invoiceWizard_chooseMonth_render_xml() {
    $form = new Form();
    $form->addElement(new FormInputElement("month","Maand", "month"));
    $form->addElement(new FormInputElement("begin","Begin", "date"));
    $form->addElement(new FormInputElement("end","Eind", "date"));
    $form->addElement(new FormInputElement("already_billed","Hergenereer", "checkbox"));
    return $form->GenerateForm(NULL, "Kies maand");
}
//------------------------------------------------------------------------------
  function invoiceWizard_chooseMonth_process() {
    $result = array();

    //DEBUG
    //$this->stone->_data['content_raw'] .= "<PRE>" . var_export($_POST,true) . "</PRE>";

    if (strlen(@$_POST['month'])) {
      $month = explode("-",$_POST['month']);
      //$this->stone->_data['content_raw'] .= "<PRE>" . var_export($month,true) . "</PRE>";
      $this->stone->Wizard->_data['invoiceData'] = $this->stone->Invoice->generateProjectMonthly($this->stone->Wizard->_data['projectId'], $month[1], $month[0],  ($_POST['already_billed'] ? NULL : 0)  );
      $result['next_page'] = "invoiceWizard_verifyInvoice";
    } else     if (strlen(@$_POST['begin']) && strlen(@$_POST['end'])) {
      $this->stone->Wizard->_data['invoiceData'] = $this->stone->Invoice->generateProjectPeriod($this->stone->Wizard->_data['projectId'], $_POST['begin'], $_POST['end'],  ($_POST['already_billed'] ? NULL : 0)  );
      $result['next_page'] = "invoiceWizard_verifyInvoice";

    }

    return $result;
  }
//------------------------------------------------------------------------------

  function getHourIdsForMonth($projectId, $month=NULL , $year=NULL ,$billable=NULL, $billed = NULL) {
    if ($month==NULL) $month = date("n");    
    if ($year==NULL) $year = date("Y");

    $query = "SELECT project_hours_id
                                       FROM project_hours  
                                       WHERE MONTH(project_hours_date) = :month
                                        AND YEAR(project_hours_date) = :year 
                                             AND project_id = :projectId ";

    if ($billed !== NULL) {
      $query .= " AND project_hours_billed = " . ($billed ? "1 " : "0 ");
    }

    if ($billable !== NULL) {
      $query .= " AND project_hours_billable = " . ($billable ? "1 " : "0 ");
    }





    $sth = $this->stone->pdo->prepare($query);
    $sth->execute(array(":year"=>$year, ":month"=>$month, ":projectId"=>$projectId));
    return $sth->fetchAll(\PDO::FETCH_COLUMN);

 
  }
//------------------------------------------------------------------------------
  function invoiceWizard_verifyInvoice_render_raw() {
    //TODO STUB !!! RAW RENDERING!!! AND EVEN NOT DONE CORRECTLY
    $this->stone->Invoice->displayInvoice($this->stone->Wizard->_data['invoiceData']);
    $form = "<FORM METHOD=POST><TABLE>";
    $form .= "<tr><td>Factuurdatum</td><td><input type=date name=date value=".date('Y-m-d') ."></td></tr>";
    $form .= "<TR><TD><INPUT NAME=AcceptInvoice TYPE=SUBMIT VALUE=OK></td></tr></table></FORM>";
    return $form;
  }


//------------------------------------------------------------------------------
  function invoiceWizard_verifyInvoice_process() {
    if (isset($_POST['AcceptInvoice'])) {
      // steps :
      // generate new invoice           V
      // add hour_ids to link table
      // mark hours as billed


      // TODO:: to be moved to invoice
      $sth = $this->stone->pdo->prepare("INSERT INTO invoice (customer_id, invoice_sequence_nr, invoice_date) 
      VALUES
      (:customer_id, 
( SELECT 1 + IFNULL(MAX(invoice_sequence_nr),0) FROM invoice i2 WHERE customer_id = :customer_id ) ,
      :invoice_date) ");

      $invoice_date = $_POST['date'];
      $customer_id = $this->stone->Wizard->_data['invoiceData']['customer_id'];
      $sth->execute(array("customer_id" => $customer_id, ":invoice_date" => $invoice_date));
      $invoice_id = $this->stone->pdo->lastInsertId();
      
      foreach ($this->stone->Wizard->_data['invoiceData']['project_hours_id'] as $project_hour) {
        $sth = $this->stone->pdo->prepare("INSERT INTO link_invoice2project_hours 
                                           (invoice_id, project_hours_id)
                                        VALUES (:invoice_id, :project_hours_id)" );
        $sth->execute(array(":invoice_id" => $invoice_id, "project_hours_id" => $project_hour));

        $sth = $this->stone->pdo->prepare("UPDATE project_hours SET project_hours_billed = 1 WHERE project_hours_id = :project_hours_id");
        $sth->execute(array("project_hours_id" => $project_hour));
        
      }
    }    
  }

//------------------------------------------------------------------------------
    function getBillingAddress($projectId){
      $result = $this->stone->Customer->getCustomerAddress($this->getCustomerId($projectId));//,"billing");
      return $result;
    }

    function getCustomerInfo($projectId){
    // getCustomerInfo()?? // getBillingAddress()


      // for some reason this query appears to give duplicate results
      // also, we need to specify billing addresses in the customer entry

    
      $sth = $this->stone->pdo->prepare("SELECT  customer_name, address_street, address_number,address_postalcode, address_city, address_province, address_country  FROM  
                                         (SELECT * FROM 
                                           (SELECT customer_id,organisation_name as customer_name, address.*
                                            FROM   link_customer2organisation
                                            JOIN   organisation 
                                              ON link_customer2organisation.organisation_id = organisation.organisation_id 
                                            JOIN   link_address2organisation 
                                              ON link_address2organisation.organisation_id = organisation.organisation_id
                                            JOIN address
                                              ON link_address2organisation.address_id = address.address_id    
                                            # WHERE address_type = 'billing'  
                                            ) as ALIAS_A
                                            
                                          UNION (SELECT customer_id,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name)  as customer_name,  address.*
                                            FROM   link_customer2person
                                            JOIN    person
                                              ON link_customer2person.person_id = person.person_id 
                                            JOIN   link_address2person 
                                              ON link_address2person.person_id = person.person_id
                                            JOIN address
                                              ON link_address2person.address_id = address.address_id  
                                            # WHERE address_type = 'billing'      
                                            ) 
                                          ) as all_customers 
                                         JOIN link_customer2project 
                                           ON link_customer2project.customer_id = all_customers.customer_id 
                                         WHERE project_id = :projectId");
    // Damn it becomes a huge ugly query... it seems the (SELECT * FROM...) is required to give the result of the UNION an alias  

      $sth->execute(array(":projectId"=>$projectId));
      $result = $sth->fetch();


      // I suppose we could use a different approach... getting the address should be part of customer

      return $result;
  }
//------------------------------------------------------------------------------
  function getCustomerId($projectId) {
    $sth = $this->stone->pdo->prepare("SELECT customer_id 
                                        FROM link_customer2project
                                        WHERE project_id = :projectId");
    $sth->execute(array(":projectId"=>$projectId));
    return $sth->fetchColumn();
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





  $projectCustomer = $this->getCustomerInfo($this->stone->Wizard->_data['projectId']);
    if ($projectCustomer) {
      $result .= "<h1>Klantinformatie</h1>";
      $result .= "<h2>".$projectCustomer['customer_name']."</h2>";
    }  

    // TODO: We need a calendar generator
    //       But this will be part of the new output generator engine to be
    //        developed.

    // TODO: Extract all this code below to make a generator for a selectable 
    //          month. Something like generateCalendat($projectId, $month)
    //          And make the date clickable to declare at the clicked date.

                                         //-------------------------------------
    // begin hours for current month
    $sth = $this->stone->pdo->prepare("SELECT project_hours_date, sum(project_hours_hours) + 0.25 * sum(project_hours_quarters) as project_time
                                       FROM project_hours  
                                       WHERE MONTH(project_hours_date) = :currentMonth 
                                             AND project_hours.project_id = :projectId
                                       GROUP BY  project_hours_date
                                       ORDER BY  project_hours_date" );
    $sth->execute(array(":currentMonth"=>$currentMonth, ":projectId"=>$this->stone->Wizard->_data['projectId']));
    //$hoursThisMonth = $sth->fetchAll();
    $hoursThisMonth=array();
    while ($project_hour = $sth->fetch()) {
      $hoursThisMonth[$project_hour['project_hours_date']] = $project_hour['project_time'];
    }

    $result .= "<STYLE>table, th, td { border: 1px solid black; }</STYLE>";

    $daysThisMonth = date("t");
    $startDayMonth = date("w",strtotime(date("Y-").$currentMonth."-01")); 
      // using currentMonth as variable to allow for different months to be displayed
      // this should be part of the generating code -- also -- make the year a variable
      // in the end we should not be creating raw html code anyways .... but that's 
      // for later concern.
    $Month = array(); 
    $Week  = array();
    for ($day = 1 ; $day <= $daysThisMonth ; $day++) {
      $checkdate = date("Y-")  . $currentMonth. "-" . sprintf("%02d",$day);

      $hours = "<div class='d'>".date("d",strtotime($checkdate)) . "</div><div class='h'>" . (float)@$hoursThisMonth[$checkdate] ."</div>";
//debug//      $hours = "<div class='d'>".$checkdate . "</div><div class='h'>" . (int)@$hoursThisMonth[$checkdate] ."</div>";

      $checkDay = date("w",strtotime($checkdate));
      if ($checkDay == 0) {
        $Month[]=$Week;
        $Week=array();
      }
      $Week[$checkDay] = $hours;
    }
    $Month[]=$Week;
    $result .= "<table><tr><th>Zo</th><th>Ma</th><th>Di</th><th>Wo</th><th>Do</th><th>Vr</th><th>Za</th></tr>";
    foreach ($Month as $Week) {

      $result .= "<tr>";

      for ($Day = 0 ; $Day < 7 ; $Day++) {
        $result .= "<td>" . $Week[$Day] . "</td>";
      }      

    }
    $result .= "</tr></table>";


    $result .= "<div>Total hours this month: ".$this->getHoursForMonth($this->stone->Wizard->_data['projectId'],$currentMonth)."     </div>";


    // very ugly in rendering we grab the user input
    $result .= "<form method=post><button type=submit name=generateInvoice>Generate Invoice</button></form>"; // STUB
    if (isset($_POST['generateInvoice'])) {
      $data = $this->stone->Invoice->generateProjectMonthly($this->stone->Wizard->_data['projectId'], $currentMonth);
      $this->stone->Invoice->displayInvoice($data);
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
  function getHoursForMonth($projectId, $month=NULL , $year=NULL ,$billable=NULL, $billed = NULL) {
    if ($month==NULL) $month = date("n");    
    if ($year==NULL) $year = date("Y");

    $query = "SELECT sum(project_hours_hours) + 0.25 * sum(project_hours_quarters) as project_time
                                       FROM project_hours  
                                       WHERE MONTH(project_hours_date) = :month
                                        AND YEAR(project_hours_date) = :year 
                                             AND project_id = :projectId ";

    if ($billed !== NULL) {
      $query .= " AND project_hours_billed = " . ($billed ? "1 " : "0 ");
    }

    if ($billable !== NULL) {
      $query .= " AND project_hours_billable = " . ($billable ? "1 " : "0 ");
    }



    $query .= " GROUP BY  project_id"; 

    $sth = $this->stone->pdo->prepare($query);
    $sth->execute(array(":year"=>$year, ":month"=>$month, ":projectId"=>$projectId));
    return  (float)$sth->fetchColumn();
 
  }
//------------------------------------------------------------------------------
  function getHoursForPeriod($projectId, $begin, $end ,$billable=NULL, $billed = NULL) {


    $query = "SELECT sum(project_hours_hours) + 0.25 * sum(project_hours_quarters) as project_time
                                       FROM project_hours  
                                       WHERE project_hours_date >= :begin
                                        AND Yproject_hours_date <= :end
                                             AND project_id = :projectId ";

    if ($billed !== NULL) {
      $query .= " AND project_hours_billed = " . ($billed ? "1 " : "0 ");
    }

    if ($billable !== NULL) {
      $query .= " AND project_hours_billable = " . ($billable ? "1 " : "0 ");
    }



    $query .= " GROUP BY  project_id"; 

    $sth = $this->stone->pdo->prepare($query);
    $sth->execute(array(":begin"=>$begin, ":end"=>$end, ":projectId"=>$projectId));
    return  (float)$sth->fetchColumn();
 
  }

//------------------------------------------------------------------------------
  function getProjectInfo($projectId) {

    $sth = $this->stone->pdo->prepare("SELECT * FROM project WHERE project_id = :projectId");
    $sth->execute(array(":projectId"=>$projectId));
    $result =  $sth->fetch();
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
  function chooseproject_invoice_process(){
    $result = array();
    if (isset($_POST['project_id'])){
      $result['next_page']="invoiceWizard_chooseMonth";
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
