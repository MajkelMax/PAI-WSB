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
// Inicjowanie sesji dla obsługi logowania
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$db_host = "localhost";
$db_port = "5432";
$db_dbname = "postgres";   
$db_username = "postgres";    
$db_password = "root";  


function connectToDatabase() {
    global $db_host, $db_port, $db_dbname, $db_username, $db_password;
    
    try {
        
        $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;
    } catch(PDOException $e) {
        error_log("Błąd połączenia z bazą danych: " . $e->getMessage());
        return null;
    }
}


function executeQuery($sql, $params = []) {
    $conn = connectToDatabase();
    if ($conn == null) {
        return false;
    }
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        
        if (stripos($sql, 'SELECT') === 0) {
            return $stmt->fetchAll();
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Błąd wykonania zapytania: " . $e->getMessage());
        return false;
    }
}


function getUserById($userId) {
    $sql = "SELECT * FROM users WHERE user_id = :user_id";
    return executeQuery($sql, [':user_id' => $userId]);
}


function getAccountByUserId($userId) {
    $sql = "SELECT * FROM accounts WHERE user_id = :user_id";
    $accounts = executeQuery($sql, [':user_id' => $userId]);
    
    if (is_array($accounts) && count($accounts) > 0) {
        return $accounts[0]; // Zwraca pierwsze konto
    }
    
    return false;
}


function getTransactionsByAccountId($accountId, $limit = null) {
    $sql = "SELECT * FROM transactions WHERE account_id = :account_id ORDER BY date DESC";
    
    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    return executeQuery($sql, [':account_id' => $accountId]);
}


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


function updateAccountBalance($accountId, $newBalance) {
    $sql = "UPDATE accounts SET balance = :balance WHERE account_id = :account_id";
    
    return executeQuery($sql, [
        ':balance' => $newBalance,
        ':account_id' => $accountId
    ]);
}


function authenticateUser($email, $password) {
    $sql = "SELECT user_id, password FROM users WHERE email = :email";
    $params = [
        ':email' => $email
    ];
    $user = executeQuery($sql, $params);

    if (is_array($user) && count($user) === 1) {
        $hash = $user[0]['password'];
        if (password_verify($password, $hash) || $hash === $password) {
            return $user[0]['user_id'];
        }
    }

    return false;
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}