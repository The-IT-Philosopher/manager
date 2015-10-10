<?php

class Customer {

  // note Wizard functionality will be implemented in a separate class in a 
  // later state of development. In this pre-alpha state this will do  
  function addWizard() {
    global $data;
    global $pdo;
   if(isset($_POST['customerReset'])) unset($_SESSION['CustomerAddWizard']);
   $data['content_raw'] .= "<form method=post><button name=customerReset>Reset</button></form>";

    if (!isset($_SESSION['CustomerAddWizard'])) {
      $_SESSION['CustomerAddWizard'] = array();
      $_SESSION['CustomerAddWizard']['page']=0;
    }
 
    switch ( $_SESSION['CustomerAddWizard']['page'] ) {
      case 0:
        if (isset($_POST['customerType'])) {
          if ($_POST['customerType']=="person" || $_POST['customerType']=="organisation") {
            $_SESSION['CustomerAddWizard']['customerType'] = $_POST['customerType'];
            $_SESSION['CustomerAddWizard']['page']++;
          }
        }
        if(!(isset($_SESSION['CustomerAddWizard']['customerType']))) {
          $data['content_raw'] .= "<form method=post><button name=customerType value=person>Prive</button><button name=customerType value=organisation>Zakelijk</button></form>";
          break;
        }
      case 1:
	if ($_SESSION['CustomerAddWizard']['customerType']=="person") {
          $_SESSION['CustomerAddWizard']['page']+=3;
        } else 
        if (isset($_POST['country']) && $_POST['country']=='NL') {
          $_SESSION['CustomerAddWizard']['page']+=2;
          $_SESSION['CustomerAddWizard']['OrganisationCountry']="NL";
        } else if (isset($_POST['region']) && in_array($_POST['region'],array("EU","world")) ) {
          $_SESSION['CustomerAddWizard']['OrganisationRegion']=$_POST['region'];
          $_SESSION['CustomerAddWizard']['page']++;
        } else {
          $data['content_raw'] .= "Landkiezer";
          $data['content_raw'] .= "<form method=post><button name=country value=NL>Nederland</button><button name=region value=EU>EU</button><button name=region value=world>Buiten EU</button>";
          break;
        }
      case 2:
          if (isset($_POST['country'])) {
            $sth = $pdo->prepare ("SELECT count(*) FROM country WHERE alpha2 = :country");
            $sth->execute(array(":country" => $_POST['country']));
            if ($sth->fetchColumn()) {
            $_SESSION['CustomerAddWizard']['OrganisationCountry'] = $_POST['country'];
            //$_SESSION['CustomerAddWizard']['page']++;
            } else echo "invalid country!";
          }
          if ($_SESSION['CustomerAddWizard']['customerType']=="organisation" && (!isset($_SESSION['CustomerAddWizard']['OrganisationCountry']))) {
            $data['content_raw'] .= "Landkiezer2";
            $in_or_not = ($_SESSION['CustomerAddWizard']['OrganisationRegion'] == "EU" ) ? "IN" : "NOT IN";
            $sth  = $pdo->prepare("SELECT alpha2, langNL FROM country where alpha2 $in_or_not (SELECT alpha2 from country_vies) ORDER BY langNL"); 
            $sth->execute();
            global $errors;
            $errors[]=$sth->errorInfo();
            $data['content_raw'] .= "<form method=post><select name=country>";
            while ($country = $sth->fetch()) {
              $data['content_raw'] .= "<option value=" . $country['alpha2'] . ">".$country['langNL'] . "</option>";
            }
            $data['content_raw'] .= "</select><input type=submit value=volgende></form>";
            break;
          }
      case 3:
        if ($_SESSION['CustomerAddWizard']['customerType']=="organisation") {
          if (in_array($_POST['organisationType'] , array("association_unregged", "association_regged", "foundation", "company"))) {
            $_SESSION['CustomerAddWizard']['organisationType'] = $_POST['organisationType'];
            $_SESSION['CustomerAddWizard']['page']++;
          } else {
            $data['content_raw'] .= "<form method=post><button name=organisationType value=association_unregged>Vereniging zonder KvK</button><button name=organisationType value=association_regged>Vereniging met KvK</button><button name=organisationType value=foundation>Stichting</button><button name=organisationType value=company>Bedrijf</button></form>";
          break;
        }
      }
      case 4:
        if ( (strlen($_POST['first_name']) || strlen($_POST['initials'])) && strlen($_POST['last_name'])) {
          $insertData = array();
          $insertData[':person_first_name'] = $_POST['first_name'];
          $insertData[':person_initials']   = $_POST['initials'];
          $insertData[':person_last_name_prefix'] = $_POST['last_name_prefix'];
          $insertData[':person_last_name'] = $_POST['last_name'];
          $sth = $pdo->prepare("INSERT INTO person (person_first_name, person_initials, person_last_name_prefix, person_last_name) VALUES (:person_first_name, :person_initials, :person_last_name_prefix, :person_last_name)");
          $sth->execute($insertData);
          $_SESSION['CustomerAddWizard']['personId'] = $pdo->lastInsertId();
          $_SESSION['CustomerAddWizard']['page']++;
        } else {

          $data['content_raw'] .= "Persoonsinformatie<form method=post>";
          $data['content_raw'] .= "<table>";
          $data['content_raw'] .= "<tr><td>Voornaam</td><td><input type=text name=first_name></td></tr>";
          $data['content_raw'] .= "<tr><td>Voorletters</td><td><input type=text name=initials></td></tr>";
          $data['content_raw'] .= "<tr><td>Tussenvoegsel</td><td><input type=text name=last_name_prefix></td></tr>";
          $data['content_raw'] .= "<tr><td>Achternaam</td><td><input type=text name=last_name></td></tr>";
          $data['content_raw'] .= "<tr><td></td><td><input type=submit value=volgense></td></tr>";
          $data['content_raw'] .= "</table></form>";
          break;
        }
      case 5:
        if ($_SESSION['CustomerAddWizard']['customerType']=="person" ) {
          $_SESSION['CustomerAddWizard']['page']++;
        } else {
          //$data['content_raw'] .= "Organisatieinformatie";
          // The wizards ask for customer country, but this form does not use this information yet
          if ($_SESSION['CustomerAddWizard']['OrganisationCountry']=="NL" && $_SESSION['CustomerAddWizard']['organisationType'] != "association_unregged") {
            $kvk_valid = false;
            if (isset($_POST['kvk'])) {
              require_once("components/kvk-validation/kvkValidation.class.php");
              //NOTE: API KEY now in configuration/configuration.php
              //Should become a database entry later
              global $OpenOverheidIO_KEY;
              $kvk_validator = new kvkValidation($OpenOverheidIO_KEY);
              $kvk_valid = $kvk_validator->check( (int)$_POST['kvk'] );
              if ($kvk_valid) {
                $kvkData = $kvk_validator->getData();
                $_SESSION['CustomerAddWizard']['KvKdata'] = $kvkData;
                $_SESSION['CustomerAddWizard']['page']+=2;
                //TODO: Store KvK data to database;
                $sth = $pdo->prepare("INSERT INTO address (address_street, address_number, address_postalcode, address_city, address_country) 
                                      VALUES (:address_street, :address_number, :address_postalcode, :address_city, 'NL')");
                $insertData = array();
                $insertData[':address_street']    = $kvkData['address_street'];
                $insertData[':address_number']    = $kvkData['address_number'];
                $insertData[':address_postalcode']= $kvkData['address_postalcode'];
                $insertData[':address_city']= $_SESSION['CustomerAddWizard']['KvKdata']['address_city'];
                $sth->execute($insertData);
                $address_id = $pdo->lastInsertId(); 
                
                $sth = $pdo->prepare("INSERT INTO organisation (organisation_name, organisation_type, organisation_nl_kvk, organisation_country)
                                      VALUES (:organisation_name, :organisation_type, :organisation_nl_kvk, 'NL')");

                $insertData = array();
                $insertData[':organisation_name'] = $kvkData['organisation_name'];
                $insertData[':organisation_type'] = $_SESSION['CustomerAddWizard']['organisationType'];
                $insertData[':organisation_nl_kvk'] = $kvkData['kvk_nummer'];
                $sth->execute($insertData);
                $organisation_id = $pdo->lastInsertId();

                $sth = $pdo->prepare ("INSERT INTO link_address2organisation (address_id, organisation_id, address_type) VALUES
                                       (:address_id, :organisation_id, 'validated' )");
                $sth->execute(array(":address_id"=>$address_id, ":organisation_id" => $organisation_id));

              } else {
                if (!($kvk_validator->KeyValid)) {
                  global $errors;
                  $errors[]="OpenOverheid.IO key invalid!";
                }
              }
              // TODO: check on library error state  
            }
            if (!$kvk_valid) {
              $data['content_raw'] .= "Organisatieinformatie";
              $data['content_raw'] .= "<form method=post>";
              $data['content_raw'] .= "<table>";
              $data['content_raw'] .= "<tr><td>KvK Nummer</td><td><input type=number name=kvk></td></tr>";
              $data['content_raw'] .= "<tr><td></td><td><input type=submit value=volgense></td></tr>";
              $data['content_raw'] .= "</table></form>";
              break;
            }
          }
        }
     case 6:
       if(!(isset($_SESSION['CustomerAddWizard']['KvKdata']))) {
         $data['content_raw'] .= "Manyally enter Address and/or company information"; // non-nl or not-registered organisation
         break;
       }
     case 7:
      $data['content_raw'] .= "VAT NUMBER";
     case 8:
      $data['content_raw'] .= "done?";

    }
  }

}


?>
