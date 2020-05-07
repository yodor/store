<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductPhotosBean.php");

include_once("forms/PhotoInputForm.php");


$ref_key = "";
$ref_val = "";
$qrystr = refkeyPageCheck(new ProductsBean(), "../list.php", $ref_key, $ref_id);

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("products.gallery"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Gallery");
$page->addAction($action_back);

$photos = new ProductPhotosBean();
$photos->select()->where = "$ref_key='$ref_id'";

$view = new InputFormView($photos, new PhotoInputForm());

//current version of dynamic page photos table is set to DBROWS
$view->getForm()->getInput("photo")->transact_mode = DataInput::TRANSACT_OBJECT;

$view->getTransactor()->appendValue($ref_key, $ref_id);

$view->processInput();

$page->startRender($menu);

$page->renderPageCaption();

$view->render();

$page->finishRender();


?>
