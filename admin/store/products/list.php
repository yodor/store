<?php
include_once("session.php");
include_once("class/pages/AdminPage.php");

include_once("class/beans/ProductsBean.php");
include_once("class/beans/ProductPhotosBean.php");
include_once("class/beans/ProductColorPhotosBean.php");
include_once("class/beans/ProductInventoryBean.php");

include_once("components/TableView.php");
include_once("components/renderers/cells/TableImageCellRenderer.php");
include_once("components/KeywordSearch.php");
include_once("iterators/SQLQuery.php");

$menu = array(

    new MenuItem("Inventory", "inventory/list.php", "list-add.png"),
    //     new MenuItem("Color Gallery", "color_gallery/list.php?prodID", "list-add.png"),
    //     new MenuItem("Photo Gallery", "gallery/list.php?prodID", "list-add.png"),
    //     new MenuItem("Add Product", "add.php", "list-add.png"),

);

$page = new AdminPage();
$page->checkAccess(ROLE_CONTENT_MENU);

$page->setPageMenu($menu);

$bean = new ProductsBean();

$h_delete = new DeleteItemResponder($bean);


$search_fields = array("product_name", "category_name", "class_name", "product_summary", "keywords", "brand_name",
                       "section");
$ksc = new KeywordSearch();
$ksc->getForm()->setFields($search_fields);
$ksc->getForm()->getRenderer()->setAttribute("method", "get");

$select_products = $bean->select();
// $select_products->fields = " *, sum(stock_amount) as stock_amount, min(price) as price_min, max(price) as price_max, 
// group_concat(color SEPARATOR ';' ) as colors, group_concat(size SEPARATOR ';') as sizes,
// min(weight) as weight_min, max(weight) as weight_max
// ";

$select_products->fields = " 
SUM(pi.stock_amount) as stock_amount,
min(pi.price) as price_min, max(pi.price) as price_max,
group_concat(distinct(size_value) SEPARATOR '<BR>') as sizes, 
p.prodID, p.product_name, p.class_name, p.brand_name, p.section, pc.category_name, p.visible, 
p.price, p.old_price, p.buy_price, cc.pi_ids, replace(cc.colors, '|','<BR>') as colors, cc.color_photos, cc.have_chips, cc.color_ids, cc.product_photos
";

$select_products->from = " products p LEFT JOIN product_inventory pi ON pi.prodID = p.prodID LEFT JOIN color_chips cc ON cc.prodID = p.prodID JOIN product_categories pc ON pc.catID=p.catID ";
$select_products->group_by = "  p.prodID, pi.prodID ";

$ksc->processSearch($select_products);

$view = new TableView(new SQLQuery($select_products, "prodID"));
$view->setCaption("Products List");
$view->setDefaultOrder("  p.insert_date DESC  ");
// $view->search_filter = " ORDER BY day_num ASC ";
$view->addColumn(new TableColumn("prodID", "ID"));

$view->addColumn(new TableColumn("section", "Section"));
$view->addColumn(new TableColumn("class_name", "Class"));
$view->addColumn(new TableColumn("category_name", "Category"));
$view->addColumn(new TableColumn("brand_name", "Brand"));
$view->addColumn(new TableColumn("product_name", "Product Name"));

$view->addColumn(new TableColumn("product_photos", "Product Photo"));

$view->addColumn(new TableColumn("color_photos", "Color Gallery"));

$view->addColumn(new TableColumn("colors", "Colors"));
$view->addColumn(new TableColumn("sizes", "Sizes"));

$view->addColumn(new TableColumn("visible", "Visible"));

$view->addColumn(new TableColumn("stock_amount", "In-stock"));

$view->addColumn(new TableColumn("price_min", "Price Min"));
$view->addColumn(new TableColumn("price_max", "Price Max"));

$view->addColumn(new TableColumn("actions", "Actions"));

$ticr1 = new TableImageCellRenderer(-1, 64);
$ticr1->setBean(new ProductPhotosBean());
$ticr1->setLimit(1);
$view->getColumn("product_photos")->setCellRenderer($ticr1);

$ticr2 = new TableImageCellRenderer(-1, 64);
$ticr2->setBean(new ProductColorPhotosBean());
$ticr2->setLimit(0);
$view->getColumn("color_photos")->setCellRenderer($ticr2);

$view->getColumn("visible")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));
// $view->getColumn("promotion")->setCellRenderer(new BooleanFieldCellRenderer("Yes", "No"));

$act = new ActionsTableCellRenderer();
$act->getActions()->append(new Action("Edit", "add.php", array(new DataParameter("editID", $bean->key()))));
$act->getActions()->append(new PipeSeparator());
$act->getActions()->append($h_delete->createAction());
$act->getActions()->append(new RowSeparator());

$act->getActions()->append(new Action("Inventory", "inventory/list.php", array(new DataParameter("prodID", $bean->key()))));
$act->getActions()->append(new RowSeparator());
$act->getActions()->append(new Action("Color Scheme", "color_gallery/list.php", array(new DataParameter("prodID", $bean->key()))));
$act->getActions()->append(new RowSeparator());

$act->getActions()->append(new Action("Photo Gallery", "gallery/list.php", array(new DataParameter("prodID", $bean->key()))));

$view->getColumn("actions")->setCellRenderer($act);


$page->startRender();

$ksc->render();
$view->render();

$page->finishRender();

?>
