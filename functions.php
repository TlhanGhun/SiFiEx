<?php

function displayFilename($filename, $length) {
	if(($length < strlen($filename)) && $length) {
		$leftPart = substr($filename, 0, intval($length/2-1));
		$rightPart = substr($filename, intval(strlen($filename) - ($length/2 -1)));
		return $leftPart."..".$rightPart;
	} else {
		return $filename;
	}
}

function getMaximumUploadSize () {
	return size_hum_read(min(sizeCpuReadable(ini_get('post_max_size')),sizeCpuReadable(ini_get('upload_max_filesize'))));
}

function writeWarning($warnText) {
	echo "<div id=\"warning\">\n";
	echo "  <p>$warnText</p>\n";
	echo "</div>\n";
}
function writeOngoing($text) {
	global $conf;
	if($conf['showOngoing']) {
		echo "<div id=\"ongoing\">\n";
		echo "  <p>$text</p>\n";
		echo "</div>\n";
	}
}
function writeSuccess($text) {
	echo "<div id=\"success\">\n";
	echo "  <p>$text</p>\n";
	echo "</div>\n";
}
function sendMail($receipient, $fileName, $conf, $lang) {
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
	} else {
		writeSuccess($lang['mailSuccess'].$receipient);
	}

	#   echo "<pre>".$header."\n\n".$body."</pre>\n";
}

function size_hum_read($size){
	$i=0;
	$iec = array("Byte", "KByte", "MByte", "GByte", "TByte", "PByte", "EByte", "ZByte", "YByte");
	while (($size/1024)>1) {
		$size=$size/1024;
		$i++;
	}
	return substr($size,0,strpos($size,'.')+4)." ".$iec[$i];
}

function sizeCpuReadable($size) {
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
	if(!file_exists($file)) return false;
	$perms = fileperms($file);
	$cut = $octal ? 2 : 3;
	return substr(decoct($perms), $cut);
}

function detectSSL(){
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
	if ($value) {
		echo "<span class=\"OK\">".$goodText."</span>";
	} else {
		echo "<span class=\"NotOK\">".$badText."</span>";
	}
	return $value;
}


if (!function_exists('ftp_chmod')) {
  function ftp_chmod($ftp_stream, $mode, $filename) {
    return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $mode, $filename));
  }
}
