# This is the Readme file for the project

## Here is the file structure

buy-me-coffee/
├── api/
    ├── get_btc_price.php       # Returns current BTC price for coffee calculation
    ├── get_btc_address.php     # Generates/returns Bitcoin address for payment

├── assets/
    ├── css/
        ├── custom.css     # Custom styles
        ├── vendor/        # Third-party CSS files
    ├── js/
        ├── donation.js    # Donation from handling
        ├── vendor/        # Third-party JS files
        img/
           bitcoin-logo.png # Images used in the system

├── classes/
    ├── DatabaseConnection.php   # Database connection handler
    ├── Donation.php             # Donation class for managing donations
    ├── BitcoinPrice.php         # Class to handle BTC price fetching
    ├── BitcoinAddress.php       # Class to manage Bitcoin addresses
    ├── ActivityLogger.php       # Class for logging system activities

├── config/
        ├── database.php         # Database configuration
        ├── constants.php        # System constants and configurations

├── handlers/
    ├── process_donation.php     # Processes donation submissions
    ├── verify_payment.php       # Verifies Bitcoin payments

├── includes/
    ├── header.php               # Common header
    ├── footer.php               # Common footer
    ├── navbar.php               # Navigation bar
    ├── sidebar.php              # Sidebar menu
    ├── main_footer.php          # Main footer content

├── views/
    ├── donate.php               # Main donation form
    ├── success.php              # Donation success page
    ├── error.php                # Error page
    ├── admin/
        ├── dashboard.php        # Admin dashboard
        ├── donations.php        # List all donations
        ├── settings.php         # System settings

├── utils/
    ├── helpers.php              # Helper functions
    ├── validators.php           # Input validation functions

├── webhooks/
       ├── bitcoin_payment.php   # Webhook for Bitcoin payment notifications

├── vendor/                      # Composer dependencies

├── .htaccess                    # Apache configuration
├── index.php                    # Main entry point
├── README.md                    # Project documentation