<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/StoreSizesBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Size");
$page->addAction($action_add);

$bean = new StoreSizesBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$view = new TableView($bean->query());
$view->items_per_page = 100;

$view->setCaption("Sizing Codes List");
$view->setDefaultOrder(" size_value ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));
$view->addColumn(new TableColumn("size_value", "Size"));

$view->addColumn(new TableColumn("actions", "Actions"));

$act = new ActionsTableCellRenderer();
$act->addAction(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->addAction(new PipeSeparator());
$act->addAction($h_delete->createAction());

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("sizing.list", $page->getPageURL());

$page->startRender($menu);

$view->render();

$page->finishRender();
?>
