<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">

        <a class="navbar-brand" href="index.php">SimpleLearn</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <!-- LINKE Navigation -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Kurse</a>
                </li>



                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="my-courses.php">Meine Kurse</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create-course.php">Kurs erstellen</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="about.php">Über SimpleLearn</a>
                </li>
            </ul>


            <!-- RECHTE Navigation -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item me-2">
                        <span class="navbar-text">
                            Angemeldet als <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>
