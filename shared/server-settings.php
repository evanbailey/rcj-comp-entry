<?php
  $httpHost =  $_SERVER['HTTP_HOST'];
  if ($httpHost == 'localhost'){
    // define("C_DB_NAME",      "rcjaclub_robocup");
    // define("C_DB_USER_NAME", "rcjaclub_robocup");
    // define("C_DB_PASSWORD",  "R0b0CupJun10r");    
    // define("C_MYSQL",        "C:/xampp/mysql/bin/mysql");
    // define("C_MYSQLDUMP",    "C:/xampp/mysql/bin/mysqldump");
    // define("C_DEBUG",        "TRUE");

    define("C_DB_NAME",      "local_rcja");
    define("C_DB_USER_NAME", "root");
    define("C_DB_PASSWORD",  "R00t");    
    define("C_MYSQL",        "C:/xampp/mysql/bin/mysql");
    define("C_MYSQLDUMP",    "C:/xampp/mysql/bin/mysqldump");
    define("C_DEBUG",        "TRUE");
  } else if ($httpHost == 'test.clubengineer.org'){
    define("C_DB_NAME",      "clubeng1_robocup");
    define("C_DB_USER_NAME", "clubeng1_robocup");
    define("C_DB_PASSWORD",  "R0b0CupJun10r");        
    define("C_MYSQL",        "mysql");
    define("C_MYSQLDUMP",    "mysqldump");
    define("C_DEBUG",        "TRUE");
  } else if ($httpHost == 'rcja.clubengineer.org'){
    define("C_DB_NAME",      "rcjaclub_robocup");
    define("C_DB_USER_NAME", "rcjaclub_robocup");
    define("C_DB_PASSWORD",  "R0b0CupJun10r");    
    define("C_MYSQL",        "mysql");
    define("C_MYSQLDUMP",    "mysqldump");
    define("C_DEBUG",        "FALSE");
  } else if ($httpHost == 'enter-preprod.rcj.org.au'){
    define("C_DB_NAME",      "rcja_entry_preprod");
    define("C_DB_USER_NAME", "root");
    define("C_DB_PASSWORD",  "RCJfor2017");    
    define("C_MYSQL",        "/usr/bin/mysql");
    define("C_MYSQLDUMP",    "/usr/bin/mysqldump");
    define("C_DEBUG",        "TRUE");
  } else if ($httpHost == 'enter.rcjonline.org.au'){
    define("C_DB_NAME",      "rcjonlin_enter");
    define("C_DB_USER_NAME", "rcjonlin_enter");
    define("C_DB_PASSWORD",  "T^mvQKRb8_z9J=Hr=x");   //this password is not current. 
    define("C_MYSQL",        "mysql");
    define("C_MYSQLDUMP",    "mysqldump");
    define("C_DEBUG",        "FALSE");
  } else {
    throw new Exception('Unknown HTTP Host: "' . $httpHost . '"'); 
  }
  
  define("C_DB_HOST",      "localhost");
  
  /*
  function echoServerSettings(){
    echo '<p>HTTP Host: '      . $httpHost . '</p>';
    echo '<p>C_DB_HOST: '      . C_DB_HOST . '</p>';
    echo '<p>C_DB_NAME: '      . C_DB_NAME . '</p>';
    echo '<p>C_DB_USER_NAME: ' . C_DB_USER_NAME . '</p>';
    echo '<p>C_DB_PASSWORD: '  . C_DB_PASSWORD . '</p>';
    echo '<p>C_MYSQL: '        . C_MYSQL . '</p>';
    echo '<p>C_MYSQLDUMP: '    . C_MYSQLDUMP . '</p>';
    echo '<p>C_DEBUG: '        . C_DEBUG . '</p>';
  }
  */

?>