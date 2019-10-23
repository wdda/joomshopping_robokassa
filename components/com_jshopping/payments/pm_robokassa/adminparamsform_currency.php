<style type="text/css">
.key{
	background-color: #f6f6f6;
	text-align: right;
	width: 140px;
	color: #666;
	font-weight: bold;
	border-bottom: 1px solid #e9e9e9;
	border-right: 1px solid #e9e9e9;
}
</style>
<div class="col100">
  <fieldset class="adminform">
    <table class="admintable" width = "100%" >
       <tr>
          <td class="key" height="30px;" colspan="4">
              <? echo $message;?>
          </td>
      <tr>
      <td width="250px" class="key" rowspan="<?php echo sizeof($currency->getAllCurrencies())+2; ?>">
      <?php echo  _JSHOP_ROBOKASSA_CURR; ?>
      </td>
      </tr>
       <tr>
          <td style="width:250px;" class="key">
              <b><?php echo _JSHOP_ROBOKASSA_CURRENCY; ?></b>
          </td>
          <td class="key">
              <b><?php echo _JSHOP_ROBOKASSA_GIVEN_VALUE; ?></b>
          </td>
          <td class="key">
              <b><?php echo _JSHOP_ROBOKASSA_INSIDE_EXCHANGE_RATE; ?></b>
          </td>
      </tr>
<?php              
    foreach ($currency->getAllCurrencies() as $currency_id => $this_currency)
    {
?>
      <tr>
         <td class="key">
            <?php echo $this_currency->currency_name; ?>
         </td>
<?php
    if ($loaded == 1) 
    {
        echo "<td class='key'><select name='pm_params[currency_".$this_currency->currency_code_iso."]'>";
        foreach($xml->document->_children as $id => $first_child) 
        {
            foreach ($first_child->_children as $group_id => $groups)
            {
                if(!empty($groups->_attributes['description']))
                {
                    echo '<optgroup label="'.$groups->_attributes['description'].'">';
                    foreach($groups->_children as $items_id =>$items)
                    {
                        foreach($items->_children as $last_id => $last)
                        {
                            echo '<option ';
                            if($params['currency_'.$this_currency->currency_code_iso]==$last->_attributes['label']) 
                                echo 'selected="selected" ';
                            echo 'value="'.$last->_attributes['label'].'">'.$last->_attributes['label'].' '.$last->_attributes['name'].'</option>';
                        }
                    }
                echo '</optgroup>';
                }
            }
        }
        echo "</select></td>";
    }
    else 
    {
        $error_msg = _JSHOP_ROBOKASSA_ERROR_OPEN_FILE . $currencies_file;
        echo $error_msg;
    }
        echo '<td class="key"><input readonly="readonly" name="pm_params[exchange_'.$this_currency->currency_code_iso.']" value="'.$this_currency->currency_value.'"/></td>';
    }
?>
        </tr>
     </table>
  </fieldset>
</div>
<div class="clr"></div>

