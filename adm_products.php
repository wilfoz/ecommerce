<?php 

use \Hcode\PageAdm;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/adm/products", function(){

	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdm();

	$page->SetTpl("products", array(
		"products"=>$products
	));

});

$app->get("/adm/products/create", function(){
	User::verifyLogin();
	
	$page = new PageAdm();
	$page->setTpl("products-create");
});

$app->post("/adm/products/create", function(){
	User::verifyLogin();
	
	$products = new Product();

	$products->setData($_POST);

	$products->save();

	header("Location: /adm/products");
	exit;
});

$app->get("/adm/products/:idproduct", function($idproduct){
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);
	
	$page = new PageAdm();

	$page->setTpl("products-update", array(
		"product"=>$product->getValues()
	));
});


$app->post("/adm/products/:idproduct", function($idproduct){
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);
	
	$product->setData($_POST);

	$product->save();

	$product->setPhoto($_FILES["file"]);

	header("Location: /adm/products");
	exit;

});

$app->get("/adm/products/:idproduct/delete", function($idproduct){
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header("Location: /adm/products");
	exit;
});


?>