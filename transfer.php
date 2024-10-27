<?php
session_start();
require 'conf.php';
global $connection;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $recipient_username = htmlspecialchars(trim($_POST['recipient']));
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        echo 'Unacceptable transfer amount.';
        exit;
    }

    if ($sender_id == $recipient_username) {
        echo 'You can not send money to yourself.';
        exit;
    }

    $connection->begin_transaction();

    try {
        $query = "SELECT balance FROM agapov WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $sender_id);
        $stmt->execute();
        $stmt->bind_result($sender_balance);
        $stmt->fetch();
        $stmt->close();

        if ($sender_balance < $amount) {
            throw new Exception('Insufficient funds for transfer.');
        }

        $query = "SELECT id FROM agapov WHERE username = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('s', $recipient_username);
        $stmt->execute();
        $stmt->bind_result($recipient_id);
        if (!$stmt->fetch()) {
            throw new Exception('Recipient not found.');
        }
        $stmt->close();

        $query = "UPDATE agapov SET balance = balance - ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('di', $amount, $sender_id);
        $stmt->execute();
        $stmt->close();

        $query = "UPDATE agapov SET balance = balance + ? WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('di', $amount, $recipient_id);
        $stmt->execute();
        $stmt->close();

        $query = "INSERT INTO transactions (sender_id, recipient_id, amount, transaction_date) VALUES (?, ?, ?, NOW())";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('iid', $sender_id, $recipient_id, $amount);
        $stmt->execute();
        $stmt->close();

        $connection->commit();
        echo 'Transfer successfully completed!';
    } catch (Exception $e) {
        $connection->rollback();
        echo 'Translation error: ' . $e->getMessage();
    }
}
?>
