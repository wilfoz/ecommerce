<?php 

use \Hcode\Page;
use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

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


$app->get("/adm/categories/:idcategory/products", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdm();

	$page->setTpl("categories-products", [
		"category"=>$category->getValues(),
		"productsRelated"=>$category->getProducts(),
		"productsNotRelated"=>$category->getProducts(false)
	]);

});

$app->get("/adm/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /adm/categories/".$idcategory."/products");
	exit;
	
});

$app->get("/adm/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /adm/categories/".$idcategory."/products");
	exit;
	
});

?>