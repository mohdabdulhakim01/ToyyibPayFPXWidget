<?php


/*
This is Toyyibpay widget for FPX payment. This library does not under ToyyibPay directly. So any changes over the original API might influence this file.
They way toyyibpay integration work is pretty easy based on official documentation
1. You generate automatic bill using php code given. and the bill will be generated. payment gateway link will be return which is bill code.
2. You need to open the payment gateway link and proceed for bank payment. After that you will return to the web hook link which is your own server file/url to control end process of transaction.


This plugin is only to generate widget on your own page so that you can control the Payment button design without going to payment gateway page.
You can use this class in your laravel. set some route and then setup some required variable and call some function to render the html to the page.
you can use javascript fetch or ajax to get the rendered data to your html page.

*/


class ToyyibPayFPXWidget
{
    public $userSecretKey = '';
    public $categoryCode = '';
    public $billName = '';
    public $billDescription = '';
    public $billPriceSetting = '';
    public $billPayorInfo = '';
    public $billAmount = '';
    public $billReturnUrl = '';
    public $billCallbackUrl = '';
    public $billExternalReferenceNo = '';
    public $billTo = '';
    public $billEmail = '';
    public $billPhone = '';
    public $billSplitPayment = '';
    public $billSplitPaymentArgs = '';
    public $billPaymentChannel = '';
    public $billContentEmail = '';
    public $billChargeToCustomer = '';
    public $billExpiryDate = '';
    public $billExpiryDays = '';
    public $dev_mode = true;
    public $billCode = '';
    public $override_mode = false;
    public $override_billCode = '';
    public function expiredDate($day_count)
    {
        // Tarikh expired diset ke 12 Pagi .
        // Fungsi ini adalah shortcut untuk variable $billExpiryDate
        $expired_date = date('d-m-Y', (86400 * 3) + time());
        return "$expired_date 00:00:00";
    }
    public function generateWidget()
    {
        // bill record will be generate on your toyyibpay account and bill data will be bind with toyyibpay widget.

        $this->billCode = $this->override_billCode; // if Override Mode is False, this variable will be rewritten with new generated bill.

        if ($this->override_mode == false) {
            $payment_result = $this->requestBill();
            $billCode = (object) $payment_result[0];
            $billCode = $billCode->BillCode;
            $this->billCode = $billCode;
        }
        $source_data = file_get_contents('test_bank_widget.html');
        $source_data = $this->modWidgetCont($source_data, 'dev_mode_site', $this->getDevModSite());
        $source_data = $this->modWidgetCont($source_data, 'bill-code', $this->billCode);
        $source_data = $this->modWidgetCont($source_data, 'bill-data', $this->arrToHtmlHid($this->getBillDataToyibGate()));
        return $source_data;
    }
    public function modWidgetCont($source_data, $target_variable, $value)
    {
        return str_replace("[$target_variable]", $value, $source_data);
    }
    public function getDevModSite()
    {
        $dev_mode_site = ($this->dev_mode == true) ? 'dev.' : '';
        return $dev_mode_site;
    }
    public function requestBill()
    {

        $bill_post_data = array(
            'userSecretKey' => $this->userSecretKey,
            'categoryCode' => $this->categoryCode,
            'billName' => $this->billName,
            'billDescription' => $this->billDescription,
            'billPriceSetting' => $this->billPriceSetting,
            'billPayorInfo' => $this->billPayorInfo,
            'billAmount' => $this->billAmount,
            'billReturnUrl' => $this->billReturnUrl,
            'billCallbackUrl' => $this->billCallbackUrl,
            'billExternalReferenceNo' => $this->billExternalReferenceNo,
            'billTo' => $this->billTo,
            'billEmail' => $this->billEmail,
            'billPhone' => $this->billPhone,
            'billSplitPayment' => $this->billSplitPayment,
            'billSplitPaymentArgs' => $this->billSplitPaymentArgs,
            'billPaymentChannel' => $this->billPaymentChannel,
            'billContentEmail' => $this->billContentEmail,
            'billChargeToCustomer' => $this->billChargeToCustomer,
            'billExpiryDate' => $this->billExpiryDate,
            'billExpiryDays' => $this->billExpiryDays
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->getDevModSite() . 'toyyibpay.com/index.php/api/createBill');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bill_post_data);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $obj = json_decode($result);
        return $obj;
    }
    public function arrToHtmlHid($array_data)
    {
        $html_data = '';
        if(is_array($array_data)){
            foreach ($array_data as $key => $html_element) {
                $html_data .= "<input type=\"hidden\" name=\"" . $key . "\" value=\"$html_element\">\n";
            }

        }
        if($html_data == ''){
            $html_data = '<span style="color:red">The Bill Code is not exist or already expired...</span><br>';
        }
        return $html_data;
    }
    public function getBillData()
    {

        $jsonData = "
        {
            \"userSecretKey\":\"$this->userSecretKey\",
            \"categoryCode\":\"$this->categoryCode\",
            \"billName\":\"$this->billName\",
            \"billDescription\":\"$this->billDescription\",
            \"billPriceSetting\":\"$this->billPriceSetting\",
            \"billPayorInfo\":\"$this->billPayorInfo\",
            \"billAmount\":\"$this->billAmount\",
            \"billReturnUrl\":\"$this->billReturnUrl\",
            \"billExternalReferenceNo\":\"$this->billExternalReferenceNo\",
            \"billTo\":\"$this->billTo\",
            \"billEmail\":\"$this->billEmail\",
            \"billPhone\":\"$this->billPhone\",
            \"billSplitPayment\":\"$this->billSplitPayment\",
            \"billSplitPaymentArgs\":\"$this->billSplitPaymentArgs\",
            \"billPaymentChannel\":\"$this->billPaymentChannel\",
            \"billContentEmail\":\"$this->billContentEmail\",
            \"billChargeToCustomer\":\"$this->billChargeToCustomer\",
            \"billExpiryDate\":\"$this->billExpiryDate\",
            \"billExpiryDays\":\"$this->billExpiryDays\"
        }
        
        ";
        return $jsonData;
    }


    public function getBillDataToyibGate()
    {
        

        $curl = curl_init();
        $url = "https://" . $this->getDevModSite() . "toyyibpay.com/$this->billCode";  // Replace with the desired URL
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $htmlContent = curl_exec($curl);
        curl_close($curl);

        // Step 2: Parse HTML content
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML($htmlContent);

        $redirectData = [
            "payment_amount", "payment_harga", "packageRate", "packageRange", "UserPackage", "PriceSetting", "PaymentCharge", "ChargeToCustomer", "totalAfterCharges", "payment_ref_no", "payment_description", "name", "email", "phone", "billID",
            "billPermalink", "SingleBill", "LastAttempt", "bankID", "channel", "web_return_address", "private_key", "billASPCode", "fpxfee", "bank"
        ];

        // Step 3: Find <input type="hidden"> elements
        $inputElements = $dom->getElementsByTagName('input');
        $jsonFormat = '';
        // why need to convert from this to json and to array. firstly this code is tested on different file.then remerge in this class. i lazy to rewrite it.
        foreach ($inputElements as $key => $inputElement) {
            // Step 4: Replace type attribute with PHP variable
            $inpType = $inputElement->getAttribute('type');
            $inpName = $inputElement->getAttribute('name');
            if (in_array($inpType, ['hidden', 'text']) &&  in_array($inpName, $redirectData)) {
                // Step 5: Retrieve the value of the modified input element
                $modifiedValue = $inputElement->getAttribute('value');
                // echo "$inpName : $modifiedValue<br>";

                if ($key == 0) {
                    $jsonFormat .= "\"$inpName\" : \"$modifiedValue\"";
                } else {
                    $jsonFormat .= ",\"$inpName\" : \"$modifiedValue\"";
                }
            }
        }
        $jsonFormat = "{ $jsonFormat }";
        $gateway_data = (array) json_decode($jsonFormat);
        return $gateway_data;
    }
}
