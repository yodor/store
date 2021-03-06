<?php
include_once("session.php");

include_once("class/mailers/OrderConfirmationMailer.php");

include_once("class/pages/CheckoutPage.php");

include_once("class/utils/OrderProcessor.php");

include_once("class/mailers/OrderConfirmationAdminMailer.php");
include_once("class/mailers/OrderErrorAdminMailer.php");

$page = new CheckoutPage();

$page->ensureCartItems();
$page->ensureClient();

$orderID = -1;
//process order below

try {

    $oproc = new OrderProcessor();
    $orderID = $oproc->createOrder($page->getCart(), $page->getUserID());

    try {
        $mailer = new OrderConfirmationMailer($orderID);
        $mailer->send();
    }
    catch (Exception $em) {
        error_log("Unable to email confirmation message for order to user: " . $em->getMessage());
    }
    try {
        $mcopy = new OrderConfirmationAdminMailer($orderID);
        $mcopy->send();
    }
    catch (Exception $em) {
        error_log("Unable to email confirmation message for order to admin: " . $em->getMessage());
    }

    header("Location: complete.php?orderID=$orderID");
    exit;
}
catch (Exception $e) {
    Session::SetAlert(tr("Възникна грешка при обработка на Вашата поръчка.") . "<BR>" . tr("Details") . ": " . $e->getMessage());
    try {
        $oem = new OrderErrorAdminMailer();
        $oem->send();
    }
    catch (Exception $oeme) {
        error_log("Unable to email order error message to admin: " . $oeme->getMessage());
    }
    header("Location: confirm.php");
    exit;
}

?>
