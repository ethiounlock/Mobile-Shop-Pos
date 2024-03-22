<!DOCTYPE html>
<html lang="eng">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Fashi Template">
    <meta name="keywords" content="Fashi, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mobile shop management system</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/themify-icons.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">



<script>
  function sum()
  {
    var price,total,quantity;
    price = parseFloat(document.add_product.purchasing_price.value);
    quantity = parseFloat(document.add_product.product_quantity.value);
    total = price * quantity;
    document.add_product.total_price.value = total.toFixed(2);
  }
</script>

</head>

<body style="background-color:#d9d9d9">
    <!-- Page Preloder -->


    <!-- Header Section Begin -->
    <header class="header-section">

        <div class="container">
            <div class="inner-header">
                <div class="row">
                    <div class="col-lg-2 col-md-2">
						<!-- mobile shop logo -->
                        


                </div>
            </div>
        </div>
		<!-- menu section begins -->
        <div class="nav-item">
            <div class="container">
                <div class="nav-depart">

                </div>
                <nav class="nav-menu mobile-menu">
                    <ul>
                        <li class="active"></li>


                                <li><a href="./add_product.php">Add Product</a></li>
                                <li><a href="./view_product.php">View Product</a></li>
                                <li><a href="./view_purchase.php">Purchase History</a></li>
                        <li><a href="./invoice.php">Sale</a></li>
                        <li><a href="#">Report</a>
                          <ul class="dropdown">
                            <li><a href="./sales_report.php">Sales Report</a></li>
                            <li><a href="./purchase_report.php">Purchase Report</a></li>
                              </ul>
                                
                                


						</li>
						<li><a href="#">Profile</a>
              <ul class="dropdown">
                    <li><a href="./change_username.php">Change username</a></li>
                    <li><a href="./change_password.php">Change password</a></li>
                    <li><a href="./logout.php">Log out</a></li>
                      </ul>
                      </li>




                </nav>
			</div>
		</div>
                <div id="mobile-menu-wrap"></div>


    </header>
    <!-- Header End -->



    <!-- Deal Of The Week Section Begin-->

    <style>
      @font-face {
        font-family: OpenSans-Regular;
        src: url('../fonts/OpenSans/OpenSans-Regular.ttf'); 
      }
      .header
      { 
         width: 94.75%;
          height:10%;
          font-family: OpenSans-Regular;
          
          color:#fff;
          background-color:#00264d;
          border:2px solid #3399ff;
          padding:2.5%;
      }
      #form {
           text-align:center;
           margin-left:20%;
           margin-right:20%;
           margin-top:5%;
           font-family: OpenSans-Regular, sans-serif;
           padding:5%;
      
           width:
