<?php
session_start();
require 'conf.php';
global $connection;

$user_id = $_SESSION['user_id'];

$query = "SELECT username FROM agapov WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($current_username);
$stmt->fetch();
$stmt->close();

$query = "
    SELECT 
    t.amount, 
    t.transaction_date, 
    sender.username AS sender_username, 
    recipient.username AS recipient_username
    FROM 
    transactions t
    JOIN 
    agapov sender ON t.sender_id = sender.id
    JOIN 
    agapov recipient ON t.recipient_id = recipient.id
    WHERE 
    t.sender_id = ? OR t.recipient_id = ?
    ORDER BY 
    t.transaction_date DESC
";
$stmt = $connection->prepare($query);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История транзакций</title>
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
            margin: 20px auto;
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
            color: #fff;
            background-color: #000;
        }

        .header .buttons button:hover {
            background-color: #333;
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

<body>
<header class="header">
    <h1>WEB-balance</h1>
    <div class="buttons">
        <button class="btn-transfer" onclick="window.location.href='index.php'">Home</button>
        <button class="btn-transfer" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</header>
<div class="container mt-5">
    <h1>Your transaction history</h1>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Sender</th>
                <th>Recipient</th>
                <th>Amount</th>
                <th>Transaction date</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ($row['sender_username'] == $current_username) ? 'You' : htmlspecialchars($row['sender_username']); ?></td>
                    <td><?php echo ($row['recipient_username'] == $current_username) ? 'You' : htmlspecialchars($row['recipient_username']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?> euro</td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Transaction history is empty.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
