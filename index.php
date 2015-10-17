<?php
ob_start();
session_start();
require_once("configuration/configuration.php");
require_once("base/Philosopher.php");

$stone = new Philosopher\Stone();
$stone->registerComponent(new Philosopher\AuthSession());
$stone->registerComponent(new Philosopher\RenderHTML5());
$stone->processRequest();


?>
