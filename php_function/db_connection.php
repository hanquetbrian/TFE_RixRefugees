<?php
require_once '/var/html/config.php';

$dbh;
try {
    $dbh = new PDO($config['db.dsn'], $config['db.user'], $config['db.password']);

} catch (PDOException $e) {
    error_log("Connexion failed : " . $e->getMessage());
    echo "failed to connect : " . $e->getMessage();
}