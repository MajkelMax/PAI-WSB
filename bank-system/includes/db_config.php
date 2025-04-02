<?php
/**
 * Konfiguracja połączenia z bazą danych
 * 
 * Ten plik będzie wykorzystywany w przyszłości do połączenia
 * z bazą danych MySQL.
 * 
 * @author Michał Grzelka
 * @version 1.0
 */

// Dane do połączenia z bazą danych
$host = "localhost";
$dbname = "bank_db";   
$username = "root";    
$password = "";         

/**
 * Funkcja do nawiązywania połączenia z bazą danych
 * 
 * @return PDO|null Obiekt połączenia z bazą danych lub null w przypadku błędu
 */
function connectToDatabase() {
    global $host, $dbname, $username, $password;
    
    try {
        // Utworzenie nowego połączenia PDO
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        
        // Ustawienia PDO do zgłaszania błędów
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conn;
    } catch(PDOException $e) {
        // W przypadku błędu wyświetl komunikat
        echo "Błąd połączenia z bazą danych: " . $e->getMessage();
        return null;
    }
}

/**
 * Przykład funkcji do wykonywania zapytań SQL
 * 
 * @param string $sql Zapytanie SQL
 * @param array $params Parametry do zapytania
 * @return array|false Wynik zapytania lub false w przypadku błędu
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
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return true;
    } catch(PDOException $e) {
        echo "Błąd wykonania zapytania: " . $e->getMessage();
        return false;
    }
}

// Na ten moment plik zawiera tylko definicje funkcji
// W przyszłości zostanie rozbudowany o faktyczne połączenie z bazą
// i funkcje do obsługi danych bankowych