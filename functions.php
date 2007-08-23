<?php

function displayFilename($filename, $length) {
    // *************************************************
    // function displayFilename
    // Parameters:
    //   $filename: string with filename
    //   $length: maximum length to be displayed
    // Return value: processed filename
    //
    // Shortens the given filename if it is longer than $lenght
    // Will cut the the middle of the name and put ".." instead there 
    // *************************************************  
	if(($length < strlen($filename)) && $length) {
		$leftPart = substr($filename, 0, intval($length/2-1));
		$rightPart = substr($filename, intval(strlen($filename) - ($length/2 -1)));
		return $leftPart."..".$rightPart;
	} else {
		return $filename;
	}
}

function getMaximumUploadSize () {
    // *************************************************
    // function getMaximumUploadSize
    // Parameters: none
    // Return value: maximum allowed upload size in human readable format
    //
    // Checks some values in the PHP-configuration and calculates the max.
    // possible upload size
    // *************************************************  
	return size_hum_read(min(sizeCpuReadable(ini_get('post_max_size')),sizeCpuReadable(ini_get('upload_max_filesize'))));
}

function writeWarning($warnText) {
    // *************************************************
    // function writeWarning
    // Parameters:
    //   $text: text to be displayed
    // Return value: none
    //
    // Displays a small DIV with the correct class for skinning
    // *************************************************  
	echo "<div class=\"warning\">\n";
	echo "  <p>$warnText</p>\n";
	echo "</div>\n";
}
function writeOngoing($text) {    // *************************************************
    // function writeOngoing
    // Parameters:
    //   $text: text to be displayed
    // Return value: none
    //
    // Displays a small DIV with the correct class for skinning
    // Only displayed if enabled in the configuration
    // *************************************************  
  
	global $conf;
	if($conf['showOngoing']) {
		echo "<div class=\"ongoing\">\n";
		echo "  <p>$text</p>\n";
		echo "</div>\n";
	}
}
function writeSuccess($text) {
    // *************************************************
    // function writeSuccess
    // Parameters:
    //   $text: text to be displayed
    // Return value: none
    //
    // Displays a small DIV with the correct class for skinning
    // *************************************************    
	echo "<div class=\"success\">\n";
	echo "  <p>$text</p>\n";
	echo "</div>\n";
}
function sendMail($receipient, $fileName, $conf, $lang) {
    // *************************************************
    // function sendMail
    // Parameters:
    //   $receipient: e-mail adress of receipient
    //   $fileName: name of file to send the link of
    //   $conf: the general configuration of SiFiEx
    //   $lang: to be used language
    // Return value: TRUE if mail was send, otherwise FALSE
    //
    // Sends an e-mail to the named e-mail-adress to notify
    // someone of a file on the SiFiEx-server
    // *************************************************  
	$header = "";
	$header .= "From: ".$conf['mailSenderName']." <".$conf['mailSenderEmail'].">\r\n";
	$body = "";
	$body .= $lang['mailStart']." ";
	$pathFull = explode("/", $_SERVER['PHP_SELF']);
	array_pop($pathFull);
	$pathToScript=implode("/", $pathFull);
	$body .= detectSSL()."://".$_SERVER['HTTP_HOST'].$pathToScript."/files/".$fileName."\n\n";
	if ($conf['mailInfoPassword']) {
		$body .= $lang['mailPassword']."\n\n";
	}
	$body .= "\n\n".$lang['mailEnd'];
	if (!mail($receipient, $lang['mailSubject'], $body, $header)) {
		writeWarning($lang['mailError']);
		return FALSE;
	} else {
		writeSuccess($lang['mailSuccess'].$receipient);
		return TRUE;
	}

	if ($conf['debug']) {echo "<pre>".$header."\n\n".$body."</pre>\n"; };
}

function size_hum_read($size){
    // *************************************************
    // function size_hum_read
    // Parameters:
    //   $size: value in digits
    // Return value: $size in human readable format
    //
    // Makes huge values mor user friendly (2048 becomes 2 KByte e.g.)
    // *************************************************  
	$i=0;
	$iec = array("Byte", "KByte", "MByte", "GByte", "TByte", "PByte", "EByte", "ZByte", "YByte");
	while (($size/1024)>1) {
		$size=$size/1024;
		$i++;
	}
	return substr($size,0,strpos($size,'.')+4)." ".$iec[$i];
}

function sizeCpuReadable($size) {
    // *************************************************
    // function sizeCpuReadable
    // Parameters:
    //   $size: value in human readable form as in php.ini
    // Return value: value in digits
    //
    // Translates the common shortcuts like 20M for 20 Megabytes
    // into a format we can calculate with
    // *************************************************  
	if (preg_match('/K$/', $size)) {
		return $size * 1024;
	} elseif (preg_match('/M$/', $size)) {
		return $size * 1024 * 1024;
	} elseif (preg_match('/G$/', $size)) {
		return $size * 1024 * 1024 * 1024;
	} else {
		return $size;
	}
}

function filePermissions($file, $octal = false) {
    // *************************************************
    // function filePermissions
    // Parameters:
    //   $file: name / path to file
    //   $octal: if or if not return value should be in octcal format
    // Return value: permissions of the file (e.g 0755)
    //
    // Returns current permissions of a file
    // *************************************************  
	if(!file_exists($file)) return false;
	$perms = fileperms($file);
	$cut = $octal ? 2 : 3;
	return substr(decoct($perms), $cut);
}

function detectSSL(){
    // *************************************************
    // function detectSSL
    // Parameters: none
    // Return value: http or https
    //
    // Uses different ways to figure out if the page has been
    // called using http or https
    // *************************************************  
	if($_SERVER["https"] == "on"){
		return "https";
	} elseif ($_SERVER["https"] == 1){
		return "https";
	} elseif ($_SERVER['SERVER_PORT'] == 443) {
		return "https";
	} else {
		return "http";
	}
}

function showResult ($value, $goodText, $badText) {
    // *************************************************
    // function showResult
    // Parameters:
    //   $value: value to be checked if success or not (should be TRUE or FALSE)
    //   $goodText: Text to be displayed upon success
    //   $badText: Text to be displayed on negative result
    // Return value: $value
    //
    // Displayes a CSS-skinable text depending on $value
    // *************************************************  
	if ($value) {
		echo "<span class=\"OK\">".$goodText."</span>";
	} else {
		echo "<span class=\"NotOK\">".$badText."</span>";
	}
	return $value;
}


if (!function_exists('ftp_chmod')) {
  function ftp_chmod($ftp_stream, $mode, $filename) {
    // *************************************************
    // function ftp_chmod
    // Parameters:
    //   $ftp_stream: link to already opened FTP-connection
    //   $mode: which permissions to be set (e.g. 0755)
    //   $filename: file to change the permissions for
    // Return value: $value
    //
    // Changes permissions of a file using FTP
    // This function is part of PHP5 but not included in PHP4
    // so this is a backport to enable it in PHP4
    // *************************************************
    return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $mode, $filename));
  }
}
