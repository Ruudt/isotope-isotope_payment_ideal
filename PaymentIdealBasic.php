<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Ruud Walraven 2010
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Handle iDEAL basic payments
 *
 * @extends IsotopePayment
 */
class PaymentIdealBasic extends IsotopePayment
{

	/**
	 * Return information or advanced features in the backend.
	 *
	 * Use this function to present advanced features or basic payment information for an order in the backend.
	 *
	 * @access public
	 * @param  int		Order ID
	 * @return string
	 */
	public function backendInterface($orderId) {
		$objOrderInfo = $this->Database->prepare("SELECT * FROM tl_iso_orders WHERE id=?")
										   ->limit(1)
										   ->execute($orderId);
				
		$arrOrderInfo = $objOrderInfo->fetchAssoc();

		$arrPaymentInfo = deserialize($arrOrderInfo['payment_data'], true);
		
		$this->fltOrderTotal = $arrOrderInfo['grandTotal'];
				
		//Get the iDEAL basic configuration data			
		$objAIMConfig = $this->Database->execute("SELECT * FROM tl_iso_payment_modules WHERE type='ideal'");
		if ($objAIMConfig->numRows < 1) {
			return '<i>' . $GLOBALS['TL_LANG']['MSC']['noPaymentModules'] . '</i>';
		}

		$objTemplate = new BackendTemplate('be_pos_terminal');

		if ( $objAIMConfig->ideal_basic_accept_status_risk ) {
			$strResponse = '<p class="tl_gerror">' . $GLOBALS['TL_LANG']['ISO']['ideal_basic_status_unsafe'] . ' ' . $arrPayment['status'] . '</p>';

			$objTemplate->isConfirmation = true;
			
			$arrOrderInfo['payment_data'] = deserialize($arrOrderInfo['payment_data']);

			$this->Input->setGet('uid', $arrOrderInfo['uniqid']);
			$objModule = new ModuleIsotopeOrderDetails($this->Database->execute("SELECT * FROM tl_module WHERE type='iso_orderdetails'"));
			$strOrderDetails = $objModule->generate(true);
			$action = ampersand($this->Environment->request, ENCODE_AMPERSANDS);

			$objTemplate->formId = 'be_pos_terminal';
		
			$return = '<div id="tl_buttons">
<input type="hidden" name="FORM_SUBMIT" value="' . $objTemplate->formId . '" />
<a href="'.ampersand(str_replace('&key=payment', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<h2 class="sub_headline">' . $GLOBALS['TL_LANG']['PAY']['ideal'][0] . '</h2>
<div class="tl_formbody_edit">
<div class="tl_tbox block">';
			$return .= $strResponse.'<div class="info_container payment_status block">
				<h2>' . $GLOBALS['TL_LANG']['ISO']['ideal_payment_information'] . '</h2>';
			if ($arrOrderInfo['payment_data']['status'] == 'success') {
				$return .= '<div class="info">'
						. $GLOBALS['TL_LANG']['ISO']['ideal_status_success']
						. '</div></div>';
			} else {
				$return .= '<div class="info">' . $GLOBALS['TL_LANG']['ISO']['ideal_no_status_success']
						. '</div></div>';
			}
			//$return .= $strOrderDetails;
			$return .= '</div></div>';

			$objTemplate->orderReview = $return;
			$objTemplate->action = $action;
			$objTemplate->rowLast = 'row_' . (count($this->editable) + 1) . ((($i % 2) == 0) ? ' odd' : ' even');						
		} else {
			return '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=payment', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<h2 class="sub_headline">' . $GLOBALS['TL_LANG']['PAY'][$this->type][0] . '</h2>
<div class="tl_formbody_edit">
<div class="tl_tbox block">
<p class="tl_info">' . $GLOBALS['TL_LANG']['ISO']['backendPaymentNoInfoIdealBasic'] . '</p>
</div>
</div>';
		}
		
		return $objTemplate->parse();
	}
	
	/**
	 * Process iDEAL basic status
	 *
	 * @access public
	 * @return void
	 */
	public function processPostSale() {
		return true;
	}

	/**
	 * processPayment function.
	 *
	 * @access public
	 * @return void
	 */
	public function processPayment() {
		$status = 'error';

		// Now deal with the payment
		$objOrder = $this->Database->prepare("SELECT * FROM `tl_iso_orders` WHERE `id`=?")->limit(1)->execute($this->Input->get('purchaseID'));

		if (!$objOrder->numRows) {
			$this->log('Order ID "' . $this->Input->get('purchaseID') . '" not found', 'PaymentIdealBasic processPostSale()', TL_ERROR);
			return;
		}

		// Set the current system to the language when the user placed the order.
		// This will result in correct e-mails and payment description.
		$GLOBALS['TL_LANGUAGE'] = $objOrder->language;
		$this->loadLanguageFile('default');

		// Load / initialize data
		$arrPayment = deserialize($objOrder->payment_data, true);

		// Store request data in order for future references
		$arrPayment['POSTSALE'][] = $_GET;

		$arrData = $objOrder->row();
		$arrData['old_payment_status'] = $arrPayment['status'];
		$arrPayment['status'] = $this->Input->get('response');
		$arrData['new_payment_status'] = $arrPayment['status'];

		// array('success', 'cancel', 'error'),
		switch( $arrPayment['status'] ) {
			case 'success':
				$this->Database->execute("UPDATE tl_iso_orders SET date_payed=" . time() . " WHERE id=" . $objOrder->id);
				$this->Database->prepare("UPDATE tl_iso_orders SET payment_data=? WHERE id=?")->execute(serialize($arrPayment), $objOrder->id);
				$this->log('Ideal (basic) payment success ' . print_r($_GET, true), 'PaymentIdealAdvanced processPayment()', TL_GENERAL);

				// Set the current system to the language when the user placed the order.
				// This will result in correct e-mails and payment description.
				$GLOBALS['TL_LANGUAGE'] = $objOrder->language;
				$this->loadLanguageFile('default');

				if ($this->postsale_mail) {
					try {
						$this->Isotope->overrideConfig($objOrder->config_id);
						$this->Isotope->sendMail($this->postsale_mail, $GLOBALS['TL_CONFIG']['adminEmail'], $GLOBALS['TL_LANGUAGE'], $arrData);
					}
					catch (Exception $e) {}
				}
				
				break;

			case 'cancel':
			case 'error':
			default:
				global $objPage;
				$this->log('Ideal (basic) payment error ' . print_r($_GET, true), 'PaymentIdealAdvanced processPayment()', TL_GENERAL);
				$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'));
				return false;
				break;
		}
	
		return true;
	}


	/**
	 * Return the iDEAL form.
	 *
	 * @access public
	 * @return string
	 */
	public function checkoutForm() {
		$this->import('Isotope');

		$objOrder = $this->Database->prepare("SELECT id, order_id, uniqid, grandTotal FROM tl_iso_orders WHERE cart_id=?")->execute($this->Isotope->Cart->id);

		/*
		 * Removed the code below to get rid of problems with products not adding up the the exact amount offered in order
		 * Also this way it is less affected by changes in the source code.
		 * The code was also faulty in general....
		 * /
		$this->Database->execute("UPDATE tl_iso_orders SET status='".$this->new_order_status."' WHERE id=" . $objOrder->id);

		$arrProducts = $this->Isotope->Cart->getProducts();

		if (!count($arrProducts)) {
		   $this->Template = new FrontendTemplate('mod_message');
		   $this->Template->type = 'empty';
		   $this->Template->message = $GLOBALS['TL_LANG']['MSC']['noItemsInCart'];
		   return;
		}
		
		$amount = 0;
		$itemCount = 1;
		$shaString = '';
		$returnHTML = '';

		// add the products which are required in the POST form by iDEAL
		foreach( $arrProducts as $i => $objProduct ) {
			$itemNumber = ($objProduct->sku ? $objProduct->sku : $objProduct->name);
			$itemDescription = $objProduct->name;
			$itemQuantity = $objProduct->quantity_requested;
			$itemPrice = $this->formatPriceAsCents($objProduct->total_price);

			list($aipAmount, $aipShaString, $aipReturnHTML) = $this->addIdealBasicProduct($itemNumber, $itemDescription, $itemQuantity, $itemPrice, $itemCount++);

			$amount +=  $aipAmount;
			$shaString .= $aipShaString;
			$returnHTML .= $aipReturnHTML;
		}

		// add TAX as a product
		$taxTotal = intval($this->formatPriceAsCents($this->Isotope->Cart->taxTotal));
		if ($taxTotal > 0) {
			$itemNumber = 'TAX';
			$itemDescription = 'TAX';
			$itemQuantity = 1;
			$itemPrice = $taxTotal;

			list($aipAmount, $aipShaString, $aipReturnHTML) = $this->addIdealBasicProduct($itemNumber, $itemDescription, $itemQuantity, $itemPrice, $itemCount++);

			$amount +=  $aipAmount;
			$shaString .= $aipShaString;
			$returnHTML .= $aipReturnHTML;
		}

		// add shipping as a product
		$shippingSurcharges = $this->Isotope->Cart->getShippingSurcharge(array());
		foreach($shippingSurcharges as $surcharge) {
			$itemNumber = 'SHIP';
			$itemDescription = 'SHIP';
			$itemQuantity = 1;
			$itemPrice = $this->formatPriceAsCents($surcharge['total_price']);

			list($aipAmount, $aipShaString, $aipReturnHTML) = $this->addIdealBasicProduct($itemNumber, $itemDescription, $itemQuantity, $itemPrice, $itemCount++);

			$amount +=  $aipAmount;
			$shaString .= $aipShaString;
			$returnHTML .= $aipReturnHTML;
		}

		// add payment as a product
		$paymentSurcharges = $this->Isotope->Cart->getPaymentSurcharge(array());
		foreach($paymentSurcharges as $surcharge) {
			$itemNumber = 'PAY';
			$itemDescription = 'PAY';
			$itemQuantity = 1;
			$itemPrice = $this->formatPriceAsCents($surcharge['total_price']);

			list($aipAmount, $aipShaString, $aipReturnHTML) = $this->addIdealBasicProduct($itemNumber, $itemDescription, $itemQuantity, $itemPrice, $itemCount++);

			$amount +=  $aipAmount;
			$shaString .= $aipShaString;
			$returnHTML .= $aipReturnHTML;
		}
		// */
		
		$grandTotal = round($objOrder->grandTotal * 100);
		$orderDescription = htmlspecialchars($this->ideal_order_description ? $this->ideal_order_description : $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'][2]);

		// Valid until options
		//$today = time();
		//$validUntil = $today + 900; // 15 minutes to pay because of the session on server
		//$validUntil = date("Y-m-dTH:i:s.0000",$validUntil);
		//$validUntil = str_replace("CEST","T",$validUntil);
		//$validUntil = $validUntil."Z";
		$subID = $this->ideal_sub_id; // Almost always 0
		$paymentType = "ideal"; // Always ideal
		$validUntil = time() + 900;

		// build sha string for hashing
		$shaString = $this->ideal_acceptant_key.$this->ideal_acceptant_id.$this->ideal_sub_id.$grandTotal.$objOrder->id.$paymentType.$validUntil.'1' . $orderDescription . '1' . $grandTotal;

		// Remove forbidde characters
		$forbiddenCharacters  = array(" ","\t","\n","&amp;","&lt;","&gt;","&quot;");
		$replaceCharacters = array("","","","&","<","gt-teken","\"");
		$shaString = str_replace($forbiddenCharacters, $replaceCharacters, $shaString);

		// Calculate SHA1 hash
		$shaSignature = sha1($shaString);

		// Generate HTML form
		$objTemplate = new FrontendTemplate('iso_payment_ideal_basic');

		$objTemplate->action = $this->aquirerUrl('issuerRequest'); //$this->Environment->base  . $this->Environment->request;
		$objTemplate->order_id = $objOrder->id;
		$objTemplate->order_description = $orderDescription;
		
		$objTemplate->name = $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][0];
		$objTemplate->message = $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][1];
		
		$objTemplate->acceptant_id = $this->ideal_acceptant_id;
		$objTemplate->sub_id = $this->ideal_sub_id;
		$objTemplate->amount = $grandTotal;
		$objTemplate->language = strtolower($GLOBALS['TL_LANGUAGE']);
		$objTemplate->currency = $this->Isotope->Config->currency;
		$objTemplate->order_description = $orderDescription;
		$objTemplate->sha = $shaSignature;
		$objTemplate->payment_type = $paymentType;
		$objTemplate->valid_until = $validUntil;
		$objTemplate->url_success = $this->Environment->base . $this->addToUrl('step=complete&amp;' . $this->id . '&amp;purchaseID=' . $objOrder->id . '&amp;response=success');
		$objTemplate->url_cancel = $this->Environment->base . $this->addToUrl('step=complete&amp;' . $this->id . '&amp;purchaseID=' . $objOrder->id . '&amp;response=cancel');
		$objTemplate->url_error = $this->Environment->base . $this->addToUrl('step=complete&amp;' . $this->id . '&amp;purchaseID=' . $objOrder->id . '&amp;response=error');
		$objTemplate->url_service = $this->Environment->base . $this->generateFrontendUrl(array('id' => $this->ideal_url_service));

		$objTemplate->submit_value = $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][2];

		return $objTemplate->parse();
	}

	/**
	 * Return current aquirer url
	 */
	private function aquirerUrl($requestType) {
		if ($this->ideal_unique_urls) {
			switch ($requestType) {
				case 'transactionRequest':
					return $this->ideal_testmode ? $this->ideal_transaction_url_test : $this->ideal_transaction_url_production;
					break;
				case 'statusRequest':
					return $this->ideal_testmode ? $this->ideal_status_url_test : $this->ideal_status_url_production;
					break;
			}
		}
		return $this->ideal_testmode ? $this->ideal_url_test : $this->ideal_url_production;
	}

	/**
	 * Add iDEAL variables to the amount, shastring and returnHTML.
	 * NOT USED FOR NOW
	 * @access protected
	 * @return array
	 * /
	protected function addIdealBasicProduct($itemNumber, $itemDescription, $itemQuantity, $itemPrice, $itemCount) {
		$itemNumber = htmlspecialchars($itemNumber);
		$itemDescription = htmlspecialchars($itemDescription);

		$amount = intval($itemQuantity * $itemPrice);

		// Part of string used for sha hash
		$shaString = $itemNumber . $itemDescription . $itemQuantity . $itemPrice;

		$returnHTML = '
<input type="hidden" name="itemNumber'.$itemCount.'" value="'.$itemNumber.'" />
<input type="hidden" name="itemDescription'.$itemCount.'" value="'.$itemDescription.'" />
<input type="hidden" name="itemQuantity'.$itemCount.'" value="'.$itemQuantity.'" />
<input type="hidden" name="itemPrice'.$itemCount.'" value="'.$itemPrice.'" />';

		return array($amount, $shaString, $returnHTML);
	}

	/**
	 * Format given price to a integer value in cents.
	 * NOT USED FOR NOW
	 * @access protected
	 * @param float $fltPrice
	 * @return integer
	 * /
	protected function formatPriceAsCents($fltPrice) {
		// If price or override price is a string
		if (!is_numeric($fltPrice))
			return $fltPrice;

		return number_format($fltPrice, '2', '', '');
	}

	/**
	 * Extend the log function to display on errors but only in test mode
	 */
	protected function log($strText, $strFunction, $strAction) {
		if (($strAction == TL_ERROR) || $this->ideal_testmode) {
			parent::log($strText, $strFunction, $strAction);
		
			if ($strAction == TL_ERROR && $this->ideal_testmode) {
				echo '<h1>iDEAL test; an error occurred</h1>'
					. '<p>Function: ' . $strFunction . '</p>'
					. '<p>Details: ' . $strText . '</p>';
				exit;
			}
		}
	}
}
