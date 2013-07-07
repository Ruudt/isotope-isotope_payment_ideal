<div class="ideal_payment ideal_payment_basic">
	<h2><?php echo $this->name; ?></h2>
	<p id="idealComment"><?php echo $this->message; ?></p>
	<form id="payment_form" method="post" action="<?php echo $this->action; ?>">
		<input type="hidden" name="merchantID" value="<?php echo $this->acceptant_id; ?>" />
		<input type="hidden" name="subID" value="<?php echo $this->sub_id; ?>" />
		<input type="hidden" name="amount" value="<?php echo $this->amount; ?>" />
		<input type="hidden" name="purchaseID" value="<?php echo $this->order_id; ?>" />
		<input type="hidden" name="language" value="<?php echo strtolower($this->language); ?>" />
		<input type="hidden" name="currency" value="<?php echo $this->currency; ?>" />
		<input type="hidden" name="description" value="<?php echo $this->order_description; ?>" />
		<input type="hidden" name="hash" value="<?php echo $this->sha; ?>" />
		<input type="hidden" name="paymentType" value="<?php echo $this->payment_type; ?>" />
		<input type="hidden" name="validUntil" value="<?php echo $this->valid_until; ?>" />
		<input type="hidden" name="itemNumber1" value="1" />
		<input type="hidden" name="itemDescription1" value="<?php echo $this->order_description; ?>" />
		<input type="hidden" name="itemQuantity1" value="1" />
		<input type="hidden" name="itemPrice1" value="<?php echo $this->amount; ?>" />
		<input type="hidden" name="urlSuccess" value="<?php echo $this->url_success; ?>" />
		<input type="hidden" name="urlCancel" value="<?php echo $this->url_cancel; ?>" />
		<input type="hidden" name="urlError" value="<?php echo $this->url_error; ?>" />
		<input type="hidden" name="urlService" value="<?php echo $this->url_service; ?>" />
		<div class="submit_container"><input class="submit" type="submit" value="<?php echo $this->submit_value; ?>" /></div>
	</form>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
window.addEvent( \'domready\' , function() {
   $(\'payment_form\').submit();
});
//--><!]]>
</script>
</div>