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

$app->get('/adm/users', function(){
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdm();
	$page->setTpl("users", array(
		'users' =>$users
	));
});

$app->get('/adm/user/create', function(){
	User::verifyLogin();
	
	$page = new PageAdm();
	$page->setTpl("users-create");
});

$app->get("/adm/users/:iduser/delete", function($iduser){
	User::verifyLogin();
});

$app->get("/adm/users/:iduser", function($iduser){
	User::verifyLogin();
	
	$page = new PageAdm();
	$page->setTpl("users-update");
});

$app->post('/adm/users/create', function(){
	User::verifyLogin();
});

$app->post('/adm/users/:iduser', function($iduser){
	User::verifyLogin();
});

$app->run();

 ?>