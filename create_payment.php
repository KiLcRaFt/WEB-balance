<?php
require 'paypal_config.php';
global $paypal;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

session_start();
global $connection;

if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['amount']) || empty($_POST['amount']) || $_POST['amount'] <= 0) {
    echo "Неверная сумма.";
    exit;
}

$amountValue = $_POST['amount'];

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$amount = new Amount();
$amount->setTotal($amountValue);
$amount->setCurrency('EUR');

$transaction = new Transaction();
$transaction->setAmount($amount);
$transaction->setDescription('Пополнение баланса для пользователя ' . $user_id);

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl('http://www.nommiste.loc/paypal_success.php?user_id=' . $user_id)
    ->setCancelUrl('http://www.nommiste.loc/paypal_cancel.php');

$payment = new Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions(array($transaction))
    ->setRedirectUrls($redirectUrls);

try {
    $payment->create($paypal);
    header("Location: " . $payment->getApprovalLink());
} catch (Exception $e) {
    echo "Ошибка при создании платежа: " . $e->getMessage();
    exit;
}
?>