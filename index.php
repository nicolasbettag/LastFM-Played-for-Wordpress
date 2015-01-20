<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Refresh Test</title>
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
  </head>
  <body>
  <script>
    jQuery.noConflict();
    jQuery(document).ready(function(){
           jQuery("#refresh").load("http://nicolas.hello.darkcookies.de/test/refresh.php");
           var refreshId = setInterval(function() {
                   jQuery("#refresh").load("http://nicolas.hello.darkcookies.de/test/refresh.php");
           }, 1000);
    });
  </script>
  <div id="refresh"></div>
  </body>
</html>