<?php
require_once 'includes/db_config.php';

// Jesli uzytkownik jest juz zalogowany, przekieruj na strone glowna
if (getCurrentUserId()) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $userId = authenticateUser($login, $password);
    if ($userId !== false) {
        $_SESSION['user_id'] = $userId;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Nieprawidłowa nazwa użytkownika lub hasło';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container login-container">
        <h2>Zaloguj się</h2>
        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="login.php" method="post" class="login-form">
            <div class="form-group">
                <label for="login">E-mail lub login:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="form-group">
                <label for="password">Hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn">Zaloguj</button>
            </div>
        </form>
    </div>
</body>
</html>