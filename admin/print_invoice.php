<?php
//print_invoice.php

require_once 'pdf.php';
include('database_connection.php');

// Check if required GET parameters are set
if(!isset($_GET["pdf"]) || !isset($_GET["id"])) {
    die('Required parameters not set.');
}

try {
    // Prepare the SQL query using a JOIN between tbl_order and tbl_order_item tables
    $statement = $connect->prepare("SELECT o.*, oi.* FROM tbl_order o JOIN tbl_order_item oi ON o.order_id = oi.order_id WHERE o.order_id = :order_id LIMIT 1");

    // Execute the statement with the given order_id
    $statement->execute(array(':order_id' => $_GET["id"]));

    // Fetch the result set
    $result = $statement->fetchAll();

    // Check if the result set is not empty
    if(empty($result)) {
        die('No order found with the given order_id.');
    }

    // Initialize $output and $count variables
    $output = '';
    $count = 0;

    // Loop through the result set
    foreach($result as $row)
    {
        // Generate the invoice HTML
        $output .= '...'; // Same as the original code

        // Increment the $count variable
        $count++;
    }

    // Create a new Pdf object
    $pdf = new Pdf();

    // Set the file name
    $file_name = 'Invoice-'.$row["order_no"].'.pdf';

    // Load the HTML into the Pdf object
    $pdf->loadHtml($output);

    // Render the PDF
    $pdf->render();

    // Stream the PDF to the browser
    $pdf->stream($file_name, array("Attachment" => false));

    // Exit to prevent any further output
    exit;
} catch(PDOException $e) {
    // Handle any database errors
    die('Database error: ' . $e->getMessage());
} catch(Exception $e) {
    // Handle any other errors
    die('Application error: ' . $e->getMessage());
}
?>
