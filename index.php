<?php 
  require_once("functions.php");
  if(!file_exists("config.php") || !is_writable("files/")) {
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
  </head>
  <body>
    <div id="logoHeader">
      <h1><a href="">Simple File Exchange <?php echo $config['version']; ?></a></h1>
    </div>
    <form id="expandUploadForm" action="<?php echo $_SELF; ?>" method="post">
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
    writeOngoing($lang['uploading']);
    $fileName = $_FILES['uploadPic']['name'];
    if ($HTTP_POST_VARS['hideSuffix'] != "") {
    	$fileName .= $config['hiddenSuffix'];
    }
    if (!move_uploaded_file($_FILES['uploadPic']['tmp_name'], "files/$fileName")) {
       writeWarning($lang['uploadError']);
    } else {
      writeSuccess($lang['uploadSuccess']);
      if ($HTTP_POST_VARS['informMail'] != "") {
        sendMail($HTTP_POST_VARS['informMail'], $fileName, $config, $lang);
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
  
  if ($HTTP_POST_VARS['delete'] == $lang['yes']) {
    writeOngoing($lang['deleting']);
    # first we have to be aware that some evil guy trys to delete files
    # outside of our directory by deleting ".." and "/" in filename
    $deleteFile=ereg_replace("\/","",$HTTP_POST_VARS['name']);
    $deleteFile=(ereg_replace("\.\.","",$deleteFile));
    if (@unlink("files/".$deleteFile)) {
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
          <li id="hideSuffix"><?php echo $lang['uploadHideSuffix']; ?>
            <input type="checkbox" name="hideSuffix" />
          </li>
	  <li id="informMail"><?php echo $lang['uploadInformMail']; ?>
            <br />
            <input name="informMail" />
          </li>
          <li id="startButton"><?php echo $lang['uploadStart']; ?>
            <input type="submit" name="doUpload" value="Import" />
          </li>
          <li id="bePatient"><?php echo $lang['uploadBePatient']; ?></li>
        </ol>
        <p id="maxFileSize"><?php echo $lang['uploadMaxSize']; echo getMaximumUploadSize(); ?></p>
      </form>
    </div>
    <?php
      } else {
    ?>
    <div id="files">
      <table id="listOfFiles">
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
$colorChanger=1;
$images=array();
$handle=opendir('files/'); 
while ($file = readdir ($handle)) { 
    if ($file != "." && $file != "..") {
        array_push($images, $file); 
    } 		
}
closedir($handle);
natcasesort($images);
if ($HTTP_GET_VARS['sort']=="NameUp") {
  $images = array_reverse($images);
}
	reset($images);
	if (count($images) == 0) {
	 ?>
	  <tr>
	    <td class="noFilesAvailable" colspan="4">
	      <?php echo $lang['noFilesAvailable']; ?>
	    </td>
	  </tr>
<?php
        }
	while (list(, $key) = each ($images)) {
		if ($colorChanger > 0) {
			$class="odd";
		} else {
			$class="even";
		}
		$colorChanger =-1 * $colorChanger;
        ?>        
        <tr class="<?php echo $class ?>">
          <td class="fileName">
            <a href="files/<?php echo $key; ?>" title="<?php echo $key; ?>">
              <?php echo displayFileName($key, $config['maxFilenameLength']); ?></a>
          </td>
          <td class="fileDate">
            <?php echo date ($config['dateFormat'], filemtime("files/$key")); ?>
          </td>
          <td class="fileSize">
            <?php echo size_hum_read(filesize("files/$key")); ?>
          </td>
          <td class="actions">
            <form method="post" action="<?php echo $_SELF?>">
              <input class="deleteButton" type="submit" name="submit" value="<?php echo $lang['listDelete']; ?>" />
              <input type="hidden" name="delete" value="first" />
              <input type="hidden" name="name" value="<?php echo $key ?>" />
            </form>
            <!--          <form method="post" action="<?php echo $_SELF?>">
            <input class="renameButton" type="submit" name="submit" value="<?php echo $lang['listRename']; ?>" />
            <input type="hidden" name="name" value="<?php echo $key ?>">
            </form>
            <form method="post" action="<?php echo $_SELF?>">
              <input class="mailButton" type="submit" name="submit" value="<?php echo $lang['listMail']; ?>" />
              <input type="hidden" name="name" value="<?php echo $key ?>">
            </form> -->
          </td>
        </tr>        
<?php
  }
        ?>
      </table>
      <?php
        }
      ?>
    </div>
    <div id="faq">
      <h2>FAQ</h2>
      <?php readfile("languageFiles/".$config['language']."/faq.html") ?>
    </div>

    <div id="footer">
      <p>SiFiEx is free software by Sven Walther - this is version <?php echo $config['version']; ?></p>
    </div>
  </body>
</html>
