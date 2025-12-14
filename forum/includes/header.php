<header class="site-header">
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">Forum Site</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="create_post.php">Create Post</a></li>
                <li class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars(getUsername()); ?>!</span>
                    <a href="logout.php" class="btn btn-secondary">Logout</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
