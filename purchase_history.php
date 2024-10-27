<?php
session_start();
require 'conf.php';
global $connection;

$user_id = $_SESSION['user_id'];

$query = "SELECT product_name, price, purchase_date FROM purchases WHERE user_id = ? ORDER BY purchase_date DESC";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История покупок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/background.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 25px;
            margin: 20px auto auto auto;
            width: 1200px;
            height: 100px;
        }

        .header h1 {
            font-size: 3rem;
        }

        .header .buttons button {
            border-radius: 20px;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            margin: 5px;
        }

        table {
            width: 100%;
            text-align: left;
            margin-top: 30px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            border-bottom: 1px solid #fff;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        table th {
            font-size: 1.5rem;
        }

        table td {
            font-size: 1.2rem;
        }
    </style>
</head>
<header class="header">
    <h1>WEB-balance</h1>
    <div class="buttons">
        <button class="btn-transfer" onclick="window.location.href='index.php'">Home</button>
        <button class="btn-transfer" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</header>
<body>
<div class="container mt-5">
    <h1>Your purchase history</h1>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Date of purchase</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['price']); ?> euro</td>
                    <td><?php echo htmlspecialchars($row['purchase_date']); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>The purchase history is empty.</p>
    <?php endif; ?>

</div>
</body>
</html>