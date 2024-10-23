<?php
require_once 'vendor/autoload.php';
use Braintree\Gateway;
$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

$customerId = '54587814549';

try {
    $transactions = $gateway->transaction()->search([
        Braintree\TransactionSearch::customerId()->is($customerId)
    ]);
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Transactions</title>
        <style>
            body {
                font-family: Arial, sans-serif;
               margin: 48px;
                margin-left: 300px;
            }
            table {
                width: 50%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: gray;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
                button{
                background-color:green;
                border:none;
                height:30px;
                width:100px;
                font-size:15px;
                border-radius:10px;
                color:white;
             }
            button:hover{
            background-color:maroon;
            cursor:pointer;
            }
        </style>
    </head>
    <body>';
    if ($transactions->maximumCount() > 0) {
        echo "<h2>Transactions for Customer ID: $customerId</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>";

        foreach ($transactions as $transaction) {
            echo "<tr>
                    <td>{$transaction->id}</td>
                    <td>{$transaction->amount}</td>
                    <td>{$transaction->status}</td>
                    <td>
                    <form action='refund.php' method='POST'>
                            <input type='hidden' name='transaction_id' value='{$transaction->id}'>
                            <button type='submit'>Refund</button>
                        </form>
                    </td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "No transactions found for Customer ID: $customerId";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
