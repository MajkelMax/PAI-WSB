<?php
/**
 * Strona szczegółów konta
 * 
 * Ten plik zawiera szczegółowe informacje o koncie
 * oraz historię wszystkich transakcji.
 * 
 * @author Michał Grzelka
 * @version 1.0
 */

// Symulacja danych użytkownika (w przyszłości będą pobierane z bazy danych)
$accountData = [
    'name' => 'Jan Kowalski',
    'account_number' => '12 3456 7890 1234 5678 9012 3456',
    'balance' => 1500.75,
    'account_type' => 'Konto osobiste',
    'open_date' => '2023-01-15'
];


$transactions = [
    [
        'date' => '2025-04-01',
        'description' => 'Wypłata wynagrodzenia',
        'amount' => 3000.00,
        'balance_after' => 1500.75
    ],
    [
        'date' => '2025-03-28',
        'description' => 'Zakupy spożywcze',
        'amount' => -125.60,
        'balance_after' => -1499.25
    ],
    [
        'date' => '2025-03-25',
        'description' => 'Opłata za mieszkanie',
        'amount' => -1200.00,
        'balance_after' => -1373.65
    ],
    [
        'date' => '2025-03-20',
        'description' => 'Przelew od Jana Nowaka',
        'amount' => 350.00,
        'balance_after' => -173.65
    ],
    [
        'date' => '2025-03-15',
        'description' => 'Zakupy online',
        'amount' => -250.50,
        'balance_after' => -523.65
    ],
    [
        'date' => '2025-03-10',
        'description' => 'Wpłata własna',
        'amount' => 500.00,
        'balance_after' => -273.15
    ],
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
                <span class="detail-value"><?php echo $accountData['name']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Numer konta:</span>
                <span class="detail-value"><?php echo $accountData['account_number']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Typ konta:</span>
                <span class="detail-value"><?php echo $accountData['account_type']; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Data otwarcia:</span>
                <span class="detail-value"><?php echo $accountData['open_date']; ?></span>
            </div>
            <div class="detail-row highlight">
                <span class="detail-label">Aktualne saldo:</span>
                <span class="detail-value"><?php echo number_format($accountData['balance'], 2, ',', ' '); ?> PLN</span>
            </div>
        </div>
    </section>
    
    <section class="transaction-history">
        <h3>Historia transakcji</h3>
        <table class="transactions-table">
            <tr>
                <th>Data</th>
                <th>Opis</th>
                <th>Kwota</th>
                <th>Saldo po operacji</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo $transaction['date']; ?></td>
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