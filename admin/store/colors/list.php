<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/StoreColorsBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/renderers/cells/ColorCodeCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$menu = array();

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$action_add = new Action("", "add.php", array());
$action_add->setAttribute("action", "add");
$action_add->setAttribute("title", "Add Color");
$page->addAction($action_add);

$bean = new StoreColorsBean();

$h_delete = new DeleteItemRequestHandler($bean);
RequestController::addRequestHandler($h_delete);

$view = new TableView($bean->query());
$view->setCaption("Available Colors");
$view->setDefaultOrder(" color ASC ");

$view->addColumn(new TableColumn($bean->key(), "ID"));

$view->addColumn(new TableColumn("color", "Color"));
$view->addColumn(new TableColumn("color_code", "Color Code"));

$view->addColumn(new TableColumn("actions", "Actions"));

$view->getColumn("color_code")->setCellRenderer(new ColorCodeCellRenderer());

$act = new ActionsTableCellRenderer();
$act->addAction(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->addAction(new PipeSeparator());
$act->addAction($h_delete->createAction());

$act->addAction(new RowSeparator());

$view->getColumn("actions")->setCellRenderer($act);

Session::Set("color_codes.list", $page->getPageURL());

$page->startRender($menu);

// $ksc->render();
$view->render();

$page->finishRender();

?>
