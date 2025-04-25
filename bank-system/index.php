<?php
/**
 * Strona główna systemu bankowego
 * 
 * Ten plik zawiera podstawową strukturę strony głównej
 * systemu bankowego, który będzie rozwijany w ramach projektu.
 * 
 * @author Michał Grzelka
 * @version 1.0
 */

// Symulacja danych użytkownika (w przyszłości będą pobierane z bazy danych)
$accountData = [
    'name' => 'Jan Kowalski',
    'account_number' => '12 3456 7890 1234 5678 9012 3456',
    'balance' => 1500.75
];


$transactions = [
    [
        'date' => '2024-04-01',
        'description' => 'Wypłata wynagrodzenia',
        'amount' => 3000.00,
    ],
    [
        'date' => '2025-03-28',
        'description' => 'Zakupy spożywcze',
        'amount' => -125.60,
    ],
    [
        'date' => '2025-03-25',
        'description' => 'Opłata za mieszkanie',
        'amount' => -1200.00,
    ]
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
            <p><strong>Właściciel:</strong> <?php echo $accountData['name']; ?></p>
            <p><strong>Numer konta:</strong> <?php echo $accountData['account_number']; ?></p>
            <p class="balance"><strong>Saldo:</strong> 
                <span class="amount"><?php echo number_format($accountData['balance'], 2, ',', ' '); ?> PLN</span>
            </p>
        </div>
        <div class="account-actions">
            <a href="transfer.php" class="btn">Wykonaj przelew</a>
            <a href="account.php" class="btn">Szczegóły konta</a>
        </div>
    </section>
    
    <section class="recent-transactions">
        <h3>Ostatnie transakcje</h3>
        <table class="transactions-table">
            <tr>
                <th>Data</th>
                <th>Opis</th>
                <th>Kwota</th>
            </tr>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo $transaction['date']; ?></td>
                <td><?php echo $transaction['description']; ?></td>
                <td class="<?php echo $transaction['amount'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo number_format($transaction['amount'], 2, ',', ' '); ?> PLN
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </section>
</div>

<?php
// Dołączenie stopki strony
include 'includes/footer.php';
?>