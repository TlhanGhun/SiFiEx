<?php
session_start();
require_once("../functions.php");
require_once("../config.php");
require_once("../languageFiles/".$config['language']."/texts.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <title><?php echo $config['appName']." - ".$config['appDesc']." ".$config['version']." - ".$lang['folderSettings']; ?></title>
    <meta name="generator" content="Sven Walther" />
    <link rel="Shortcut Icon" type="image/x-icon" href="../favicon.ico" />
    <link href="<?php echo "../themes/".$config['theme']."/stylesheet.css"; ?>" rel="stylesheet" type="text/css" />
  </head>
  <body>
<?php
  echo "<h2>".$lang['folderSettings']."</h2>\n";
if ($HTTP_POST_VARS['r00t']){
  writeFile("../".$config['fileDir'].'.htaccess', $HTTP_POST_VARS['hta']);
}
if ($HTTP_POST_VARS['createFolder']){
  mkdir("../".$config['fileDir'].$HTTP_POST_VARS['createFolderName']);

  $htaccess = $HTTP_POST_VARS['hta'];
  $htaccess .= "\n"."IndexIgnore ".$HTTP_POST_VARS['createFolderName'];

  writeFile("../".$config['fileDir'].'.htaccess', $htaccess);

  $htaccess = "IndexIgnore .htaccess , .htpasswd , .. , . \n";
  $htaccess .= 'AuthName "Folder '.$HTTP_POST_VARS['createFolderName'].'"';
  $htaccess .= "\n"."AuthType Basic \n";
  $htaccess .= "AuthUserFile ". $config['currentDir']."/../".$config['fileDir'].$HTTP_POST_VARS['createFolderName']."/.htpasswd \n";
  $htaccess .= "Require valid-user";

  writeFile("../".$config['fileDir'].$HTTP_POST_VARS['createFolderName'].'/.htaccess', $htaccess);
}
$handle=opendir("../".$config['fileDir']);
while ($dir = readdir ($handle)) {
  if ($dir != "." && $dir != ".."  && filetype("../".$config['fileDir'] . $dir) == "dir") {
    if ($HTTP_POST_VARS[$dir]){

	  writeFile("../".$config['fileDir'].$dir.'/.htaccess', $HTTP_POST_VARS['hta'.$dir]);
	  writeFile("../".$config['fileDir'].$dir.'/.htpasswd', $HTTP_POST_VARS['htp'.$dir]);
	}
	if ($HTTP_POST_VARS['submit'.$dir]){
	  if ( isset($_POST['user'.$dir]) && isset($_POST['password1'.$dir]))
	  {
		if( $_POST['password1'.$dir] == $_POST['password2'.$dir] )
		{
		  $user = $_POST['user'.$dir];
      $password1 = $_POST['password1'.$dir];
		  $htpasswd_text = "\n".$user .":".crypt($password1,CRYPT_STD_DES);

      $htpasswd = $HTTP_POST_VARS['htp'.$dir];
		  $htpasswd .= $htpasswd_text;

		  writeFile("../".$config['fileDir'].$dir.'/.htpasswd', $htpasswd);
		}
		else
		{
		  echo "<hr />";
		  echo "<strong>".$lang['passwordMismatch']."</strong>";
		  echo "<hr />";
		}
      }
	}
  }
}
closedir($handle);

echo '<form method="post" action=""><table>';
$handle=opendir("../".$config['fileDir']);
echo '<tr><td colspan="3">root directory</td></tr>';
$htaccess = file_get_contents("../".$config['fileDir'].'.htaccess');
echo '<tr><td colspan="3"><textarea name="hta" cols="50" rows="10" wrap="off" readonly>'.$htaccess.'</textarea></td>';
echo '<tr><td colspan="3"><input type="submit" name="r00t" value="Change root folder" disabled/></td></tr>';

while ($dir = readdir ($handle)) {
  if ($dir != "." && $dir != ".."  && filetype("../".$config['fileDir'] . $dir) == "dir") {
    echo '<tr><td colspan="3">'.$dir.'</td></tr>';
    $htaccess = @file_get_contents("../".$config['fileDir'].$dir.'/.htaccess');
	echo '<tr><td><textarea name="hta'.$dir.'" cols="50" rows="10" wrap="off" readonly>'.$htaccess.'</textarea></td>';
	$htpasswd = @file_get_contents("../".$config['fileDir'].$dir.'/.htpasswd');
	echo '<td><textarea name="htp'.$dir.'" cols="50" rows="10">'.$htpasswd.'</textarea></td>';
    echo '<td><table>';
	echo '<tr><td>'.$lang['username'].':</td><td><INPUT TYPE="TEXT" NAME="user'.$dir.'"></td></tr>';
	echo '<tr><td>'.$lang['password'].':</td><td><INPUT TYPE="PASSWORD" NAME="password1'.$dir.'"></td></tr>';
	echo '<tr><td>'.$lang['passwordAgain'].':</td><td><INPUT TYPE="PASSWORD" NAME="password2'.$dir.'"></td></tr>';
	echo '<tr><td><center><INPUT type=submit name="submit'.$dir.'" VALUE="'.$lang['addUser'].'">';
	echo '</center></td></tr>';
	echo '</table></td></tr>';
	echo '<tr><td colspan="3"><input type="submit" name="'.$dir.'" value="'.$lang['changeFolder'].' '.$dir.'"/></td></tr>';
  }
}

echo '<tr><td colspan="3">'.$lang['newFolderName'].': <INPUT TYPE="TEXT" NAME="createFolderName"></td></tr>';
echo '<tr><td colspan="3"><input type="submit" name="createFolder" value="'.$lang['createFolder'].'"/></td></tr>';

echo "</table></form>";
closedir($handle);

?>
  </body>
</html>