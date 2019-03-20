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
    
    // Stripe: to use the bindings, include the init.php file
    require_once("stripe-php-6.31.0/init.php");
    
    /** 
     * Reseller Club Key 
     * 
     * Replace ur 32 bit secure key , Get your secure key from your Reseller Control panel
     */
    
    $key = "Enter your Resellerclub key"; 
    
    /** 
     * Stripe Publishable key
     * Stripe secret Key or Encryption Key
     */
    
    $stripe = [
      "secret_key"      => "Enter your Stripe Secret Key here",
      "publishable_key" => "Enter your Stripe Publishable Key here",
    ];
    
    \Stripe\Stripe::setApiKey($stripe['secret_key']);