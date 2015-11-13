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

class Wizard_Organisation extends Component {

  private $_donePage = "done";
  
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }
  
  function init() {

    $this->stone->_wizard->registerPage(
      array("Wizard_Organisation_ChooseCountry"=>array(
                               'render_raw'    => array( $this, "Wizard_Organisation_ChooseCountry_render_raw"), 
                               "process"       => array( $this, "Wizard_Organisation_ChooseCountry_process"))));

    $this->stone->_wizard->registerPage(
      array("Wizard_Organisation_ChooseCountryEU"=>array(
                               'render_raw' => array( $this, "Wizard_Organisation_ChooseCountryEU_render_raw"), 
                               "process"    => array( $this, "Wizard_Organisation_ChooseCountryEU_process"))));

    $this->stone->_wizard->registerPage(
      array("Wizard_Organisation_ChooseCountryNOTEU"=>array(
                               'render_raw'    => array( $this, "Wizard_Organisation_ChooseCountryNOTEU_render_raw"), 
                               "process"       => array( $this, "Wizard_Organisation_ChooseCountryNOTEU_process"))));

    $this->stone->_wizard->registerPage(
      array("Wizard_Organisation_ChooseOrganisationType"=>array(
                               'render_raw'    => array( $this, "Wizard_Organisation_ChooseOrganisationType_render_raw"), 
                               "process"       => array( $this, "Wizard_Organisation_ChooseOrganisationType_process"))));

  }

  function Wizard_Organisation_ChooseCountry_render_raw(){
    $result  = "<form method=post>";
    $result .= "<button name=country value=NL>Nederland</button>";
    $result .= "<button name=region value=EU>EU</button>";
    $result .= "<button name=region value=NOTEU>Buiten EU</button>";
    $result .= "</form>";
    return $result;
  }

  function Wizard_Organisation_ChooseCountryEU_render_raw(){
  //STUB
    $sth  = $this->stone->pdo->prepare("SELECT alpha2, langNL FROM country where alpha2 IN (SELECT alpha2 from country_vies) ORDER BY langNL"); 
    $sth->execute();

    $result = "<form method=post><select name=country>";
    while ($country = $sth->fetch()) {
      $result .= "<option value=" . $country['alpha2'] . ">".$country['langNL'] . "</option>";
    }
    $result .= "</select><input type=submit value=volgende></form>";
    return $result;
  }

  function Wizard_Organisation_ChooseCountryNOTEU_render_raw(){
  //STUB
    $sth  = $this->stone->pdo->prepare("SELECT alpha2, langNL FROM country where alpha2 NOT IN (SELECT alpha2 from country_vies) ORDER BY langNL"); 
    $sth->execute();

    $result = "<form method=post><select name=country>";
    while ($country = $sth->fetch()) {
      $result .= "<option value=" . $country['alpha2'] . ">".$country['langNL'] . "</option>";
    }
    $result .= "</select><input type=submit value=volgende></form>";
    return $result;
  }

  function Wizard_Organisation_ChooseOrganisationType_render_raw(){
    $result  = "<form method=post>";
    $result .= "<button name=organisationType value='in_formation'>In oprichting</button>";
    $result .= "<button name=organisationType value='association_unregged'>Vereniging (zonder kvk)</button>";
    $result .= "<button name=organisationType value='association_regged'>Vereniging (met kvk)</button>";
    $result .= "<button name=organisationType value='foundation'>Stichting</button>";
    $result .= "<button name=organisationType value='company'>Bedrijf</button>";
    $result .= "<button name=organisationType value='other'>Overige</button>";
    $result .= "</form>";

    return $result;
  }

  function Wizard_Organisation_ChooseCountry_process(){
    if (!(isset($_POST['country'])||isset($_POST['region']))) return array();
    $result = array();
    if (isset($_POST['country']) && $_POST['country']=='NL') {
      $this->stone->_data['organisationCountry'] = "NL";
      $this->stone->_data['organisationCountryVIES'] = 1;
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
    $result = array("next_page"=>"Wizard_Organisation_ChooseOrganisationType");
    if (isset($_POST['country'])) {
      $this->stone->_data['organisationCountry'] = $_POST['country'];
      $this->stone->_data['organisationCountryVIES'] = 1;
    }
    return $result;
  }

  function Wizard_Organisation_ChooseCountryNOTEU_process(){
    $result = array("next_page"=>"Wizard_Organisation_ChooseOrganisationType");
    if (isset($_POST['country'])) {
      $this->stone->_data['organisationCountry'] = $_POST['country'];
      $this->stone->_data['organisationCountryVIES'] = 0;
    }
    return $result;
  }

  function Wizard_Organisation_ChooseOrganisationType_process(){
    $result = array();    
    $result['next_page'] = $this->_donePage;
    if (isset($_POST['organisationType'])) {
      $this->stone->_data['organisationType'] = $_POST['organisationType'];
      if ($this->stone->_data['organisationCountry']=="NL") {
        if (! in_array( $this->stone->_data['organisationType'], array('in_formation','association_unregged') )) {
          // Dutch organisation registered by the KvK (Dutch Chamber of Commerce)
          $Wizard_KvK = $this->stone->Wizard_KvK;
          if ($Wizard_KvK) {
            $Wizard_KvK->setDonePage($this->_donePage);
            $result['next_page'] = "kvk_enter"; 
          } else {
            //$result['error'] = "KvK Wizard not available"; 
          }
        }        
      } else if ($this->stone->_data['organisationCountryVIES']) {
        if (! in_array( $this->stone->_data['organisationType'], array('in_formation','association_unregged', 'association_regged') )) {
          // EU Organisation might have a tax number (Companies always have one, but foundations might not)
          $Wizard_VIES = $this->stone->Wizard_VIES;
          if ($Wizard_VIES) {
            $Wizard_VIES->setDonePage($this->_donePage);
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
