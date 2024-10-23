<?php
require 'vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '34.205.207.196';
$db = 'paypal'; 
$user = 'postgres'; 
$pass = 'canqualify'; 
$charset = 'utf8mb4';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

use Braintree\Gateway;

$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

$customer_id = '54587814549';

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE customer_id = :customer_id");
$stmt->execute(['customer_id' => $customer_id]);
$customer_exists = $stmt->fetch();

if ($customer_exists) {
    $firstname = $customer_exists['firstname'];
    $lastname = $customer_exists['lastname'];
    $cardholder_name = $customer_exists['cardholder_name'];

    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Gateway</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://js.braintreegateway.com/js/braintree-2.31.0.min.js"></script>
        <style>
            form {
                width: 300px;
                box-shadow: 0 4px 10px gray;
                margin: auto;
                padding: 10px;
            }
            #dropin-container {
                margin-top: 20px;
            }
        </style>
        <script>
            $(document).ready(function() {
                $.ajax({
                    url: "token.php",
                    type: "get",
                    dataType: "json",
                    success: function(data) {
                        braintree.setup(data, "dropin", { container: "dropin-container" });
                    },
                    error: function(xhr, status, error) {
                        alert("Error fetching Braintree token: " + error);
                    }
                });
            });
        </script>
    </head>
    <body style="text-align:center;">
        <form action="newpayment.php" method="post" class="payment-form">
            <input type="hidden" name="customer_id" value="' . htmlspecialchars($customer_id) . '">
            <input type="hidden" name="firstname" value="' . htmlspecialchars($firstname) . '">
            <input type="hidden" name="lastname" value="' . htmlspecialchars($lastname) . '">
            <label for="amount" class="heading">Amount</label><br>
            <input type="number" name="amount" placeholder="Enter amount" min="1" step="0.01" required><br>
            <div id="dropin-container"></div>
            <button type="submit">Pay with Braintree</button>
        </form>
    </body>
    </html>';
} else {
    echo "Customer ID not found in the transactions table.";
}
?>