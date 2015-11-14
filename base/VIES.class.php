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


class VIES extends Component {

  private $_donePage = "done";
  
  function setDonePage($donepage) {
    $this->_donePage=$donepage;
  }

  function init() {
    $this->stone->Wizard->registerPage(
      array("vies_enter"=>array('render_raw'=> array($this, "vies_enter_render_raw"), 
                               "process"   => array($this, "vies_enter_process"))));

    $this->stone->Wizard->registerPage(
      array("vies_ok"=>array('render_raw'=> array($this, "vies_ok_render_raw"), 
                               "process" => array($this, "vies_ok_process"))));
  }

  function vies_enter_render_raw(){
    $result  = "<form method=post>";
    $result .= "<table>";
    $result .= "<tr><td>BTW Nummer</td><td><input type=text name=vat></td></tr>";
    $result .= "<tr><td></td><td><input type=submit value=volgense></td></tr>";
    $result .= "</table></form>";
    return $result;
  }

  function vies_ok_render_raw(){
    $result  = "<PRE>DATA FROM OPENOVERHEID.IO\n";
    $result .= var_export($this->stone->_data['viesData'],true);
    $result .= "</PRE>";
    return $result;
  }

  function vies_enter_process() {
    $result = array();
    if (!isset($_POST)) return $result;
    $vat_number = strtoupper($_POST['vat']); // capitalise string
    $vat_number = str_replace(' ', '', $vat_number); // remove spaces
    if (substr($vat_number,0,2)!=$this->stone->_data['organisationCountry']) {
      //VAT Number entered without country prefix
      $vat_number = $this->stone->_data['organisationCountry'] . $vat_number;
    }

    $this->stone->_data['content_raw'] .= "<br>vat nr = $vat_number <br>";
    //TODO :: Integrate this class into new autoloader stucture
    //        Set up GIT with submodules

    //return; //DEBUG
    require_once("components/vat-validation/vatValidation.class.php");
    $vat_validator = new \vatValidation();

    $vat_valid = $vat_validator->check( $vat_number );
    if ($vat_valid) {
      $vatData = $vat_validator->getData();
      // note: German VAT number responses don't include a company name or address
      //       Belgian VAT number responses contain a prefixed company name
      //        Therefore.... only valid/invalid information is reliable
      //                      other information is inconsequent between states
      $this->stone->_data['viesData'] = $vatData;
      //$data['content_raw'] .= "<pre>VIES DATA\n" . var_export($vatData,true) . "</pre>";
      /*      
      $sth = $pdo->prepare("UPDATE organisation 
                            SET    organisation_vat = :organisation_vat 
                            WHERE  organisation_id  = :organisation_id");
      $sth->execute(array(":organisation_vat" => $_POST['vat'], "organisation_id" => $_SESSION['CustomerAddWizard']['organisationId'] ));
      */
      $result['next_page'] = "vies_ok"; //debug
    } else {
      //TODO DEBUG
      //$result['error'] = "<PRE> vies_error\n". var_export($vat_validator,true) . "</pre>";
      $result['error'] = "vies_error";
    }       

  return $result;
  }


}
