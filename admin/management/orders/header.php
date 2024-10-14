<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header-orders-container {
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

        .sidebar.close~.home .header-orders-container {
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

        /* Ensure Home and orders are always visible */
        .breadcrumb-item.active {
            display: inline-block;
        }

        .breadcrumb {
            position: relative;
            left: 60px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="header-orders-container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb my-0" id="breadcrumb-nav">
                    <li class="breadcrumb-item active" id="breadcrumb-home"><a href="../../home.php">Home</a></li>
                    <li class="breadcrumb-item active" id="breadcrumb-orders"><a href="../products.php">Management</a></li>
                    <li class="breadcrumb-item active" id="breadcrumb-orders">Orders</li>
                    <li class="breadcrumb-item active" id="breadcrumb-pending"><span>Pending</span></li>
                    <li class="breadcrumb-item active" id="breadcrumb-processing"><span>Processing</span></li>
                    <li class="breadcrumb-item active" id="breadcrumb-shipped"><span>Shipping</span></li>
                    <li class="breadcrumb-item active" id="breadcrumb-delivered"><span>Delivered</span></li>
                    <li class="breadcrumb-item active" id="breadcrumb-cancelled"><span>Cancelled</span></li>
                    <li class="breadcrumb-item active" id="breadcrumb-scanner"><span>Scan QR Code</span></li>
                </ol>
            </nav>
            <div class="profile-info pt-2 p-1">
                <img src="../../<?= $admin_photo ?>" alt="Profile Photo" class="profile-photo">
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Function to update breadcrumbs visibility based on active section
            function updateBreadcrumbs(activeSection) {
                // List of sections
                const sections = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'scanner'];

                // Hide all section-specific breadcrumbs initially
                sections.forEach(section => {
                    const breadcrumb = document.getElementById(`breadcrumb-${section}`);
                    breadcrumb.style.display = 'none';
                });

                // Show the breadcrumb for the active section
                const activeBreadcrumb = document.getElementById(`breadcrumb-${activeSection}`);
                if (activeBreadcrumb) {
                    activeBreadcrumb.style.display = 'inline-block';
                }
            }

            // Extract the current page's filename (e.g., pending.php) and use it to determine active section
            const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
            updateBreadcrumbs(currentPage);
        });
    </script>
</body>

</html>