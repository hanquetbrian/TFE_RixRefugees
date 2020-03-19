<?php
require_once '../config.php';

try {
    $dbh = new PDO($config['db.dsn'], $config['db.user'], $config['db.password']);

} catch (PDOException $e) {
    error_log("Connexion échouée : " . $e->getMessage());
}