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
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

* {
    font-family: "Poppins", sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* :root {
    --body-color: #f4f4f4;
    --sidebar-color: #fff;
    --primary-color: #695CFE;
    --primary--color-light: #F6F5FF;
    --toggle-color: #DDD;
    --text-color: #707070;

    --tran-02: all 0.2s ease;
    --tran-03: all 0.3s ease;
    --tran-04: all 0.4s ease;
    --tran-05: all 0.5s ease;
} */

:root {
    --body-color: #f4f4f4;
    --sidebar-color: #222d32;
    --primary-color: green;
    --primary--color-light: #F6F5FF;
    --toggle-color: #DDD;
    --text-color: #fff;
    --text-color-hover: #212121;

    --tran-02: all 0.2s ease;
    --tran-03: all 0.3s ease;
    --tran-04: all 0.4s ease;
    --tran-05: all 0.5s ease;
}

body {
    height: 100vh;
    background-color: var(--body-color);
    transition: var(--tran-05);
}

/* ====reusable CSS==== */
.sidebar-text {
    font-size: 16px;
    font-weight: 500;
    color: var(--text-color);
    transition: var(--tran-04);
    white-space: nowrap;
    opacity: 1;
}

.sidebar .image {
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar.close .text,
.sidebar.close .header.title {
    opacity: 0;
}

.sidebar.close header .toggle {
    transform: translateY(-50%);
}

/* ====sidebar==== */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 250px;
    padding: 10px 14px;
    background-color: var(--sidebar-color);
    transition: var(--tran-05);
    z-index: 100;
}

.sidebar .title {
    width: 100%;
    padding: 4px 15px;
    color: lightgreen;
    /* background-color: lightgreen; */
    border-radius: 20px;
}

.sidebar .menu {
    margin-top: 35px;
}

.sidebar.close {
    width: 88px;
}

.sidebar li {
    height: 37px;
    margin-top: 5px;
    list-style: none;
    display: flex;
    align-items: center;
    font-size: 12px;
}

.sidebar .icon {
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar li .icon {
    min-width: 60px;
    /* font-size: 20px; */
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar li .icon,
.sidebar li .text {
    color: var(--text-color);
    transition: var(--tran-02);
}

.sidebar header {
    position: relative;
}

.sidebar .image-text img {
    width: 40px;
    border-radius: 6px
}

.sidebar header .image-text {
    display: flex;
    align-items: center;
    color: lightgreen;
}

header .image-text .header-text {
    display: flex;
    flex-direction: column;
}

.header-text .name {
    font-weight: bold;
}

.header-text .profession {
    margin-top: -2px;
    font-size: 10px;
}

.sidebar header .toggle {
    position: absolute;
    top: 50%;
    right: -25px;
    transform: translateY(-50%) rotate(180deg);
    height: 30px;
    width: 30px;
    background: lightgreen;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--sidebar-color);
    font-size: 22px;
    transition: translateY(-50%);
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.9);
    transition: all 0.1s ease-in-out;
}

.sidebar header .toggle:hover {
    background-color: #008000;
    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.9);
    transition: all 0.1s ease-in-out;
}

.sidebar li a {
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.sidebar .menu-bar .menu li a:hover {
    background: var(--text-color-hover);
    font-weight: bold;
    font-size: 13px;
}

.sidebar .bottom-content li a:hover {
    background-color: lightcoral;
    color: #000;
}

/* .sidebar .menu-bar .menu li a:hover .icon, */
.sidebar .menu-bar .menu li a:hover .text

/* .sidebar .bottom-content li a:hover .icon */
    {
    color: lightgreen;
}

.sidebar .menu-bar {
    height: calc(100% - 50px);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* ====== */
.sidebar.close~.home {
    left: 88px;
    width: calc(100% - 88px);
}

.sidebar.close~.nav-home {
    left: 88px;
    width: calc(100% - 88px);
}

/* ===== Dashboard===== */
.home {
    position: relative;
    height: 100vh;
    left: 250px;
    width: calc(100% - 250px);
    transition: var(--tran-05);
    background: var(--body-color);
}

.nav-home {
    position: relative;
    left: 250px;
    width: calc(100% - 250px);
    transition: var(--tran-05);
    background: var(--body-color);
}

.home .text {
    font-size: 30px;
    font-weight: 500;
    color: var(--text-color);
    padding: 8px 14px;
}
</style>
<body>
    <nav class="sidebar close">
        <header>
            <hr>
            <div class="image-text">
                <span class="image">
                    <img src="MAPARCO.png" alt="logo">
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
                        <a href="index.php?page=payroll">
                            <i class='fa-solid fa-money-check icon'></i>
                            <span class="nav-text text">Payroll List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=employee">
                            <i class="bx bxs-user icon"></i>
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
                            <i class='bx bx-dollar icon'></i>
                            <span class="nav-text text">Allowance List</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="index.php?page=deductions">
                            <i class='bx bx-receipt icon'></i>
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
<li class="nav-link">
    <a href="ajax.php?action=logout">
        <i class='bx bx-log-out icon'></i>
        <span class="nav-text text">Logout</span>
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
</html>
