<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= SITE_DESCRIPTION ?></title>

    <!-- Boostrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fas fa-coffee"></i> <?= SITE_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/discover.php">Discover Creators</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/about.php">How It Works</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (!is_logged_in()): ?>
                     <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/login.php">Login</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link btn btn-outline-light" href="<?= BASE_URL ?>/register.php">Sign Up</a>
                     </li>
                    <?php else: ?>
                        <?php if (is_creator()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/dashboard.php">Creator Dashboard</a>
                            </li> 
                        <?php else: ?>    
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/become-creator.php">Become a Creator</a>
                            </li>
                        <?php endif; ?>    
                </ul>
            </div>
        </div>
    </nav>
</body>

</html>