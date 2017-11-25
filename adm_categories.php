<?php 

use \Hcode\Page;
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Category;

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

$app->get("/categories/:idcategory", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();
	$page->setTpl("category", [
		"category"=>$category->getValues(),
		"products"=>[]
	]);

});

?>