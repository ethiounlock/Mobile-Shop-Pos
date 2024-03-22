<?php
// database_connection.php

try {
    $connect = new PDO('mysql:host=localhost;dbname=project1;charset=utf8', 'root', '123456789');
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

?>
