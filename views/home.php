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

            <div class="col-md-5">
                <div class="hero-stats">
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($creator_count) ?></div>
                        <div class="stats-label">Creators</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-number"><?= number_format($donation_stats['count'] ?? 0) ?></div>
                        <div class="stats-label">Donations</div>
                    </div>
                    <div class="stats-card">
                        <div class="stats-number"><?= format_currency($donation_stats['total'] ?? 0) ?></div>
                        <div class="stats-label">Total Support</div>
                    </div>
                </div>

                <!-- Bitcoin Address QR -->
                 <div class="bitcoin-demo">
                    <h4>How it works</h4>
                    <p>1. Find a creator you want to support</p>
                    <p>2. Send Bitcoin directly to their wallet</p>
                    <p>3. 100% of your donation goes to the creator</p>

                    <div class="qr-example">
                        <img src="<?= BASE_URL ?>/assets/images/qr-example.png" alt="">
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>