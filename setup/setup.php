<?php

// this file will be called on first time setup of SiFiEx
// to provide an easy installation and enough help for problems
// coming out of insufficient write access to specific directories

class setup {
  function writeAnalysis () {
    echo "<div id=\"setup\">\n";
    echo "<h1>Installation</h1>\n";
    echo "<p>Welcome to the firsttime setup of SiFiEx on this machine. To be able to share files SiFiEx will need some writing rights on your webspace. I will check this for you right now...</p>\n";
    echo "  <p>Checking if I'm allowed to write the initial configuration: ";
    if ($this->checkWritableConfig ()) {
      echo "<span class=\"OK\">OK</span>";
    } else {
      echo "<span class=\"NotOK\">NOT OK</span>";
      $this->configProblem = TRUE;
    }
    echo "</p>\n";

    echo "  <p>Checking if I'm allowed to write files into the exchange directory: ";
    if ($this->checkWritableFilesFolder ()) {
      echo "<span class=\"OK\">OK</span>";
    } else {
      echo "<span class=\"NotOK\">NOT OK</span>";
      $this->filesProblem = TRUE;
    }
    echo "</p>\n";
    
    if(!$this->foundProblems()) {
      echo "<p>Everything seems to be fine on your computer. I now will create a default configuration for you in the file called &quot;config.php&quot;. Maybe you want to change some settings in this file using a simple texteditor to adapt SiFiEx to your needs.</p>\n";
      $this->writeNewConfig();
      echo "<p>You can now go to the <a href=\"index.php\">start page of SiFiEx</a> and begin with up- and downloading files. Have fun.</p>";
      echo "</div>\n";
      echo "</body></html>";
      die();
   } else {
      echo "<p>Sorry, I don't have enough rights to finish the installation for you. Please read the following paragraphs to change the permissions.</p>\n";
      echo "</div>\n";
      $this->fixProblems($this->configProblem, $this->filesProblem);
    }
  }

  function foundProblems () {
    if ($this->filesProblem || $this->configProblem) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function fixProblems($files, $config) {
    echo "<div id=\"describeSetupProblems\">\n";
    echo "<h1>Howto make needed adjustments</h1>\n";
    echo "</div>\n";
    echo "<div id=\"fixSetupProblems\">\n";
    readfile("setup/examplesFTP.html");
    echo "</div>\n";
    echo "</body></html>";
    die();
  }

  function checkWritableConfig () {
    # return FALSE;
    return(is_writable("./"));
  }

  function checkWritableFilesFolder () {
    return(is_writable("./files/"));
  }

  function writeNewConfig() {
    #echo "<p>Writing config...</p>";
    copy("./config.php.templ", "./config.php");    
  }

  function writeHtmlHeader() {
 ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title>SiFiEx - Simple File Exchange - Installation</title>
    <meta name="generator" content="Sven Walther" />
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
    <link href="themes/default/stylesheet.css" rel="stylesheet" type="text/css" />
    <link href="setup/setupStyles.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
<?php
  }
}

?>
