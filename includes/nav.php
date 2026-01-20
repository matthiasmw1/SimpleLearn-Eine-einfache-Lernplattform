<?php
require_once __DIR__ . '/../util/auth_helper.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>index.php">
            SimpleLearn
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>index.php">
                            🏠 Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>my-courses.php">
                            📖 Meine Kurse
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            👤 <?php echo htmlspecialchars(getCurrentUsername()); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>profile.php">
                                    ⚙️ Profil
                                </a>
                            </li>
                            <?php if (isAdmin()): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>admin.php">
                                        🔑 Admin Panel
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>logout.php">
                                    🚪 Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>login.php">
                            🔐 Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $GLOBALS['root_path'] ?? '/simplelearn/'; ?>register.php">
                            ✍️ Registrieren
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
