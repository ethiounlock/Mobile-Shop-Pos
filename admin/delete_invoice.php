<?php
// Check if id is set in the URL
if (isset($_GET['id'])) {
    include 'database_connection.php';

    // Sanitize the input to prevent SQL injection
    $id = $_GET['id'];
    $id = mysqli_real_escape_string($conn, $id);

    // Get the order items for the given order id
    $query5 = mysqli_query($conn, "SELECT * FROM tbl_order_item WHERE order_id='$id'");
    $rowquery5 = mysqli_num_rows($query5);

    // Prepare the query to fetch the order items
    $query = "SELECT item_name, order_item_quantity FROM tbl_order_item WHERE order_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind the parameter
        $stmt->bind_param("i", $id);

        // Execute the query
        $stmt->execute();

        // Bind the result variables
        $stmt->bind_result($item_name, $order_item_quantity);

        // Loop through the result set
        while ($stmt->fetch()) {
            // Concatenate the item names and quantities to be displayed on the next page
            $names[] = $item_name;
            $qties[] = $order_item_quantity;

            // Update the product quantity
            $name = $item_name;
            $qty = $order_item_quantity;
            $query8 = mysqli_query($conn, "UPDATE product SET p_quantity = p_quantity + $qty WHERE p_name = '$name'");
        }

        // Delete the order items
        foreach ($names as $name) {
            $query6 = mysqli_query($conn, "DELETE FROM tbl_order_item WHERE order_id='$id' AND item_name='$name'");
        }

        // Delete the order
        $query7 = mysqli_query($conn, "DELETE FROM
