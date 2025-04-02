<?php
/**
 * Strona przelewów systemu bankowego
 * 
 * Ten plik zawiera formularz do wykonywania przelewów
 * oraz logikę obsługi przelewów.
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


$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $recipientAccount = $_POST['recipient_account'] ?? '';
    $recipientName = $_POST['recipient_name'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $title = $_POST['title'] ?? '';
    
    // Walidacja danych
    if (empty($recipientAccount) || empty($recipientName) || empty($amount) || empty($title)) {
        $message = 'Wszystkie pola formularza są wymagane';
        $messageType = 'error';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $message = 'Kwota musi być liczbą większą od zera';
        $messageType = 'error';
    } elseif ($amount > $accountData['balance']) {
        $message = 'Niewystarczające środki na koncie';
        $messageType = 'error';
    } else {

        $message = 'Przelew został zrealizowany pomyślnie';
        $messageType = 'success';
        
        
        $accountData['balance'] -= $amount;
    }
}

// Dołączenie nagłówka strony
include 'includes/header.php';
?>

<div class="container">
    <h2>Wykonaj przelew</h2>
    
    <?php if ($message): ?>
    <div class="message <?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>
    
    <div class="account-summary">
        <p><strong>Dostępne środki:</strong> 
            <span class="amount"><?php echo number_format($accountData['balance'], 2, ',', ' '); ?> PLN</span>
        </p>
    </div>
    
    <form action="transfer.php" method="post" class="transfer-form">
        <div class="form-group">
            <label for="recipient_account">Numer konta odbiorcy:</label>
            <input type="text" id="recipient_account" name="recipient_account" required>
        </div>
        
        <div class="form-group">
            <label for="recipient_name">Nazwa odbiorcy:</label>
            <input type="text" id="recipient_name" name="recipient_name" required>
        </div>
        
        <div class="form-group">
            <label for="amount">Kwota:</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="title">Tytuł przelewu:</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn">Wykonaj przelew</button>
            <a href="index.php" class="btn btn-secondary">Anuluj</a>
        </div>
    </form>
</div>

<?php
// Dołączenie stopki strony
include 'includes/footer.php';
?>