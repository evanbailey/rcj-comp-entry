<?php

  $incDirShared = $_SERVER["DOCUMENT_ROOT"]. "/shared/";
  $incDirCE     = $_SERVER["DOCUMENT_ROOT"]. "/ce/";
  
  require $incDirShared . 'server-settings.php'; // server-settings.php and 
  require $incDirCE     . 'ce-constants.php';    // ce-constants.php must come before anything ele
  require $incDirCE     . 'ce-except.php';
  require $incDirCE     . 'ce-utils.php';
  require $incDirCE     . 'ce-form-utils.php';
  require $incDirShared . 'connect-pdo.php';
  require $incDirShared . 'constants.php';
  require $incDirShared . 'user-utils.php';
  require $incDirShared . 'form-utils.php';

  
  
?>