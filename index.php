<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Me a Coffee with Bitcoin</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Buy Me a Coffee with Bitcoin</h1>
            <p>Support my work with self-custodial Bitcoin payments</p>
        </header>

        <main>
            <section class="coffee-options">
                <h2>Choose Your Coffee Size</h2>
                <div class="coffee-grid">
                    <div class="coffee-item" data-amount="0.0001">
                        <h3>Small Coffee</h3>
                        <div class="coffee-icon">☕</div>
                        <p class="price">0.0001 BTC</p>
                        <button class="buy-btn" data-size="small">Buy Now</button>
                    </div>
                    <div class="coffee-item" data-amount="0.0002">
                        <h3>Medium Coffee</h3>
                        <div class="coffee-icon">☕☕</div>
                        <p class="price">0.0002 BTC</p>
                        <button class="buy-btn" data-size="medium">Buy Now</button>
                    </div>
                    <div class="coffee-item" data-amount="0.0005">
                        <h3>Large Coffee</h3>
                        <div class="coffee-icon">☕☕☕</div>
                        <p class="price">0.0005 BTC</p>
                        <button class="buy-btn" data-size="large">Buy Now</button>
                    </div>
                </div>
            </section>

            <section class="payment-section hidden" id="payment-section">
                <h2>Send Bitcoin Payment</h2>
                <div class="wallet-address">
                    <p>Send exactly <span id="btc-amount">0.0000</span> BTC to:</p>
                    <div class="qr-code" id="qr-code"></div>
                    <div class="address-container">
                        <input type="text" id="btc-address" readonly>
                        <button id="copy-address">Copy</button>
                    </div>
                </div>
                <div class="payment-buttons">
                    <button id="check-payment">Check Payment</button>
                    <button id="cancel-payment">Cancel</button>
                </div>
                <div class="payment-status" id="payment-status"></div>
            </section>

            <section class="wallet-section" id="wallet-section">
                <h2>Your Bitcoin Wallet</h2>
                <div class="wallet-actions">
                    <button id="create-wallet">Create New Wallet</button>
                    <button id="import-wallet">Import Existing Wallet</button>
                </div>
                <div class="wallet-info hidden" id="wallet-info">
                    <p>Public Address: <span id="public-address"></span></p>
                    <p>Balance: <span id="wallet-balance">0.0000</span> BTC</p>
                    <button id="view-private-key">View Private Key</button>
                    <button id="hide-wallet-info">Hide Wallet Info</button>
                </div>
            </section>

            <section class="transaction-history" id="transaction-history">
                <h2>Transaction History</h2>
                <table id="tx-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tx-list">
                        <!-- Transactions will be loaded here -->
                    </tbody>
                </table>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 Buy Me a Coffee with Bitcoin | <a href="#" id="show-terms">Terms</a></p>
        </footer>
    </div>

    <!-- Wallet Import Modal -->
    <div class="modal" id="import-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Import Wallet</h2>
            <form id="import-form">
                <div class="form-group">
                    <label for="private-key">Private Key or Seed Phrase:</label>
                    <input type="password" id="private-key" required>
                </div>
                <button type="submit">Import</button>
            </form>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal" id="terms-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Terms and Conditions</h2>
            <div class="terms-content">
                <p>This is a self-custodial wallet. You are responsible for securing your private keys.</p>
                <p>We never store your private keys on our servers.</p>
                <p>Bitcoin transactions are irreversible. Please verify all payment information before sending.</p>
            </div>
        </div>
    </div>

    <script src="assets/js/qrcode.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>