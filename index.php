<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Category;

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

$app->get("/adm/users/create", function(){
	User::verifyLogin();
	
	$page = new PageAdm();
	$page->setTpl("users-create");
});

$app->get("/adm/users/:iduser/delete", function($iduser){
	User::verifyLogin();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user = new User();

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->delete();

	header("Location: /adm/users");
	exit;
});

$app->get("/adm/users/:iduser", function($iduser){
	User::verifyLogin();
	 $user = new User();
	 $user->get((int)$iduser);
	 $page = new PageAdm();
	 $page ->setTpl("users-update", array(
         "user"=>$user->getValues()
     ));
});

$app->post("/adm/users/create", function(){
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /adm/users");
	exit;
});

$app->post('/adm/users/:iduser', function($iduser){
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /adm/users");
	exit;
});

// Recuperação de senha

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

// Categorias

$app->get("/adm/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdm();
	
	$page->setTpl("categories", array(
		"categories"=>$categories
	));
});

$app->get("/adm/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdm();
	
	$page->setTpl("categories-create");
});

$app->post("/adm/categories/create", function(){

	User::verifyLogin();

	$category = new Category();
	
	$category->setData($_POST);

	$category->save();

	header("Location: /adm/categories");
	exit;
});

$app->get("/adm/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /adm/categories");
	exit;
});

$app->get("/adm/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdm();
	
	$page->setTpl("categories-update", [
		"category"=>$category->getValues()
	]);
});

$app->post("/adm/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header("Location: /adm/categories");
	exit;

});


$app->run();

 ?>