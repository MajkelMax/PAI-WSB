
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Bankowy</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php $loggedIn = getCurrentUserId(); ?>
    <header class="main-header">
        <div class="logo">
            <img src="img/logo.png" alt="Logo Banku" class="logo-img">
            <h1>System Bankowy</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Strona główna</a></li>
                <li><a href="account.php">Moje konto</a></li>
                <li><a href="transfer.php">Przelewy</a></li>
                <?php if ($loggedIn): ?>
                <li><a href="logout.php">Wyloguj</a></li>
                <?php else: ?>
                <li><a href="login.php">Zaloguj</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>