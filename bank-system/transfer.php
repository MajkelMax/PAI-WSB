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

require_once 'includes/db_config.php';

$userId = getCurrentUserId();
if (!$userId) {
    header('Location: login.php');
    exit();
}
$userData = getUserById($userId);
$accountData = getAccountByUserId($userId);

if (!$userData || !$accountData) {
    die("Błąd: Nie można pobrać danych użytkownika lub konta");
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = connectToDatabase();
    $conn->beginTransaction();
    
    try {
        $recipientAccount = $_POST['recipient_account'] ?? '';
        $recipientName = $_POST['recipient_name'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $title = $_POST['title'] ?? '';
        
        if (empty($recipientAccount) || empty($recipientName) || empty($amount) || empty($title)) {
            throw new Exception('Wszystkie pola formularza są wymagane');
        } 
        
        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception('Kwota musi być liczbą większą od zera');
        } 
        
        if ($amount > $accountData['balance']) {
            throw new Exception('Niewystarczające środki na koncie');
        }
        
        $transactionData = [
            'account_id' => $accountData['account_id'],
            'description' => 'Przelew do: ' . $recipientName,
            'amount' => -$amount, 
            'transaction_type' => 'przelew',
            'recipient_account' => $recipientAccount,
            'recipient_name' => $recipientName,
            'title' => $title
        ];
        
        if (!addTransaction($transactionData)) {
            throw new Exception('Błąd podczas dodawania transakcji');
        }
        
        $accountData = getAccountByUserId($userId);
        
        $conn->commit();
        
        $message = 'Przelew został zrealizowany pomyślnie';
        $messageType = 'success';
        
    } catch (Exception $e) {
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
                <span class="amount" id="currentBalance" data-balance="<?php echo $accountData['balance']; ?>">
                    <?php echo number_format($accountData['balance'], 2, ',', ' '); ?> PLN
                </span>
            </p>
        </div>
    
    <form action="transfer.php" method="post" class="transfer-form">
        <div class="form-group">
            <label for="recipient_account">Numer konta odbiorcy:</label>
            <input type="text" id="recipient_account" name="recipient_account" required pattern="[0-9]{26}" maxlength="26">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.transfer-form');
    const balance = parseFloat(document.getElementById('currentBalance').dataset.balance);

    form.addEventListener('submit', function(e) {
        const account = document.getElementById('recipient_account').value.trim();
        const name = document.getElementById('recipient_name').value.trim();
        const amount = parseFloat(document.getElementById('amount').value);
        let errors = [];

        if (!/^[0-9]{26}$/.test(account)) {
            errors.push('Numer konta musi mieć dokładnie 26 cyfr.');
        }

        if (name === '') {
            errors.push('Nazwa odbiorcy jest wymagana.');
        }

        if (isNaN(amount) || amount <= 0) {
            errors.push('Kwota musi być większa od zera.');
        } else if (amount > balance) {
            errors.push('Kwota nie może przekraczać dostępnego salda.');
        }

        if (errors.length) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
});
</script>
<?php
include 'includes/footer.php';
?>