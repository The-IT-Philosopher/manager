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
          $_SESSION['CustomerAddWizard']['OrganisationRegion']="EU";
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
          $person_id = $pdo->lastInsertId();
          $_SESSION['CustomerAddWizard']['personId'] = $person_id;
          if (strlen($_POST['email_address'])) {
            $insertData = array();
            $insertData[":email_verification"]=sha1(mcrypt_create_iv(16), MCRYPT_DEV_URANDOM ); 
            $insertData[":email_address"]=$_POST['email_address'];
            $sth = $pdo->prepare("INSERT INTO email (email_address,email_verification) VALUES (:email_address,:email_verification)");
            $sth->execute($insertData);
            $email_id = $pdo->lastInsertId();
            $_SESSION['CustomerAddWizard']['emailId'] = $email_id;
          }
          $_SESSION['CustomerAddWizard']['page']++;
        } else {

          $data['content_raw'] .= "Persoonsinformatie<form method=post>";
          $data['content_raw'] .= "<table>";
          $data['content_raw'] .= "<tr><td>Voornaam</td><td><input type=text name=first_name></td></tr>";
          $data['content_raw'] .= "<tr><td>Voorletters</td><td><input type=text name=initials></td></tr>";
          $data['content_raw'] .= "<tr><td>Tussenvoegsel</td><td><input type=text name=last_name_prefix></td></tr>";
          $data['content_raw'] .= "<tr><td>Achternaam</td><td><input type=text name=last_name></td></tr>";
          $data['content_raw'] .= "<tr><td>E-mail adres</td><td><input type=email name=email_address></td></tr>";
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
                $_SESSION['CustomerAddWizard']['addressId'] = $address_id; 
                $sth = $pdo->prepare("INSERT INTO organisation (organisation_name, organisation_type, organisation_nl_kvk, organisation_country)
                                      VALUES (:organisation_name, :organisation_type, :organisation_nl_kvk, 'NL')");

                $insertData = array();
                $insertData[':organisation_name'] = $kvkData['organisation_name'];
                $insertData[':organisation_type'] = $_SESSION['CustomerAddWizard']['organisationType'];
                $insertData[':organisation_nl_kvk'] = $kvkData['kvk_nummer'];
                $sth->execute($insertData);
                $organisation_id = $pdo->lastInsertId();
                $_SESSION['CustomerAddWizard']['organisationId'] = $organisation_id;
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
      if ($_SESSION['CustomerAddWizard']['OrganisationRegion']=="EU" && !isset($_POST['skip_vat'])) {

        //TODO check organisation type to see if VAT is required!

        $vat_valid = false;
        if (isset($_POST['vat'])) {
          require_once("components/vat-validation/vatValidation.class.php");
          $vat_number = strtoupper($_POST['vat']); // capitalise string
          $vat_number = str_replace(' ', '', $vat_number); // remove spaces
          if (substr($vat_number,0,2)!=$_SESSION['CustomerAddWizard']['OrganisationCountry']) {
            //VAT Number entered without country prefix
            $vat_number = $_SESSION['CustomerAddWizard']['OrganisationCountry'] . $vat_number;
          }
                                 
            $vat_validator = new vatValidation();
            $vat_valid = $vat_validator->check( $_POST['vat'] );
            if ($vat_valid) {
              $vatData = $vat_validator->getData();
              //$data['content_raw'] .= "<pre>VIES DATA\n" . var_export($vatData,true) . "</pre>";
              $sth = $pdo->prepare("UPDATE organisation 
                                    SET    organisation_vat = :organisation_vat 
                                    WHERE  organisation_id  = :organisation_id");
              $sth->execute(array(":organisation_vat" => $_POST['vat'], "organisation_id" => $_SESSION['CustomerAddWizard']['organisationId'] ));
              $_SESSION['CustomerAddWizard']['page']++;
            }        
        
        }
        if (!$vat_valid) {
         $data['content_raw'] .= "Belastinginformatie";
         $data['content_raw'] .= "<form method=post>";
         $data['content_raw'] .= "<table>";
         $data['content_raw'] .= "<tr><td>BTW Nummer</td><td><input type=text name=vat></td></tr>";
         $data['content_raw'] .= "<tr><td></td><td><input type=submit value=volgense></td></tr>";
         if ($_SESSION['CustomerAddWizard']['organisationType']!="company") {
           // Companies always have a VAT number
           // Foundations and associations might have a VAT number
           // So we offer to skip VAT validation if the Orgnisation type is not a company
           $data['content_raw'] .= "<tr><td></td><td><input type=submit name='skip_vat' value='Overslaan'></td></tr>";
         } 
         $data['content_raw'] .= "</table></form>";
         break;
       }
     } else {
       //VAT number only applicable to EU countries
       $_SESSION['CustomerAddWizard']['page']++;
     }  
     case 8:
      //all done, add the record to customer data;
      $sth = $pdo->prepare("INSERT INTO customer (customer_id) VALUES (NULL)");
      $sth->execute();
      $insertData = array();
      // last step, we don't need to store the value elsewhere
      // but in future revisions we'll propably handle ids differently anyways
      $insertData[':customer_id']=$pdo->lastInsertId();
 
      if ($_SESSION['CustomerAddWizard']['customerType']=="person") {
        $insertData[':person_id']=$_SESSION['CustomerAddWizard']['personId'];
        $sth = $pdo->prepare("INSERT INTO link_customer2person (customer_id, person_id) 
                              VALUES (:customer_id, :person_id)");

      }

      if ($_SESSION['CustomerAddWizard']['customerType']=="organisation") {
        $insertData[':organisation_id']=$_SESSION['CustomerAddWizard']['organisationId'];
        $sth = $pdo->prepare("INSERT INTO link_customer2organisation (customer_id, organisation_id)
                              VALUES (:customer_id, :organisation_id)");
      }
      $sth->execute($insertData);
    }
  }

  function view(){
    global $pdo;
    global $data;
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
            $data['content_raw'] .= "<table>";
            while ($customer = $sth->fetch()){
              $data['content_raw'] .= "<tr><td>".sprintf("%04d ",$customer['customer_id'])."</td><td>".$customer['customer_name']."</td></tr>";

            }
            $data['content_raw'] .= "</table>";

  }

}


?>
