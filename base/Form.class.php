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

class Form {
  private $_elements = array();
  function clear() {
    $this->_elements = array();
  }
//------------------------------------------------------------------------------
  function addElement($element) {
    $this->_elements[]=$element;
  }
//------------------------------------------------------------------------------
// code from bswpbase
  function GenerateForm($values=NULL, $header=NULL,$hide=false, $buttonID=NULL, $buttonTitle=NULL ,$action=NULL, $echo=false) {
    $xmlroot = new \SimpleXMLElement('<div />');
    
    if ($header!=NULL)     $xmlmenu = $xmlroot->addChild("h1", $header);
    $xmlform = $xmlroot->addChild("form");    
    $xmlform->addAttribute("enctype","multipart/form-data");
    $xmlform->addAttribute("method","post");
    if ($action) $xmlform->addAttribute("action", $action);


    $xmltable = $xmlform->addChild("table");  
    
    foreach ($this->_elements as $element) {
      $xmlrow = $xmltable->addChild("tr");
      $xmlrow->addChild("th", $element->title);
      switch ($element->type) {
        case "select":
          $xmloption = $xmlrow->addChild("td")->addChild("select");
          foreach($element->options as $opt) {
            $xmlselectoption=$xmloption->addChild("option", $opt->display);
            $xmlselectoption->addAttribute("value",$opt->value);
        // TODO values will be turned into array later
            if ($values && $values[$element->name]) {
              if ($values[$element->name]==$opt->value) $xmlselectoption->addAttribute("selected",true);
            } else
            if ($element->default==$opt->value) $xmlselectoption->addAttribute("selected",true);
          } 
          break;
        case "textarea":
          if ($values && isset($values[$element->name])) {
            $xmloption = $xmlrow->addChild("td")->addChild("textarea",$values[$element->name]);      
          } else {
            $xmloption = $xmlrow->addChild("td")->addChild("textarea");
          }
          break;
        case "checkbox":
          $xmltd = $xmlrow->addChild("td");
          $xmlhiddenoption=$xmltd->addChild("input");
          $xmlhiddenoption->addAttribute("type","hidden");
          $xmlhiddenoption->addAttribute("value","0");
          $xmlhiddenoption->addAttribute("name",$element->name);
          $xmlhiddenoption->addAttribute("id",$element->name."_hidden");    
          $xmloption=$xmltd->addChild("input");
          $xmloption->addAttribute("type","checkbox");
          $xmloption->addAttribute("value","1");
          if ($values) { 
            if (isset($values[$element->name]) && $values[$element->name]) 
              $xmloption->addAttribute("checked","true");
          } elseif ($element->default==true) {
            $xmloption->addAttribute("checked",true);
          }
          break;
        default:
          $xmloption = $xmlrow->addChild("td")->addChild("input");
          $xmloption->addAttribute("type",$element->type);
          if ($values) {
            if(isset($values[$element->name])) {
              $xmloption->addAttribute("value",$values[$element->name]);      
            }
          } elseif ($element->default) $xmloption->addAttribute("value",$element->default); 
      }
      $xmloption->addAttribute("name",$element->name);
      $xmloption->addAttribute("id",$element->name);    
      //if ($element->required==true) $xmloption->addAttribute("required",true);
      // required doesn't seem to work as desired? disable it for now.
    }


    if (!hide) {
      if ($buttonTitle==NULL) $buttonTitle="Save";
      $xmlSaveButton = $xmlform->addChild("button", $buttonTitle);
      if ($buttonID==NULL) $buttonID = "save";
      $xmlSaveButton->addAttribute("id", $buttonID);
      $xmlSaveButton->addAttribute("name", $buttonID);
    }
    $xmlCancelButton = $xmlform->addChild("button", "Cancel");
    $xmlCancelButton->addAttribute("id",   "cancel");
    $xmlCancelButton->addAttribute("name", "cancel");

    // In the new application we're going to render in a renderer module
    // No more RAW HTML output like we had to do in the WordPress plugin
    //return BlaatSchaap::xml2html($xmlroot, $echo);  
    return $xmlroot;
  }
}
//------------------------------------------------------------------------------
// code from bswpbase
class FormElement {
	public $title;
	public $name;
	public $type;
	public $required;
	public $default;
	public $options;
  function __construct($name, $title, $type="text", $required=false, $default=null){
		$this->name=$name;
		$this->title=$title;
		$this->type=$type;
		$this->required=$required;
		$this->default=$default;
		$this->options=array();
    }
	function addOption($option) {
		$this->options[]=$option;
	}
}
//------------------------------------------------------------------------------
// code from bswpbase
class FormSelectElement{
	public $value;
	public $display;
	function __construct($value, $display) {
		$this->value=$value;
		$this->display=$display;
	}

}
