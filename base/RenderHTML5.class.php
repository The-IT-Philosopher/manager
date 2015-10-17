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

class RenderHTML5 extends Component implements Render {

  public const Content-Type    = "text/html";
  public const DefaultPriority = 10;

  function render($data) {
    $output = file_get_contents(__DIR__."/template/index.tpl");

    $template_url = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__."/template/");
    $output = str_replace("{template_path}",$template_url,$output);
    $output = str_replace("{main_center}",$data['content_raw'],$output);
    $output = str_replace("{html_title}",$data['title'], $output);   

    $output = str_replace("{main_right}",$data['content_right_raw'],$output);

 
    $menu = "";
    foreach ($data['menu'] as $menuItem) {
      $menu .= "<a href=/" .$menuItem['slug'] ."><button>". $menuItem['title'] . "</button></a><br>";
    }
    $output = str_replace("{main_left}",$menu,$output);


    echo $output;
  }

}

?>
