<?php
    /**
     * @package  StripeCheckoutReseller
     */
    /**
      Plugin Name: Stripe Payment Gateway for Reseller Club 
      Plugin URI: https://loggcity.africa/item/Stripe-payment-gateway-for-reseller-club
      Description: This extends Reseller Club to accepts money/payments through Stripes Payment gateway on your supersite. 
      File Description: The base configurations of the plugin.
      This file has the following configurations:  Reseller Club Key, Stripe Merchant ID, Stripe Working Key and Stripe Access Code
      Author: Loggcity
      Author URI: https://loggcity.africa
      Version: 1.0.1
      Copyright 2019 Loggcity
      
    ******************************************************************************************
      Copyright (C) 2019 Adewale Adegoroye
    
      This program is free software: you can redistribute it and/or modify
      it under the terms of the GNU General Public License as published by
      the Free Software Foundation, either version 3 of the License, or
      at your option) any later version.
    
      This program is distributed in the hope that it will be useful,
      but WITHOUT ANY WARRANTY; without even the implied warranty of
      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
      GNU General Public License for more details.
    
      You should have received a copy of the GNU General Public License
      along with this program.  If not, see <http://www.gnu.org/licenses/>.
    *******************************************************************************************/ 
    session_start();
    @session_save_path("./");
    
    include('lib/config.php');
    
    //file which has required functions	
    require("lib/functions.php");
    
    $stripeToken = $_POST['stripeToken'];
    $stripeemail = $_POST['stripeEmail'];
    
    $redirectUrl = $_SESSION['redirecturl'];  // redirectUrl received from foundation
	$transId = $_SESSION['transid'];		 //Pass the same transid which was passsed to your Gateway URL at the beginning of the transaction.
	$sellingCurrencyAmount = $_SESSION['sellingcurrencyamount'];
	$accountingCurrencyAmount = $_SESSION['accountingcurencyamount'];
	$amount = $_SESSION['amount'];
	$description = $_SESSION['description'];
	$invoice = $_SESSION['invoice'];  // redirectUrl received from foundation
	
	// Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
        
    // Token is created using Checkout or Elements!
    // Get the payment token ID submitted by the form:
    $token = $stripeToken;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $error = false;
        try {
            if (isset($token)) {
                $customer = \Stripe\Customer::create([
                    'email' => $stripeemail,
                    'description' => $description,
                    'source'  => $token,
                ]);
                
                $charge = \Stripe\Charge::create([
                    'amount' => $amount,
                    'currency' => 'gbp',
                    'description' => $description,
                    'statement_descriptor' => $invoice,
                    'customer' =>$customer
                ]);
            } else {
                throw new Exception("The Stripe Token was not generated correctly");
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        
        if (!$error) {
            $responseMessage = '<div class="alert-message alert-message-success text-center">
                            <h4>Thanks for choosing us!</h4>
                            <p>Please click "Confirm Payment" below to complete transaction </p>
                            <p style="color:dd3333"><em>(Please do not use "Refresh" or "Back" button)</em></p>
                        </div>';
        } else {
            $responseMessage = '<div id ="notificationBar" class="alert alert-danger text-center" role="alert">
                                <b>Alert </b>Something went wrong!
                                <p>'.$error.'</p>
                            </div>';
        }
    }
	//$status = $_REQUEST["status"];	 // Transaction status received from your Payment Gateway
    //This can be either 'Y' or 'N'. A 'Y' signifies that the Transaction went through SUCCESSFULLY and that the amount has been collected.
    //An 'N' on the other hand, signifies that the Transaction FAILED.

	/**HERE YOU HAVE TO VERIFY THAT THE STATUS PASSED FROM YOUR PAYMENT GATEWAY IS VALID.
    * And it has not been tampered with. The data has not been changed since it can * easily be done with HTTP request. 
	**/
		
	srand((double)microtime()*1000000);
	$rkey = rand();
	$status = 'Y';

	$checksum = generateChecksum($transId,$sellingCurrencyAmount,$accountingCurrencyAmount,$status,$rkey,$key);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Stripe Post Payment</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
   
  </head>
<body style="background:#ecf0f1;">
    <section class="container" style="background:#fbfbfb;border:1px solid #ccc;margin-top:32px;padding:2% 2% 15px;border-radius:10px;">
        <div class="row">
    		<div id="messageDiv" class="col-md-12">
               <?= $responseMessage ?>
                <p> </p>   
                
    		    <center>
            	<form name="f1" action="<?php echo $redirectUrl; ?>">
            		<input type="hidden" name="transid" value="<?php echo $transId; ?>">
            		<input type="hidden" name="rkey" value="<?php echo $rkey; ?>">
            	    <input type="hidden" name="checksum" value="<?php echo $checksum; ?>">
            	    <input type="hidden" name="sellingamount" value="<?php echo $sellingCurrencyAmount; ?>">
            		<input type="hidden" name="accountingamount" value="<?php echo $accountingCurrencyAmount; ?>">
            		<input type="hidden" name="status" value="<?php echo $status; ?>">
            		<?php
            		if (!$error) {
            		    echo '<input type="submit" value="Confirm Payment"><BR>';
                    } else {
                        echo '<a class="btn btn-danger" style="color:#fff" href="https://cherec.co.uk/">&laquo; Back to site</a>';
                    }?>
            
            	</form>
    		    </center>
            </div>
        </div>
    </section>
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>