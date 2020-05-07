<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");

include_once("forms/PhotoInputForm.php");


$ref_key = "";
$ref_val = "";
$qrystr = refkeyPageCheck(new SectionsBean(), "../list.php", $ref_key, $ref_id);

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_back = new Action("", Session::Get("section.banners.list"), array());
$action_back->setAttribute("action", "back");
$action_back->setAttribute("title", "Back To Banners");
$page->addAction($action_back);

$photos = new SectionBannersBean();
$photos->select()->where = "$ref_key='$ref_id'";

$form = new PhotoInputForm();
$field = new DataInput("link", "Link", 1);
$field->setRenderer(new TextField());
// $field->content_after = "<a class='ActionRenderer DynamicPageChooser' href='".ADMIN_ROOT."content/pages/list.php?chooser=1'>".tr("Choose Dynamic Page")."</a>";
$form->addInput($field);
$view = new InputFormView($photos, $form);

//current version of dynamic page photos table is set to DBROWS
$view->getForm()->getInput("photo")->transact_mode = DataInput::TRANSACT_OBJECT;

$view->getTransactor()->appendValue($ref_key, $ref_id);

$view->processInput();

$page->startRender($menu);

$page->renderPageCaption();

$view->render();

$page->finishRender();


?>
