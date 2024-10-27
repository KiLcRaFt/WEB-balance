<?php
session_start();
require 'conf.php';
global $connection;

$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn) {
    $login = $_SESSION['user_id'];

    $stmt = $connection->prepare("SELECT id, username, email FROM agapov");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    $stmt->close();
} else {
    echo "User not logged in.\n";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB-Balance</title>
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
        }

        table th {
            font-size: 1.5rem;
        }

        table td {
            font-size: 1.2rem;
        }

        .btn-transfer {
            background-color: #fff;
            color: #000;
            border-radius: 20px;
            padding: 5px 15px;
            cursor: pointer;
        }

        .btn-transfer:hover {
            background-color: black;
            color: white;
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
<div class="container">
    <h2>Funds transfer</h2>
    <table>
        <thead>
        <tr>
            <th>User</th>
            <th>Email</th>
            <th>Transfer</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><button class="btn-transfer btn btn-primary" data-username="<?php echo htmlspecialchars($user['username']); ?>" data-email="<?php echo htmlspecialchars($user['email']); ?>" data-toggle="modal" data-target="#exampleModal">Transfer</button></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">There are no users to display.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="color: black;">Funds transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transfer-form">
                    <div class="mb-3">
                        <label for="recipient-username" class="form-label" style="color: black;">User:</label>
                        <input type="text" class="form-control" id="recipient-username" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="recipient-email" class="form-label" style="color: black;">Email:</label>
                        <input type="email" class="form-control" id="recipient-email" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label" style="color: black;">Transfer amount:</label>
                        <input type="number" class="form-control" id="amount" required>
                    </div>
                    <button type="submit" class="btn btn-success">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-transfer').click(function() {
            var username = $(this).data('username');
            var email = $(this).data('email');

            $('#recipient-username').val(username);
            $('#recipient-email').val(email);

            $('#exampleModal').modal('show');
        });

        $('#transfer-form').submit(function(e) {
            e.preventDefault();

            var recipient = $('#recipient-username').val();
            var amount = $('#amount').val();

            if (amount <= 0) {
                alert("Enter the correct amount.");
                return;
            }

            $.ajax({
                url: 'transfer.php',
                type: 'POST',
                data: {
                    recipient: recipient,
                    amount: amount
                },
                success: function(response) {
                    alert(response);
                    $('#exampleModal').modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('Error while performing the translation.');
                }
            });
        });
    });
</script>
</body>
</html>
