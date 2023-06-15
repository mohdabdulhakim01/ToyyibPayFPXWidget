<?php

require_once("ToyyibPayFPXWidget.php");
// The important of this function is to fill required data to create bill and to generate the widget. You can apply the same code on laravel controller or any php code
Class FpxTest{
    public function run(){
        $payment = new ToyyibPayFPXWidget();
        $payment->userSecretKey = 'yourSecretKey';
        $payment->categoryCode = 'aa72byra';
        $payment->billName = 'Nama Produk '.rand();
        $payment->billDescription = 'Deskripsi Produk';
        $payment->billPriceSetting = 0;
        $payment->billPayorInfo = 1;
        $payment->billAmount = 100;
        $payment->billReturnUrl = 'http://localhost/website/payment_hook.php';
        $payment->billCallbackUrl = 'http://localhost/website/payment_callback.php';
        $payment->billExternalReferenceNo = time().rand();
        $payment->billTo = 'Mohammad Abdul Hakim';
        $payment->billEmail = 'mohdabdulhakim01@gmail.com';
        $payment->billPhone = '0143032619';
        $payment->billSplitPayment = 0;
        $payment->billSplitPaymentArgs = '';
        $payment->billPaymentChannel = '0';
        $payment->billContentEmail = 'Thank you for purchasing our product!';
        $payment->billChargeToCustomer = 0;
        $payment->billExpiryDate = $payment->expiredDate(3);
        $payment->billExpiryDays = 3;
        $payment->dev_mode = true;
        // Override Mode
        // If you want to reuse same bill code for testing. and skip generating new bill code. you can use this mode
        $payment->override_mode = false; // default is false. 
        // $payment->override_billCode = $_GET['billCode']; // you can use one of your bill code for keep repeated test.
        echo $payment->generateWidget();
        
    }
}
$fpxTest = new FpxTest();
$fpxTest->run();
