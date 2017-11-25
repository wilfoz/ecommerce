<?php 


use \Hcode\PageAdm;
use \Hcode\Model\User;

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

$app->get("/adm/forgot", function(){
	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);

$page->setTpl("forgot");

});

$app->post("/adm/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /adm/forgot/sent");
	exit;

});

$app->get("/adm/forgot/sent", function(){
	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-sent");

});

$app->get("/adm/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});

$app->post("/adm/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User;

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT,[
		"cost"=>12
	]);

	$user->setPassword($password);

	$page = new PageAdm([
		"header"=>false,
		"footer"=>false
	]);
	
	$page->setTpl("forgot-reset-success");

});

?>