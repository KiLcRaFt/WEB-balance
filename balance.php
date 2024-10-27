<?php
session_start();
require_once("conf.php");
global $connection;
$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn) {
    $login = $_SESSION['user_id'];

    $kask = $connection->prepare("SELECT username, email, balance FROM agapov WHERE id=?;");
    $kask -> bind_param('i', $login);
    $userData = null;

    $kask->execute();
    $kask->store_result();

    if ($kask->num_rows > 0) {
        $kask->bind_result($username, $email, $balance);
        while ($kask->fetch()) {
            $userData = [
                'username' => $username,
                'email' => $email,
                'balance' => $balance
            ];
        }
    }
    else {
        echo "Nothing";
    }

    $kask->close();
} else {
    echo "User  not logged in.\n";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WEB-Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/money.js/0.2.0/money.min.js"></script>
    <script>
        function convertToRUB(balanceInEUR) {
            fetch('https://api.exchangerate-api.com/v4/latest/EUR')
                .then(response => response.json())
                .then(data => {
                    fx.rates = data.rates;
                    fx.base = "EUR";
                    var convertedAmount = fx(balanceInEUR).from("EUR").to("RUB");
                    var convertedAmount2 = fx(balanceInEUR).from("EUR").to("USD");

                    document.getElementById('balance-in-rub').innerText = convertedAmount.toFixed(2) + " ₽";
                    document.getElementById('balance-in-dollar').innerText = convertedAmount2.toFixed(2) + " $";
                })
                .catch(error => console.error('Error fetching exchange rates:', error));
        }
        document.addEventListener("DOMContentLoaded", function() {
            const balanceInEUR = <?php echo $balance; ?>;
            convertToRUB(balanceInEUR);
        });
    </script>
    <style>
        body {
            background: url('images/background.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .profile-container {
            width: 100%;
            max-width: 1200px;
            margin: 50px auto;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
        }

        .profile-header {
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .balance-display {
            font-size: 4rem;
            font-weight: bold;
            margin: 40px 0;
        }

        .balance-display span {
            display: block;
            font-size: 1.5rem;
            color: #ccc;
        }

        .btn-custom1 {
            background-color: #fff;
            color: #000;
        }

        .btn-custom2 {
            background-color: #777;
            color: #fff;
        }

        .btn-custom1:hover, .btn-custom2:hover {
            background-color: black;
            color: white;
            cursor: pointer;
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
        .profile-container .buttons button {
            border-radius: 20px;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            margin: 5px;
        }

    </style>
</head>
<body>
<header class="header">
    <h1>WEB-Balance</h1>
    <div class="buttons">
        <button class="btn-custom1" onclick="window.location.href='index.php'">Home</button>
        <button class="btn-custom2" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</header>

<?php if ($isLoggedIn): ?>
<div class="profile-container">
    <div class="profile-header">Ваш профиль:</div>
    <div>Имя пользователя: <?php echo $userData["username"]; ?></div>
    <div>Электронная почта: <?php echo $userData["email"]; ?></div>

    <div class="balance-display">
        <?php echo $balance; ?> €
    </div>
    <div class="balance-display">
        <p id="balance-in-rub">Loading...</p>
    </div>
    <div class="balance-display">
        <p id="balance-in-dollar">Loading...</p>
    </div>

    <div class="buttons">
        <button class="btn-custom1" data-bs-toggle="modal" data-bs-target="#paymentModal">Пополнить</button>
        <button class="btn-custom1" onclick="window.location.href='transaction.php'">Перевести</button>
    </div>
    <div class="buttons">
        <button class="btn-custom2" onclick="window.location.href='purchase_history.php'">История покупок</button>
        <button class="btn-custom2" onclick="window.location.href='transaction_history.php'">История транзакций</button>
    </div>
</div>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel" style="color: black;">Введите сумму пополнения</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="paymentForm" method="POST" action="create_payment.php">
          <div class="mb-3">
            <label for="amountInput" class="form-label" style="color: black;">Сумма (€)</label>
            <input type="number" class="form-control" id="amountInput" name="amount" placeholder="Введите сумму" required>
          </div>
          <button type="submit" class="btn-custom2" style="border-radius: 20px; border: none; padding: 10px 20px; font-size: 1.2rem;" >Пополнить</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
<?php else: ?>
<script type='text/javascript'>
    window.location.href = 'index.php';
</script>

<?php endif; ?>