<!DOCTYPE html>
<html lang="eng">
<head>
    <!-- meta, link, title tags and stylesheet links here -->
</head>
<body>
    <!-- Header Section Begin -->
    <header class="header-section">
        <!-- header content here -->
    </header>
    <!-- Header End -->

    <!-- View section -->
    <section>
        <div>
            <br>
            <h2 align="center"> <b>
                    <u>LIST OF PRODUCTS</u></b></h2><br><br>
        </div>

        <table border='2' cellpadding='5' align='center'>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Brand Name</th>
                <th>Product Quantity</th>
                <th>Old unit price</th>
                <th>New unit price</th>
                <th>action</th>
            </tr>
            <?php
            require_once 'config.php';
            $query = mysqli_query($conn, "SELECT * FROM product");
            while ($row = mysqli_fetch_array($query)) {
                echo "<tr>";
                echo "<td>" . $row['p_id'] . "</td>";
                echo "<td>" . $row['p_name'] . "</td>";
                echo "<td>" . $row['b_name'] . "</td>";
                echo "<td>" . $row['p_quantity'] . "</td>";
                echo "<td>" . $row['old_p_unit_price'] . "</td>";
                echo "<td>" . $row['new_p_unit_price'] . "</td>";
                echo "<td><form method='post' action='buy.php'><input type='hidden' name='product_id' value='" . $row['p_id'] . "'><input type='submit' name='buysell' value='buy'></form></td>";
                echo "</tr>";
            }
            mysqli_close($conn);
            ?>
        </table>
    </section>
    <!-- Deal Of The Week Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/jquery.countdown.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/jquery.zoom.min.js"></script>
    <script src="js/jquery.dd.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>


<?php
$conn = mysqli_connect('localhost', 'root', '', 'project');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>


<?php
require_once 'config.php';
$product_id = $_POST['product_id'];
// Perform the buy action here
header('Location: index.html');
exit;
?>
