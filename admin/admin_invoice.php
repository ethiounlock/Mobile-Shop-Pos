<?php
// invoice.php

// Include the database connection file
require_once 'database_connection.php';

// Prepare a statement to fetch all orders
$statement = $connect->prepare("SELECT * FROM tbl_order ORDER BY order_id DESC");
$statement->execute();
$all_result = $statement->fetchAll();
$total_rows = $statement->rowCount();

// Check if the form is submitted
if (isset($_POST['create_invoice'])) {
    // Initialize the order totals to 0
    $order_total_before_tax = 0;
    $order_total_tax1 = 
