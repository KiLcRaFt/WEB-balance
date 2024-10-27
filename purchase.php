<?php
session_start();
require 'conf.php';
global $connection;

$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn) {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $product = htmlspecialchars(trim($_POST['product']));
        $price = floatval($_POST['price']);

        $user_id = $_SESSION['user_id'];

        $query = "SELECT balance FROM agapov WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($user_balance);
        $stmt->fetch();
        $stmt->close();

        if ($user_balance >= $price) {
            $connection->begin_transaction();
            try {
                $query = "UPDATE agapov SET balance = balance - ? WHERE id = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param('di', $price, $user_id);
                $stmt->execute();
                $stmt->close();

                $query = "INSERT INTO purchases (user_id, product_name, price) VALUES (?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param('isd', $user_id, $product, $price);
                $stmt->execute();
                $stmt->close();

                $connection->commit();

                $response = [
                    'status' => 'success',
                    'message' => 'The purchase has been successfully completed!',
                    'new_balance' => $user_balance - $price
                ];
                echo json_encode($response);
            } catch (Exception $e) {
                $connection->rollback();
                echo json_encode(['status' => 'error', 'message' => 'Buying mistake: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient funds for the purchase.']);
        }
    }
}
?>