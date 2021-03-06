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
  private $_data       = array("content_raw"=>"", "content_xml"=>array() );

  // might not be needed anymore with current magic
  private $_wizard;
//  private $_page;

  private $_renders    = array();
  private $_auths      = array();

  private $_errors     = array();

  private $_request;

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
  private $uid = 0;
  private $cap = array();

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

    if (isset($this->_components["Philosopher\\$name"])) return $this->_components["Philosopher\\$name"]; 
    if (isset($this->$name)) return $this->$name;
    $this->_data['content_raw'] = "<PRE>STONE: Could not retrieve $name </pre>";
    $snull=NULL;
    return $null;
  }

  public function registerComponent($component) {

    try {
      $component->load($this);
      $this->_components[get_class($component)] = $component; 

      if ($component instanceof Render) $this->_renders[] = $component;
      if ($component instanceof Auth) $this->_auths[] = $component;
      if ($component instanceof Wizard) $this->_wizard = $component;
      if ($component instanceof Page) $this->_page = $component;

      // The following line are for testing only
      // Probably merge to init() functions
      if ($component instanceof DatabaseConnection) {
        $this->databaseConnection = $component;
        $this->pdo = $component->connect();
      }
      if ($component instanceof Session) $component->resume();

      return true;
    } catch (\Exception $e) {
      $_errors[]=$e;
      return false;
    }
  }

  public function processRequest(){
    $this->_request = explode("/", $_SERVER['REQUEST_URI']);
    array_shift($this->_request);
    

    // TODO register pages
    if ($this->_request[0]=="logout") {
      $this->Session->terminate();
      //reset session
      session_destroy();
      $_SESSION = array();

      // how to reset the stone safely?
      // TODO ACL
      
    }

    foreach ($this->_components as $component ) {
      if (method_exists($component, "init")) $component->init();
    }

    $this->_data['title']="The IT Philosopher - Manager";
//    $this->_data['content_raw'] = "H€llo world!";
    $this->_data['menu']=array();  


  if (in_array("admin", $this->cap)) {
    //STUB
    $menuItem = array();
    $menuItem['title'] = "Organisaties";
    $menuItem['slug']  = "organisations";
    $this->_data['menu'][]=$menuItem;

    $menuItem = array();
    $menuItem['title'] = "Personen";
    $menuItem['slug']  = "persons";
    $this->_data['menu'][]=$menuItem;


    $menuItem = array();
    $menuItem['title'] = "Klanten";
    $menuItem['slug']  = "customers";
    $this->_data['menu'][]=$menuItem;

    $menuItem = array();
    $menuItem['title'] = "Leveranciers";
    $menuItem['slug']  = "suppliers";
    $this->_data['menu'][]=$menuItem;

    $menuItem = array();
    $menuItem['title'] = "Projecten";
    $menuItem['slug']  = "projects";
    $this->_data['menu'][]=$menuItem;

  } else $this->_data['content_raw'] .= "PLEASE LOG IN";

// STUB
//    if ($this->_request[0]=="organisations") {
      // organisation add wizard
      //$this->_wizard->initPage("Wizard_Organisation_ChooseCountry");
      //$this->Wizard_Organisation->setDonePage("person_enter");
      //$this->_wizard->process();    
      //$this->_wizard->render();
      
//    }
// STUB
    $this->Page->render();

    if (defined("dev")) $this->_data['content_right_raw'] = "<PRE>" . htmlentities(@var_export($_SESSION,true)) . "></PRE>";
   
    $this->_renders[0]->render($this->_data);
    $this->_data['copyright'] = "Copyright &copy; 2015 The IT Philosopher";
    $this->_data['content_right_raw'] = ""; // prevent reflection of previous right_raw
    $this->_data['content_raw'] = "";
    $this->_data['content_xml'] = array();
  }



}
?>
