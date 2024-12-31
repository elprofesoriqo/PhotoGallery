<nav id="main-navbar" class="navbar">
    <div id="navbar-container" class="container">
        <a id="navbar-brand" class="brand" href="/">Gallery</a>
        <ul id="navbar-items" class="nav-items">
            <li class="nav-item">
                <a class="nav-link" href="/saved">Saved Images</a>
            </li>
            <?php if ($model->loggedIn) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/register">Register</a>
                </li>
            <?php } ?>
            <li class="nav-item">
                <a class="nav-link" href="/uploader">Post Photos</a>
            </li>
        </ul>
    </div>
</nav>
