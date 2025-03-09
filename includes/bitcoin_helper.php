<?php

/**
 * Get current Bitcoin price in USD
 * 
 * @return float|false Bitcoin price or false on failure   
 */
function get_bitcoin_price() {
    // Get the API URL from settings
    $db = Database::getInstance();
    $sql = "SELECT setting_value FROM settings WHERE setting_name = 'bitcoin_price_api'";
    $result = $db->getRow($sql);
    $api_url = $result['setting_value'] ?? 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd';
    
    // Fetch data from API
    $json_data = file_get_contents($api_url);
    if (!$json_data) {
        return false;
    } 

    $data = json_decode($json_data, true);

    // Check if we have valid data
    if (!isset($data['bitcoin']['usd'])) {
        return false;
    }

    return floatval($data['bitcoin']['usd']);
}

/**
 * Derive Bitcoin address from xpub key
 * 
 * Note: This is a simplified example. In a real application,
 * you would use a proper Bitcoin library like BitWasp\Bitcoin
 * 
 * @param string $xpub Extended public key
 * @param int $index Derivation index
 * @param string $type Wallet type (standard, segwit, native_segwit)
 * @return string Bitcoin address
 */ 
function derive_address_from_xpub($xpub, $index, $type = 'native_segwit') {
    // In a real implementation, you would use a Bitcoin library
    // This is a simplified placeholder

    // For demo purposes, we'll just generate a valid-looking address
    $prefix = '';
    switch ($type) {
        case 'standard':
            $prefix = '1'; // Legacy address
            $length = 33;
            break;
        case 'segwit':
            $prefix = '3'; // P2SH address
            $length = 33;
            break;
        case 'native_segwit':
            $prefix = 'bc1q'; // Bech32 address
            $length = 39;
            break;
        default:
            $prefix = 'bc1q';
            $length = 39;            
    }

    // Use xpub and index to create a deterministic but fake address
    $hash = hash('sha256', $xpub . $index);
    $address = $prefix . substr($hash, 0, $length - strlen($prefix));

    return $address;
} 

/**
 * Get transaction information from blockchain
 * 
 * Note: In a real application, you would use a proper blockchain API
 * like Blockstream.info, Blockchain.info, or run your own node
 * 
 * @param string $tx_hash Transaction hash
 * @return array|false Transaction info or false on failure
 */
function get_transaction_info($tx_hash) {
    // In a real implementation, you would call a blockchain API
    // This is a simplified placeholder for demo purposes

    // For demo purposes, we'll just return a fake response
    if (strlen($tx_hash) !== 64 || !ctype_xdigit($tx_hash)) {
        return false;
    }

    // Simulate a random number of confirmations
    $confirmations = rand(0, 6);

    return [
        'tx_hash' => $tx_hash,
        'confirmations' => $confirmations,
        'time' => time(),
        'status' => $confirmations > 0 ? 'confirmed' : 'pending'
    ];
}

/**
 * Verify a Bitcoin transaction
 * 
 * Note: In a real application, you would use a proper blockchain API
 * 
 * @param string $tx_hash Transaction hash
 * @param string $address Recipient address
 * @param float $amount Expected amount
 * @return bool True if transaction is valid
 */
function verify_transaction($tx_hash, $address, $amount) {
    // In a real implementation, you would verify this against the blockchain
    // This is a simplified placeholder for demo purposes

    // For demo purposes, we'll just validate the inputs
    if (strlen($tx_hash) !== 64 || !ctype_xdigit($tx_hash)) {
        return false;
    }

    // Validate address format (very basic check)
    if (!(strpos($address, '1') === 0 || strpos($address, '3') === 0 || strpos($address, 'bc1') === 0)) {
        return false;
    }

    // Validate amount
    if ($amount <= 0) {
        return false;
    }

    // For demo purposes, always return true (transaction is valid)
    return true;
}

/**
 * Generate a QR code for a Bitcoin payment
 * 
 * @param string $address Bitcoin address
 * @param float $amount Bitcoin amount
 * @param string $message Optional message/label
 * @return string URL for QR code 
 */
function get_bitcoin_qr_code($address, $amount, $message = '') {
    // Encode the bitcoin URI
    $uri = "bitcoin:{$address}?amount={$amount}";

    if (!empty($message)) {
        $uri .= "&message=" . urlencode($message); 
    }

    // Use Google Charts API to generate QR code
    $qr_url = "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($uri);

    return $qr_url;
}

/**
 * Format a Bitcoin amount with proper decimal places
 * 
 * @param float $amount Bitcoin amount
 * @return string Formatted amount
 */
function format_bitcoin_amount($amount) {
    // Bitcoin amounts should be displayed with 8 decimal places
    return number_format($amount, 8, '.', ',');
}

/**
 * Convert satoshis to BTC
 * 
 * @param int $satoshis Amount in satoshis
 * @return float Amount in BTC
 */
function satoshi_to_btc($satoshis) {
    return $satoshis / 100000000;
}

/**
 * Convert BTC to satoshis
 * 
 * @param float $btc Amount in BTC
 * @return int Amount in satoshis
 */
function btc_to_satoshi($btc) {
    return (int)($btc * 100000000);
}