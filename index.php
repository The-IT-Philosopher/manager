<?php
ob_start();
session_start();
require_once("configuration/configuration.php");
require_once("components/Database/Database.php");

require_once("components/Renderer/Renderer.php");
require_once("components/PasswordHash/PasswordHashClass.php");
require_once("components/Login/Login.php");
$login = new Login();
$renderer = new Renderer();
$data = array();
$data['title']="test";
$data['content_raw']  = "<h3>Hello World!</h3>";

if (!isset($_SESSION['uid'])) {
  $data['content_raw'] .= "<h2>Login Required</h2>";
  $data['content_raw'] .= $login->LoginForm();
  //
  $test = $login->ProcessLogin();
  $data['content_raw'] .= "<br>$test<br>";
}

if (isset($errors)) {
  echo "processing errors<br>";
  foreach ($errors as $error) {
    $data['content_raw'] .= "<pre>" . var_export($error,true) . "</pre>";
  }
} else echo "no errors set<br>";
$data['content_raw'] .= "<br><table><tr><td>SERVER<br><pre>" . var_export($_SERVER,true) . "</pre></td><td>POST<br><pre>" . var_export($_POST,true) . "</pre></td></tr></table>";
$renderer->render($data);
?>
