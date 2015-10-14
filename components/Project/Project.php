<?php
  class Project {
    

    public function addWizard() {
      global $data;
      global $pdo;
      $data['content_raw'] .= "<br>Nieuw project<br>";

   if(isset($_POST['projectReset'])) unset($_SESSION['ProjectAddWizard']);
   $data['content_raw'] .= "<form method=post><button name=projectReset>Reset</button></form>";


      if (!isset($_SESSION['ProjectAddWizard'])) {
        $_SESSION['ProjectAddWizard'] = array();
        $_SESSION['ProjectAddWizard']['page']=0;
      }

      switch ($_SESSION['ProjectAddWizard']['page']) {
        case 0:
          if (isset($_POST['customer_id'])) {
            $_SESSION['ProjectAddWizard']['customerId'] = $_POST['customer_id'];
            $_SESSION['ProjectAddWizard']['page']++;
          } else if (isset($_POST['internal'])) {
            $_SESSION['ProjectAddWizard']['page']++;
          } else {
            $sth = $pdo->prepare("SELECT customer_id, customer_name
                                  FROM (SELECT customer_id,organisation_name as customer_name
                                        FROM   link_customer2organisation
                                        JOIN   organisation 
                                        ON link_customer2organisation.organisation_id = organisation.organisation_id) ALIAS_A  
                                  UNION (SELECT customer_id,CONCAT_WS(' ',person_first_name,person_last_name_prefix,person_last_name)  as customer_name
                                        FROM   link_customer2person
                                        JOIN    person
                                        ON link_customer2person.person_id = person.person_id ) ");
            $sth->execute();
            $data['content_raw'] .= "<form method=post>";
            $data['content_raw'] .= "<table><tr><td></td><td><input type=submit name=internal value='Intern project'></td></tr>";
            $data['content_raw'] .= "<tr><td>Kies klant</td><td>";
            $data['content_raw'] .= "<select size=5 name=customer_id>";
            while ($customer = $sth->fetch()){
              $data['content_raw'] .= "<OPTION VALUE=". $customer['customer_id'] . ">". sprintf("%04d ",$customer['customer_id']) . $customer['customer_name'] . "</option>";
            }
            $data['content_raw'] .="</select></td></tr>";
            $data['content_raw'] .="<tr><td></td><td><input type=submit value=volgende></td></tr></table>";
            $data['content_raw'] .= "</form>";                           
            break; 
        }
        case 1:
          $data['content_raw'] .= "Project data<br>";
          $project_data_ok= false;
          if (isset($_POST['description_short']) && strlen($_POST['description_short'])){
              //accept projects with just a short description
              //This might/will change in the future but we want
              //the manager up and running ASAP
              //Another consideration is adding project while still nagitiating details
              $insertData=array();
                $insertData[':project_description_short'] = $_POST['description_short'];
                $insertData[':project_description_long']  = $_POST['description_long'];
                $insertData[':project_status']            = $_POST['status'];

              if (isset($_SESSION['ProjectAddWizard']['customerId'])) {
                $sth = $pdo->prepare("INSERT INTO project (project_description_short,
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
                $sth = $pdo->prepare("INSERT INTO project (project_description_short,
                                                            project_description_long,
                                                            project_status) VALUES
                                                           (:project_description_short,
                                                            :project_description_long,
                                                            :project_status) ");

              }
              $sth->execute($insertData);
              $project_id = $pdo->lastInsertId();
              if (isset($_SESSION['ProjectAddWizard']['customerId'])) {
                $sth = $pdo->prepare("INSERT INTO link_customer2project (project_id,customer_id) values (:project_id,:customer_id)");
                $sth->execute(array(":customer_id"=>$_SESSION['ProjectAddWizard']['customerId'],":project_id"=>$project_id));
                
              }
              $project_data_ok = true;
              $_SESSION['ProjectAddWizard']['page']++;
            }
;         if (!$project_data_ok) {
            $data['content_raw'] .= "<form method=post>";
            $data['content_raw'] .= "<table>";
            $data['content_raw'] .= "<tr><td>Projectnaam</td><td><input type=text name=description_short></td></tr>";
            $data['content_raw'] .= "<tr><td>Omschrijving</td><td><textarea name=description_long cols=65 rows=5></textarea></td></tr>";

            if (isset($_SESSION['ProjectAddWizard']['customerId'])) {
              //$data['content_raw'] .= "project voor klant";
              $data['content_raw'] .= "<tr><td>Kostenberekening</td><td><select name=billing_type>";
              $data['content_raw'] .= "<option value=timed>Uurtarief</option>";
              $data['content_raw'] .= "<option value=fixed>Vast bedrag</option>";
              $data['content_raw'] .= "</select></td></tr>";
              $data['content_raw'] .= "<tr><td>Bedrag (centen)</td><td><input type=number name=billing_rate></td></tr>";
            } else {
              //$data['content_raw'] .= "intern project";
            }
            $data['content_raw'] .= "<tr><td>Status</td><td><select name=status>";
            $data['content_raw'] .= "<option value=planned>Gepland</option>";
            $data['content_raw'] .= "<option value=running>Lopende</option>";
            $data['content_raw'] .= "<option value=finished>Afgerond</option>";
            $data['content_raw'] .= "</select></td></tr>";
            $data['content_raw'] .="<tr><td></td><td><input type=submit value=volgende></td></tr>";
            $data['content_raw'] .= "</table></form>";
          }
      }
    }

    public function view(){
      global $pdo;
      global $data;
      $sth = $pdo->prepare("SELECT project_id, project_description_short FROM project ");
      $sth->execute();
      $data["content_raw"] .= "<table>";
      while ($project = $sth->fetch()){
        $data['content_raw'].="<tr><td>" . $project['project_id'] . "</td><td>" . $project['project_description_short'] . "</td></tr>";
      }
      $data['content_raw'].="</table>";
    }

    public function declareWizard() {
      global $data;
      global $pdo;
      $data['content_raw'] .= "Urendeclaratie projecten";

      if(isset($_POST['declareReset'])) unset($_SESSION['ProjectDeclareWizard']);
      $data['content_raw'] .= "<form method=post><button name=declareReset>Reset</button></form>";


      if (!isset($_SESSION['ProjectDeclareWizard'])) {
        $_SESSION['ProjectDeclareWizard'] = array();
        $_SESSION['ProjectDeclareWizard']['page']=0;
      }

      switch ($_SESSION['ProjectDeclareWizard']['page']) {
        case 0:
          if (isset($_POST['declareProject'])){
            $_SESSION['ProjectDeclareWizard']['page']++;
            $_SESSION['ProjectDeclareWizard']['projectId'] = $_POST['declareProject'];
          } else {
            $sth = $pdo->prepare("SELECT project_id, project_description_short FROM project ");
            $sth->execute();
            $data["content_raw"] .= "<form method=post><table>";
            while ($project = $sth->fetch()){
              $data['content_raw'].="<tr><td>" . $project['project_id'] . "</td><td>" . $project['project_description_short'] . "</td><td><button name=declareProject value=".$project['project_id'].">Declareren</button></td></tr>";
            }
            $data['content_raw'].="</table></form>";
            break; 
        }
        case 1:
          if (isset($_POST['declare'])) {
            $sth = $pdo->prepare("INSERT INTO project_hours (project_id, project_hours_date,project_hours_hours,project_hours_quarters) VALUES (:project_id, :date,:hours,:quarters)");
            $retval = $sth->execute(array(":project_id" => $_SESSION['ProjectDeclareWizard']['projectId'], 
                                ":date" =>$_POST['date'] ,
                                ":hours"=>$_POST['hours'] ,
                                ":quarters"=>$_POST['quarters']));
            if ($retval===false) {
              $data["content_raw"] .= "<pre>DB ERROR\n".$pdo->errorInfo().var_export($sth->errorInfo(),true)."</pre>";
              break;
            } else
            $_SESSION['ProjectDeclareWizard']['page']++;
          } else {
            $sth = $pdo->prepare ( "SELECT * FROM project WHERE project_id = :project_id");
            $sth->execute(array(":project_id" => $_SESSION['ProjectDeclareWizard']['projectId']));
            $projectInfo = $sth->fetch();
            // we need some more project info, such as the owner, but we'll add that later
            $data["content_raw"] .= "<form method=post><table>";
            $data['content_raw'] .= "<tr><td>Uren</td><td><input type=number name=hours min=0 max=12></td></tr>";
            $data['content_raw'] .= "<tr><td>Kwartier</td><td><input type=number name=quarters min=0 max=3></td></tr>";
            // make it default to 'today'
            // note: Firefox doesn't yet support 'date', Chromium does
            $data['content_raw'] .= "<tr><td>Datum</td><td><input type=date name=date value=".date('Y-m-d') ."></td></tr>";
            $data['content_raw'] .= "<tr><td></td><td><input name=declare type=submit value=Declareren></td></tr>";
            $data['content_raw'].="</table></form>";
            break;
          }
        case 2:
          $data['content_raw'].="done";
      }
      
    }
  }
?>
