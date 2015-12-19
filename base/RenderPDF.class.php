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

require_once ("base/3rdparty/tcpdf/tcpdf.php");


class MyPDF extends \TCPDF {


  function __construct (){
    parent::__construct();
    $myfonts = array();
  }

  function Header(){

    // filename , x , y , w , h ....
    $this->ImageSVG("base/template/logo.svg" , 20 , 5 , 45  );
  
    //w,h,txt
    $this->SetXY(70,15);
    $this->SetFont($this->myfonts['ubuntu'], '', 22);
    $this->Cell(0, 0, "The IT Philosopher");


    $this->SetXY(70,25);
    $this->SetFont($this->myfonts['ubuntu'], '', 12);
    $this->Cell(0, 0, "Onderscheidend in kennis en prijs");

    $this->SetXY(150,10);
    $this->SetFont($this->myfonts['ubuntu_mono'], '', 8);
    $this->Multicell(0, 0, 
"E-mail    info@philosopher.it 
WWW       http://www.philosopher.it
Tel nr.   +31 6 49 80 21 08
KvK nr.   64184684
BTW nr.   NL156981671B01
IBAN      NL72 KNAB 0765 2685 66 
Paypal    info@philosopher.it
");

  }
}


class RenderPDF extends Component implements Render {
  function render($data) {
    $pdf = new MYPDF();

    $pdf->myfonts['ubuntu'] = \TCPDF_FONTS::addTTFfont(getcwd()."/base/3rdparty/ubuntu-font-family-0.83/Ubuntu-R.ttf");
    $pdf->myfonts['ubuntu_mono'] = \TCPDF_FONTS::addTTFfont(getcwd()."/base/3rdparty/ubuntu-font-family-0.83/UbuntuMono-R.ttf");

    // set font
    $pdf->SetFont($pdf->myfonts['ubuntu_mono'], '', 12);
    $pdf->setHeaderMargin(10);
    // add a page

    // left/top/right
    $pdf->SetMargins(10, 50, 10);

    $pdf->AddPage();

    $address_format = "{customer_name}\n{address_street} {address_number}\n{address_postalcode}  {address_city}\n";
    $address = str_replace("{customer_name}", $data['address']['customer_name'], $address_format);
    $address = str_replace("{address_street}", $data['address']['address_street'], $address);
    $address = str_replace("{address_number}", $data['address']['address_number'], $address);
    $address = str_replace("{address_postalcode}", $data['address']['address_postalcode'], $address);
    $address = str_replace("{address_city}", $data['address']['address_city'], $address);
    //$pdf->SetXY(150,10);
    $pdf->SetY(60);
    $pdf->Multicell(0, 0, $address);


    $invoice_data_format ="Klantnummer:   {customer_number}\nFactuurnummer: {invoice_number}\nFactuurdatum:  {invoice_date}\n";
    $invoice_data = str_replace("{customer_number}", $data['invoice_data']['customer_number'], $invoice_data_format);
    $invoice_data = str_replace("{invoice_number}", $data['invoice_data']['invoice_number'], $invoice_data);
    $invoice_data = str_replace("{invoice_date}", $data['invoice_data']['invoice_date'], $invoice_data);
    //$pdf->SetXY(150,10);
    $pdf->SetY(90);
    $pdf->Multicell(0, 0, $invoice_data);


    foreach ($data['products'] as $product) {
      // TODO reserve space for product numbers?
      $product_name_splitted = explode("\n",wordwrap($product['name'], 30, "\n", true));
      $text .= sprintf("%-30s   %' 9.2f        € %' 9.2f        € %' 9.2f\n",  $product_name_splitted[0],$product['amount'], $product['price']/100 , $product['total_price']/100);
      // handle large product names
      if (count($product_name_splitted) > 1) {
        for ($i = 1 ; $i < count($product_name_splitted); $i++) $text.= $product_name_splitted[$i] ."\n";
      }

    }

    $text .= "\n";

    $text .= sprintf("%-69s€ %' 9.2f\n" , "totaal excl. btw",$data['total_price_ex']/100);

    // tax
    foreach ($data['taxes'] as $tax) {
      $text .= sprintf("%-30s    %' 9.2f%%%25s€ %' 9.2f\n","BTW". " ". $tax['rate_rate_type'],  $tax['tax_rate'], "" , $tax['tax_amount']/100);
    }

    //$text .= sprintf(str_pad("totaal inc. btw",69)."€ %' 9.2f\n" , $data['total_price_in']/100);
    $text .= sprintf("%-69s€ %' 9.2f\n" , "totaal incl. btw",$data['total_price_in']/100);



    $pdf->SetY(120);
//    $pdf->writeHTML($html);
    $pdf->Multicell(0, 0, $text);
    
/* END STUB -- as copied from Invoice::displayInvoice */

    // clear the output buffer as some code has already outputted something
    // this must be investigated // $sent = headers_sent($file, $line);
//    ob_clean(); // how is this doing anything when I've disabled output buffering?

    $pdf->Output('invoice.pdf', 'I');
  }
}
?>
