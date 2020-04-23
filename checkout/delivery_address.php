<?php
include_once("session.php");
include_once("class/pages/CheckoutPage.php");
include_once("class/forms/ClientAddressInputForm.php");
include_once("class/beans/ClientAddressesBean.php");
include_once("class/forms/processors/ClientAddressFormProcessor.php");


$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$cab = new ClientAddressesBean();
$form = new ClientAddressInputForm();


$editID = -1;
$row = $cab->findFieldValue("userID", $page->getUserID());
if ($row) {
    $editID = $row[$cab->key()];
    $form->loadBeanData($editID, $cab);
}

$proc = new ClientAddressFormProcessor();
$proc->setEditID($editID);
$proc->setUserID($page->getUserID());
$proc->setBean($cab);

$frend = new FormRenderer();
$frend->setName("ClientAddress");

$form->setRenderer($frend);
$form->setProcessor($proc);
$frend->setForm($form);


$proc->processForm($form, "submit_item");

if ($proc->getStatus() == FormProcessor::STATUS_OK) {
    //   Session::set("DeliveryDetailsForm", serialize($dform));
    header("Location: confirm.php");
    exit;
}
else if ($proc->getStatus() == FormProcessor::STATUS_ERROR) {
    Session::SetAlert($proc->getMessage());
}


$page->startRender();


$page->setPreferredTitle(tr("Адрес за доставка"));


$page->drawCartItems();


echo "<div class='delivery_details'>";

echo "<div class='caption'>" . tr("Адрес за доставка") . "</div>";


$frend->startRender();

$frend->renderImpl();


echo "<input type=hidden name='submit_item' value='submit'>";

<<<<<<< HEAD
$frend->finishRender();


echo "</div>"; //delivery_details


echo "<div class='navigation'>";

echo "<div class='slot left'>";
echo "<a href='confirm.php'>";
echo "<img src='" . SITE_ROOT . "images/cart_edit.png'>";
echo "<div class='DefaultButton checkout_button' >" . tr("Назад") . "</div>";
echo "</a>";
echo "</div>";

echo "<div class='slot center'>";
=======
// $back_url = Session::get("checkout.navigation.back", $page->getPageURL());

echo "<div class='navigation'>";

    echo "<div class='slot left'>";
        echo "<a href='delivery.php'>";
        echo "<img src='".SITE_ROOT."images/cart_edit.png'>";
        echo "<div class='DefaultButton checkout_button' >".tr("Назад")."</div>";
        echo "</a>";
    echo "</div>";
>>>>>>> origin/master

echo "</div>";

echo "<div class='slot right'>";
echo "<a href='javascript:document.forms.ClientAddress.submit();'>";
echo "<img src='" . SITE_ROOT . "images/cart_checkout.png'>";
echo "<div class='DefaultButton checkout_button'  >" . tr("Продължи") . "</div>";
echo "</a>";
echo "</div>";


echo "</div>";


$page->finishRender();
?>
