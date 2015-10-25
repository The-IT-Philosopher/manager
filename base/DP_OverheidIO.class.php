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

class DP_OverheidIO extends component implements DataProvider {

  private $APIKEY;
  private $data;
  private $KvKvalid;
  private $KeyValid;
  private $ServiceURL;

  public function init() {

    //parent::init();
    $sth = $this->stone->pdo->prepare('SELECT xt_key_val1, xt_service_url1
                                       FROM xt_service 
            JOIN linkxt_service2key ON xt_service.xt_service_id = linkxt_service2key.service_id
            JOIN xt_key ON xt_key.xt_key_id = linkxt_service2key.key_id
                                       WHERE xt_service_type = :xt_service_type ');
    $sth->execute(array("xt_service_type"=>"overheid.io"));
    $data  = $sth->fetch();
    $_SESSION['blag'] = $sth->errorInfo();
    $this->APIKEY     = $data['xt_key_val1'];
    $this->ServiceURL = $data['xt_service_url1']; 
  }

  public function reset() {
    $this->data  = NULL;
    $this->KvKvalid = NULL;
    $this->KeyError = NULL;
  }

  function provides(){
    // note: preliminary result format
    $result = array();
    $result['in'] = array();
    $result['in']["crn"]="NL";
    $result['out'] = array();
    $result['out'] = "crn_valid";
    $result['out'] = "address";
    return $result;
  }

  public function check($KvKnummer) {
    $this->reset();
    //KvK nummers kunnen met 0 beginnen
    //Niet geverifieerde bron: https://www.higherlevel.nl/forum/index.php?board=50;action=display;threadid=28479
    //Response wijkt af met langere nummers, een array met een entry met het
    //falende nummer
    if (!$KvKnummer || $KvKnummer > 99999999) {
      $this->KvKvalid = false;
      return false;
    }
    $KvKnummer = sprintf("%08d",$KvKnummer);
    $response = $this->QueryOverheidIO($KvKnummer);
    $data = json_decode($response,true); 

    if (isset($data['_embedded']['rechtspersoon'][0])) { 
       $this->data = $data['_embedded']['rechtspersoon'][0];
       $this->KvKvalid = true;
       $this->KeyError = false;
    } else if (isset($data['error'])) {  
      if (strstr($data['error'], "niet gevonden")) {
       $this->KvKvalid = false;
       $this->KeyError = false;
      } else if (strstr($data['error'], "Geen geldige API")) {
       $this->KeyError = true;       
      } else {
        //echo "unknown error!"; // TODO : keep error state flag
      }
    } else {
      //echo "unkown response!"; // TODO : keep error state flag
    }
    return $this->KvKvalid;
  }

  public function getAddress() {
    if (!$this->data) return null;

    $data = array();
    $data['kvk_nummer']        = $this->data['dossiernummer'];
    $data['organisation_name'] = $this->data['handelsnaam'];
    $data['address_street']     = $this->data['straat'];

    // Contatenating number and suffix
    $data['address_number']     = $this->data['huisnummer'] .$this->data['huisnummertoevoeging'];

    // formatting postal code, as the data provided by OpenKVK.io appears to 
    // leave the space between the numbers and letters out.
    if (strlen($this->data['postcode'])==6) {
      $data['address_postalcode'] = substr($this->data['postcode'],0,4) . " " . substr($this->data['postcode'],4,2);
    } else {
      $data['address_postalcode'] = $this->data['postcode'];
    }

    $data['address_city']       = $this->data['plaats'];
    return $data;
  }

  private function QueryOverheidIO($KvKnummer){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $this->ServiceURL . $KvKnummer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, 
                                      array( 'ovio-api-key: ' . $this->APIKEY));
    $result = curl_exec($ch);

    curl_close($ch);
    return $result;
  }
}

?>
