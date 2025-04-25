<?php
/**
 * Strona szczegółów konta
 * 
 * Ten plik zawiera szczegółowe informacje o koncie
 * oraz historię wszystkich transakcji, pobieranych z bazy PostgreSQL.
 * 
 * @author Michał Grzelka
 * @version 2.0
 */

// Dołączenie pliku konfiguracyjnego bazy danych
require_once 'db_config.php';

// Pobieranie danych użytkownika z bazy danych
$userId = getCurrentUserId();
$userData = getUserById($userId);
$accountData = getAccountByUserId($userId);

if (!$userData || !$accountData) {
    die("Błąd: Nie można pobrać danych użytkownika lub konta");
}

// Pobieranie wszystkich transakcji
$transactions = getTransactionsByAccountId($accountData['account_id']);

// Przekształcenie danych do wyświetlenia
$accountInfo = [
    'name' => $userData[0]['first_name'] . ' ' . $userData[0]['last_name'],
    'account_number' => $accountData['account_number'],
    'balance' => $accountData['balance'],
    'account_type' => $accountData['account_type'],
    'open_date' => $accountData['open_date']
];

// Dołączenie nagłówka strony
include 'includes/header.php';
?>

<div class="container">
    <h2>Szczegóły konta</h2>
    
    <section class="account-details">
        <h3>Informacje o koncie</h3>
        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Właściciel:</span>
                <span class="detail-value"><?php echo $accountInfo['name']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Numer konta:</span>
                <span class="detail-value"><?php echo $accountInfo['account_number']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Typ konta:</span>
                <span class="detail-value"><?php echo $accountInfo['account_type']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data otwarcia:</span>
                <span class="detail-value"><?php echo date('Y-m-d', strtotime($accountInfo['open_date'])); ?></span>
            </div>
            <div class="detail-row highlight">
                <span class="detail-label">Aktualne saldo:</span>
                <span class="detail-value"><?php echo number_format($accountInfo['balance'], 2, ',', ' '); ?> PLN</span>
            </div>
        </div>
    </section>
    
    <section class="transaction-history">
        <h3>Historia transakcji</h3>
        <?php if ($transactions && count($transactions) > 0): ?>
        <table class="transactions-table">
            <tr>
                <th>Data</th>
                <th>Opis</th>
                <th>Kwota</th>
                <th>Saldo po operacji</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo date('Y-m-d', strtotime($transaction['date'])); ?></td>
                <td><?php echo $transaction['description']; ?></td>
                <td class="<?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo number_format($transaction['amount'], 2, ',', ' '); ?> PLN
                </td>
                <td>
                    <?php echo number_format($transaction['balance_after'], 2, ',', ' '); ?> PLN
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>Brak transakcji do wyświetlenia.</p>
        <?php endif; ?>
    </section>
    
    <div class="action-buttons">
        <a href="transfer.php" class="btn">Wykonaj przelew</a>
        <a href="index.php" class="btn btn-secondary">Powrót do strony głównej</a>
    </div>
</div>

<?php
// Dołączenie stopki strony
include 'includes/footer.php';
?>