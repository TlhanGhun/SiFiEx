<?php
session_start();
require_once("functions.php");
checkConfig();
require_once("config.php");
checkMainFolderPermissions();
initialize();
require_once("languageFiles/".$config['language']."/texts.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title><?php echo $config['appName']." - ".$config['appDesc']." ".$config['version']; ?></title>
    <meta name="generator" content="Sven Walther" />
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
    <link href="<?php echo "themes/".$config['theme']."/stylesheet.css"; ?>" rel="stylesheet" type="text/css" />
    <?php
      if($config['notificationsEnableSnarl']) {
    ?>
        <script src="snarl.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" charset="utf-8">
        function uploadStartFunc() {
          myClass = new Snarl.NotificationType('Upload started', true);
          Snarl.register('SiFiEx', [myClass]);
          Snarl.notify(myClass, 'Upload started', 'Upload has started', Snarl.Priority.VeryLow, false);
        }
        </script>
    <?php
      }
      $iconPath = "";
    ?>
  </head>
  <body>
    <?php checkAdminPassword(); ?>
    <div id="logoHeader">
      <h1><a href=""><?php echo $config['appDesc']." ".$config['version']; ?></a></h1>
    </div>
    <div id="helperFunctions">
      <?php 
        if($config['showLanguageSelector']) {
          generateLanguagesDropdown();
        }
        if($config['showThemeSelector']) {
          generateThemesDropdown();
        }
      ?>
      <a href="help.php" id="helpLink"><?php echo $lang['help']; ?></a>
      <?php
	    if($config['showAdminLink']) {
	    ?>
	      <br />
        <a href="admin/folders.php" id="adminLink">Admininistration</a>
      <?php
      }
      ?>
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
  showNotification("firstStart", $config['appName'],$lang['firstStart'],$iconPath);
  writeSuccess($lang['firstStart']);
}
?>
      <div id="greeting">
        <?php echo $lang['greeting']; ?>
      </div>
<?php
if ($HTTP_POST_VARS['doUpload'] != "") {
  showNotification("Upload started", $config['appName'], $lang['uploading'], $iconPath);
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
    showNotification("Upload error", $config['appName'], $lang['uploadError'], $iconPath);
    writeWarning($lang['uploadError']);
  } else {
    showNotification("Upload finished", $config['appName'], $lang['uploadSuccess'], $iconPath);
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
      showNotification("File has been renamed", $config['appName'], $lang['renameDone'], $iconPath);
      writeSuccess($lang['renameDone']);
    } else {
      showNotification("File rename error", $config['appName'], $lang['renameError'], $iconPath);
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
    showNotification("File has been deleted", $config['appName'], $lang['deleteSuccess'], $iconPath);
    writeSuccess($lang['deleteSuccess']);
  } else {
    showNotification("Delete failed", $config['appName'], $lang['deleteError'], $iconPath);
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
			<li id="chooseFolder"><?php echo $lang['uploadChooseFolder']; ?><br />
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
natcasesort($images);
$images = array_reverse($images);
array_push($folders, $images);
echo '<div id="files">';
while ((list(, $images) = each ($folders)) && (list(, $file) = each ($folders2))){
?>
    <h2 class="folderName"><?php echo $file ;?></h2>
    <?php
    if(checkFolderPermissions($config['fileDir']."/".$file)) {
      echo "<h3 class=\"folderIsRestricted\">".$lang["folderAccessRestricted"]."</h3>\n";
    }
    ?>
      <table class="listOfFiles">
        <tr>
          <th>
            <?php echo $lang['listName']; ?> <a href="?sort=NameUp">&uarr;</a> <a href="?sort=NameDown">&darr;</a>
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
      <p>SiFiEx is free software (see <a href="LICENSE.txt">license</a>) by Sven Walther - this is version <?php echo $config['version']; ?></p>
    </div>
  </body>
</html>
