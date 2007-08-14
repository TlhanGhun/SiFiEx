<?php

// this file will be called on first time setup of SiFiEx
// to provide an easy installation and enough help for problems
// coming out of insufficient write access to specific directories

class setup {
  function checkWritableConfig () {
    return(is_writable("../config.php"));
  }

  function checkWritableFilesFolder () {
    return(is_writable("../files/"));
  }

  function writeNewConfig() {
    copy("../config.php.templ", "../config.php");    
  }
}

?>
