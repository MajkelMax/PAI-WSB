<?php
/**
 * Strona przelewów systemu bankowego
 * 
 * Ten plik zawiera formularz do wykonywania przelewów
 * oraz logikę obsługi przelewów, z wykorzystaniem bazy PostgreSQL.
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

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rozpoczęcie transakcji bazodanowej
    $conn = connectToDatabase();
    $conn->beginTransaction();
    
    try {
        $recipientAccount = $_POST['recipient_account'] ?? '';
        $recipientName = $_POST['recipient_name'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $title = $_POST['title'] ?? '';
        
        // Walidacja danych
        if (empty($recipientAccount) || empty($recipientName) || empty($amount) || empty($title)) {
            throw new Exception('Wszystkie pola formularza są wymagane');
        } 
        
        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception('Kwota musi być liczbą większą od zera');
        } 
        
        if ($amount > $accountData['balance']) {
            throw new Exception('Niewystarczające środki na koncie');
        }
        
        // Dane transakcji
        $transactionData = [
            'account_id' => $accountData['account_id'],
            'description' => 'Przelew do: ' . $recipientName,
            'amount' => -$amount, // Ujemna kwota dla przelewu wychodzącego
            'transaction_type' => 'przelew',
            'recipient_account' => $recipientAccount,
            'recipient_name' => $recipientName,
            'title' => $title
        ];
        
        // Dodanie transakcji
        if (!addTransaction($transactionData)) {
            throw new Exception('Błąd podczas dodawania transakcji');
        }
        
        // Aktualizacja danych konta (saldo jest aktualizowane przez trigger)
        $accountData = getAccountByUserId($userId);
        
        // Zatwierdzenie transakcji
        $conn->commit();
        
        $message = 'Przelew został zrealizowany pomyślnie';
        $messageType = 'success';
        
    } catch (Exception $e) {
        // Wycofanie transakcji w przypadku błędu
        $conn->rollBack();
        
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

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
include 'includes/footer.php';
?>