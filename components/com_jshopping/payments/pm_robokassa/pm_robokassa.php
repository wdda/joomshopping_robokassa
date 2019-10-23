<?php
defined('_JEXEC') or die('Restricted access');



class pm_robokassa extends PaymentRoot
{
    
    function showPaymentForm($params, $pmconfigs)
    {
        include(dirname(__FILE__)."/paymentform.php");
    }

    function showAdminFormParams($params)
    {
        $jmlThisDocument = & JFactory::getDocument();
        switch ($jmlThisDocument->language) 
        {
            case 'en-gb': include(JPATH_SITE.'/administrator/components/com_jshopping/lang/en-GB_robokassa.php'); $language = 'en'; break;
            case 'ru-ru': include(JPATH_SITE.'/administrator/components/com_jshopping/lang/ru-RU_robokassa.php'); $language = 'ru'; break;
            default: include(JPATH_SITE.'/administrator/components/com_jshopping/lang/ru-RU_robokassa.php');
        }
        $array_params = array('testmode', 'email_received', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status', 'password_1', 'password_2', 'login');
        foreach ($array_params as $key)
            if (!isset($params[$key])) 
                $params[$key] = '';
        $orders = &JModelLegacy ::getInstance('orders', 'JshoppingModel');
        $currency = &JModelLegacy ::getInstance('currencies', 'JshoppingModel'); 
        


        $xmlfile = file_get_contents('http://merchant.roboxchange.com/WebService/Service.asmx/GetCurrencies?MerchantLogin='.$params['login'].'&Language='.$language);
        $file = JPATH_SITE.'/components/com_jshopping/payments/pm_robokassa/personal_currencies.xml';
        $f = fopen($file, 'w+'); 
        fwrite($f,$xmlfile); 
        fclose($f);
		
		$xml_test = simplexml_load_file($file);
       
        
        if ($xml_test) 
        {
            if($xml_test->Result->Code[0] == 2)
            {
                $currencies_file = JPATH_SITE.'/components/com_jshopping/payments/pm_robokassa/default_currencies.xml';
                $message = '<span style="color: #A84040; padding: 20px;">'._JSHOP_ROBOKASSA_DEFAULT_PARAMETRS.'</span>';
            }
            else
            {
                $currencies_file = JPATH_SITE.'/components/com_jshopping/payments/pm_robokassa/personal_currencies.xml';
                $message = '<span style="color: #50A840; padding: 20px;">'._JSHOP_ROBOKASSA_PERSONAL_PARAMETRS.'</span>';
            }
        }
        else
            echo '<span style="color: #A84040; padding: 20px;">'._JSHOP_ROBOKASSA_ERROR_PHP_INI.'</span>';
			$xml = simplexml_load_file($currencies_file);
        if($xml)
            $loaded = 1;
        else
            $loaded = 0;
        include(dirname(__FILE__)."/adminparamsform.php");	
        
        jimport('joomla.html.pane');
		

        //$pane =& JPane::getInstance('Tabs');
      //  echo $pane->endPanel();
        //echo $pane->startPanel('Настройка валют', 'third-tab');

		include(dirname(__FILE__)."/adminparamsform_currency.php");	  
        //echo $pane->endPane();
    }

    function checkTransaction($pmconfigs, $order, $act)
    {
        $jshopConfig = &JSFactory::getConfig();
        if ($pmconfigs['testmode'])
            $host = "http://test.robokassa.ru/Index.aspx";
        else
            $host = "https://merchant.roboxchange.com/Index.aspx";
        $mrh_pass1 =  $pmconfigs['password_1'];
        $mrh_pass2 = $pmconfigs['password_2'];
        $tm=getdate(time()+9*3600);
        $date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";
        
        // read parameters
        $out_summ = $_POST["OutSum"];
        $inv_id = $_POST["InvId"];
        $shp_item = $_POST["Shp_item"];
        $crc = $_POST["SignatureValue"];
        $crc = strtoupper($crc);
        $my_crc_complete = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item"));
        $my_crc_pending = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
        
        // check signature
        if ($my_crc_pending ==$crc)
        {
            // success
            echo "OK$inv_id\n";
            
            // save order info to file
            $f=@fopen("order.txt","a+") or
            die("error");
            fputs($f,"order_num :$inv_id;Summ :$out_summ;Date :$date\n");
            fclose($f);
            saveToLog("payment.log", "Status pending. Order ID ".$order->order_id);
            return array(2, $_POST['InvId']);
        }
        else
        {
            if ($my_crc_complete == $crc)
            {
                $f=@fopen("order.txt","r+") or die("error");
                while(!feof($f))
                {
                    $str=fgets($f);
                    $str_exp = explode(";", $str);
                    if ($str_exp[0]=="order_num :$inv_id")
                        return array(1, $_POST['InvId']);
                }
                fclose($f);
            }
            else
                return array(0, "bad sign\n".$_POST['InvId']);
        }

	}

	function showEndForm($pmconfigs, $order)
	{
        $jshopConfig = &JSFactory::getConfig();        
        $item_name = sprintf(_JSHOP_PAYMENT_NUMBER, $order->order_number);
        
        if ($pmconfigs['testmode'])
            $host = "http://test.robokassa.ru/Index.aspx";
        else
            $host = "https://merchant.roboxchange.com/Index.aspx";
            
        $email = $pmconfigs['email_received'];
        $notify_url = JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_robokassa";
        $return = JURI::root(). "index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_robokassa";
        $cancel_return = JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_robokassa";
        
        $_country = &JTable::getInstance('country', 'jshop');
        $_country->load($order->country);
        $country = $_country->country_code_2;
        
        // registration info (login, password #1)
        $mrh_login = $pmconfigs['login'];
        $mrh_pass1 = $pmconfigs['password_1'];

        // number of order
        $inv_id = $order->order_id;

        // order description
        $inv_desc = 'good #'.$order->order_id;

        // sum of order
        $out_summ = $order->order_total / $order->currency_exchange;

        // code of goods
        $shp_item = "2";

        // convert to default payment e-currency
        $in_curr = $pmconfigs['currency_'.$order->currency_code_iso];

        // language
        if(empty($_GET['lang']))
            $culture = 'ru';
        else
            $culture = $_GET['lang'];

        // generate signature
        $crc  = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");

?>
        <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=UTF-8" />          
        </head>        
        <body>
            <form id="paymentform" action="<?php print $host?>" name = "paymentform" method = "post">
                <input type='hidden' name='cmd' value='_xclick'>
                <input type=hidden name=MrchLogin value='<?php print $mrh_login?>'>
                <input type=hidden name=SignatureValue value='<?php print $crc ?>'>
                <input type=hidden name=Culture value='<?php print $culture ?>'>
                <input type='hidden' name='business' value='<?php print $email?>'>        
                <input type='hidden' name='notify_url' value='<?php print $notify_url?>'>
                <input type=hidden name=IncCurrLabel value='<?php print $in_curr ?>'>
                <input type='hidden' name='return' value='<?php print $return ?>'>
                <input type=hidden name=Shp_item value='<?php print $shp_item ?>'>
                <input type='hidden' name='cancel_return' value='<?php print $cancel_return?>'>
                <input type=hidden name=InvId value='<?php print $inv_id?>'>
                <input type='hidden' name='rm' value='2'>
                <input type='hidden' name='handling' value='0.00'>
                <input type='hidden' name='tax' value='0.00'>        
                <input type='hidden' name='no_shipping' value='1'>
                <input type='hidden' name='no_note' value='1'>
                <input type='hidden' name='item_name' value='<?php print $item_name;?>'>
                <input type='hidden' name='item_number' value='<?php print $order->order_id?>'>
                <input type='hidden' name='OutSum' value='<?php print $out_summ?>'>
            </form>        
            <?php print _JSHOP_REDIRECT_TO_PAYMENT_PAGE ?>
        <br>
        <script type="text/javascript">document.getElementById('paymentform').submit();</script>
        </body>
        </html>
        <?php
        die();
      }
	
    function getUrlParams($pmconfigs)
    {                        
        $params = array(); 
        $params['order_id'] = JRequest::getInt("InvId");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
        return $params;
    }
    
}
?>