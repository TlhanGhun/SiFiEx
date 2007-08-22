<?php

// this file will be called on first time setup of SiFiEx
// to provide an easy installation and enough help for problems
// coming out of insufficient write access to specific directories
// most of the functions have to called from files yling directly in
// the upmost SiFiEx-folder bacause of relative pathes in there

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

    if($this->foundProblems()) {
      echo "<p>I don't have enough write permissions to the needed directories. I now will try to change the permissions myself but on many hosted webspaces the needed commands are deactivated out of security reasons...</p>\n";
      echo "<ul>\n";
      echo "  <li>Trying to change permissions: ";
      showResult($this->changePermissionsViaPhp ($this->configProblem, $this->filesProblem),"SUCCESS","NO SUCCESS");
      echo "</li>\n";
      echo "</ul>\n";

    }

    if(!$this->foundProblems()) {
      echo "<p>Everything seems to be fine on your computer.</p>\n";
      if(!file_exists("./config.php")) {
        echo "<p>I now will create a default configuration for you in the file called &quot;config.php&quot;. Maybe you want to change some settings in this file using a simple texteditor to adapt SiFiEx to your needs.</p>\n";
      }
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
    echo "<div id=\"setupViaFtp\">\n";
    $this->createFtpForm($this->filesProblem, $this->configProblem);
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

  function createFtpForm ($files, $config) {
    echo "<h1>Automatical change of needed adjustments</h1>\n";
    echo "<p>With the following form you can SiFiEx change all needed parameters for you automatically. Just enter the connection data to your FTP-Server and SiFiEx will make the rest for you. If you don't like to enter your connection data into this formular you can also make the changes by hand - see below howto make this.</p>\n";
    echo "<form id=\"ftpForm\" method=\"post\" action=\"$PHP_SELF\">\n";
    echo "  Your FTP-Server: <input name=\"ftpHost\" id=\"ftpHost\" /><br />\n";
    echo "  Your path to SiFiEx: <input name=\"ftpPath\" id=\"ftpPath\" value=\"SiFiEx/\" /><br />\n";
    echo "  Your FTP-User: <input name=\"ftpUser\" id=\"ftpUser\" /><br />\n";
    echo "  Your FTP-Password: <input name=\"ftpPassword\" type=\"password\" id=\"ftpPassword\" /><br />\n";
    if ($files) {
      echo "  <input type=\"hidden\" name=\"files\" value=\"TRUE\" />";
    }
    if ($config) {
      echo "  <input type=\"hidden\" name=\"config\" value=\"$config\" />";
    }
    echo "  <input type=\"submit\" name=\"doFtpChanges\" value=\"Complete installation\" />\n";
    echo "</form>\n";
  }
  function writeUsingFtp ($vars) {
    echo "<div id=\"tryingFtp\">\n";
    echo "<h1>Automatic installation</h1>\n";
    echo "<p>Trying now to change everything for you.</p>\n";
    echo "<ol>\n";
    echo "  <li>Logging onto host ".$vars['ftpHost'].": ";
    $this->outputSuccess($ch = ftp_connect($ftpHost));
    echo "</li>\n";
    echo "  <li>Logging in using user ".$vars['ftpUser']." and the supplied password: ";
    $this->outputSuccess($lh = ftp_login($ch, $vars['ftpUser'], $vars['ftpPassword']));
    echo "</li>\n";
    if($vars['ftpPath'] != "") {
      echo "  <li>Changing to your SiFiEx directory &quot;".$vars['ftpPath']."&quot;: ";
      $this->outputSuccess(ftp_chdir($ch, $vars['ftpPath']));
      echo "</li>\n";
    }
    if ($vars['files']) {
      echo "<li>Changing now the permissions for the files-folder: ";
      $this->outputSuccess(ftp_chmod($ch, 0777, "./files"));
      echo "</li>\n";
    }
    if ($vars['config']) {
      echo "<li>Creating now the default configuration: ";
      $getFile = ftp_get($ch, "./files/temp.php", "./config.php.templ", FTP_ASCII);
      $this->outputSuccess(ftp_put($ch, "./config.php", "./files/temp.php", FTP_ASCII));
      $deleteFile = ftp_delete($ch, "./files/temp.php");
      echo "</li>\n";
    }

    echo "  <li>Closing connection :";
    $this->outputSuccess (ftp_quit($ch));
    echo "</li>\n";

    echo "</ol>\n";
    if (!$this->success) {
      echo "<p><strong>Sorry, I had some problems in the automatic installation. Maybe SiFiEx will work now (see following analysis) but if not please follow the documentation at bottom of this page to install SiFiEx manualy</strong></p>\n";
    }
    echo "</div>\n";
    $this->writeAnalysis();
  }

  function outputSuccess ($value) {
    $this->success = showResult($value);
  }

  function changePermissionsViaPhp ($configProblem, $filesProblem) {
    if(!function_exists("chmod")) { return FALSE; };
    $filesDone = FALSE;
    $configDone = FALSE;
    if ($filesProblem) {
      if (chmod("./files", 0777)) { $filesDone = TRUE; };
    } else {
      $filesDone = TRUE;
    }

    if ($configProblem) {
      $oldPerm = filePermissions("./", TRUE);
      if (chmod("./", 0777)) {
        if($this->writeNewConfig ()) {
          chmod("./", $oldPerm);
          $configDone = TRUE;
        }
      }
    } else {
      $configDone = TRUE;
    }
    if ($filesDone && $configDone) {
      $this->filesProblem = FALSE;
      $this->configProblem = FALSE;
      return TRUE;
    } else {
      return FALSE;
    }
  }



  function writeNewConfig() {
    if(!is_file("./config.php")) {
      if (copy("./config.php.templ", "./config.php")) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      return TRUE;
    }
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
