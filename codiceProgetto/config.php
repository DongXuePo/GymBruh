<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// questo serve a farmi vedere gli errori se ci sono (così evito di vedere tutta la pagina error)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$db_host = 'localhost';  
$db_name = 'gymbruh';    
$db_user = 'root';      
$db_pass = '';          


try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    
    
    // mi stampa a schermo eventuali valori (se non faccio questa cosa, non mi stampa niente )
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ti fa automaticamente la fetch e restituisce array subito
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Errore di connessione al Database: " . $e->getMessage());
}

//indirizzo base sito
define('BASE_URL', value: 'http://localhost/ProgettoGymBruh/');
define('ROOT_PATH', value: __DIR__. '/');
?>