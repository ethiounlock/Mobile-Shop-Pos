<?php
// Include database connection file
include 'database_connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input from form
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $cat = $_POST['login'];

    // Sanitize input to prevent SQL injection
    $user = stripslashes($user);
    $pass = stripslashes($pass);
    $user = mysqli_real_escape_string($conn, $user);
    $pass = mysqli_real_escape_string($conn, $pass);

    // Query database based on category
    if ($cat == 'Employee') {
        $query = mysqli_query($conn, "SELECT * FROM login_tbl WHERE user_name='$user' AND usr_password='$pass' AND category='Employee'");
    } else {
        $query = mysqli_query($conn, "SELECT * FROM login_tbl WHERE user_name='$user' AND usr_password='$pass' AND category='Admin'");
    }

    // Check if query returns a result
    if (mysqli_num_rows($query) == 1) {
        // Redirect to appropriate page based on category
        if ($cat == 'Employee') {
            header("Location: add_product.php");
        } else {
            header("Location: admin/admin_employee.php");
        }
    } else {
        // Redirect to invalid login page
        header("Location: invalid_login.html");
    }

    // Close database connection
    mysqli_close($conn);

}
?>
