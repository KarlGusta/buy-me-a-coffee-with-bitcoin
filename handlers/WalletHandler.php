<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Wallet.php';
require_once __DIR__ . '/../classes/Transaction.php';
require_once __DIR__ . '/../classes/Database.php';

class WalletHandler {
    private $db;
    private $wallet;
    private $transaction;

    public function __construct() {
        $this->db = new Database();
        $this->wallet = new Wallet();
        $this->transaction = new Transaction();
    }

    public function handleRequest() {
        $action = $_REQUEST['action'] ?? '';

        switch ($action) {
            case 'create':
                return $this->createWallet();
            case 'import':
                return $this->importWallet();
            case 'getBalance':
                return $this->getWalletBalance();
            case 'getAddress':
                return $this->getWalletAddress();
            case 'getPrivateKey':
                return $this->getPrivateKey();
            default:
                return json_encode(['error' => 'Invalid action']);                    
        }
    }

    private function createWallet() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        $walletDetails = $this->wallet->generateNewWallet();
        $walletId = $this->wallet->saveWallet($userId, $walletDetails['public_key'], $walletDetails['private_key_encrypted']);

        return json_encode([
            'success' => true,
            'wallet_id' => $walletId,
            'public_address' => $walletDetails['public_key']
        ]);
    }

    private function importWallet() {
        $userId = $_SESSION['user_id'] ?? null;
        $privateKey = $_POST['private_key'] ?? '';

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        if (empty($privateKey)) {
            return json_encode(['error' => 'Private key is required']);
        }

        $importResult = $this->wallet->importWallet($privateKey);

        if (isset($importResult['error'])) {
            return json_encode($importResult);
        }

        $walletId = $this->wallet->saveWallet($userId, $importResult['public_key'], $importResult['private_key_encrypted']);

        return json_encode([
            'success' => true,
            'wallet_id' => $walletId,
            'public_address' => $importResult['public_key']
        ]);
    }

    private function getWalletBalance() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        $walletId = $this->wallet->getUserWalletId($userId);

        if (!$walletId) {
            return json_encode(['error' => 'Wallet not found']);
        }

        $balance = $this->wallet->getWalletBalance($walletId);

        return json_encode([
            'success' => true,
            'balance' => $balance
        ]);
    }

    private function getWalletAddress() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        $walletAddress = $this->wallet->getUserWalletAddress($userId);

        if (!$walletAddress) {
            return json_encode(['error' => 'Wallet not found']);
        }

        return json_encode([
            'success' => true,
            'address' => $walletAddress
        ]);
    }

    private function getPrivateKey() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        $privateKey = $this->wallet->getDecryptedPrivateKey($userId);

        if (!$privateKey) {
            return json_encode(['error' => 'Private key not found']);
        }

        return json_encode([
            'success' => true,
            'private_key' => $privateKey
        ]);
    }
}