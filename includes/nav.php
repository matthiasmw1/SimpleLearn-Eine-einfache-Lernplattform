<?php
// includes/nav.php
start_session_once();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">SimpleLearn</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if ($isLoggedIn): ?>
          <li class="nav-item"><span class="navbar-text me-2">Hi, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Registrieren</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
