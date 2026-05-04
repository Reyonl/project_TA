<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=percobaan_1', 'root', '');
$stmt = $pdo->query('SELECT * FROM produks');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
