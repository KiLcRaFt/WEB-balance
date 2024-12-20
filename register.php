<?php
require_once("conf.php");
global $connection;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $login = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $pass = htmlspecialchars(trim($_POST['password']));

        $checkUserQuery = $connection->prepare("SELECT * FROM agapov WHERE username = ? OR email = ?");
        $checkUserQuery->bind_param("ss", $login, $email);
        $checkUserQuery->execute();
        $checkResult = $checkUserQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo '<script>alert("User with this username or email already exists!");</script>';
        } else {
            $cool = 'YOUR_CRYPT_WORD';
            $kryp = crypt($pass, $cool);

            $kask2 = $connection->prepare("INSERT INTO agapov (username, email, password) VALUES (?, ?, ?)");
            $kask2->bind_param("sss", $login, $email, $kryp);
            $kask2->execute();

            echo '<script>alert("Registration successful!");</script>';

            $kask2->close();
            $connection->close();
            echo "<script type='text/javascript'>
                window.location.href = 'index.php';
            </script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WEB-Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('images/background.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: #fff;
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
        h1 {
            font-size: 3rem;
        }
        .btn-group {
            display: flex;
            gap: 15px;
        }
        .btn-custom {
            border-radius: 20px;
            border: none;
            padding: 10px 20px;
            font-size: 1.2rem;
            margin: 5px;
        }

        .btn-home {
            background-color: #fff;
            color: #000;
        }

        .btn-login {
            background-color: #777;
            color: #fff;
        }
        .btn-home:hover, .btn-login:hover {
            background-color: black;
            color: white;
            cursor: pointer;
        }

        .register-container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 15px;
            margin: 50px auto;
            max-width: 500px;
            text-align: left;
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid #ccc;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            box-shadow: none;
            border-color: #d10000;
        }
        label {
            color: #ddd;
        }
    </style>
</head>
<body>
<header class="header">
    <h1>WEB-Balance</h1>
    <div class="btn-group">
        <button class="btn-home btn-custom" onclick="window.location.href='index.php'">Home</button>
        <button class="btn-login btn-custom" onclick="window.location.href='login.php'">Login</button>
    </div>
</header>
<div class="register-container">
    <h2 class="text-center">Register</h2>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn-custom btn-block">Register</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>