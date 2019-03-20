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
    require("lib/functions.php");	//file which has required functions
    //include('crypto.php');
    
    error_reporting(0);
		
	//This filter removes data that is potentially harmful for your application. It is used to strip tags and remove or encode unwanted characters.
	$_GET = filter_var_array($_GET, FILTER_SANITIZE_STRING);
		
	// Below are the parameters which will be passed from foundation as http GET request
	$paymentTypeId = $_GET["paymenttypeid"];  // payment type id
	$transId = $_GET["transid"];	// This refers to a unique transaction ID which we generate for each transaction
	$userId = $_GET["userid"];      // userid of the user who is trying to make the payment
	$userType = $_GET["usertype"];  // This refers to the type of user perofrming this transaction. The possible values are "Customer" or "Reseller"
	$transactionType = $_GET["transactiontype"];  //Type of transaction (ResellerAddFund/CustomerAddFund/ResellerPayment/CustomerPayment)
	$invoiceIds = $_GET["invoiceids"];	// comma separated Invoice Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
	$debitNoteIds = $_GET["debitnoteids"];	// comma separated DebitNotes Ids, This will have a value only if the transactiontype is "ResellerPayment" or "CustomerPayment"
	$description = $_GET["description"];
	$sellingCurrencyAmount = $_GET["sellingcurrencyamount"]; //This refers to the amount of transaction in your Selling Currency
    $accountingCurrencyAmount = $_GET["accountingcurrencyamount"]; //This refers to the amount of transaction in your Accounting Currency
	$redirectUrl = $_GET["redirecturl"];  // This is the URL on our server, to which you need to send the user once you have finished charging him
	$checksum = $_GET["checksum"];	 // checksum for validation

	/* custom code starts here */
	
	$order_id = $transId;
    $amount = $sellingCurrencyAmount * 100;
    $cancel_url = $redirect_url;
    $billing_name = $_GET["name"];
    $billing_company = $_GET["company"];
    $billing_email = $_GET["emailAddr"];
    $billing_tel = $_POST["telNoCc"] . $_GET["telNo"];
    $currency = "gbp";
	
	/* custom code ends here */
	
	if(verifyChecksum($paymentTypeId, $transId, $userId, $userType, $transactionType, $invoiceIds, $debitNoteIds, $description, $sellingCurrencyAmount, $accountingCurrencyAmount, $key, $checksum)) {
		/** 
		* since all these data has to be passed back to foundation after making the payment you need to save these data
		*	
		* You can make a database entry with all the required details which has been passed from foundation.  
		*
		*							OR
		*	
		* keep the data to the session which will be available in postpayment.php as we have done here.
		*
		* It is recommended that you make database entry.
		**/

		$_SESSION['redirecturl'] = $redirectUrl;
		$_SESSION['transid'] = $transId;
		$_SESSION['sellingcurrencyamount'] = $sellingCurrencyAmount;
		$_SESSION['accountingcurrencyamount'] = $accountingCurrencyAmount;
		$_SESSION['invoice'] = 'INV'.$invoiceIds;
		$_SESSION['amount'] = $amount;
		$_SESSION['description'] = $description;
		$checksumStatus = 1;
    }
	else {
		/**This message will be dispayed in any of the following case
		*
		* 1. You are not using a valid 32 bit secure key from your Reseller Control panel
		* 2. The data passed from foundation has been tampered.
		*
		* In both these cases the customer has to be shown error message and shound not
		* be allowed to proceed  and do the payment.
		*
		**/
		$checksumStatus = 0;
		//$base_url="";			
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Stripe Payment Gateway</title>
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
   
  </head>
  <body style="background:#ecf0f1;">
 	<section id="transMessageSec" class="container" style="background:#fbfbfb;border:1px solid #ccc;margin-top:32px;padding:2% 2% 15px;border-radius:10px;">
       <?php if($checksumStatus){ ?>
		<!--TRANSACTION MESSAGE-->  
		<div class="row">
            <div id="messageDiv" class="col-md-12 text-center">
			    <h2><strong>Confirm <?= '£' . $sellingCurrencyAmount ?> Payment</strong></h2>
			    <h3><?= ( ! isset($description) ? $transId : $description ) ?></h3>
			    <p>Please confirm the above payment information is correct.  
			     Loggcity Limited and/or any of its sister companies/divisions is PCI-compliant and does not keep your payment information on their system.</p>
			     <h5>Click the button below continue payment</h5>
			    <p><br></p>
			</div>
		</div>
        <div class="row">
			<div id="messageDiv" class="col-md-12 text-center">
               <form name="paymentpage" action="postpayment.php" method="POST">
					<input type="hidden" id="stripeToken" name="stripeToken" />
					<input type="hidden" id="stripeEmail" name="stripeEmail" />
					    
                    <script
                        src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                        data-key="<?= $stripe['publishable_key'] ?>"
                        data-name="Loggcity Limited"
                        data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                        data-amount="<?= $amount ?>"
                        data-description="<?= $description ?>"
                        data-email="<?= $billing_email ?>"
                        data-locale="auto"
                        data-billing-address="true"
                        data-label="Pay with Stripe"
                        data-panel-label="Pay {{amount}}"
                        data-currency="<?= $currency ?>">
                    </script>
                    <a class="btn btn-danger" style="color:#fff" onClick="javascript:history.go(-1)">&laquo; Go back</a>
                </form>
			    <p>By continue, you agree to all <a href="https://cherec.co.uk/support/legal.php"><?= $_GET["brandName"] ?> terms and policies</a> of service </p>
            </div>
        </div> 
		<?php } ?>
		<?php if(!$checksumStatus){ ?>
		<!--NOTIFICATION MESSAGE-->
		<div class="row">
			<div class="col-sm-3"></div>
			<div id="messageDiv" class="col-md-6">
               <center>
					<div id ="notificationBar" class="alert alert-danger" role="alert">
					<b>Security Error!</b> Illegal access detected or Checksum mismatch !</div>
				</center>
            </div>
			<div class="col-sm-3"></div>
        </div>
    	<?php } ?>
	</section>
	
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
