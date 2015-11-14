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

//------------------------------------------------------------------------------
    function classLoader($classname) {
      $classname = str_replace("Philosopher\\","",$classname);
      include ( __DIR__ . "/$classname.class.php") ;
    }

    function interfaceLoader($classname) {
      $classname = str_replace("Philosopher\\","",$classname);
      @include ( __DIR__ . "/$classname.interface.php") ;
    }

    spl_autoload_register();
    spl_autoload_register('Philosopher\classLoader');
    spl_autoload_register('Philosopher\interfaceLoader');
//------------------------------------------------------------------------------
try {
  ob_start();
  session_start();
  if (isset($_GET['reset'])) unset ($_SESSION['stone']); // test
  if ( isset($_SESSION['stone'])) {
    $stone = $_SESSION['stone'];
  } else {
    $stone = new Stone();
    $_SESSION['stone']=$stone;
    $stone->registerComponent(new RenderHTML5());
    $stone->registerComponent(new DatabaseConnection());
    $stone->registerComponent(new AuthSession());
    $stone->registerComponent(new Wizard());
    $stone->registerComponent(new Page());

    $stone->registerComponent(new KvK());
    $stone->registerComponent(new VIES());
    $stone->registerComponent(new Organisation());
    $stone->registerComponent(new Person());
    $stone->registerComponent(new Test_Wizard());
    $stone->registerComponent(new DP_OverheidIO());
  //
    //$stone->registerComponent(new RenderXML());
    //$stone->registerComponent(new RenderJSON());
    //$stone->registerComponent(new RenderHTML3());
  }

  $stone->processRequest();
} catch (Exception $e) {
  stoned($e);  
}
//------------------------------------------------------------------------------






?>
