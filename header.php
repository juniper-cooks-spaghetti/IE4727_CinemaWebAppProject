<?php
require_once 'auth.inc.php';
?>
<header>
    <div class="logo">CineBox</div>
    <nav>
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a>
        <a href="catalogue.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'catalogue.php' ? 'active' : ''; ?>">Catalogue</a>
        <a href="cart.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">My Bookings</a>
    </nav>
    <div class="user-menu">
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-profile">
                <div class="profile-trigger">
                    <span class="profile-icon">👤</span>
                    <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="dropdown-menu">
                    <a href="edit_profile.php" class="dropdown-item">
                        Change Particulars ✍️
                    </a>
                    <a href="logout.php" class="dropdown-item">
                        Log out 👋
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="profile-icon">👤</a>
        <?php endif; ?>
    </div>
</header>
<script src="header.js"></script>