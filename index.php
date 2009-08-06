<?php
session_start();
require_once("functions.php");

if(!file_exists("config.php")) {
  require("setup/setup.php");
  if ($HTTP_POST_VARS['doFtpChanges']) {
    $tryUsingFtp = new setup;
    $tryUsingFtp->writeHtmlHeader();
    $tryUsingFtp->writeUsingFtp($HTTP_POST_VARS);
  } else {
    $initialSetup = new setup;
    $initialSetup->writeHtmlHeader();
    $initialSetup->writeAnalysis();
  }
}
require_once("config.php");

if(!is_writable($config['fileDir'])) {
  require("setup/setup.php");
  if ($HTTP_POST_VARS['doFtpChanges']) {
    $tryUsingFtp = new setup;
    $tryUsingFtp->writeHtmlHeader();
    $tryUsingFtp->writeUsingFtp($HTTP_POST_VARS);
  } else {
    $initialSetup = new setup;
    $initialSetup->writeHtmlHeader();
    $initialSetup->writeAnalysis();
  }
}

if(!file_exists(".htaccess") || !file_exists(".htpasswd")) {
  echo "Set the Administrator Username and Password";
  if(isset($HTTP_POST_VARS['submit'])){
    if ( isset($_POST['user']) && isset($_POST['password1'])){
      if( $_POST['password1'] == $_POST['password2'] ){
        $user = $_POST['user'];
        $password1 = $_POST['password1'];
        $htpasswd_text = "\n".$user .":".crypt($password1,CRYPT_STD_DES);

        writeFile('.htpasswd', $htpasswd_text);

        $htaccess = "IndexIgnore .htaccess , .htpasswd , .. , . \n";
        $htaccess .= 'AuthName "Admin Access"';
        $htaccess .= "\n"."AuthType Basic \n";
        $htaccess .= "AuthUserFile ".$_SERVER['DOCUMENT_ROOT']."/".$config['installDir'].".htpasswd \n";
        $htaccess .= "Require valid-user";

        writeFile('.htaccess', $htaccess);
      } else {
        echo "<p><hr></p>";
        echo "<b>Passwords do not match</b>";
        echo "<p><hr></p>";
      }
    }
  } else {
    echo '<form method="post" action="index.php"><table>';
    echo '<tr><td>Username:</td><td><INPUT TYPE="TEXT" NAME="user"></td></tr>';
    echo '<tr><td>Password:</td><td><INPUT TYPE="PASSWORD" NAME="password1"></td></tr>';
    echo '<tr><td>Password again:</td><td><INPUT TYPE="PASSWORD" NAME="password2"></td></tr>';
    echo '<tr><td><center><INPUT type=submit name="submit" VALUE="Set User / Pass">';
    echo '</center></td></tr>';
    echo '</table><form>';
  }
}

if ($HTTP_POST_VARS['changeLanguage']) {
  $_SESSION['language'] = $HTTP_POST_VARS['language'];
}
if ($HTTP_POST_VARS['changeTheme']) {
  $_SESSION['theme'] = $HTTP_POST_VARS['theme'];
}
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
    <title>SiFiEx - Simple File Exchange <?php echo $config['version']; ?></title>
    <meta name="generator" content="Sven Walther" />
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
    <link href="<?php echo "themes/".$config['theme']."/stylesheet.css"; ?>" rel="stylesheet" type="text/css" />
    <?php
      if($config['snarlEnabled']) {
    ?>
        <script src="snarl.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript">
          uploadStarted = new Snarl.NotificationType("Upload started", true);
          Snarl.register("SiFiEx", [uploadStarted]);
          
          function uploadStartFunc() {
          uploadStarted = new Snarl.NotificationType("Upload started", true);
          Snarl.register("SiFiEx", [uploadStarted]);
          Snarl.notify(uploadStarted, 'Upload started', 'Uplod of file has been started', Snarl.Priority.VeryLow, false);
          }

        </script>
        
    <?php
      }
    ?>
  </head>
  <body>
    <div id="logoHeader">
      <h1><a href="">Simple File Exchange <?php echo $config['version']; ?></a></h1>
    </div>
    <div id="helperFunctions">
      <?php 
        generateLanguagesDropdown();
        generateThemesDropdown();
      ?>
      <a href="help.php" target="_blank" id="helpLink"><?php echo $lang['help']; ?></a>
	  <a href="htedit.php" target="_blank" id="helpLink">Admin (use with caution)</a>
    </div>
    <form id="expandUploadForm" action="<?php echo $PHP_SELF; ?>" method="post">
      <?php if ($HTTP_POST_VARS['expandUploadSubmit']) {
      ?>
      <input type="submit" id="unExpandUpload" name="unExpandUpload" value="<?php echo $lang['unExpandUpload']."&nbsp;&uarr;"; ?>" />
      <?php } else { ?>
      <input type="submit" id="expandUploadSubmit" name="expandUploadSubmit" value="<?php echo $lang['uploadExpand']."&nbsp;&darr;"; ?>" />
      <?php } ?>
    </form>
    <div id="messageBox">
<?php
if ($firstStart) {
  writeSuccess($lang['firstStart']);
}
?>
      <div id="greeting">
        <?php echo $lang['greeting']; ?>
      </div>
<?php
if ($HTTP_POST_VARS['doUpload'] != "") {

      if($config['snarlEnabled']) {
    ?>
            <script type="text/javascript">
         // uploadStarted = new Snarl.NotificationType("Upload started", true);
         // Snarl.register("SiFiEx", [uploadStarted]);
         // Snarl.notify(uploadStarted, 'Upload started', 'Uplod of file has been started', Snarl.Priority.VeryLow, false);
          </script>
        </script>
    <?php
      }

  writeOngoing($lang['uploading']);
  flush();
  $fileName = $_FILES['uploadPic']['name'];
  if ($HTTP_POST_VARS['hideSuffix'] != "") {
    $fileName .= $config['hiddenSuffix'];
  }
  if($fileName == ".htaccess") {
    $fileName = "htaccess-Leading dot erased by SiFiEx";
  }
  if (!move_uploaded_file($_FILES['uploadPic']['tmp_name'], $config['fileDir'].$HTTP_POST_VARS['chooseFolder']."/".$fileName)) {
    writeWarning($lang['uploadError']);
  } else {
  
        if($config['snarlEnabled']) {
    ?>
            <script type="text/javascript">
          uploadFinished = new Snarl.NotificationType("Upload finished", true);
          Snarl.register("SiFiEx", [uploadFinished]);
          Snarl.notify(uploadStarted, 'Upload finished', 'Uplod of file has been finished succesfully', Snarl.Priority.VeryLow, false);
          </script>
        </script>
    <?php
      }
    writeSuccess($lang['uploadSuccess']);
    
    if ($HTTP_POST_VARS['informMail'] != "") {
      sendMail($HTTP_POST_VARS['informMail'], "/".$config['fileDir'].$HTTP_POST_VARS['chooseFolder']."/".$fileName, $config, $lang);
    }
  }
}

if ($HTTP_POST_VARS['delete'] == "first") {
  writeWarning($lang['deleteSure']);
      ?>
      <form method="post" action="index.php">
        <input type="submit" name="delete" value="<?php echo $lang['yes']; ?>" />
        <input type="submit" name="egal" value="<?php echo $lang['no']; ?>" />
        <input type="hidden" name="name" value="<?php echo $HTTP_POST_VARS['name']; ?>" />
      </form>
    </div>
<?php
}


if ($HTTP_POST_VARS['remailButton'] != "") {
    writeWarning($lang['remailHeader']);
      ?>
      <div class="ongoing">
      <form method="post" action="index.php">
        <input name="mailAdresses" value="" />
        <input type="submit" name="remail" value="<?php echo $lang['remailNow'];  ?>" />
        <input type="submit" name="egal" value="<?php echo $lang['cancel']; ?>" />
        <input type="hidden" name="name" value="<?php echo $HTTP_POST_VARS['name']; ?>" />
      </form>
    </div>
<?php
}

if ($HTTP_POST_VARS['renameButton'] != "") {
    writeWarning($lang['renameHeader']);
      ?>
      <div class="ongoing">
      <form method="post" action="index.php">
        <input name="newName" value="<?php echo $HTTP_POST_VARS['name']; ?>" />
        <input type="submit" name="rename" value="<?php echo $lang['listRename'];  ?>" />
        <input type="submit" name="egal" value="<?php echo $lang['cancel']; ?>" />
        <input type="hidden" name="name" value="<?php echo $HTTP_POST_VARS['name']; ?>" />
      </form>
    </div>
<?php
}

if ($HTTP_POST_VARS['rename'] != "") {
    writeOngoing($lang['renamingFile']);
    if (renameFile($HTTP_POST_VARS['name'], $HTTP_POST_VARS['newName'])) {
      writeSuccess($lang['renameDone']);
    } else {
      writeWarning($lang['renameError']);
    }
}

if ($HTTP_POST_VARS['remail'] != "") {
    sendMail($HTTP_POST_VARS['mailAdresses'],"/".$config['fileDir'].$HTTP_POST_VARS['name'],$config,$lang);
}


if ($HTTP_POST_VARS['delete'] == $lang['yes']) {
  writeOngoing($lang['deleting']);
  # first we have to be aware that some evil guy trys to delete files
  # outside of our directory by deleting ".." and "/" in filename
  $deleteFile=$HTTP_POST_VARS['name'];
  //$deleteFile=ereg_replace("\/","",$HTTP_POST_VARS['name']);
  //$deleteFile=(ereg_replace("\.\.","",$deleteFile));
  if (@unlink($config['fileDir'].$deleteFile)) {
    writeSuccess($lang['deleteSuccess']);
  } else {
    writeWarning($lang['deleteError']);
  }
}

    ?>    
    </div>
    <?php
    if ($HTTP_POST_VARS['expandUploadSubmit']) {
    ?>
    <div id="uploadForm">      
      <h2><?php echo $lang['uploadHeading']; ?></h2>
      <form method="post" action="index.php" enctype="multipart/form-data">
        <ol>
          <li id="chooseFile"><?php echo $lang['uploadChooseFile']; ?>
            <br />
            <input type="file" name="uploadPic" size="4" /></li>
			<li id="chooseFolder">Choose Folder for upload<br />
		  <?php
		  $handle=opendir($config['fileDir']);
		  echo '<select name="chooseFolder">';
		  while ($dir = readdir ($handle)) {
		    if ($dir != "." && $dir != ".."  && filetype($config['fileDir'] . $dir) == "dir") {
			  echo '<option value="'.$dir.'">'.$dir.'</option>';
		    }
		  }
		  closedir($handle);
		  echo "</select>"
		  ?>
          </li>
          <li id="hideSuffix"><?php echo $lang['uploadHideSuffix']; ?>
            <input type="checkbox" name="hideSuffix" />
          </li>
	  <li id="informMail"><?php echo $lang['uploadInformMail']; ?>
            <br />
            <input name="informMail" />
          </li>
          <li id="startButton"><?php echo $lang['uploadStart']; ?>
            <input type="submit" name="doUpload" value="Import" onclick="uploadStartFunc()"/>
          </li>
          <li id="bePatient"><?php echo $lang['uploadBePatient']; ?></li>
        </ol>
        <p id="maxFileSize"><?php echo $lang['uploadMaxSize']; echo getMaximumUploadSize(); ?></p>
      </form>
    </div>
    <?php
    } else {
    ?>
	<?php
	$colorChanger=1;
$folders=array();
$folders2=array();
$images=array();
$handle=opendir($config['fileDir']);
while ($file = readdir ($handle)) {

if ($file != "." && $file != ".."  && filetype($config['fileDir'] . $file) == "dir" ) {
    $handle2=opendir($config['fileDir'] ."/". $file);
	$images2=array();
	while ($file2 = readdir ($handle2)) {
	  if ($file2 != "." && $file2 != ".." && $file2!= ".htaccess" && $file2!= ".htpasswd" && $file2 != ".svn" && filetype($config['fileDir'] ."/". $file ."/". $file2) != "dir" ) {
        array_push($images2, $file ."/". $file2);
      }
	}
	closedir($handle2);
	natcasesort($images2);
	$images2 = array_reverse($images2);
	array_push($folders,$images2);
	array_push($folders2,$file);
  }
}
closedir($handle);
//natcasesort($images);
//$images = array_reverse($images);
//array_push($folders, $images);
echo '<div id="files">';
while ((list(, $images) = each ($folders)) && (list(, $file) = each ($folders2))){
?>
      <table id="listOfFiles">
        <tr>
          <th>
            <?php echo $file ;?>
          </th>
          <th>
	    <?php echo $lang['listDate']; ?>
          </th>
          <th>
	    <?php echo $lang['listSize']; ?>
          </th>
          <th>
	    <?php echo $lang['listActions']; ?>
          </th>
        </tr>
<?php

if (count($images) == 0) {
	 ?>
	  <tr>
	    <td class="noFilesAvailable" colspan="4">
	      <?php echo $lang['noFilesAvailable']; ?>
	    </td>
	  </tr>
<?php
}
while (list( ,$key) = each ($images)) {
  if ($colorChanger > 0) {
    $class="odd";
  } else {
    $class="even";
  }
  $colorChanger =-1 * $colorChanger;
        ?>        
        <tr class="<?php echo $class ?>">
          <td class="fileName">
            <a href="<?php echo $config['fileDir'].$key; ?>" title="<?php echo $key; ?>" target="_blank">
              <?php echo displayFileName($key, $config['maxFilenameLength']); ?></a>
          </td>
          <td class="fileDate">
            <?php echo date ($config['dateFormat'],  filemtime($config['fileDir'].$key)); ?>
          </td>
          <td class="fileSize">
            <?php echo size_hum_read(filesize($config['fileDir'].$key)); ?>
          </td>
          <td class="actions">
            <form method="post" action="<?php echo $_SELF?>">
              <input class="deleteButton" type="submit" name="submit" value="<?php echo $lang['listDelete']; ?>" />
              <input type="hidden" name="delete" value="first" />
              <input type="hidden" name="name" value="<?php echo $key ?>" />
            </form>
            <form method="post" action="<?php echo $_SELF?>">
            <input class="renameButton" type="submit" name="renameButton" value="<?php echo $lang['listRename']; ?>" />
            <input type="hidden" name="name" value="<?php echo $key ?>">
            </form>
            <form method="post" action="<?php echo $_SELF?>">
              <input class="mailButton" type="submit" name="remailButton" value="<?php echo $lang['listMail']; ?>" />
              <input type="hidden" name="name" value="<?php echo $key ?>">
            </form>
          </td>
        </tr>        
<?php
}
        ?>
      </table>
      <?php
	  }
    }
      ?>
    </div>


    <div id="footer">
      <p>SiFiEx is free software (see <a href="LICENSE.txt">license</a>) by Sven Walther (modified by Rob Tabberer) - this is version <?php echo $config['version']; ?></p>
    </div>
  </body>
</html>
