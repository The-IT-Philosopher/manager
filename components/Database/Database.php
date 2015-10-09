<?php
try {
  $pdo = new PDO( 
      "mysql:host=$DBHOST;dbname=$DBNAME", 
      $DBUSER, 
      $DBPASS, 
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") 
  );
} catch (Exception $e) {
  if (!isset($errors)) $errors = array();
  $errors[]=$e;
}
unset($DBNAME);
unset($DBUSER);
unset($DBPASS);
unset($DBHOST); 
?>
