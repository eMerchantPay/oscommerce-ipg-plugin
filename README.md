oscommerce-emerchantpay-plugin
==============================

OSCommerce plugin that integrates eMerchantPay hosted payment form as a payment method

Installation
==============================
Copy the files:

1) Open the zip file and copy the following files:

oscommerce-emerchantpay-plugin/include/modules/payment/emp.php
oscommerce-emerchantpay-plugin/include/modules/payment/ParamSigner.class

TO 

YourOSCommerceFolder/include/modules/payment/

2) Copy:

oscommerce-emerchantpay-plugin/languages/english/modules/payment/emp.php 

TO 

YourOSCommerce/include/languages/english/modules/payment


Administrator Configuration
==============================
1) Log on to your Administrator Login of OSCommerce

2) Go to Modules >> Payment.

3) You will have a new payment module called eMerchantPay/eMPPay Credit Card eComm Payment form on the payment module list.

4) Click on eMerchantPay/eMPPay Credit Card eComm Payment form Click on Install on the right hand side menu that will appear.

5) Once it’s done, click on Edit and configure your details then click Update to save the settings.


Using the plugin
==============================
After the instalation and configuration the plugin will appear as eMerchantPay/eMPPay Credit Card eComm Payment form.
Please note the name could be changed from the file:

YourOSCommerce/include/languages/english/modules/payment/emp.php


Completing Purchase
==============================
After click Confirm Order a new window will appear containing the payment form.
