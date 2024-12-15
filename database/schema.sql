-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    bio TEXT,
    profile_image VARCHAR(255),
    bitcoin_address VARCHAR(100),
    lightning_address VARCHAR(100),
    two_factor_secret VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donation Tiers
CREATE TABLE donation_tiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    amount_btc DECIMAL(10,8),
    description TEXT,
    perks TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) 
);

-- Donations Table
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Recipient user
    donor_name VARCHAR(100),
    donor_email VARCHAR(100),
    amount_btc DECIMAL(10,8) NOT NULL,
    amount_usd DECIMAL(10,2),
    transaction_hash VARCHAR(100),
    status ENUM('pending', 'confirmed', 'failed') DEFAULT 'pending',
    is_anonymous BOOLEAN DEFAULT FALSE,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Recurring Donations
CREATE TABLE recurring_donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    donor_id INT,
    amount_btc DECIMAL(10,8),
    frequecny ENUM('weekly', 'monthly', 'yearly'),
    next_donation_date DATE,
    status ENUM('active', 'paused', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (donor_id) REFERENCES users(id)
);

-- Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('donation', 'milestone', 'system'),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) 
);