CREATE TABLE donations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    donation_id VARCHAR(20) UNIQUE NOT NULL,
    donor_name VARCHAR(100) NOT NULL,
    donor_email VARCHAR(255),
    coffee_count INT NOT NULL,
    btc_amount DECIMAL(18,8) NOT NULL,
    message TEXT,
    bitcoin_address VARCHAR(100) NOT NULL,
    status ENUM('pending', 'confirmed', 'failed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX idx_donation_id ON donations(donation_id);
CREATE INDEX idx_status ON donations(status);
CREATE INDEX idx_created_at ON donations(created_at); 