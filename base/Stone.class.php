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


class Stone {
  private $_components = array();
  private $_data       = array();
  private $_wizard;
  private $_renders    = array();
  private $_auths      = array();

  private $_errors     = array();

  private $pdo;
  private $databaseConnection;

  function __sleep() {
    $this->pdo = NULL; //PDO cannot be resialised
    return array_keys(get_object_vars($this));
  }
  function __wakeup(){
    $this->pdo = $this->databaseConnection->connect();
  }

  // Should the uid/cap remain here or be moves to a user class?
  // As they are essential to the system, they might remain here, for now.
  private $uid;
  private $cap;

  public function setUserID($uid) {
    $this->uid = $uid;
  }

  public function setUserCapabilities($cap) {
    $this->cap = $cap;
  }

  public function &__get($name) {
    //Indirect modification of overloaded property Philosopher\\Stone::$_data has no effect
    //Does & make it "direct"? YES!!!
    // https://phpolyk.wordpress.com/2012/07/25/indirect-modification-of-overloaded-property/
    return $this->$name;
  }

  public function registerComponent($component) {

    try {
      $component->load($this);
      $this->_components[] = $component; 

      if ($component instanceof Render) $this->_renders[] = $component;
      if ($component instanceof Auth) $this->_auths[] = $component;
      if ($component instanceof Wizard) $this->_wizard = $component;

      // The following line are for testing only
      // Probably merge to init() functions
      if ($component instanceof DatabaseConnection) {
        $this->databaseConnection = $component;
        $this->pdo = $component->connect();
      }
      if ($component instanceof AuthSession) $component->resume();

      return true;
    } catch (\Exception $e) {
      $_errors[]=$e;
      return false;
    }
  }

  public function processRequest(){

    foreach ($this->_components as $component ) {
      if (method_exists($component, "init")) $component->init();
    }

    //testing
    $this->_wizard->setPage("kvk_enter_form");
    $this->_wizard->render();

    $this->_data['title']="The IT Philosopher - Manager";
    $this->_data['content_right_raw'] .= "<PRE>" . var_export($_SESSION,true) . "</PRE>";
    $this->_data['content_raw'] .= "H€llo world!";
    $this->_data['menu']=array();  
    
    $this->_renders[0]->render($this->_data);
  }



}
?>
