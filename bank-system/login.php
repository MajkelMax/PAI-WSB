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

include 'includes/header.php';
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
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100 login-container">
        <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
            <h2 class="card-title text-center mb-4">Zaloguj się</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="post" class="login-form">
                <div class="mb-3">
                    <label for="login" class="form-label">E-mail:</label>
                    <input type="text" class="form-control" id="login" name="login" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Hasło:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Zaloguj</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'includes/footer.php';
?>
