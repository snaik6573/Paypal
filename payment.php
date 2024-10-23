<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "boot.php"; 

if (empty($_POST['payment_method_nonce'])) {
    echo "Nonce not provided.";
    print_r($_POST); 
    header('location:index.php');
    exit();
}

$customerResult = $gateway->customer()->create([
    'firstName' => $_POST['firstname'],
    'lastName' => $_POST['lastname'],
]);

if ($customerResult->success) {
    $paymentMethodResult = $gateway->paymentMethod()->create([
        'customerId' => $customerResult->customer->id,
        'paymentMethodNonce' => $_POST['payment_method_nonce'], 
        'options' => [
            'verifyCard' => true,
        ]
    ]);

    if ($paymentMethodResult->success) {
        $paymentMethodToken = $paymentMethodResult->paymentMethod->token;

        $transactionResult = $gateway->transaction()->sale([
            'amount' => $_POST['amount'],
            'paymentMethodToken' => $paymentMethodToken,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($transactionResult->success) {
            $cardType = isset($transactionResult->transaction->creditCard) ? 
                        $transactionResult->transaction->creditCard['cardType'] : 
                        'Unknown';

            $stmt = $pdo->prepare("INSERT INTO transactions (firstname, lastname, customer_id, cardholder_name, amount, transaction_id, status, card_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['firstname'],
                $_POST['lastname'],
                $customerResult->customer->id,
                $_POST['cardholder_name'],
                $_POST['amount'],
                $transactionResult->transaction->id,
                'successful',
                $cardType
            ]);
        } else {
            echo "Transaction Error: " . $transactionResult->message;
            print_r($transactionResult->errors);
            die();
        }
    } else {
        echo "Payment Method Creation Error: " . $paymentMethodResult->message;
        print_r($paymentMethodResult->errors);
        die();
    }
} else {
    echo "Customer Creation Error: " . $customerResult->message;
    print_r($customerResult->errors);
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Result</title>
    <style>
        form {
            width: 300px;
            border: 1px solid black;
            margin-left: auto;
            margin-right: auto;
            padding: 10px;
        }
        label.heading {
            margin-right: 100px;
            font-size: 20px;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <form class="payment-form">
        <label for="Id" class="heading">Transaction Id</label><br>
        <input type="text" disabled="disabled" name="Id" value="<?php echo htmlspecialchars($transactionResult->transaction->id); ?>"><br>
        <label for="status" class="heading">Status</label><br>
        <input type="text" disabled="disabled" name="status" value="successful"><br>
        <label for="card_type" class="heading">Card Type</label><br>
        <input type="text" disabled="disabled" name="card_type" value="<?php echo htmlspecialchars($cardType); ?>"><br>
    </form>
</body>
</html>
