<?php
ob_start();
session_start();
require_once("configuration/configuration.php");
require_once("components/Database/Database.php");

require_once("components/Renderer/Renderer.php");
require_once("components/PasswordHash/PasswordHashClass.php");
require_once("components/User/User.php");
$login = new User();
$renderer = new Renderer();
$request = explode("/", $_SERVER['REQUEST_URI']);
array_shift($request);
$data = array();
$data['title']="test";
$data['content_raw']  = "<h3>Hello World!</h3>";


    $data['content_raw'] .= "<pre>" . var_export($request,true) . "</pre>";

if (!isset($_SESSION['user'])) {
  $data['content_raw'] .= "<h2>Login Required</h2>";
  $data['content_raw'] .= $user->LoginForm();
  $test = $user->ProcessLogin();
  //$data['content_raw'] .= "<br>$test<br>";
}


$data['content_raw'] .= "<table width=100%><tr valign=top><td width=66%>";
if (isset($_SESSION['user'])) {
  if (in_array("admin", $_SESSION['user']['capabilities'])) {
    $menuItem = array();
    $menuItem['title'] = "Projecten";
    $menuItem['slug']  = "projects";
    $data['menu'][]=$menuItem;

    $menuItem = array();
    $menuItem['title'] = "Klanten";
    $menuItem['slug']  = "customers";
    $data['menu'][]=$menuItem;

    if ($request[0]=="customers") {
      require_once("components/Customer/Customer.php");
      echo "Klantenbeheer";
      echo "<a href=/customers/add><button>Nieuwe Klant</button></a>";
      if ($request[1]=="add") Customer::addWizard();
    }

  } 

}

if (isset($errors)) {
  echo "processing errors<br>";
  foreach ($errors as $error) {
    $data['content_raw'] .= "ERRORS:<pre>" . var_export($error,true) . "</pre>";
  }
} else echo "no errors set<br>";
//$data['content_raw'] .= "<br><table><tr><td>SERVER<br><pre>" . var_export($_SERVER,true) . "</pre></td><td>POST<br><pre>" . var_export($_POST,true) . "</pre>SESSION<br><pre>" . var_export($_SESSION,true) . "</pre></td></tr></table>";
$data['content_raw'] .= "<td>SESSION<br><pre>" . var_export($_SESSION,true) . "</pre></td></tr></table>";

$renderer->render($data);
?>
