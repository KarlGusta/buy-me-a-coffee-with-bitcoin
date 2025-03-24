<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../includes/bitcoin_helper.php';

class BitcoinWallet {
    private $id;
    private $user_id;
    private $wallet_name;
    private $public_key;
    private $xpub;
    private $wallet_type;
    private $is_primary;
    private $db;

    public function __construct($wallet_data = null) {
        $this->db = Database::getInstance();

        if ($wallet_data) {
            $this->id = $wallet_data['id'] ?? null;
            $this->user_id = $wallet_data['user_id'] ?? null;
            $this->wallet_name = $wallet_data['wallet_name'] ?? null;
            $this->public_key = $wallet_data['public_key'] ?? null;
            $this->xpub = $wallet_data['xpub'] ?? null;
            $this->wallet_type = $wallet_data['wallet_type'] ?? 'native_segwit';
            $this->is_primary = $wallet_data['is_primary'] ?? false;
        }
    }

    // Create a new wallet
    public function create() {
        if (!$this->user_id || !$this->wallet_name || !$this->public_key) {
            return false;
        }

        // Check if this is the first wallet for this user
        $sql = "SELECT COUNT(*) as count FROM bitcoin_wallets WHERE user_id = ?";
        $result = $this->db->getRow($sql, [$this->user_id]);
        $is_first_wallet = ($result['count'] == 0);

        // If this is the first wallet, make it primary
        $this->is_primary = $is_first_wallet ? 1 : 0;

        $sql = "INSERT INTO bitcoin_wallets (user_id, wallet_name, public_key, xpub, wallet_type, is_primary)
                VALUES (?, ?, ?, ?, ?, ?)";

        $params = [
            $this->user_id,
            $this->wallet_name,
            $this->public_key,
            $this->xpub,
            $this->wallet_type,
            $this->is_primary
        ];   
        
        $this->id = $this->db->insert($sql, $params);

        // If this is the first wallet, generate a few addresses
        if ($is_first_wallet) {
            $this->generateNewAddresses(3);
        }

        return $this->id ? true : false;
    }

    // Generate new payment addresses for this wallet
    public function generateNewAddresses($count = 1) {
        if (!$this->id) {
            return false;
        }

        $addresses = [];

        // If we have an xpub, we can derive multiple addresses
        if ($this->xpub) {
            // We would use the BIP32/44/49/84 derivation here
            // This is a simplified version
            $usedAddressCount = $this->getUsedAddressCount();

            for ($i = 0; $i < $count; $i++) {
                $index = $usedAddressCount + $i;
                $address = derive_address_from_xpub($this->xpub, $index, $this->wallet_type);

                $sql = "INSERT INTO payment_addresses (wallet_id, address, is_used) VALUES (?, ?, 0)";
                $this->db->query($sql, [$this->id, $address]);

                $addresses[] = $address;
            }
        } else {
            // For simple wallets, we would just store the provided address
            $address = $this->public_key;
            $sql = "INSERT INTO payment_addresses (wallet_id, address, is_used) VALUES (?, ?, 0)";
            $this->db->query($sql, [$this->id, $address]);

            $addresses[] = $address;
        }

        return $addresses;
    } 

    // Get the next unused address for this wallet
    public function getUnusedAddress() {
        if (!$this->id) {
            return false;
        }

        $sql = "SELECT * FROM payment_addresses WHERE wallet_id = ? AND is_used = 0 LIMIT 1";
        $address = $this->db->getRow($sql, [$this->id]);

        // If no unused address is found, generate a new one
        if (!$address) {
            $new_addresses = $this->generateNewAddresses(1);
            if (!empty($new_addresses)) {
                return $new_addresses[0];
            } 
            return false;
        }

        return $address['address'];
    }

    // Mark an address as used
    public function markAddressAsUsed($address, $tx_hash) {
        $sql = "UPDATE payment_addresses SET is_used = 1, tx_hash = ? WHERE address = ? AND wallet_id = ?";
        return $this->db->update($sql, [$tx_hash, $address, $this->id]);
    }

    // Get used address count
    private function getUsedAddressCount() {
        $sql = "SELECT COUNT(*) as count FROM payment_addresses WHERE wallet_id = ?";
        $result = $this->db->getRow($sql, [$this->id]);
        return $result['count'];
    }

    // Set this wallet as primary
    public function setAsPrimary() {
        if (!$this->id || !$this->user_id) {
            return false;
        }

        // First, set all wallets for this user as not primary
        $sql = "UPDATE bitcoin_wallets SET is_primary = 0 WHERE user_id = ?";
        $this->db->update($sql, [$this->user_id]);

        $this->is_primary = true;

        return $result > 0;
    }

    // Get wallet by ID
    public static function getById($wallet_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM bitcoin_wallets WHERE id = ?";
        $wallet_data = $db->getRow($sql, [$wallet_id]);

        if (!$wallet_data) {
            return null;
        }

        return new BitcoinWallet($wallet_data);
    }

    // Get primary wallet for user
    public static function getPrimaryForUser($user_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM bitcoin_wallets WHERE user_id = ? AND is_primary = 1";
        $wallet_data = $db->getRow($sql, [$user_id]);

        if (!$wallet_data) {
            // If no primary wallet, get the most recent one
            $sql = "SELECT * FROM bitcoin_wallets WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
            $wallet_data = $db->getRow($sql, [$user_id]);

            if (!$wallet_data) {
                return null;
            }
        }

        return new BitcoinWallet($wallet_data);
    }

    // Get all wallets for user
    public static function getAllForUser($user_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM bitcoin_wallets WHERE user_id = ? ORDER BY is_primary DESC, created_at DESC";
        $wallets_data = $db->getRows($sql, [$user_id]);

        $wallets = [];
        foreach ($wallets_data as $wallet_data) {
            $wallets[] = new BitcoinWallet($wallet_data);
        }

        return $wallets;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getWalletName() {
        return $this->wallet_name;
    }

    public function getPublicKey() {
        return $this->public_key;
    }

    public function getXpub() {
        return $this->xpub;
    }

    public function getWalletType() {
        return $this->wallet_type;
    }

    public function isPrimary() {
        return (bool)$this->is_primary;
    }
}