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

  function Footer() {
    $this->SetY(-10);
    $this->SetFont($this->myfonts['ubuntu_mono'], '', 8);
    $this->Cell(0, 0, "Gelieve deze factuur binnen 14 dagen na ontvangst te betalen.");
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

    // left/top/right/keep
    $pdf->SetMargins(10, 60, 10, true);
    // for some reason, the bottom margin is set through a different function
    // orientation P = Portrait (Staand) L = Landscape (Liggend) / auto pagebreaj / bottom margin
    $pdf->setPageOrientation 	("P", true , 25);  		


    $pdf->AddPage();

    $positions = array(10,32,88,115,156);

    // TODO: address_format needs to be configurable / a parameter / as there are different formats per country  
    $address_format = "{customer_name}\n{address_street} {address_number}\n{address_postalcode}  {address_city}\n";
    $address = str_replace("{customer_name}", $data['billing_address']['customer_name'], $address_format);
    $address = str_replace("{address_street}", $data['billing_address']['address_street'], $address);
    $address = str_replace("{address_number}", $data['billing_address']['address_number'], $address);
    $address = str_replace("{address_postalcode}", $data['billing_address']['address_postalcode'], $address);
    $address = str_replace("{address_city}", $data['billing_address']['address_city'], $address);

//    $pdf->SetY(60); // we set the margins to 60 top so no setting Y here
    $pdf->Multicell(0, 0, $address);


    //$invoice_data_format ="Klantnummer:   {customer_number}\nFactuurnummer: {invoice_number}\nFactuurdatum:  {invoice_date}\n";
    //$invoice_data = str_replace("{customer_number}", $data['invoice_data']['customer_number'], $invoice_data_format);
    //$invoice_data = str_replace("{invoice_number}", $data['invoice_data']['invoice_number'], $invoice_data);
    //$invoice_data = str_replace("{invoice_date}", $data['invoice_data']['invoice_date'], $invoice_data);
    //$pdf->SetXY(150,10);
    $pdf->SetY(90);
    //$pdf->Multicell(0, 0, $invoice_data);
    $pdf->Cell(0,0, "Klantnummer    " . sprintf("%04u",$data['invoice_data']['customer_id']),0,1);
    $pdf->Cell(0,0, "Factuurnummer  " . $data['invoice_data']['invoice_number'],0,1);
    $pdf->Cell(0,0, "Factuurdatum   " . $data['invoice_data']['invoice_date'],0,1);
    if (strlen(@$data['invoice_data']['vat_number'])) $pdf->Cell(0,0, "BTW nr. Klant: " . $data['invoice_data']['vat_number'],0,1);


    $pdf->SetY(130);


    //$pdf->Cell(0, 0, sprintf("%-8s  %-25s  %-13s%-19s%s","SKU#","Omschrijving", "Aantal", "Prijs", "Totaal"),0,1);
    $pdf->SetX($positions[0]);
    $pdf->Write(0,"SKU#");
    $pdf->SetX($positions[1]);
    $pdf->Write(0,"Omschrijving");
    $pdf->SetX($positions[2]);
    $pdf->Write(0,"Aantal");
    $pdf->SetX($positions[3]);
    $pdf->Write(0,"Stukprijs");   
    $pdf->SetX($positions[4]); 
    $pdf->Write(0,"Totaal");
    $pdf->Ln();


    foreach ($data['products'] as $product) {
      // TODO are we going to support product numbers?
      $product_name_splitted = explode("\n",wordwrap($product['name'], 25, "\n", true));
    //$pdf->Cell(0, 0,sprintf("%-8s  %-25s  %-' 9.2f    € %' 9.2f        € %' 9.2f",  $product['sku'], $product_name_splitted[0],$product['amount'], $product['price']/100 , $product['total_price']/100),0,1);

      // TODO currency is hard coded to EURO now

      $pdf->SetX($positions[0]);
      $pdf->Write(0,$product['sku']);
      $pdf->SetX($positions[1]);
      $pdf->Write(0,$product_name_splitted[0]);
      $pdf->SetX($positions[2]);
      $pdf->Write(0,$product['amount']);
      $pdf->SetX($positions[3]);
      $pdf->Write(0,sprintf("€ %' 9.2f",$product['price']/100));   
      $pdf->SetX($positions[4]); 
      $pdf->Write(0,sprintf("€ %' 9.2f",$product['total_price']/100));
      $pdf->Ln();

      // handle large product names
      if (count($product_name_splitted) > 1) {
        for ($i = 1 ; $i < count($product_name_splitted); $i++) 
          //$pdf->Cell(0, 0,$product_name_splitted[$i] ,0,1);
          $pdf->SetX($positions[1]);
          $pdf->Write(0,$product_name_splitted[$i]);
          $pdt->Ln();
      }

    }

    $pdf->Ln();



    $pdf->SetX($positions[1]);
    $pdf->Write(0,"Totaal excl. BTW");
    $pdf->SetX($positions[4]); 
    $pdf->Write(0,sprintf("€ %' 9.2f",$product['total_price_ex']/100));
    $pdf->Ln();

//    $pdf->Cell(0, 0,sprintf("          %-57s  € %' 9.2f" , ,$data['total_price_ex']/100),0,1);

    // tax
    foreach ($data['taxes'] as $tax) {
      //$pdf->Cell(0, 0,sprintf("          %-25s  %-' 7.2f  %%   € %9.2f        € %' 9.2f","BTW". " ". $tax['rate_rate_type'],  $tax['tax_rate'], $tax['taxed_amount']/100 , $tax['tax_amount']/100),0,1);

      $pdf->SetX($positions[1]);
      $pdf->Write(0,"BTW". " ". $tax['rate_rate_type']);
      $pdf->SetX($positions[2]);
      $pdf->Write(0,sprintf("%-' 7.2f  %%",$tax['tax_rate']));
      $pdf->SetX($positions[3]);
      $pdf->Write(0,sprintf("€ %' 9.2f",$tax['taxed_amount']/100));   
      $pdf->SetX($positions[4]); 
      $pdf->Write(0,sprintf("€ %' 9.2f",$tax['tax_amount']/100));
      $pdf->Ln();


    }

//    $pdf->Cell(0, 0,sprintf("          %-57s  € %' 9.2f" , "Totaal incl. BTW",$data['total_price_in']/100),0,1);
  $pdf->SetX($positions[1]);
    $pdf->Write(0,"Totaal incl. BTW");
    $pdf->SetX($positions[4]); 
    $pdf->Write(0,sprintf("€ %' 9.2f",$product['total_price_in']/100));
    $pdf->Ln();




    
    // just to be sure... there *should* be nothing in the buffer
    ob_clean(); 

    $pdf->Output($data['invoice_data']['invoice_number'].'.pdf', 'I');
  }
}
?>
