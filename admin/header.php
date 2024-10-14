<?php
include('../connection.php');

// Fetch admin details from the database
$admin_id = $_SESSION['admin_id'];
$query = $conn->prepare("SELECT Username, photo, Full_Name FROM admins WHERE ID = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Set default values in case data is missing
$admin_username = htmlspecialchars($admin['Username'] ?? 'Admin');
$admin_photo = htmlspecialchars($admin['photo'] ?? 'path/to/default/photo.png');
$admin_full_name = htmlspecialchars($admin['Full_Name'] ?? 'Administrator');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/home2.css">
    <style>
        .header-container {
            position: fixed;
            top: 0;
            right: 0;
            height: 50px;
            background-color: #ffffff;
            color: black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1;
            transition: width 0.5s ease;
            width: calc(100% - 200px);
        }

        .sidebar.close~.home .header-container {
            width: calc(100% - 70px);
        }

        .profile-info {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-right: 20px;
        }

        .profile-photo {
            display: flex;
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 5px;
            border: 1px solid #c4c4c4;
        }

        /* Hidden breadcrumbs by default */
        .breadcrumb-item {
            display: none;
        }

        .breadcrumb-item.active {
            display: inline-block;
        }

        .breadcrumb {
            position: relative;
            left: 60px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="header-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb my-0" id="breadcrumb-nav">
                    <li class="breadcrumb-item active" id="breadcrumb-home"><a href="#">Home</a></li>
                    <li class="breadcrumb-item" id="breadcrumb-dashboard"><span>Dashboard</span></li>
                    <li class="breadcrumb-item" id="breadcrumb-inventory"><span>Inventory</span></li>
                    <li class="breadcrumb-item" id="breadcrumb-sales"><span>Sales</span></li>
                </ol>
            </nav>
            <div class="profile-info pt-2 p-1">
                <!-- <button class="nav-link" id="logout-tab" type="button"> -->
                    <img src="<?= $admin_photo ?>" alt="Profile Photo" class="profile-photo">
                <!-- </button> -->
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to update breadcrumbs
            function updateBreadcrumbs(activeTabId) {
                const breadcrumbDashboard = document.getElementById("breadcrumb-dashboard");
                const breadcrumbInventory = document.getElementById("breadcrumb-inventory");
                const breadcrumbSales = document.getElementById("breadcrumb-sales");

                if (activeTabId === "dashboard") {
                    breadcrumbDashboard.classList.add("active");
                    breadcrumbInventory.classList.remove("active");
                    breadcrumbSales.classList.remove("active");
                } else if (activeTabId === "inventory") {
                    breadcrumbDashboard.classList.remove("active");
                    breadcrumbInventory.classList.add("active");
                    breadcrumbSales.classList.remove("active");
                } else if (activeTabId === "sales") {
                    breadcrumbDashboard.classList.remove("active");
                    breadcrumbInventory.classList.remove("active");
                    breadcrumbSales.classList.add("active");
                }
            }

            // Listen for tab changes using Bootstrap's 'shown.bs.tab' event
            const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabs.forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function(event) {
                    const activeTabId = event.target.getAttribute("data-bs-target").substring(1); // Get active tab-pane id
                    updateBreadcrumbs(activeTabId);
                });
            });

            // Set initial breadcrumbs on page load
            const activePane = document.querySelector('.tab-pane.show.active');
            if (activePane) {
                updateBreadcrumbs(activePane.id);
            }
        });
    </script>
</body>

</html>