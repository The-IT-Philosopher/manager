<?php
require_once("configuration/configuration.php");
require_once("base/Philosopher.php");
ob_start();
session_start();
if (isset($_GET['reset'])) unset ($_SESSION['stone']); // test
if ( isset($_SESSION['stone'])) {
  $stone = $_SESSION['stone'];
} else {
  $stone = new Philosopher\Stone();
  $_SESSION['stone']=$stone;
  $stone->registerComponent(new Philosopher\RenderHTML5());
  $stone->registerComponent(new Philosopher\DatabaseConnection());
  $stone->registerComponent(new Philosopher\AuthSession());
  $stone->registerComponent(new Philosopher\Wizard());
  $stone->registerComponent(new Philosopher\Wizard_KvK());
  $stone->registerComponent(new Philosopher\Wizard_Company());
  //$stone->registerComponent(new Philosopher\Test_Wizard());
  $stone->registerComponent(new Philosopher\DP_OverheidIO());
//
  //$stone->registerComponent(new Philosopher\RenderXML());
  //$stone->registerComponent(new Philosopher\RenderJSON());
  //$stone->registerComponent(new Philosopher\RenderHTML3());
}
$stone->processRequest();


?>
