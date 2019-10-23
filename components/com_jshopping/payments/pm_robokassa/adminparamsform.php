<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
 <tr>
   <td style="width:250px;" class="key">
     <?php echo _JSHOP_TESTMODE;?>
   </td>
   <td>
     <?php              
     print JHTML::_('select.booleanlist', 'pm_params[testmode]', 'class = "inputbox" size = "1"', $params['testmode']);
     echo " ".JHTML::tooltip(_JSHOP_ROBOKASSA_TESTMODE_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo _JSHOP_ROBOKASSA_EMAIL;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[email_received]" size="45" value = "<?php echo $params['email_received']?>" />
     <?php echo JHTML::tooltip(_JSHOP_ROBOKASSA_EMAIL_DESCRIPTION);?>
   </td>
 </tr>
   <tr>
   <td  class="key">
     <?php echo   _JSHOP_ROBOKASSA_PASSWORD_1;?>
   </td>
   <td>
     <input type = "password" class = "inputbox" name = "pm_params[password_1]" size="45" value = "<?php echo $params['password_1']?>" />
     <?php echo JHTML::tooltip(_JSHOP_ROBOKASSA_PASSWORD_2_DESCRIPTION);?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo  _JSHOP_ROBOKASSA_PASSWORD_2;?>
   </td>
   <td>
     <input type = "password" class = "inputbox" name = "pm_params[password_2]" size="45" value = "<?php echo $params['password_2']?>" />
     <?php echo JHTML::tooltip(_JSHOP_ROBOKASSA_PASSWORD_2_DESCRIPTION);?>
   </td>
 </tr>
 <tr>
   <td  class="key">
     <?php echo  _JSHOP_ROBOKASSA_LOGIN;?>
   </td>
   <td>
     <input type = "text" class = "inputbox" name = "pm_params[login]" size="45" value = "<?php echo $params['login']?>" />
     <?php echo JHTML::tooltip(_JSHOP_ROBOKASSA_LOGIN_DESCRIPTION);?>
   </td>
 </tr>
 
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_END;?>
   </td>
   <td>
     <?php              
         print JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_end_status'] );
         echo " ".JHTML::tooltip(_JSHOP_ROBOKASSA_TRANSACTION_END_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_PENDING;?>
   </td>
   <td>
     <?php 
         echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_pending_status']);
         echo " ".JHTML::tooltip(_JSHOP_ROBOKASSA_TRANSACTION_PENDING_DESCRIPTION);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo _JSHOP_TRANSACTION_FAILED;?>
   </td>
   <td>
     <?php 
     echo JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox" size = "1"', 'status_id', 'name', $params['transaction_failed_status']);
     echo " ".JHTML::tooltip(_JSHOP_ROBOKASSA_TRANSACTION_FAILED_DESCRIPTION);
     ?>
   </td>
 </tr>
  <tr>
   <td>
   <?php echo _JSHOP_ROBOKASSA_VERSION; ?>
   </td>
   <td>
<a href="http://wdda.pro"><?php echo _JSHOP_ROBOKASSA_DEVELOPERS; ?></a>
   </td>
 </tr>
</table>
</fieldset>   
<div class="clr"></div>

