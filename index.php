<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once('site.php');
require_once('funcoes.php');
require_once('adm.php');
require_once('adm_users.php');
require_once('adm_categories.php');
require_once('adm_products.php');

$app->run();

 ?>