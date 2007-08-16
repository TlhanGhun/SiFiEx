<?php

// this file will be called on first time setup of SiFiEx
// to provide an easy installation and enough help for problems
// coming out of insufficient write access to specific directories

class setup {
  function writeAnalysis () {
    echo "<div id=\"setup\">\n";
    echo "<h1>Installation</h1>\n";
    echo "<p>Welcome to the firsttime setup of SiFiEx on this machine. To be able to share files SiFiEx will need some writing rights on your webspace. I will check this for you right now...</p>\n";
    echo "<ul>\n";
    echo "  <li>Checking if I'm allowed to write the initial configuration or if it is already there: ";
    if ($this->checkWritableConfig ()) {
      echo "<span class=\"OK\">OK</span>";
    } else {
      echo "<span class=\"NotOK\">NOT OK</span>";
      $this->configProblem = TRUE;
    }
    echo "  </li>\n";

    echo "  <li>Checking if I'm allowed to write files into the exchange directory: ";
    if ($this->checkWritableFilesFolder ()) {
      echo "<span class=\"OK\">OK</span>";
    } else {
      echo "<span class=\"NotOK\">NOT OK</span>";
      $this->filesProblem = TRUE;
    }
    echo "  </li>\n";
    echo "</ul>\n";
    
    if(!$this->foundProblems()) {
      echo "<p>Everything seems to be fine on your computer. I now will create a default configuration for you in the file called &quot;config.php&quot;. Maybe you want to change some settings in this file using a simple texteditor to adapt SiFiEx to your needs.</p>\n";
      $this->writeNewConfig();
      echo "<p>You can now go to the <a href=\"index.php\">start page of SiFiEx</a> and begin with up- and downloading files. Have fun.</p>";
      echo "</div>\n";
      echo "</body></html>";
      die();
   } else {
      echo "<p>Sorry, I don't have enough rights to finish the installation for you. Please read the following paragraphs to finish the installation.</p>\n";
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
    if ($config) {
      echo "<h2>Install config.php</h2>\n";
      echo "<p>You have now two options to install the default configuration - just choose the one you prefer...</p>\n";
      echo "<h3>Creating config.php by yourself</h3>\n";
      echo "<p>SiFiEx tried to install a default configuration file for you. To reach this goal it copies the included file &quot;config.php.templ&quot; to a file called &config.php&quot;. The easiest way to solve this is simply that you just copy it yourself in this way and upload the &quot;config.php&quot; to your webserver in the installation directory.</p>\n";
      echo "<h3>Change permissions for automatic installation</h3>\n";
      echo "<p>The other way is to change the write permissions on the topmost folder of your installation (normally &quot;/SiFiEx&quot;) as shown in the examples at the bottom of this page.</p>\n";
      echo "<p>Now reload the start page of SiFiEx the the configuration will be created for you</p>\n";
    }
    if ($files) {
      echo "<h2>Changing permissions for the files-folder</h2>\n";
      echo "<p>To be able to upload files to the webserver SiFiEx needs write permissions in the files folder. See below some example howto make this change.</p>\n";
      echo "<p>Background: On the webservers of mass hosters normally the user uploading files via FTP (the way you most probably installed SiFiEx) and the user running the PHP-scripts are not equal. So most of the times on such servers the PHP-script is not allowed to upload files to the files directory until you change those permissions to make this folder writable for the PHP-user.</p>\n";
    }
    echo "</div>\n";
    echo "<div id=\"fixSetupProblems\">\n";
    readfile("setup/examplesFTP.html");
    echo "</div>\n";
    echo "</body></html>";
    die();
  }

  function checkWritableConfig () {
    # return FALSE;
    if (is_writable("./") || file_exists("config.php")) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  function checkWritableFilesFolder () {
    # return FALSE;
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
