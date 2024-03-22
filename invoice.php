<?php
// invoice.php

// Include database connection file
require_once 'database_connection.php';

// Prepare SELECT query
$statement = $connect->prepare("SELECT * FROM tbl_order ORDER BY order_id DESC");
$statement->execute();
$all_result = $statement->fetchAll();
$total_rows = $statement->rowCount();

// Define a function to calculate the total amount of an order item
function calculateTotal($quantity, $price, $tax1_rate, $tax1_amount, $tax2_rate, $tax2_amount, $tax3_rate, $tax3_amount) {
  $item_total = $quantity * $price;
  $item_total += $tax1_amount + $tax2_amount + $tax3_amount;
  return $item_total;
}

// Handle form submission
if (isset($_POST['create_invoice'])) {
  // Initialize order totals
  $order_total_before_tax = 0;
  $order_total_tax1 = 0;
  $order_total_tax2 = 0;
  $order_total_tax3 = 0;
  $order_total_tax = 0;
  $order_total_after_tax = 0;

  // Prepare INSERT query
  $statement = $connect->prepare("INSERT INTO tbl_order (order_no, order_date, order_receiver_name, order_receiver_address, order_total_before_tax, order_total_tax1, order_total_tax2, order_total_tax3, order_total_tax, order_total_after_tax, order_datetime) VALUES (:order_no, :order_date, :order_receiver_name, :order_receiver_address, :order_total_before_tax, :order_total_tax1, :order_total_tax2, :order_total_tax3, :order_total_tax, :order_total_after_tax, :order_datetime)");
  $statement->execute(array(
    ':order_no' => $_POST['order_no'],
    ':order_date' => $_POST['order_date'],
    ':order_receiver_name' => $_POST['order_receiver_name'],
    ':order_receiver_address' => $_POST['order_receiver_address'],
    ':order_total_before_tax' => $order_total_before_tax,
    ':order_total_tax1' => $order_total_tax1,
    ':order_total_tax2' => $order_total_tax2,
    ':order_total_tax3' => $order_total_tax3,
    ':order_total_tax' => $order_total_tax,
    ':order_total_after_tax' => $order_total_after_tax,
    ':order_datetime' => date("Y-m-d")
  ));

  // Get the last inserted order ID
  $order_id = $connect->lastInsertId();

  // Prepare INSERT query for order items
  $statement = $connect->prepare("INSERT INTO tbl_order_item (order_id, item_name, order_item_quantity, order_item_price, order_item_actual_amount, order_item_tax1_rate, order_item_tax1_amount, order_item_tax2_rate, order_item_tax2_amount, order_item_tax3_rate, order_item_tax3_amount, order_item_final_amount, order_date) VALUES (:order_id, :item_name, :order_item_quantity, :order_item_price, :order_item_actual_amount, :order_item_tax1_rate, :order_item_tax1_amount, :order_item_tax2_rate, :order_item_tax2_amount, :order_item_tax3_rate, :order_item_tax3_amount, :order_item_final_amount, :order_date)");

  // Loop through the posted order items
  for ($count = 0; $count < $_POST['total_item']; $count++) {
    // Calculate the order item totals
    $order_item_actual_amount = floatval(trim($_POST["order_item_actual_amount"][$count]));
    $order_item_tax1_amount = floatval(trim($_POST["order_item_tax1_amount"][$count]));
    $order_item_tax2_amount = floatval(trim($_POST["order_item_tax2_amount"][$count]));
    $order_item_tax3_amount = floatval(trim($_POST["order_item_tax3_amount"][$count]));
    $order_item_final_amount = floatval(trim($_POST["order_item_final_amount"][$count]));

    // Update the order totals
    $order_total_before_tax += $order_item_actual_amount;
    $order_total_tax1 += $order_item_tax1_amount;
    $order_total_tax2 += $order_item_tax2_amount;
    $order_total_tax3 += $order_item_tax3_amount;
    $order_total_after_tax += $order_item_final_amount;

    // Calculate the order item tax rates
    $order_item_tax1_rate = floatval(trim($_POST["order_item_tax1_rate"][$count])) / 100;
    $order_item_tax2_rate = floatval(trim($_POST["order_item_tax2_rate"][$count])) / 100;
    $order_item_tax3_rate = floatval(trim($_POST["order_item_tax3_rate"][$count])) / 100;

    // Execute the INSERT query for the current order item
    $statement->execute(array(
      ':order_id' => $order_id,
      ':item_name' => trim($_POST["item_name"][$count]),
      ':order_item_quantity' => trim($_POST["order_item_quantity"]
