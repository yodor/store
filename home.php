<?php
include_once("session.php");
include_once("class/pages/StorePage.php");
include_once("class/beans/SectionsBean.php");
include_once("class/beans/SectionBannersBean.php");
include_once("class/utils/ProductsSQL.php");
include_once("class/components/renderers/items/ProductListItem.php");

$page = new StorePage();
$page->setTitle("Начало");

$item = new ProductListItem();

$page->startRender();

$banners = new SectionBannersBean();

$qry = $page->sections->query("secID", "section_title");
$qry->select->order_by = " position ASC ";

$sel = new ProductsSQL();
$sel->order_by = " pi.order_counter DESC, pi.view_counter DESC ";
$sel->group_by = " pi.prodID, pi.color ";
$sel->limit = "4";
//echo $sel->getSQL();

$prodQry = new SQLQuery($sel, "pi.prodID");

$qry->exec();

//TODO list only sections with products
while ($section = $qry->next()) {

    $sectionName = $section["section_title"];
    $secID = $section["secID"];

    $sel->where()->clear();
    $sel->where()->add("p.section", "'$sectionName'");

    $num = $prodQry->exec();
    if ($num < 1) continue;

    echo "<div class='section $sectionName'>";

    $secion_url = LOCAL . "/products.php?section=$sectionName";
    echo "<a class='caption' href='$secion_url'>$sectionName</a>";

    $qry1 = $banners->queryField("secID", $secID, 1, "sbID", "caption", "link", "position");
    $qry1->select->order_by = " RAND() ";

    $num = $qry1->exec();

    if ($banner = $qry1->next()) {
        $banner_url = $secion_url;
        if ($banner["link"]) {
            $banner_url = $banner["link"];
        }
        echo "<a class='banner' href='$banner_url'>";
        $img_href = StorageItem::Image($banner["sbID"], $banners);
        echo "<img width='100%' src='$img_href'>";
        echo "</a>";
    }

    echo "<div class='products'>";

    while ($row = $prodQry->next()) {
        $item->setData($row);
        $item->render();
    }

    echo "</div>";

    echo "</div>";
}

Session::Set("shopping.list", $page->getPageURL());

$page->finishRender();

?>
