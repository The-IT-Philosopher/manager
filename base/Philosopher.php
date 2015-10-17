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

/*
    // Your custom class dir
    define('CLASS_DIR', __DIR__ .'/');

    // Add your class dir to include path
    set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);

    // You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
    spl_autoload_extensions('.class.php');

    // Use default autoload implementation
*/
    spl_autoload_register();

    function classLoader($classname) {
      $classname = str_replace("Philosopher\\","",$classname);
      include ( __DIR__ . "/$classname.class.php") ;
    }

    spl_autoload_register('Philosopher\classLoader');


?>
