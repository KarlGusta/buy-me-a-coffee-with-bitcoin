<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../includes/bitcoin_helper.php';

class Donation {
    private $id;
    private $donor_id;
    private $creator_id;
    private $amount;
    private $bitcoin_amount;
    private $tx_hash;
    private $message;
    private $is_anonymous;
    private $status;
    private $confirmations;
    private $created_at;
    private $db;

    public function __construct($donation_data = null) {
        $this->db = Database::getInstance();

        if ($donation_data) {
            $this->id = $donation_data['id'] ?? null;
            $this->donor_id = $donation_data['donor_id'] ?? null;
            $this->creator_id = $donation_data['creator_id'] ?? null;
            $this->amount = $donation_data['amount'] ?? 0;
            $this->bitcoin_amount = $donation_data['bitcoin_amount'] ?? 0;
            $this->tx_hash = $donation_data['tx_hash'] ?? null;
            $this->message = $donation_data['message'] ?? null;
            $this->is_anonymous = $donation_data['is_anonymous'] ?? false;
            $this->status = $donation_data['status'] ?? 'pending';
            $this->confirmations = $donation_data['confirmations'] ?? 0;
            $this->created_at = $donation_data['created_at'] ?? null;
        }
    }

    // Create a new donation
    public function create() {
        if (!$this->creator_id || !$this->bitcoin_amount || !$this->tx_hash) {
            return false;
        }

        $sql = "INSERT INTO donations (donor_id, creator_id, amount, bitcoin_amount, tx_hash, message, is_anonymous, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $this->donor_id,
            $this->creator_id,
            $this->amount,
            $this->bitcoin_amount,
            $this->tx_hash,
            $this->message,
            $this->is_anonymous ? 1 : 0,
            $this->status
        ];        

        $this->id = $this->db->insert($sql, $params);

        return $this->id ? true : false;
    }

    // Update donation status based on blockchain confirmations
    public function updateStatus() {
        if (!$this->id || !$this->tx_hash) {
            return false;
        }

        // Get current confirmations from blockchain
        $tx_info = get_transaction_info($this->tx_hash);

        if (!$tx_info) {
            return false;
        }

        // Get confirmation threshold from settings
        $sql = "SELECT setting_value FROM settings WHERE setting_name = 'confirmation_threshold'";
        $result = $this->db->getRow($sql);
        $confirmation_threshold = (int)($result['setting_value'] ?? 3);

        $this->confirmations = $tx_info['confirmations'] ?? 0;

        // Update status based on confirmations
        if ($this->confirmations >= $confirmation_threshold) {
            $this->status = 'confirmed';
        } else if ($this->confirmations < 0) {
            $this->status = 'failed'; // Negative confirmations mean transaction was reversed 
        }

        // Update the database
        $sql = "UPDATE donations SET status = ?, confirmations = ? WHERE id = ?";
        return $this->db->update($sql, [$this->status, $this->confirmations, $this->id]);
    }

    // Get donation by ID
    public static function getById($donation_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM donations WHERE id = ?";
        $donation_data = $db->getRow($sql, [$donation_id]);
        
        if (!$donation_data) {
            return null;
        }

        return new Donation($donation_data);
    }

    // Get donations by creator ID
    public static function getByCreator($creator_id, $limit = 10, $offset = 0) {
        $db = Database::getInstance();
        $sql = "SELECT d.*, u.username as donor_username
                FROM donations d
                LEFT JOIN users u ON d.donor_id = u.id
                WHERE d.creator_id = ?
                ORDER BY d.created_at DESC
                LIMIT ? OFFSET ?";

        $donations_data = $db->getRows($sql, [$creator_id, $limit, $offset]);
        
        $donations = [];
        foreach ($donations_data as $donation_data) {
            $donations[] = new Donation($donation_data);
        }

        return $donations;
    }

    // Get donations by donor ID
    public static function getByDonor($donor_id, $limit = 10, $offset = 0) {
        $db = Database::getInstance();
        $sql = "SELECT d.*, c.display_name as creator_name
                FROM donations d
                JOIN creators c ON d.creator_id = c.id
                WHERE d.donor_id = ?
                ORDER BY d.created_at DESC
                LIMIT ? OFFSET ?";

        $donations_data = $db->getRows($sql, [$donor_id, $limit, $offset]);
        
        $donations = [];
        foreach ($donations_data as $donation_data) {
            $donations[] = new Donation($donation_data);
        }

        return $donations;
    }

    // Get pending donations
    public static function getPending() {
        $db = Database::getInstance();
        $sql = "SELECT * FROM donations WHERE status = 'pending'";
        $donations_data = $db->getRows($sql);

        $donations = [];
        foreach ($donations_data as $donation_data){
            $donations[] = new Donation($donation_data);
        }

        return $donations;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getDonorId() {
        return $this->donor_id;
    }

    public function getCreatorId() {
        return $this->creator_id;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getBitcoinAmount() {
        return $this->bitcoin_amount;
    }

    public function getTxHash() {
        return $this->tx_hash;
    }

    public function getMessage() {
        return $this->message;
    }

    public function isAnonymous() {
        return (bool)$this->is_anonymous;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getConfirmations() {
        return $this->confirmations;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }
}
