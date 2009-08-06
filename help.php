<?php
session_start();
require_once("functions.php");
require_once("config.php");

if ($_SESSION['language']) {
  $config['language'] =$_SESSION['language'];
}
if ($_SESSION['theme']) {
  $config['theme'] =$_SESSION['theme'];
}
require_once("languageFiles/".$config['language']."/texts.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>Help - SiFiEx - Simple File Exchange <?php echo $config['version']; ?></title>
    <meta name="generator" content="Sven Walther" />
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
    <link href="<?php echo "themes/".$config['theme']."/stylesheet.css"; ?>" rel="stylesheet" type="text/css" />
  </head>
  <body>
      <div id="logoHeader">
      <h1><a href="index.php">Simple File Exchange <?php echo $config['version']; ?></a></h1>
    </div>
    <div id="faq">
      <h2>FAQ</h2>
      <?php readfile("languageFiles/".$config['language']."/faq.html") ?>
    </div>
  </body>
</html>