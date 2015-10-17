<?php

class Renderer {
  public function render($data,$type="html") {
    switch ($type) {
      case "html":
        //$this->html_header($data);
        //$this->html_content($data);
        //$this->html_footer($data);
        $this->html_template($data);
        break;
    }
  }

  public function html_template($data) {
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
  public function html_header($data) {
    // this will be handled differently later
    // but will do for alpha version
    echo "<!DOCTYPE html>";
    echo "<html><head><title>" . $data['title'] . "</title>";
    echo "<meta charset='utf-8'></head><body>";
  }
  public function html_content($data) {
//    echo $data['content_raw'];
    echo "<div id=menu>";
    foreach ($data['menu'] as $menuItem) {
      echo "<a href=/" .$menuItem['slug'] ."><button>". $menuItem['title'] . "</button></a>";
    }
    echo "</div>";
    echo "<div id=raw>" . $data['content_raw'] . "</div>";
  }
  public function html_footer($data) {
    echo "</body>";
  }
}

?>
