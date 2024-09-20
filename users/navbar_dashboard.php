<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        /* Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        /* Body Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Navigation Bar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #15BE2F;
            color: white;
            padding: 0px 5px;
            width: 100%;
            z-index: 1000;
            border-bottom: solid green 1px;
        }

        .navbar-nav .nav-link {
            font-size: 12px;
            color: white;
            transition: all 0.3s ease;
            height: 100%;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: #f8f9fa;
            /* Change color on hover */
            background-color: rgba(255, 255, 255, 0.2);
            /* Change background color on hover */
        }

        .navbar-nav .nav-link.active {
            font-weight: bold;
            /* Bold font weight for active link */
        }

        #offcanvasNavbar {
            /* padding: 0 8%; */
        }

        /* Dropdown Menu Styles */
        .dropdown-menu {
            min-width: auto;
            width: 180px;
            /* Make the dropdown menu responsive */
        }

        .dropdown-menu li a {
            color: #555;
            padding: 8px 10px;
        }

        .dropdown-menu li a:hover{
            background-color: dodgerblue;
            color: #f8f9fa;
        }

        .dropdown-menu.dropdown-menu-end {
            right: 0;
            left: auto;
        }

        .offcanvas {
            background-color: #15BE2F;
            max-width: 80%;
            /* Adjust max-width as needed */
        }

        /* Add hover effect inside offcanvas menu */
        .offcanvas.offcanvas-end.show .offcanvas-body a.nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Add active effect inside offcanvas menu */
        .offcanvas.offcanvas-end.show .offcanvas-body a.nav-link.active {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }

        a img.logo{
                width: 50px;
            }

        /* Adjustments for Responsive Offcanvas */
        @media (max-width: 767.98px) {
            .nav-item.dropdown {
                position: static;
                display: block;
                margin-top: 10px;
            }

            .dropdown-menu {
                position: static;
                float: none;
                width: auto;
                max-height: 200px;
                /* Adjust max-height for scrollability */
                overflow-y: auto;
                /* Enable vertical scrolling */
            }

            a img.logo{
                width: 40px;
            }
            
            .dropdown-menu li a {
            color: #555;
            padding: 8px 10px;
            font-size:12px;
        }
            
            .vr {
                display: none;
            }
            
        }

        /* Positioning the dropdown menu to the right */
        @media (min-width: 768px) {
            .nav-item.dropdown {
                position: absolute;
                right: 0;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-light" href="#"><img src="../img/MAPARCO.png" alt="MAPARCO Logo" class="logo">MAPARCO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon btn btn-sm"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <a class="navbar-brand fw-bold text-light" href="#"><img src="../img/MAPARCO.png" alt="MAPARCO Logo" class="logo" width="50px">MAPARCO</a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <div class="vr text-light"></div>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="dashboard.php"><i class="fa-solid fa-house"></i> Home</a>
                        </li>
                        <div class="vr text-light"></div>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="view_cart.php"><i class="fa-solid fa-cart-shopping"></i> Shopping Cart</a>
                        </li>
                        <div class="vr text-light"></div>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="purchase.php"><i class="fa-solid fa-credit-card"></i> Purchase</a>
                        </li>
                        <div class="vr text-light"></div>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="review_product.php"><i class="fa-solid fa-star"></i> To Review</a>
                        </li>
                        <div class="vr text-light"></div>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="history.php"><i class="fa-solid fa-clock-rotate-left"></i> History</a>
                        </li>
                        <li>
                            <form class="d-flex" role="search" action="search.php" method="GET">
                                <div class="input-group">
                                    <input class="form-control" type="search" name="query" placeholder="Search" aria-label="Search">
                                    <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </form>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle bg-success p-2 rounded" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-gear"></i>    
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2">
                                <li><a href="profile.php" class="dropdown-item"><i class="fa-solid fa-user"></i> Profile</a></li>
                                <li><a href="address.php" class="dropdown-item"><i class="fa-solid fa-location-dot"></i> Address</a></li>
                                <li><a href="purchase.php" class="dropdown-item"><i class="fa-solid fa-credit-card"></i> Purchase</a></li>
                                <!--<li> <a href="#" class="dropdown-item"><i class="fa-solid fa-gear"></i> Settings</a></li>-->
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a href="../logout.php" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>