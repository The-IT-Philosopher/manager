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

class Wizard_Company extends Component {

  private $_donePage = "done";
  
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }
  
  function init() {

    $this->stone->_wizard->registerPage(
      array("Wizard_Company_ChooseCountry"=>array(
                               'render_raw'    => array( $this, "Wizard_Company_ChooseCountry_render_raw"), 
                               "process"       => array( $this, "Wizard_Company_ChooseCountry_process"))));

    $this->stone->_wizard->registerPage(
      array("Wizard_Company_ChooseCountryEU"=>array(
                               'render_raw' => array( $this, "Wizard_Company_ChooseCountryEU_render_raw"), 
                               "process"    => array( $this, "Wizard_Company_ChooseCountryEU_process"))));

    $this->stone->_wizard->registerPage(
      array("Wizard_Company_ChooseCountryWORLD"=>array(
                               'render_raw'    => array( $this, "Wizard_Company_ChooseCountryWORLD_render_raw"), 
                               "process"       => array( $this, "Wizard_Company_ChooseCountryWORLD_process"))));

  }

  function Wizard_Company_ChooseCountry_render_raw(){
    $result  = "<form method=post>";
    $result .= "<button name=country value=NL>Nederland</button>";
    $result .= "<button name=region value=EU>EU</button>";
    $result .= "<button name=region value=world>Buiten EU</button>";
    $result .= "</form>";
    return $result;
  }

  function Wizard_Company_ChooseCountryEU_render_raw(){
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

  function Wizard_Company_ChooseCountryWORLD_render_raw(){
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

  function Wizard_Company_ChooseCountry_process(){
    if (!isset($_POST)) return $array();
    $result = array();
    if (isset($_POST['country']) && $_POST['country']=='NL') {
      $Wizard_KVK = $this->stone->Wizard_KvK;
      if ($Wizard_KVK) {
        //perhaps we need a better page identifier?
        $result['next_page'] = "kvk_enter"; 
      } else {
        $result['error'] = "KvK Wizard not available"; 
      }
    }
    if (isset($_POST['region']))  {
      if ($_POST['region'] == "EU") {
        $result['next_page'] = "Wizard_Company_ChooseCountryEU";
      } else {
        $result['next_page'] = "Wizard_Company_ChooseCountryWORLD";
      }
    }
    return $result;
  }

  function Wizard_Company_ChooseCountryEU_process(){
    return array();
  }
  function Wizard_Company_ChooseCountryWORLD_process(){
    return array();
  }

}
?>
