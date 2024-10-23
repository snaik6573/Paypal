<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paypal Payment Gateway</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://js.braintreegateway.com/js/braintree-2.31.0.min.js"></script>
</head>
<style>
  form {
    width: 300px;
    box-shadow: 0 4px 10px gray;
    margin-left: auto;
    margin-top: 30px;
    margin-right: auto;
    padding: 10px;
  }
  #dropin-container {
    margin-top: 20px;
  }
  label.heading {
    font-size: 20px;
    font-weight: 400;
  }
  button {
    background-color: #008CBA;
    border: none;
    border-radius: 5px;
    height: 40px;
    font-size: 15px;
    font-weight: 600;
    width: 160px;
    color: white;
    cursor: pointer;
  }
  button:hover {
    background-color: #04AA6D;
  }
</style>

<script>
    $.ajax({
        url: "token.php",
        type: "get",
        dataType: "json",
        success: function(data) {
            braintree.setup(data, 'dropin', { container: 'dropin-container' });
        }
    });
</script>
<body style="text-align:center;">
    <form action="payment.php" method="post" class="payment-form">
        <label for="firstname" class="heading">Firstname</label><br>
        <input type="text" name="firstname" placeholder="Enter firstname" required pattern="[a-zA-Z]+" title="Firstname must contain only letters"><br><br>
        
        <label for="lastname" class="heading">Lastname</label><br>
        <input type="text" name="lastname" placeholder="Enter lastname" required pattern="[a-zA-Z]+" title="Lastname must contain only letters"><br><br>
        
        <label for="cardholder-name" class="heading">Cardholder Name</label><br>
        <input type="text" name="cardholder_name" placeholder="Enter cardholder name" required title="Cardholder name is required"><br><br>
        
        <label for="amount" class="heading">Amount</label><br>
        <input type="number" name="amount" placeholder="Enter amount" min="1" step="0.01" title="Amount must be a positive number" required><br>
        
        <div id="dropin-container"></div>
        <br><br>
        
        <button type="submit">Pay with Braintree</button>
    </form>
</body>
</html>
