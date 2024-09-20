<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="side1bar.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
            integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Sidebar</title>
</head>

<body>
    <nav class="sidebar close">
        <header>
            <hr>
            <div class="image-text">
                <span class="image">
                    <img src="../MAPARCO.png" alt="logo">
                </span>
                <div class="header-text text">
                    <span class="name">MAPARCO</span>
                    <span class="profession text ">admin</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
            <hr>
        </header>

        <div class="menu-bar">
            <div class="menu">
                <!-- ===== Management ====== -->
                <div class="header title">
                    <strong>Payroll</strong>
                </div>
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="index.php?page=home">
                            <i class='bx bxs-home icon'></i>
                            <span class="nav-text text">Home</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=attendance">
                            <i class='bx bxl-product-hunt icon'></i>
                            <span class="nav-text text">Attendance</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=employee">
                            <i class="fa-solid fa-users icon"></i>
                            <span class="nav-text text">Employee List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=department">
                            <i class='bx bxs-bar-chart-alt-2 icon'></i>
                            <span class="nav-text text">Department List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=position">
                            <i class='bx bx-line-chart icon'></i>
                            <span class="nav-text text">Position List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=allowances">
                            <i class='bx bx-line-chart icon'></i>
                            <span class="nav-text text">Allowance List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=deductions">
                            <i class='bx bx-line-chart icon'></i>
                            <span class="nav-text text">Deduction List</span>
                        </a>
                    </li>
                </ul>
                <!-- ===== settings ===== -->
                <br>
                <div class="header title">
                    <strong>Settings</strong>
                </div>
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="index.php?page=users">
                            <i class='bx bxs-user-circle icon'></i>
                            <span class="nav-text text">Users</span>
                        </a>
                    </li>
    
                </ul>
            </div>

        </div>
    </nav>
    <!-- <section class="home">
        <div class="text">Dashbaord</div>
    </section> -->

    <script>
        // Check if the sidebar was previously open
        const isSidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
        const sidebar = document.querySelector(".sidebar");

        // Set the initial state of the sidebar
        if (isSidebarOpen) {
            sidebar.classList.remove("close");
        }

        const toggle = document.querySelector(".toggle");
        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");

            // Save the state of the sidebar to local storage
            localStorage.setItem('sidebarOpen', !sidebar.classList.contains("close"));
        });
    </script>
</body>
<script>
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')

</script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3347241354173382"
     crossorigin="anonymous"></script>
</html>
