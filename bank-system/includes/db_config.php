<?php
/**
 * Konfiguracja połączenia z bazą danych PostgreSQL
 * 
 * Ten plik zawiera funkcje do połączenia z bazą danych
 * PostgreSQL oraz funkcje pomocnicze do obsługi zapytań.
 * 
 * @author Michał Grzelka
 * @version 2.0
 */

// Dane do połączenia z bazą danych PostgreSQL
$host = "localhost";
$port = "5432";
$dbname = "postgres";   
$username = "postgres";    
$password = "root"; // Zmień na właściwe hasło    

/**
 * Funkcja do nawiązywania połączenia z bazą danych PostgreSQL
 * 
 * @return PDO|null Obiekt połączenia z bazą danych lub null w przypadku błędu
 */
function connectToDatabase() {
    global $host, $port, $dbname, $username, $password;
    
    try {
        // Utworzenie nowego połączenia PDO do PostgreSQL
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        
        // Ustawienia PDO do zgłaszania błędów
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $conn;
    } catch(PDOException $e) {
        // W przypadku błędu wyświetl komunikat
        error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
        return null;
    }
}

/**
 * Funkcja do wykonywania zapytań SQL
 * 
 * @param string $sql Zapytanie SQL
 * @param array $params Parametry do zapytania
 * @return array|bool Wynik zapytania lub false w przypadku błędu
 */
function executeQuery($sql, $params = []) {
    $conn = connectToDatabase();
    
    if ($conn == null) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        // Sprawdź, czy to zapytanie SELECT
        if (stripos($sql, 'SELECT') === 0) {
            return $stmt->fetchAll();
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Błąd wykonania zapytania: " . $e->getMessage());
        return false;
    }
}

/**
 * Pobiera dane użytkownika na podstawie ID
 * 
 * @param int $userId ID użytkownika
 * @return array|false Dane użytkownika lub false w przypadku błędu
 */
function getUserById($userId) {
    $sql = "SELECT * FROM users WHERE user_id = :user_id";
    return executeQuery($sql, [':user_id' => $userId]);
}

/**
 * Pobiera dane konta na podstawie ID użytkownika
 * 
 * @param int $userId ID użytkownika
 * @return array|false Dane konta lub false w przypadku błędu
 */
function getAccountByUserId($userId) {
    $sql = "SELECT * FROM accounts WHERE user_id = :user_id";
    $accounts = executeQuery($sql, [':user_id' => $userId]);
    
    if (is_array($accounts) && count($accounts) > 0) {
        return $accounts[0]; // Zwraca pierwsze konto
    }
    
    return false;
}

/**
 * Pobiera transakcje dla danego konta
 * 
 * @param int $accountId ID konta
 * @param int $limit Limit liczby transakcji do pobrania (opcjonalnie)
 * @return array|false Lista transakcji lub false w przypadku błędu
 */
function getTransactionsByAccountId($accountId, $limit = null) {
    $sql = "SELECT * FROM transactions WHERE account_id = :account_id ORDER BY date DESC";
    
    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return executeQuery($sql, [':account_id' => $accountId]);
}

/**
 * Dodaje nową transakcję
 * 
 * @param array $transactionData Dane transakcji
 * @return bool True jeśli transakcja została dodana, false w przypadku błędu
 */
function addTransaction($transactionData) {
    $sql = "INSERT INTO transactions (account_id, description, amount, transaction_type, recipient_account, recipient_name, title) 
            VALUES (:account_id, :description, :amount, :transaction_type, :recipient_account, :recipient_name, :title)";
    
    return executeQuery($sql, [
        ':account_id' => $transactionData['account_id'],
        ':description' => $transactionData['description'],
        ':amount' => $transactionData['amount'],
        ':transaction_type' => $transactionData['transaction_type'],
        ':recipient_account' => $transactionData['recipient_account'] ?? null,
        ':recipient_name' => $transactionData['recipient_name'] ?? null,
        ':title' => $transactionData['title'] ?? null
    ]);
}

/**
 * Aktualizuje saldo konta
 * 
 * @param int $accountId ID konta
 * @param float $newBalance Nowe saldo
 * @return bool True jeśli saldo zostało zaktualizowane, false w przypadku błędu
 */
function updateAccountBalance($accountId, $newBalance) {
    $sql = "UPDATE accounts SET balance = :balance WHERE account_id = :account_id";
    
    return executeQuery($sql, [
        ':balance' => $newBalance,
        ':account_id' => $accountId
    ]);
}

// Tymczasowa funkcja do symulacji logowania - w rzeczywistości powinna być obsługa sesji
function getCurrentUserId() {
    // W rzeczywistości powinno być pobierane z sesji
    return 1; // ID przykładowego użytkownika
}