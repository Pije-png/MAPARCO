<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebar copy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Sidebar</title>
</head>

<body>
    <nav class="sidebar close scrollspy">
        <header class="mt-3">
            <div class="image-text">
                <span class="image">
                    <img src="../../img/MAPARCO.png" alt="logo">
                </span>
                <div class="header-text text">
                    <span class="name">MAPARCO</span>
                    <small>Online <i class="fa-solid fa-circle online-icon"></i></small>
                </div>
            </div>
            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar mt-4">
            <div class="menu">
                <!-- ===== Management ====== -->
                <div class="header title">
                    <strong>Management</strong>
                </div>
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="../home.php" class="nav-link-item" id="dashboard-link">
                            <i class='bx bxs-home icon'></i>
                            <span class="nav-text text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./products.php" class="nav-link-item" id="products-link">
                            <i class='fa-solid fa-boxes icon'></i>
                            <span class="nav-text text">Products</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./customers.php" class="nav-link-item" id="customers-link">
                            <i class="fa-solid fa-users icon"></i>
                            <span class="nav-text text">Customers</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./sales.php" class="nav-link-item" id="sales-link">
                            <i class="fa-solid fa-chart-line icon"></i>
                            <span class="nav-text text">Sales</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#" class="nav-link-item" id="orders-link" onclick="toggleCollapse('orders-status')">
                            <i class='bx bxs-bar-chart-alt-2 icon'></i>
                            <span class="nav-text text">Orders</span>
                            <i class="fa-solid fa-angle-down offset-md-4 collapse-icon" id="orders-icon"></i>
                        </a>
                    </li>
                    <!-- Collapsible section for orders status -->
                    <div id="orders-status" class="collapse bg-dark">
                        <ul class="submenu">
                            <li><a href="./orders/pending.php">Pending</a></li>
                            <li><a href="./orders/processing.php">Processing</a></li>
                            <li><a href="./orders/shipped.php">Shipped</a></li>
                            <li><a href="./orders/delivered.php">Delivered</a></li>
                            <li><a href="./orders/scanner.php">Scan QR Code</a></li>
                            <li><a href="./orders/cancelled.php">Cancelled</a></li>
                        </ul>
                    </div>
                    <li class="nav-link">
                        <a href="./inventory.php" class="nav-link-item" id="inventory-link">
                            <i class='fa-solid fa-warehouse icon'></i>
                            <span class="nav-text text">Inventory</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./reviews.php" class="nav-link-item" id="reviews-link">
                            <i class='fa-solid fa-star icon'></i>
                            <span class="nav-text text">Reviews</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./topsales.php" class="nav-link-item" id="top-selling-link">
                            <i class='fa-solid fa-trophy icon'></i>
                            <span class="nav-text text">Top Selling</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="./history.php" class="nav-link-item" id="history-link">
                            <i class='fa-solid fa-history icon'></i>
                            <span class="nav-text text">History</span>
                        </a>
                    </li>
                </ul>
                <div class="header title">
                    <strong>Settings</strong>
                </div>
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="#" class="nav-link-item" id="profile-link">
                            <i class='bx bxs-user-circle icon'></i>
                            <span class="nav-text text">Profile</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="#" class="nav-link-item" id="shipping-options-link">
                            <i class="fa-solid fa-truck-fast icon"></i>
                            <span class="nav-text text">Shipping Options</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-content">
                <li class="nav-link">
                    <a href="../../logout.php">
                        <i class='bx bx-log-out icon'></i>
                        <span class="nav-text text">Logout</span>
                    </a>
                </li>
            </div>
        </div>
    </nav>
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