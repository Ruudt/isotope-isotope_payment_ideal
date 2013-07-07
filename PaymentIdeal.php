<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Class PaymentIdeal
 */
class PaymentIdeal extends Controller
{
	/**
	 * Add payment info to e-mail if available
	 */
	public function opreCheckout($objOrder, $objCart)
	{
		return $true;
	}
	
	/**
	 * Add iDEAL variables to e-mail so payment info can be inserted into email
	 *
	 * acquirerID: ##acquirerID##
	 * consumerName: ##consumerName##
	 * consumerIBAN: ##consumerIBAN##
	 * consumerBIC: ##consumerBIC##
	 * amount: ##amount##
	 * currency: ##currency##
	 * statusDateTime: ##statusDateTime
	 * transactionID: ##transactionID#
	 * status: ##status##
	 */
	public function preCheckout(&$objOrder, $objCart)
	{
		$payment_data = deserialize($objOrder->payment_data);
		if (is_array($payment_data['POSTSALE']))
		{
			foreach ($payment_data['POSTSALE'] as $key => $value)
			{
				if (is_object($value))
				{
					$payment_data['POSTSALE'][$key] = serialize($value);
				}
			}
			$objOrder->email_data = array_merge($objOrder->email_data, $payment_data['POSTSALE']);
		}

		return $true;
	}
}