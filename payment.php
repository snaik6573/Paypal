<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "boot.php"; 

if (empty($_POST['payment_method_nonce'])) {
    header('location:index.php');
    exit();
}

$result = $gateway->transaction()->sale([
    'amount' => $_POST['amount'],
    'paymentMethodNonce' => $_POST['payment_method_nonce'],
    'customer' => [
        'firstName' => $_POST['firstname'],
        'lastName' => $_POST['lastname'],
    ],
    'options' => [
        'submitForSettlement' => true
    ]
]);

if ($result->success === true) {
    $stmt = $pdo->prepare("INSERT INTO transactions (firstname, lastname, amount, transaction_id, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['firstname'],
        $_POST['lastname'],
        $_POST['amount'],
        $result->transaction->id,
        'successful'
    ]);
} else {
    echo "Error: " . $result->message;
    print_r($result->errors);
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
        <input type="text" disabled="disabled" name="Id" value="<?php echo htmlspecialchars($result->transaction->id); ?>"><br>
        <label for="status" class="heading">Status</label><br>
        <input type="text" disabled="disabled" name="status" value="successful"><br>
    </form>
</body>
</html>
