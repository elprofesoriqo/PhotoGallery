<div id="navbar">
    <div class="container">
        <a href="gallery" class="nav-item">Gallery</a>
        <a href="edit" class="nav-item">Add Photo</a>
        <a href="selected" class="nav-item">Selected Photos</a>

        <?php if (isset($_SESSION['islogged'])): ?>
            <a href="logout" class="nav-action">Logout</a>
            <?php if (isset($_SESSION['loggeduser'])): ?>
                <span class="nav-user">Logged in as: <?= htmlspecialchars($_SESSION['loggeduser']) ?></span>
            <?php endif ?>
        <?php else: ?>
            <a href="register" class="nav-action">Register</a>
            <a href="login" class="nav-action">Login</a>
        <?php endif ?>
    </div>
    <div class="error-container">
        <?php if (isset($_SESSION['error']) && $_SESSION['error'] != ''): ?>
            <p class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></p>
            <?php $_SESSION['error'] = ''; ?>
        <?php endif ?>
    </div>
    <hr />
</div>
