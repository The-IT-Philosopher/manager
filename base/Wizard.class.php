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

class Wizard extends Component {
  private $_current_page;
  private $_init_page;
  private $_pages = array();
  
  public function render() {
    // STUB
    if (!isset($this->_current_page)) $this->_current_page=$this->_init_page;

    $this->stone->_data['content_raw'] .= "rendering...";    
    //$this->stone->_data['content_raw'] .= $this->_current_page['content_raw'];
    //$form = call_user_func(__NAMESPACE__ .'\\' . $this->_current_page['render_raw']);
      $form = call_user_func(array( $this->_current_page['object'], 
                                    $this->_current_page['render_raw']));
    $this->stone->_data['content_raw'] .= $form;

  }

  public function process() {
    if (!isset($this->_current_page)) $this->_current_page=$this->_init_page;

    //$this->stone->_data['content_raw'] .= "calling " . __NAMESPACE__ .'\\' . $this->_current_page['process'] . "<BR>";

    //$result = call_user_func(__NAMESPACE__ .'\\' . $this->_current_page['process']);
    // TODO: rename $result as $result should be reserved for return values
      $result = call_user_func(array( $this->_current_page['object'], 
                                    $this->_current_page['process']));

    if (isset($result['next_page'])) {
      $this->stone->_data['content_raw'] .= "Retrieved next page from processing"; 
      if (isset($this->_pages[$result['next_page']])) {
      $this->stone->_data['content_raw'] .= "Retrieved next page set"; 
        $this->_current_page = $this->_pages[$result['next_page']];
      } else {
        //page not found
      $this->stone->_data['content_raw'] .= "Retrieved next page not found"; 
      }
    } else {
      $this->stone->_data['content_raw'] .= "Retrieved no next page from processing"; 
      $this->stone->_data['content_raw'] .= "<PRE>" . var_export($result,true) . "</PRE>";
    }
    if (isset($result['error'])) {
      $this->stone->_data['content_raw'] .= $result['error'];
    }
  }

  public function setPage($page) {
    if (isset($this->_pages[$page])) {
      $this->_current_page = $this->_pages[$page];
          $this->stone->_data['content_raw'] .= "Current Page $page set<br>"; 
    } else {
      //page not found
          $this->stone->_data['content_raw'] .= "Page $page not found<br>"; 
    }
  }

  public function initPage($page) {
    if (isset($this->_pages[$page])) {
      $this->_init_page = $this->_pages[$page];
          $this->stone->_data['content_raw'] .= "Init Page $page set<br>"; 
    } else {
      //page not found
          $this->stone->_data['content_raw'] .= "Page $page not found<br>"; 
    }
  }

  public function registerPage($page) {
        $this->stone->_data['content_raw'] .= "Page " . key($page) . "added<br>"; 
    $this->_pages=array_merge($this->_pages,$page);
  }
  
}
?>
