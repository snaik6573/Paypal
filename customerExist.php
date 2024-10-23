<?php

require 'vendor/autoload.php';
use Braintree\Gateway;

$gateway = new Gateway([
    'environment' => 'sandbox',
    'merchantId' => 'sk39ptqh27m5jnnv',
    'publicKey' => 'xk5mf5hjy5qv57yp',
    'privateKey' => '8a036ed45e5f5c0a6803468613c1926c',
]);

$customer_id = '54587814549';

try {
    $customer = $gateway->customer()->find($customer_id);
    $payment_methods = $customer->paymentMethods;
} catch (Exception $e) {
    die("Error fetching customer: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Method</title>
    <style>
        body { 
            text-align: center;
            width: 500px;
            box-shadow: 0 4px 10px gray;
            margin-left: auto;
            margin-top: 30px;
            margin-right: auto;
            padding: 10px;
        }
        label {
             display: flex; 
             align-items: center; 
             margin: 10px 0;
        } 
        input[type="radio"] { 
            margin-right: 10px;
        }
        h1{
            color:maroon;
        }
    </style>
        <script>
        function redirectToNewPayment() {
            window.location.href = 'main.php';
        }
        function redirectToCardDetails(paymentMethodNonce) {
    window.location.href = 'carddetails.php?payment_method_nonce=' + paymentMethodNonce;
}

    </script>
</head>
<body>
    <h1>Select Your Payment Method</h1>
    <form action="newpayment.php" method="post">
        <?php if (!empty($payment_methods)): ?>
            <?php foreach ($payment_methods as $payment_method): ?>
                <label>
            <input type="radio" name="payment_method_nonce" value="<?php echo htmlspecialchars($payment_method->token); ?>" required onclick="redirectToCardDetails('<?php echo htmlspecialchars($payment_method->token); ?>')">
            Card ending with <?php echo htmlspecialchars($payment_method->last4); ?><img src="<?php echo htmlspecialchars($payment_method->imageUrl); ?>" alt="Card icon" style="width: 40px; margin-left:20px;">
        </label>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No existing payment methods found.</p>
        <?php endif; ?>

        <label>
            <input  type="radio" name="new_payment" value="new_payment" required  onclick="redirectToNewPayment()">
            Do you want to use a new payment method?
        </label>

    </form>
</body>
</html>