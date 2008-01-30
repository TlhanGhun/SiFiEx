<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
  require_once("config.php");
  require_once("languageFiles/".$config['language']."/texts.php");
  require_once("functions.php");
  $uploadDone = size_hum_read(filesize("files/".$HTTP_GET_VARS["currentFile"]));
?>
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
  <meta name="generator" content="Sven Walther" />
  <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
  <link href="<?php echo "themes/".$config['theme']."/stylesheet.css"; ?>" rel="stylesheet" type="text/css" />
  <title>xx% done</title>
  </head>
  <body>
    <div id="progressBar">
      <div id="progressDone" style="width:30px;">a</div>
      <div id="progressToDo" style="width:170px;">b</div>
      <div id="progressInfo">
        xx% / <?php echo $uploadDone; ?> of 456KB
      </div>
    </div>
  </body>
</html>
