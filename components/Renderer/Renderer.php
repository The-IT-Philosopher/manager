<?php

class Renderer {
  public function render($data,$type="html") {
    switch ($type) {
      case "html":
        $this->html_header($data);
        $this->html_content($data);
        $this->html_footer($data);
        break;
    }
  }
  public function html_header($data) {
    // this will be handled differently later
    // but will do for alpha version
    echo "<!DOCTYPE html>";
    echo "<html><head><title>" . $data['title'] . "</title>";
    echo "<meta charset='utf-8'></head><body>";
  }
  public function html_content($data) {
    echo $data['content_raw'];
  }
  public function html_footer($data) {
    echo "</body>";
  }
}

?>
