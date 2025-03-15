<?php

// Include header
require_once __DIR__ . '/layout/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>Support your favourite creators with Bitcoin</h1>
                <p class="lead">A self-custodial Bitcoin donation platform that lets creators receive support directly from their audience without intermediaries.</p>

                <?php if (!is_logged_in()): ?>
                <div class="hero-buttons">
                    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="<?= BASE_URL ?>/about.php" class="btn btn-outline-light btn-lg">Learn More</a>
                </div>    
                <?php else: ?>
                <div class="hero-buttons">
                    <a href="<?= BASE_URL ?>/discover.php" class="btn btn-primary btn-lg">Discover Creators</a>
                    <?php if (!is_creator()): ?>
                    <a href="<?= BASE_URL ?>/become-creator.php" class="btn btn-outline-light btn-lg">Become a Creator</a>
                    <?php else: ?>
                    <a href="<?= BASE_URL ?>/dashboard.php" class="btn btn-outline-light btn-lg">Creator Dashboard</a>
                    <?php endif; ?>        
                </div>    
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>