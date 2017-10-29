<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdm;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");
});

$app->get('/adm', function() {

	User::verifyLogin();
    
	$page = new PageAdm();
	$page->setTpl("index");
});

$app->get('/adm/login', function() {
    
	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);
	$page->setTpl("login");
});

$app->post('/adm/login', function() {
    
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /adm");
	exit;

});

$app->get('/adm/logout', function(){
	User::logout();

	header("Location: /adm/login");
	exit;
});

$app->run();

 ?>