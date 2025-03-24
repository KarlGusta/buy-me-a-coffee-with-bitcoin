<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Transaction.php';
require_once __DIR__ . '/../classes/Wallet.php';
require_once __DIR__ . '/../classes/Database.php';

class TransactionHandler {
    private $db;
    private $transaction;
    private $wallet;

    public function __construct() {
        $this->db = new Database();
        $this->transaction = new Transaction();
        $this->wallet = new Wallet();
    }

    public function handleRequest() {
        $action = $_REQUEST['action'] ?? '';

        switch ($action) {
            case 'create':
                return $this->createTransaction();
            case 'check':
                return $this->checkTransactionStatus();
            case 'getHistory':
                return $this->getTransactionHistory();
            default:
                return json_encode(['error' => 'Invalid action']);            
        }
    }

    private function createTransaction() {
        $userId = $_SESSION['user_id'] ?? null;
        $amount = $_POST['amount'] ?? 0;
        $coffeeSize = $_POST['size'] ?? '';

        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        if (!$amount || !$coffeeSize) {
            return json_encode(['error' => 'Amount and coffee size are required']);
        }

        $creatorWalletAddress = CREATOR_WALLET_ADDRESS;
        $transactionId = $this->transaction->createTransaction($userId, $creatorWalletAddress, $amount, $coffeeSize);

        return json_encode([
            'success' => true,
            'transaction_id' => $transactionId,
            'address' => $creatorWalletAddress,
            'amount' => $amount
        ]);
    }

    private function checkTransactionStatus() {
        $transactionId = $_GET['transaction_id'] ?? '';

        if (!$transactionId) {
            return json_encode(['error' => 'Transaction ID is required']);
        }

        $status = $this->transaction->checkTransactionStatus($transactionId);

        return json_encode([
            'success' => true,
            'status' => $status,
            'transaction_id' => $transactionId
        ]);
    }

    private function getTransactionHistory() {
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            return json_encode(['error' => 'User not authenticated']);
        }

        $history = $this->transaction->getUserTransactionHistory($userId);

        return json_encode([
            'success' => true,
            'history' => $history
        ]);
    }
}