<?php
/**
 * Strona główna systemu bankowego
 * 
 * Ten plik zawiera podstawową strukturę strony głównej
 * systemu bankowego, podłączoną do bazy danych PostgreSQL.
 * 
 * @author Michał Grzelka
 * @version 2.0
 */

// Dołączenie pliku konfiguracyjnego bazy danych
require_once 'includes/db_config.php';

// Pobieranie danych użytkownika z bazy danych
$userId = getCurrentUserId();
$userData = getUserById($userId);
$accountData = getAccountByUserId($userId);

if (!$userData || !$accountData) {
    die("Błąd: Nie można pobrać danych użytkownika lub konta");
}

// Pobieranie ostatnich 3 transakcji
$transactions = getTransactionsByAccountId($accountData['account_id'], 3);

// Przekształcenie danych do wyświetlenia
$accountInfo = [
    'name' => $userData[0]['first_name'] . ' ' . $userData[0]['last_name'],
    'account_number' => $accountData['account_number'],
    'balance' => $accountData['balance']
];

// Dołączenie nagłówka strony
include 'includes/header.php';
?>

<div class="container">
    <section class="welcome-section">
        <h2>Witaj w systemie bankowym</h2>
        <p>Prostym i bezpiecznym sposobie zarządzania finansami</p>
    </section>
    
    <section class="account-summary">
        <h3>Twoje konto</h3>
        <div class="account-info">
            <p><strong>Właściciel:</strong> <?php echo $accountInfo['name']; ?></p>
            <p><strong>Numer konta:</strong> <?php echo $accountInfo['account_number']; ?></p>
            <p class="balance"><strong>Saldo:</strong> 
                <span class="amount"><?php echo number_format($accountInfo['balance'], 2, ',', ' '); ?> PLN</span>
            </p>
        </div>
        <div class="account-actions">
            <a href="transfer.php" class="btn">Wykonaj przelew</a>
            <a href="account.php" class="btn">Szczegóły konta</a>
        </div>
    </section>
    
    <section class="recent-transactions">
        <h3>Ostatnie transakcje</h3>
        <?php if ($transactions && count($transactions) > 0): ?>
        <table class="transactions-table">
            <tr>
                <th>Data</th>
                <th>Opis</th>
                <th>Kwota</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($transaction['date'])); ?></td>
                <td><?php echo $transaction['description']; ?></td>
                <td class="<?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo number_format($transaction['amount'], 2, ',', ' '); ?> PLN
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>Brak transakcji do wyświetlenia.</p>
        <?php endif; ?>
    </section>
</div>

<?php
// Dołączenie stopki strony
include 'includes/footer.php';
?>