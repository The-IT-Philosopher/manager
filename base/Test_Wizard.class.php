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



class Test_Wizard extends Component {


  private $_done_page;
  function setDonePage($donepage) {
    $this->_done_page=$donepage;
  }

  
  function init() {
    $this->stone->_data['content_raw'] .= "Attemting to add Test Wizard"; 
    $rawform = 
    $this->stone->_wizard->registerPage(
      array("is_6"=>array('render_raw'=> "\\Philosopher\\Test_Wizard::form6" , "process" => "\\Philosopher\\Test_Wizard::process6")));

    $rawform = 
    $this->stone->_wizard->registerPage(
      array("is_7"=>array('render_raw'=> "\\Philosopher\\Test_Wizard::form7" , "process" => array($this,"process7") )));
  }

  function form6(){
    return "<form method=post>Enter 6<input name=test><input type=submit></form>";
  }
  function form7(){
    return "<form method=post>Enter 7<input name=test><input type=submit></form>";
  }



  function process6() {
    $result = array();
    if (isset($_POST['test'])) { 
      if (6==$_POST['test']) $result['next_page'] = 'is_7'; else $result['error'] = 'please enter 6';
    }
    return $result;
  }

  function process7() {
    $result = array();
    if (isset($_POST['test'])) { 
      if (7==$_POST['test']) $result['next_page'] = $this->_done_page; else $result['error'] = 'please enter 7';
    }
    return $result;
  }
}



