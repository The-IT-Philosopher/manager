<!DOCTYPE html>
<html>
  <head>
    <meta charset=utf-8>
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- make Android behave -->
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
        <div class="content">{top_left}</div>
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
        <div class="content">{top_right}</div>
      </div>
    </div>

    <div class="container main">
      <div class="left">
        <div class="content">{main_left}</div>
      </div>
      <div class="center">
        <div class="content">{main_center}</div>
      </div>
      <div class="right">
        <div class="content">{main_right}</div>
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
