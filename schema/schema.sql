-- Bitcoin Coffee App Schema

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User profiles table for creators
CREATE TABLE creators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    display_name VARCHAR(50) NOT NULL,
    bio TEXT,
    profile_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    coffee_price DECIMAL(10,2) DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  
); 

-- Bitcoin wallets table
CREATE TABLE bitcoin_wallets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    wallet_name VARCHAR(100) NOT NULL,
    public_key VARCHAR(255) NOT NULL,
    xpub VARCHAR(255),
    wallet_type ENUM('standard', 'segwit', 'native_segwit') DEFAULT 'native_segwit',
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Donations table
CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    creator_id INT NOT NULL,
    amount DECIMAL(18,8) NOT NULL,
    bitcoin_amount DECIMAL(18,8) NOT NULL,
    tx_hash VARCHAR(255) NOT NULL,
    message TEXT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'confirmed', 'failed') DEFAULT 'pending',
    confirmations INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
);

-- Payment addresses table
CREATE TABLE payment_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,    
    wallet_id INT NOT NULL,
    address VARCHAR(255) NOT NULL UNIQUE,
    is_used BOOLEAN DEFAULT FALSE,
    tx_hash VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT 
);