<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="sidebar-orders1.css">
    <title>Sidebar</title>
    <style>
        .pending {
            color: #FFC107;
        }

        .processing {
            color: #17A2B8;
            animation: spin 1s infinite linear;
        }

        .shipped {
            color: #007BFF;
        }

        .delivered {
            color: #28A745;
        }

        .cancelled {
            color: #DC3545;
        }

        .scan-qr {
            color: #6C757D;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="../../../img/MAPARCO.png" alt="logo" width="40px">
                </span>
                <div class="header-text text">
                    <span class="name">MAPARCO</span>
                    <p class="smol">Online <i class="fa-solid fa-circle online-icon"></i></p>
                </div>
            </div>
            <i class='fa-solid fa-bars toggle'></i>
        </header>

        <div class="menu-bar mt-5 pt-4">
            <div class="menu">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="../../home.php" class="nav-link-item" id="dashboard-link">
                            <i class='fas fa-tachometer-alt icon'></i>
                            <span class="nav-text text">Dashboard</span>
                        </a>
                    </li>
                </ul>
                <!-- ===== Management ====== -->
                <div class="header title">
                    <strong>Orders</strong>
                </div>
                <table>
                    <ul class="menu-links">
                        <li class="nav-link">
                            <a href="pending.php" class="nav-link-item" id="pending-link">
                                <i class="fas fa-hourglass-start icon pending"></i>
                                <span class="nav-text text">Pending</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="processing.php" class="nav-link-item" id="processing-link">
                                <i class="fas fa-sync icon processing"></i>
                                <span class="nav-text text">Processing</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="shipped.php" class="nav-link-item" id="shipped-link">
                                <i class="fas fa-shipping-fast icon shipped"></i>
                                <span class="nav-text text">Shipped</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="delivered.php" class="nav-link-item" id="delivered-link">
                                <i class="fas fa-check-circle icon delivered"></i>
                                <span class="nav-text text">Delivered</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="cancelled.php" class="nav-link-item" id="cancelled-link">
                                <i class="fas fa-times-circle icon cancelled"></i>
                                <span class="nav-text text">Cancelled</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a href="scanner.php" class="nav-link-item" id="scanner-link">
                                <i class="fas fa-qrcode icon scan-qr"></i>
                                <span class="nav-text text">Scan QR Code</span>
                            </a>
                        </li>
                    </ul>
                </table>
            </div>
        </div>

    </nav>
    <!-- <section class="home">
        <div class="text">Dashbaord</div>
    </section> -->

    <script>
        function toggleCollapse(id) {
            var collapseElement = document.getElementById(id);
            var isCollapsed = collapseElement.classList.toggle("show");
            var height = isCollapsed ? collapseElement.scrollHeight : 0;
            collapseElement.style.maxHeight = (isCollapsed ? height + "px" : 0);

            var ordersIcon = document.getElementById("orders-icon");
            ordersIcon.classList.toggle("rotate");
        }
    </script>

    <script>
        // Check if the sidebar was previously open
        const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
        const sidebar = document.querySelector(".sidebar");
        const collapseIcon = document.querySelector(".collapse-icon");

        // Set the initial state of the sidebar
        if (isSidebarOpen) {
            sidebar.classList.remove("close");
        }

        const toggle = document.querySelector(".toggle");
        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");

            // Toggle collapse icon opacity based on sidebar state
            collapseIcon.classList.toggle("collapsed");

            // Save the state of the sidebar to local storage
            localStorage.setItem('sidebarOpen', !sidebar.classList.contains("close"));
        });

        // Get the current page URL
        var currentPageUrl = window.location.href;

        // Get all navigation links
        var navLinks = document.querySelectorAll('.nav-link-item');

        // Loop through each navigation link
        navLinks.forEach(function(navLink) {
            // Check if the link's href matches the current page URL
            if (navLink.getAttribute('href') === currentPageUrl) {
                // Add the 'active' class to the link
                navLink.classList.add('active');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var currentPageUrl = window.location.href;
            var links = document.querySelectorAll('.menu-bar a');
            links.forEach(function(link) {
                if (link.href === currentPageUrl) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>