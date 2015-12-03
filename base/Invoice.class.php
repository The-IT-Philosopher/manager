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

class Invoice extends Component {


  function displayInvoice($data) {
    //STUB
    //!! RAW RENDERING



    $address_format = "{customer_name}\n{address_street} {address_number}\n{address_postalcode}  {address_city}";

    $address = str_replace("{customer_name}", $data['address']['customer_name'], $address_format);
    $address = str_replace("{address_street}", $data['address']['address_street'], $address);
    $address = str_replace("{address_number}", $data['address']['address_number'], $address);
    $address = str_replace("{address_postalcode}", $data['address']['address_postalcode'], $address);
    $address = str_replace("{address_city}", $data['address']['address_city'], $address);
    $this->stone->_data['content_raw'] .= "<PRE>" . var_export($data,true) . "</PRE>";
    $this->stone->_data['content_raw'] .= "<PRE>Formatted Address:\n$address </PRE>";

    $this->stone->_data['content_raw'] .= "<table><tr><th></th><th></th><th></th><th></th></tr>";
    foreach ($data['products'] as $product) {
      $this->stone->_data['content_raw'] .= "<tr><td>" . $product['name'] . "</td><td>";
      $this->stone->_data['content_raw'] .= $product['amount'] . "</td><td> €" . $product['price']/100;
      $this->stone->_data['content_raw'] .= "</td><td>€ " . $product['total_price']/100 . "</td></tr>";
    }

    // tax
    foreach ($data['taxes'] as $tax) {
      $this->stone->_data['content_raw'] .= "<tr><td>" . $tax['rate_rate_type'] . "</td><td>";
      $this->stone->_data['content_raw'] .= $tax['tax_rate'] . "%</td><td> ";
      $this->stone->_data['content_raw'] .= "</td><td>€ " . $tax['tax_amount']/100 . "</td></tr>";
    }

    $this->stone->_data['content_raw'] .= "<tr><td>totaal exc. btw.</td><td>";
    $this->stone->_data['content_raw'] .= "</td><td> ";
    $this->stone->_data['content_raw'] .= "</td><td>€ " . $data['total_price_ex']/100 . "</td></tr>";

    $this->stone->_data['content_raw'] .= "<tr><td>totaal inc. btw.</td><td>";
    $this->stone->_data['content_raw'] .= "</td><td> ";
    $this->stone->_data['content_raw'] .= "</td><td>€ " . $data['total_price_in']/100 . "</td></tr>";


    $this->stone->_data['content_raw'] .= "</table>";
  }

  function generateProjectMonthly($projectId, $Month){
    $info    = $this->stone->Project->getProjectInfo($projectId);
    $address = $this->stone->Project->getBillingAddress($projectId);
    $product = array();
    $project_name = $info['project_description_short'];
    $product['name'] = $project_name;

    switch ($info['project_billing_type']) {
      case "timed":
        $hours = $this->stone->Project->getHoursForMonth($projectId, $Month, true, false);
        $product['amount'] = $hours;
        $rate  = $info['project_billing_rate'];
        $product['price'] = $rate;
        $amount = round($hours * $rate);
        $product['total_price'] = $amount;
        break;
      case "fixed":
        $amount = $info['project_billing_rate'];
        $product['amount'] = 1;
        $product['price'] = $amount;
        $product['total_price'] = $amount;
        break;
      case "free":
        $amount = 1;
        $product['amount'] = 1;
        $product['price'] = 0;
        $product['total_price'] = 0;
    }
    $products = array($product);


    // TODO: determine taxrate
    // To do this, we need to determine the country, product type, customer type
    // and collect the data for all EU countries. 
    // However, since we're generating for a project now, 
    // it will be tax rate "high", which is 21 for the Netherlands
    $tax_rate = 21;
    $tax_amount = round(($tax_rate * $amount) / 100);

    $tax = array ("rate_rate_type"=>"high", "tax_rate"=>$tax_rate, "tax_amount"=> $tax_amount);
    $taxes = array ($tax);

    $total_price = $product['total_price'];    
    $data = array("address" => $address , "products"=>$products, "taxes" => $taxes, "total_price_ex" => $total_price, "total_price_in" => $total_price + $tax_amount, "currency" => $info['project_billing_currency']);
    
    return $data;
  }
}
