<?php 

use \Hcode\PageAdm;
use \Hcode\Model\User;

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



?>