<!DOCTYPE html>
<html>
  <head>
    <meta charset=utf-8>
    <title>{html_title}</title>
    <link rel="stylesheet" href="{template_path}/style.css">
    <!-- 
        NOTE: Google Font API!!! We need to make the font local. 
              Google doesn't need to know who is visiting my website!
    -->
    <link href='https://fonts.googleapis.com/css?family=Ubuntu' rel='stylesheet' type='text/css'>    
  </head>
  <body>
    <div class="container header">
      <div class="left">
        Mobiel<br>Simple HTML<br><b>Live HTML</b>
      </div>
      

      <div class="center">
        <div class="container">
          <div class="spacer"></div>
          <div class="logo">
            <img class="logo" src="{template_path}/logo.svg" alt="logo">
          </div>
          <div class="title">
            <div class="name">The IT Philosopher</div>
            <div class="slogan">Onderscheidend in kennis en prijs</div>
          </div>
          <div class="spacer"></div>
        </div>
      </div>
      <div class="right">
        <div class="language"><b>Nederlands</b><br>English<br>Deutsch</div>
      </div>
    </div>

    <div class="container main">
      <div class="left">
        {main_left}
      </div>
      <div class="center">
        {main_center}
      </div>
      <div class="right">
        {main_right}
      </div>
    </div>

    <div class="container footer">
      <div class="left">
        
      </div>
      <div class="center">
        {footer_center}
      </div>
      <div class="right">
        
      </div>
    </div>


    <div class="copyright">
        {copyright}
    </div>




  </body>
</html>
