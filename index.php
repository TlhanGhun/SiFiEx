<?php 
  if(!file_exists("config.php")) {
    if (copy("config.php.templ", "config.php")) {
      $firstStart = TRUE;
    } else {
      echo "<br /><br /><br /><div style=\"background-color:red;border:1px solid black;padding:1em;\"><strong>Sorry, I was unable to create the default configuration for you. Please copy manually the file called config.php.templ to config.php in the folder where you installed SiFiEx.</strong>. It looks like I didn't have enough rights to write this file for you...<br /><br />SiFiEx</div>";
     die();
    }
  }
  require_once("functions.php");
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
    <div id="messageBox">
<?php
  if ($firstStart) {
    writeSuccess($lang['firstStart']);
  }
  if (!$HTTP_POST_VARS && !$HTTP_GET_VARS) {
?>
      <div id="greeting">
        <?php echo $lang['greeting']; ?>
      </div>
<?php
  }
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
    <div id="uploadForm">      
      <h2><?php echo $lang['uploadHeading']; ?></h2>
      <form method="post" action="index.php" enctype="multipart/form-data">
        <ol>
          <li><?php echo $lang['uploadChooseFile']; ?>
            <br />
            <input type="file" name="uploadPic" size="4" /></li>
          <li><?php echo $lang['uploadHideSuffix']; ?>
            <input type="checkbox" name="hideSuffix" />
          </li>
	  <li><?php echo $lang['uploadInformMail']; ?>
            <br />
            <input name="informMail" />
          </li>
          <li><?php echo $lang['uploadStart']; ?>
            <input type="submit" name="doUpload" value="Import" />
          </li>
          <li><?php echo $lang['uploadBePatient']; ?></li>
        </ol>
        <p><?php echo $lang['uploadMaxSize']; echo getMaximumUploadSize(); ?></p>
      </form>
    </div>
    <div id="files">
      <table>
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
	while (list(, $key) = each ($images)) {
		if ($colorChanger > 0) {
			$class="odd";
		} else {
			$class="even";
		}
		$colorChanger =-1 * $colorChanger;
        ?>        
        <tr class="<?php echo $class ?>">
          <td>
            <a href="files/<?php echo $key; ?>">
              <?php echo $key; ?></a>
          </td>
          <td>
            <?php echo date ($config['dateFormat'], filemtime("files/$key")); ?>
          </td>
          <td>
            <?php echo size_hum_read(filesize("files/$key")); ?>
          </td>
          <td>
            <form method="post" action="<?php echo $_SELF?>">
              <input type="submit" name="submit" value="<?php echo $lang['listDelete']; ?>" />
              <input type="hidden" name="delete" value="first" />
              <input type="hidden" name="name" value="<?php echo $key ?>" />
            </form>
            <!--          <form method="post" action="<?php echo $_SELF?>">
            <input type="submit" name="submit" value="<?php echo $lang['listRename']; ?>" />
            <input type="hidden" name="name" value="<?php echo $key ?>">
            </form>
            <form method="post" action="<?php echo $_SELF?>">
            <input type="submit" name="submit" value="<?php echo $lang['listMail']; ?>" />
            <input type="hidden" name="name" value="<?php echo $key ?>">
            </form> -->
          </td>
        </tr>        
<?php
  }
        ?>
      </table>
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
