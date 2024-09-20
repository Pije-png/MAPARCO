<nav class="navbar">
    <div class="back-button">
        <a href="dashboard.php"><i class="fa-solid fa-angle-left"></i></a>
    </div>

    <div class="left-section">
        <h3><img src="../img/MAPARCO.png" alt="MAPARCO Logo" class="logo">MAPARCO</h3>
    </div>
    <form class="search-form" action="search.php" method="GET">
        <input type="text" name="search" placeholder="Search">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
    <a href="view_cart.php" class="cart-icon" title="My Shopping Cart"><i class="fa-solid fa-cart-shopping"></i></a>
    <div class="right-section">
        <div class="dropdown">
            <button style="background-color:#15BE2F; border:none" class="btn btn-success" type="button" id="menuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="menuDropdown">
                <div class="logo-text">
                    <h6 class="dropdown-header">
                        MENU
                    </h6>
                </div>
                <a href="profile.php" class="dropdown-item">Profile</a>
                <a href="address.php" class="dropdown-item">Address</a>
                <a href="purchase.php" class="dropdown-item">Purchase</a>
                <a href="view_cart.php" class="dropdown-item">Shopping Cart</a>
                <a href="#" class="dropdown-item">Settings</a>
                <a href="../logout.php" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>
</nav>