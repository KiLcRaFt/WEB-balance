<?php
require 'paypal_config.php';
global $paypal;

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

session_start();
require 'conf.php';
global $connection;

if (!isset($_GET['paymentId'], $_GET['PayerID'], $_GET['user_id'])) {
    echo "Invalid payment data.";
    exit;
}

$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];
$user_id = $_SESSION['user_id'];

$payment = Payment::get($paymentId, $paypal);
$execution = new PaymentExecution();
$execution->setPayerId($payerId);

try {
    $payment->execute($execution, $paypal);

    $transactions = $payment->getTransactions();
    $transaction = $transactions[0];
    $amount = $transaction->getAmount()->getTotal();

    $stmt = $connection->prepare("UPDATE agapov SET balance = balance + ? WHERE id = ?");
    $stmt->bind_param('di', $amount, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "The payment has been successfully completed. The balance has been updated.";
    echo "<script>window.location.href='index.php';</script>";
} catch (Exception $e) {
    echo "Error during payment confirmation: " . $e->getMessage();
}
?>
