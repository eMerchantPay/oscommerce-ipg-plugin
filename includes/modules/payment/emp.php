<?php

include 'ParamSigner.class'; //Include ParamSigner class

class emp
{
    var $secret;

// class constructor
      function emp() {
      global $order;

      $this->code = 'emp';
      $this->title = MODULE_PAYMENT_EMP_TEXT_TITLE;
      $this->public_title = MODULE_PAYMENT_EMP_TEXT_PUBLIC_TITLE;
      $this->description = MODULE_PAYMENT_EMP_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_EMP_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_EMP_STATUS == 'True') ? true : false);
      if ((int)MODULE_PAYMENT_EMP_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_EMP_ORDER_STATUS_ID;
      }
      if (is_object($order)) $this->update_status();

		$this->form_action_url = MODULE_PAYMENT_EMP_SELECTED;
    } 
	 
// class methods
    function update_status() 
	{
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_EMP_ZONE > 0) ) 
	  {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_EMP_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while (!$check_query->EOF) {
          if ($check_query['zone_id'] < 1) 
		  {
            $check_flag = true;
            break;
          } 
		  elseif ($check_query['zone_id'] == $order->billing['zone_id'])
		  {
            $check_flag = true;
            break;
            $check_query->MoveNext();
          }
        }

        if ($check_flag == false)
		{
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title);
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
	  return false;
    }
 function process_button() {
 
 
 global $HTTP_POST_VARS, $order, $currencies, $currency;
		echo $currency;
		srand ((double) microtime( )*1000000);
		$random_number = 'ORD' . rand( );
		$ip=$_SERVER['REMOTE_ADDR']; 
		//Assigning values with the paramsigner class
		$ps = new Paramsigner;
		$ps->setParam('amount',$amount);
	

	 if (TEST_TRANSACTION == 'Test')  //MENU TEST TRANSACTIO KEY SWITCH
	  {
		$test_transaction = 1;
	  } 
	 
	 if (TEST_TRANSACTION == 'Live') //MENU TEST TRANSACTIO KEY SWITCH
	  {
		$test_transaction = 0;
	  } 
	    
		$client_id = MODULE_PAYMENT_EMP_ID;
		$secret = MODULE_PAYMENT_EMP_KEY;
		$form_id = FORM_ID;
        	$my_currency = $currency;
      		
		$ps->setParam("item_1_unit_price_$currency",number_format($order->info['total'] * $currencies->get_value($my_currency), $currencies->get_decimal_places($my_currency)));
	   
	// Set the secret key
		$ps->setSecret($secret) ;
		$ps->setParam ( "client_id", $client_id);
		$ps->setParam( "form_id",$form_id);
	
	if (MODULE_PAYMENT_EMP_PRODUCT_TYPE == 'Physical')
	{
		$ps->setParam ( "item_1_digital",'0');	
	}
	
	else
	{
		$ps->setParam ( "item_1_digital",'1');
	}
	
	$ps->setParam ("reference",  $random_number  );
	for($i = 0, $n = sizeof ($order->products); $i < $n; $i ++) 
	{
		$description .= $order->products [$i] ['name'] . ' Qty ' . ( int ) $order->products [$i] ['qty'] ;
		$ps->setParam ( 'CF_prod_' . ($i + 1), ( int ) $order->products [$i] ['id'] . '.' . ( int ) $order->products [$i] ['qty'] );
	}

	$ps->setParam( "CF_customer_id", $_SESSION['customer_id']);
  
		$ps->setParam ( "customer_first_name", $order->customer ['firstname'] );
		$ps->setParam ( "customer_last_name", $order->customer ['lastname'] );
		$ps->setParam ( "customer_address", trim ( $order->customer ['street_address'] . ' ' . $order->customer ['suburb'] ) );
		$ps->setParam ( "customer_city", $order->customer ['city'] );
		$ps->setParam ( "customer_state", $order->customer ['state'] );
		$ps->setParam ( "customer_country", $order->customer ['country'] ['iso_code_2'] );
		$ps->setParam ( "customer_postcode", $order->customer ['postcode'] );
		$ps->setParam ( "customer_phone", $order->customer ['telephone'] );
		$ps->setParam ( "customer_email", $order->customer ['email_address'] );
		$ps->setParam ( "test_transaction", $test_transaction );
		$ps->setParam ( "item_1_name", $description);
		$ps->setParam ( "shipping_first_name", $order->delivery ['firstname'] );
		$ps->setParam ( "shipping_last_name", $order->delivery ['lastname'] );
		$ps->setParam ( "shipping_address", trim ( $order->delivery ['street_address'] . ' ' . $order->delivery ['suburb'] ) );
		$ps->setParam ( "shipping_city", $order->delivery ['city'] );
		$ps->setParam ( "shipping_state", $order->delivery ['state'] );
		$ps->setParam ( "shipping_postcode", $order->delivery ['postcode'] );
		$ps->setParam ( "shipping_country", $order->delivery ['country'] ['iso_code_2'] );
		$ps->setParam ( "shipping_phone", $order->customer ['telephone']  );
		$ps->setParam ( "shipping_company", $order->delivery ['company'] );

		$requestquery = $ps->getQueryString();
		$parameters = array ( );
		// Get the querystring
		parse_str ( $ps->getQueryString (), $parameters );
  		$process_button_string = ' ';
	
	foreach ($parameters as $name => $value) 
	{
   		$process_button_string.=tep_draw_hidden_field($name,$value);
	}

	return $process_button_string;
	}

	function before_process() { return false; }	
   
    function after_process() {
		global $HTTP_POST_VARS, $order, $insert_id;
		return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EMP_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
		      }
		      return $this->_check;
    }

    function install()
 {
	tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");

      	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('EnableEnable EMP Module', 'MODULE_PAYMENT_EMP_STATUS', 'True', 'Do you want to accept DWS/ICPay payments?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

      	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account Client ID', 'MODULE_PAYMENT_EMP_ID', 'Enter your account ID here', 'The client account ID of your USD account', '6', '4', now())");
	  
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Account Secret Key', 'MODULE_PAYMENT_EMP_KEY', 'Enter your account secret key here', 'The secret key of your USD account', '6', '4', now())");

	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Assigned Form URL', 'MODULE_PAYMENT_EMP_SELECTED', 'https://payment-2793.emppay.com/payment/form/post', 'Please include /payment/form/post after the URL <br><b> Example:</b></br> https://payment-2793.emppay.com/payment/form/post', '6', '4', now())");
	  
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Assigned Form ID', 'FORM_ID', 'Form ID', 'Form ID', '6', '4', now())");

	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Type', 'TEST_TRANSACTION', 'True', 'Select - Test or Live', '6', '3', 'tep_cfg_select_option(array(\'Test\', \'Live\'), ', now())");
	  
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Select Product Type', 'MODULE_PAYMENT_EMP_PRODUCT_TYPE', 'True', 'Select one of the product types', '6', '3', 'tep_cfg_select_option(array(\'Digital\', \'Physical\'), ', now())");

	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_EMP_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");

	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_EMP_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
	   
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, 
configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_EMP_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");  
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {

      return array('MODULE_PAYMENT_EMP_STATUS','MODULE_PAYMENT_EMP_SELECTED' ,
		  'MODULE_PAYMENT_EMP_ID', 'MODULE_PAYMENT_EMP_KEY', 'FORM_ID', 'TEST_TRANSACTION',
		  'MODULE_PAYMENT_EMP_PRODUCT_TYPE', 'MODULE_PAYMENT_EMP_SORT_ORDER','MODULE_PAYMENT_EMP_ZONE',
		  'MODULE_PAYMENT_EMP_ORDER_STATUS_ID');
 	}
}
?>
