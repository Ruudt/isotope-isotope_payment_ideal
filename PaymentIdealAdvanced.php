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
 * Handle iDEAL advanced/professional/zelfbouw payments
 *
 * @extends Payment
 */
class PaymentIdealAdvanced extends IsotopePayment
{
	/**
	 * Constants
	 */
	protected $LF = "\n";
	protected $CRLF = "\r\n";

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
				
		//Get the iDEAL advanced configuration data			
		$objAIMConfig = $this->Database->execute("SELECT * FROM tl_iso_payment_modules WHERE type='idealadvanced'");
		if ($objAIMConfig->numRows < 1) {
			return '<i>' . $GLOBALS['TL_LANG']['MSC']['noPaymentModules'] . '</i>';
		}
		
		$this->ideal_sub_id = $objAIMConfig->ideal_sub_id;
		$this->ideal_acceptant_id = $objAIMConfig->ideal_acceptant_id;
		$this->ideal_acceptant_key = $objAIMConfig->ideal_acceptant_key;
		$this->ideal_url_test = $objAIMConfig->ideal_url_test;
		$this->ideal_url_production = $objAIMConfig->ideal_url_production;
		$this->ideal_unique_urls = $objAIMConfig->ideal_unique_urls;
		$this->ideal_status_url_test = $objAIMConfig->ideal_status_url_test;
		$this->ideal_status_url_production = $objAIMConfig->ideal_status_url_production;
		$this->ideal_testmode = $objAIMConfig->ideal_testmode;
		$this->ideal_acceptant_key_file = $objAIMConfig->ideal_acceptant_key_file;
		$this->ideal_priv_cert_file = $objAIMConfig->ideal_priv_cert_file;
		$this->ideal_publ_cert_file = $objAIMConfig->ideal_publ_cert_file;
		$this->ideal_use_publ_test_cert_file = $objAIMConfig->ideal_use_publ_test_cert_file;
		$this->ideal_publ_test_cert_file = $objAIMConfig->ideal_publ_test_cert_file;
		
		$objTemplate = new BackendTemplate('be_pos_terminal');
									
		if ($this->Input->post('FORM_SUBMIT') == 'be_pos_terminal' && $objOrderInfo->ideal_transaction_id!='') {
			// Load / initialize data
			$arrPayment = deserialize($objOrderInfo->payment_data, true);
		
			list($arrPayment['status']
				, $arrPayment['POSTSALE']['accountCity']
				, $arrPayment['POSTSALE']['accountName']
				, $arrPayment['POSTSALE']['accountNumber']
				) = $this->statusRequest($objOrderInfo->ideal_transaction_id); // (success, cancelled, failure, open, expired)

			$arrPayment['POSTSALE']['trxid'] = $objOrderInfo->ideal_transaction_id;
			$arrPayment['POSTSALE']['ec'] = $objOrderInfo->ideal_entrance_code;
					
			$this->Database->prepare("UPDATE tl_iso_orders SET payment_data=? WHERE id=?")->execute(serialize($arrPayment), $orderId);
			$arrOrderInfo['payment_data'] = serialize($arrPayment);
			
			$strResponse = '<p class="tl_info">' . $GLOBALS['TL_LANG']['ISO']['ideal_status_reponse'][1] . ' ' . $arrPayment['status'] . '</p>';

			$objTemplate->isConfirmation = true;
		}
		
		$arrOrderInfo['payment_data'] = deserialize($arrOrderInfo['payment_data']);

		$this->Input->setGet('uid', $arrOrderInfo['uniqid']);
		$objModule = new ModuleIsotopeOrderDetails($this->Database->execute("SELECT * FROM tl_module WHERE type='iso_orderdetails'"));
		$strOrderDetails = $objModule->generate(true);
		$action = ampersand($this->Environment->request, ENCODE_AMPERSANDS);

		$objTemplate->formId = 'be_pos_terminal';
	
		$objTemplate->slabel = specialchars($GLOBALS['TL_LANG']['ISO']['ideal_status_reponse'][0]);
		
		$return = '<div id="tl_buttons">
<input type="hidden" name="FORM_SUBMIT" value="' . $objTemplate->formId . '" />
<a href="'.ampersand(str_replace('&key=payment', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>
<h2 class="sub_headline">' . $GLOBALS['TL_LANG']['PAY']['idealadvanced'][0] . '</h2>
<div class="tl_formbody_edit">
<div class="tl_tbox block">';
		$return .= ($strResponse ? $strResponse : '');
		$return .= '<div class="info_container payment_status block">
			<h2>' . $GLOBALS['TL_LANG']['ISO']['ideal_payment_information'] . '</h2>';
		if ($arrOrderInfo['payment_data']['status'] == 'success') {
			$return .= '<div class="info">'
					. $GLOBALS['TL_LANG']['ISO']['ideal_status_success'] . '<br />'
					. '<br />' . $GLOBALS['TL_LANG']['ISO']['ideal_account_city'] . ' ' . $arrOrderInfo['payment_data']['POSTSALE']['accountCity']
					. '<br />' . $GLOBALS['TL_LANG']['ISO']['ideal_account_name'] . ' ' . $arrOrderInfo['payment_data']['POSTSALE']['accountName']
					. '<br />' . $GLOBALS['TL_LANG']['ISO']['ideal_account_number'] . ' ' . $arrOrderInfo['payment_data']['POSTSALE']['accountNumber']
					. '</div></div>';
		} else {
			$return .= '<div class="info">' . $GLOBALS['TL_LANG']['ISO']['ideal_status_no_additional_information']
					. '</div></div>';
		}
		//$return .= $strOrderDetails;
		$return .= '</div></div>';

		if ($arrOrderInfo['payment_data']['status'] != 'success') {
			$return .= '<div class="tl_formbody_submit"><div class="tl_submit_container">';
			$return .= '<input type="submit" class="submit" value="' . $objTemplate->slabel . '" /></div></div>';
		}

		$objTemplate->orderReview = $return;
		$objTemplate->action = $action;
		$objTemplate->rowLast = 'row_' . (count($this->editable) + 1) . ((($i % 2) == 0) ? ' odd' : ' even');						

		return $objTemplate->parse();
	}
	
	/**
	 * Return the iDEAL form.
	 * (Issuer request) -> (Transaction request)
	 *
	 * @access public
	 * @return string
	 */
	public function checkoutForm() {
		$this->import('Isotope');

		$objOrder = $this->Database->prepare("SELECT id, order_id, grandTotal FROM tl_iso_orders WHERE cart_id=?")->execute($this->Isotope->Cart->id);
		
		// Step 1 -> get issuers via issuer request
		// Step 2 -> request the transaction id and the redirect
		if ( !$this->Input->post('order_issuer') ) {
			if (!count($this->Isotope->Cart->getProducts())) {
			   $this->Template = new FrontendTemplate('mod_message');
			   $this->Template->type = 'empty';
			   $this->Template->message = $GLOBALS['TL_LANG']['MSC']['noItemsInCart'];
			   return;
			}

			// Setting order information.
			$orderDescription = htmlspecialchars($this->ideal_order_description ? $this->ideal_order_description : $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'][2]);
			$orderAmount = round($this->Isotope->Cart->grandTotal, 2);

			// Vraag de lijst met banken op
			$issuerOptions = '';
			$issuerList = $this->issuerRequest();

			foreach ($issuerList as $issuerId => $issuerName)
			{
				$issuerOptions .= '<option value="' . $issuerId . '">' . htmlentities($issuerName) . '</option>';
			}

			$objTemplate = new FrontendTemplate('iso_payment_ideal_advanced');

			$objTemplate->action = $this->Environment->base  . $this->Environment->request;
			$objTemplate->order_id = $objOrder->order_id;
			$objTemplate->order_description = $orderDescription;
			
			$objTemplate->name = $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][0];
			$objTemplate->message = $GLOBALS['TL_LANG']['ISO']['ideal_choose_issuer'];
			
			$objTemplate->issuer_label = $GLOBALS['TL_LANG']['ISO']['ideal_your_issuer'];
			$objTemplate->issuer_options = '<select class="select" name="order_issuer" size="1">' . $issuerOptions . '</select>';
			
			$objTemplate->amount_label = $GLOBALS['TL_LANG']['ISO']['ideal_amount'];
			$objTemplate->amount = number_format($orderAmount, 2, ',', '') . ' ' . $this->Isotope->Config->currency;

			$objTemplate->order_description_label = $GLOBALS['TL_LANG']['ISO']['ideal_description'];
			$objTemplate->order_description = $orderDescription;
			
			$objTemplate->submit_value = $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][2];

			return $objTemplate->parse();
		} else {
			$entranceCode = sha1($objOrder->id . '-' . date('YmdHis'));
			$returnUrl = $this->Environment->base . $this->addToUrl('step=complete');

			// Escape [ and ] for ING Bank if they are in the returnUrl
			// $returnUrl = str_replace(array('[', ']'), array('%5B', '%5D'), $returnUrl);
			
			list($transactionId, $transactionUrl) = $this->transactionRequest(substr($returnUrl, 0, 512)
																			, $objOrder->id
																			, substr($this->Input->post('order_description'), 0, 32)
																			, round($objOrder->grandTotal * 100)
																			, $this->Input->post('order_issuer')
																			, $entranceCode
																			);

			// Save order information
			$this->Database->execute("UPDATE tl_iso_orders SET status='".$this->new_order_status."', ideal_transaction_id='" . $transactionId . "', ideal_entrance_code='" . $entranceCode . "' WHERE id=" . $objOrder->id);

			// Customer will be redirected to issuer website to pay
			$this->redirect($transactionUrl);
			
			// If something went wrong then have the customer click the link
			return '<h1>' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][0] . '</h1><p>' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][1] . ' <a href="' . htmlentities($transactionUrl()) . '">' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][2] . '</a></p>';		
		}
	}
	
	/**
	 * Execute request (Lookup issuer list)
	 */
	public function issuerRequest() {
		$cacheFile = false;

		if ($this->ideal_cache_path) {
			$cacheFile = $this->ideal_cache_path . '/' . 'issuerrequest.dat';

			if (file_exists($cacheFile) == false) {
				// Attempt to create cache file
				if (@touch($cacheFile)) {
					@chmod($cacheFile, 0777);
				}
			}

			if (file_exists($cacheFile) && is_readable($cacheFile) && is_writable($cacheFile)) {
				if (filemtime($cacheFile) > strtotime('-24 Hours')) {
					// Read data from cache file
					if ($data = file_get_contents($cacheFile)) {
						return unserialize($data);
					}
				}
			} else {
				$cacheFile = false;
			}
		}

		$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';
		$message = $this->removeSpaces($timestamp . $this->ideal_acceptant_id . $this->ideal_sub_id);

		$token = $this->getCertificateFingerprint(TL_ROOT . '/' . $this->ideal_priv_cert_file);
		$tokenCode = $this->getSignature($message);

		$xmlMessage = '<?xml version="1.0" encoding="UTF-8" ?>' . $this->LF
					. '<DirectoryReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">' . $this->LF
					. '<createDateTimeStamp>' . $this->escapeXml($timestamp) . '</createDateTimeStamp>' . $this->LF
					. '<Merchant>' . $this->LF
					. '<merchantID>' . $this->escapeXml($this->ideal_acceptant_id) . '</merchantID>' . $this->LF
					. '<subID>' . $this->escapeXml($this->ideal_sub_id) . '</subID>' . $this->LF
					. '<authentication>SHA1_RSA</authentication>' . $this->LF
					. '<token>' . $this->escapeXml($token) . '</token>' . $this->LF
					. '<tokenCode>' . $this->escapeXml($tokenCode) . '</tokenCode>' . $this->LF
					. '</Merchant>' . $this->LF
					. '</DirectoryReq>';

		$xmlReply = $this->postData($this->aquirerUrl('issuerRequest'), $xmlMessage, 10);

		if ($xmlReply) {
			if ($this->getXmlTagValue('errorCode', $xmlReply)) {
				$this->log($this->getXmlTagValue('errorMessage', $xmlReply) . ' - ' . $this->getXmlTagValue('errorDetail', $xmlReply) . ' - ' . $this->getXmlTagValue('errorCode', $xmlReply), 'PaymentIdealAdvanced issuerRequest()', TL_ERROR);
				$this->redirect($this->Environment->request . (strpos($this->Environment->request, '?') === false ? '?' : '&') . 'error=' . $this->escapeSpecialChars($this->removeSpaces($this->getXmlTagValue('errorCode', $xmlReply))));
				exit;
			} else {
				$issuerShortList = array();
				$issuerLongList = array();

				while (strpos($xmlReply, '<issuerID>')) {
					$issuerId = $this->getXmlTagValue('issuerID', $xmlReply);
					$issuerName = $this->getXmlTagValue('issuerName', $xmlReply);
					$issuerList = $this->getXmlTagValue('issuerList', $xmlReply);

					if (strcmp($issuerList, 'Short') === 0) {
						// Only support ABN Amro Bank when in HTTPS mode.
						// if ((strcasecmp(substr($_SERVER['SERVER_PROTOCOL'], 0, 5), 'HTTPS') === 0) || (stripos($issuerName, 'ABN') === false)) {
						$issuerShortList[$issuerId] = $issuerName;
						// }
					} else {
						$issuerLongList[$issuerId] = $issuerName;
					}

					$xmlReply = substr($xmlReply, strpos($xmlReply, '</issuerList>') + 13);
				}

				$issuerList = array_merge($issuerShortList, $issuerLongList);

				// Save data to cachefile
				if ($cacheFile) {
					if ($handle = fopen($cacheFile, 'w')) {
						fwrite($handle, serialize($issuerList));
						fclose($handle);
					}
				}

				return $issuerList;
			}
		}
		
		return false;
	}

	/**
	 * Execute request (Setup transaction)
	 */
	public function transactionRequest($returnUrl, $orderId, $orderDescription, $orderAmount, $issuerId, $entranceCode) {
		$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';
		$message = $this->removeSpaces($timestamp . $issuerId . $this->ideal_acceptant_id . $this->ideal_sub_id . $returnUrl . $orderId . $orderAmount . $this->Isotope->Config->currency . strtolower($GLOBALS['TL_LANGUAGE']) . $orderDescription . $entranceCode);
		$token = $this->getCertificateFingerprint(TL_ROOT . '/' . $this->ideal_priv_cert_file);
		$tokenCode = $this->getSignature($message);

		$xmlMessage = '<?xml version="1.0" encoding="UTF-8" ?>' . $this->LF
					. '<AcquirerTrxReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">' . $this->LF
					. '<createDateTimeStamp>' . $this->escapeXml($timestamp) .  '</createDateTimeStamp>' . $this->LF
					. '<Issuer>' . $this->LF
					. '<issuerID>' . $this->escapeXml($issuerId) . '</issuerID>' . $this->LF
					. '</Issuer>' . $this->LF
					. '<Merchant>' . $this->LF 
					. '<merchantID>' . $this->escapeXml($this->ideal_acceptant_id) . '</merchantID>' . $this->LF
					. '<subID>' . $this->escapeXml($this->ideal_sub_id) . '</subID>' . $this->LF
					. '<authentication>SHA1_RSA</authentication>' . $this->LF
					. '<token>' . $this->escapeXml($token) . '</token>' . $this->LF
					. '<tokenCode>' . $this->escapeXml($tokenCode) . '</tokenCode>' . $this->LF
					. '<merchantReturnURL>' . $this->escapeXml($returnUrl) . '</merchantReturnURL>' . $this->LF
					. '</Merchant>' . $this->LF
					. '<Transaction>' . $this->LF
					. '<purchaseID>' . $this->escapeXml($orderId) . '</purchaseID>' . $this->LF
					. '<amount>' . $this->escapeXml($orderAmount) . '</amount>' . $this->LF
					. '<currency>' . $this->Isotope->Config->currency . '</currency>' . $this->LF
					. '<expirationPeriod>PT30M</expirationPeriod>' . $this->LF
					. '<language>' . strtolower($GLOBALS['TL_LANGUAGE']) . '</language>' . $this->LF
					. '<description>' . $this->escapeXml($orderDescription) . '</description>' . $this->LF
					. '<entranceCode>' . $this->escapeXml($entranceCode) . '</entranceCode>' . $this->LF
					. '</Transaction>' . $this->LF 
					. '</AcquirerTrxReq>';

		$xmlReply = $this->postData($this->aquirerUrl('transactionRequest'), $xmlMessage, 10);

		if ($xmlReply) {
			if ($this->getXmlTagValue('errorCode', $xmlReply)) {
				$this->log($this->getXmlTagValue('errorMessage', $xmlReply) . ' - ' . $this->getXmlTagValue('errorDetail', $xmlReply) . ' - ' . $this->getXmlTagValue('errorCode', $xmlReply), 'PaymentIdealAdvanced transactionRequest()', TL_ERROR);
			} else {
				return array($this->getXmlTagValue('transactionID', $xmlReply), html_entity_decode($this->getXmlTagValue('issuerAuthenticationURL', $xmlReply)));
			}
		}

		return array(false, false);
	}
	
	/**
	 * Process iDEAL advanced status
	 * Process payment on confirmation page.
	 * 
	 * @access public
	 * @return void
	 */
	public function processPostSale() {
		return true;
	}	

	/**
	 * Process payment on confirmation page.
	 *
	 * @access public
	 * @return void
	 */
	public function processPayment() {
		$status = '';

		// Get the transaction id and entrance code
		$transactionId = $this->Input->get('trxid');
		$entranceCode = $this->Input->get('ec');

		$this->import('Isotope');

		$objOrder = $this->Database->execute("SELECT * FROM tl_iso_orders WHERE ideal_transaction_id='" . $transactionId . "' AND ideal_entrance_code = '" . $entranceCode . "'");

		if (!$objOrder->numRows) {
			$this->log('Transaction ID "' . $transactionId . '" not found', 'PaymentIdealAdvanced processPayment()', TL_ERROR);
			$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'));
		} else {
			// Load / initialize data
			$arrPayment = deserialize($objOrder->payment_data, true);
			
			// Store request data in order for future references
			$arrData = $objOrder->row();
			$arrData['old_payment_status'] = $arrPayment['status'];
			list($arrPayment['status']
				, $arrData['accountCity']
				, $arrData['accountName']
				, $arrData['$accountNumber']
				) = $this->statusRequest($transactionId); // (success, cancelled, failure, open, expired)
			$arrData['new_payment_status'] = $arrPayment['status'];

			$arrPayment['POSTSALE']['trxid'] = $this->Input->get('trxid');
			$arrPayment['POSTSALE']['ec'] = $this->Input->get('ec');
			$arrPayment['POSTSALE']['accountCity'] = $arrData['accountCity'];
			$arrPayment['POSTSALE']['accountName'] = $arrData['accountName'];
			$arrPayment['POSTSALE']['accountNumber'] = $arrData['$accountNumber'];

			switch( $arrPayment['status'] ) {
				case 'success':
					$status = 'success';
					$this->Database->execute("UPDATE tl_iso_orders SET date_payed=" . time() . " WHERE id=" . $objOrder->id);
					$this->Database->prepare("UPDATE tl_iso_orders SET payment_data=? WHERE id=?")->execute(serialize($arrPayment), $objOrder->id);
					$this->log('Ideal (advanced) payment success ' . print_r($_GET, true), 'PaymentIdealAdvanced processPayment()', TL_GENERAL);

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

				case 'cancelled':
				case 'expired':
				case 'error':
				case 'failure':
				case 'open':
				default:
					global $objPage;
					$this->log('Ideal (advanced) payment error ' . print_r($_GET, true), 'PaymentIdealAdvanced processPayment()', TL_GENERAL);
					$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'));
					break;
			}
			
			return true;
		}
	}

	/**
	 * Get the trabsaction status
	 */
	public function statusRequest($transactionId) {
		$timestamp = gmdate('Y-m-d') . 'T' . gmdate('H:i:s') . '.000Z';
		$message = $this->removeSpaces($timestamp . $this->ideal_acceptant_id . $this->ideal_sub_id . $transactionId);
		$token = $this->getCertificateFingerprint(TL_ROOT . '/' . $this->ideal_priv_cert_file);
		$tokenCode = $this->getSignature($message);

		$xmlMessage = '<?xml version="1.0" encoding="UTF-8" ?>' . $this->LF
					. '<AcquirerStatusReq xmlns="http://www.idealdesk.com/Message" version="1.1.0">' . $this->LF
					. '<createDateTimeStamp>' . $this->escapeXml($timestamp) . '</createDateTimeStamp>' . $this->LF
					. '<Merchant>' 
					. '<merchantID>' . $this->escapeXml($this->ideal_acceptant_id) . '</merchantID>' . $this->LF
					. '<subID>' . $this->escapeXml($this->ideal_sub_id) . '</subID>' . $this->LF
					. '<authentication>SHA1_RSA</authentication>' . $this->LF
					. '<token>' . $this->escapeXml($token) . '</token>' . $this->LF
					. '<tokenCode>' . $this->escapeXml($tokenCode) . '</tokenCode>' . $this->LF
					. '</Merchant>' . $this->LF
					. '<Transaction>' 
					. '<transactionID>' . $this->escapeXml($transactionId) . '</transactionID>' . $this->LF
					. '</Transaction>'
					. '</AcquirerStatusReq>';
	
		$xmlReply = $this->postData($this->aquirerUrl('statusRequest'), $xmlMessage, 10);

		if ($xmlReply) {
			if ($this->getXmlTagValue('errorCode', $xmlReply)) {
				$this->log($this->getXmlTagValue('errorMessage', $xmlReply) . ' - ' . $this->getXmlTagValue('errorDetail', $xmlReply) . ' - ' . $this->getXmlTagValue('errorCode', $xmlReply), 'PaymentIdealAdvanced statusRequest()', TL_ERROR);
			} else {
				$timestamp = $this->getXmlTagValue('createDateTimeStamp', $xmlReply);
				$transactionId = $this->getXmlTagValue('transactionID', $xmlReply);
				$transactionStatus = $this->getXmlTagValue('status', $xmlReply);

				$accountNumber = $this->getXmlTagValue('consumerAccountNumber', $xmlReply);
				$accountName = $this->getXmlTagValue('consumerName', $xmlReply);
				$accountCity = $this->getXmlTagValue('consumerCity', $xmlReply);

				$message = $this->removeSpaces($timestamp . $transactionId . $transactionStatus . $accountNumber);

				$signature = base64_decode($this->getXmlTagValue('signatureValue', $xmlReply));
				$fingerprint = $this->getXmlTagValue('fingerprint', $xmlReply);

				if (strcasecmp($fingerprint, $this->getCertificateFingerprint(TL_ROOT . '/' . $this->publicCertificate())) !== 0) {
					$this->log('Unknown fingerprint.', 'PaymentIdealAdvanced statusRequest()', TL_ERROR);
				} elseif ($this->verifySignature($message, $signature) == false) {
					$this->log('Bad signature.', 'PaymentIdealAdvanced statusRequest()', TL_ERROR);
				} else {
					return array(strtolower($transactionStatus), $accountCity, $accountName, $accountNumber);
				}
			}
		}

		return array(false, false, false, false);
	}

	/**
	 * Send GET/POST data through sockets
	 */
	protected function postData($url, $data, $timeout = 30) {
		$__url = $url;
		$idx = strrpos($url, ':');
		$host = substr($url, 0, $idx);
		$url = substr($url, $idx + 1);
		$idx = strpos($url, '/');
		$port = substr($url, 0, $idx);
		$path = substr($url, $idx);

		$fsp = fsockopen($host, $port, $errno, $errstr, $timeout);
		$res = '';
		
		if ($fsp) {
			// echo "\n\nSEND DATA: \n\n" . $data . "\n\n";

			fputs($fsp, 'POST ' . $path . ' HTTP/1.0' . $this->CRLF);
			fputs($fsp, 'Host: ' . substr($host, 6) . $this->CRLF);
			fputs($fsp, 'Accept: text/html' . $this->CRLF);
			fputs($fsp, 'Accept: charset=ISO-8859-1' . $this->CRLF);
			fputs($fsp, 'Content-Length:' . strlen($data) . $this->CRLF);
			fputs($fsp, 'Content-Type: text/html; charset=ISO-8859-1' . $this->CRLF . $this->CRLF);
			fputs($fsp, $data, strlen($data));

			while (!feof($fsp)) {
				$res .= @fgets($fsp, 128);
			}

			fclose($fsp);

			// echo "\n\nRECIEVED DATA: \n\n" . $res . "\n\n";
		} else {
			$this->log('Error while connecting to ' . $__url, 'PaymentIdealAdvanced postData()', TL_ERROR);
		}

		return $res;
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
	 * Return correct certificate file
	 * Some banks might have a different test and production certificate
	 */
	private function publicCertificate() {
		if ($this->ideal_testmode && $this->ideal_use_publ_test_cert_file) {
			return $this->ideal_publ_test_cert_file;
		}

		return $this->ideal_publ_cert_file;
	}
	

	/**
	 * Get value within XML tag
	 */
	protected function getXmlTagValue($tag, $xml) {
		$begin = 0;
		$end = 0;
		$begin = strpos($xml, '<' . $tag . '>');
		
		if ($begin === false) {
			return false;
		}

		$begin += strlen($tag) + 2;
		$end = strpos($xml, '</' . $tag . '>');

		if ($end === false) {
			return false;
		}

		$result = substr($xml, $begin, $end - $begin);
		return $this->unescapeXml($result);
	}

	/**
	 * Remove space chars from string
	 */
	protected function removeSpaces($string) {
		if ($this->ideal_unique_urls) {
			// Assuming ideal_unique_urls indicates ABN Amro 
			// TODO: If needed add switch that DOES indicate ABN Amro
			return preg_replace('/(\f|\n|\r|\t|\v)/', '', $string);
		} else {
			return preg_replace('/\s/', '', $string);
		}
	}

	/**
	 * Escape (replace/remove) special characters in string
	 */
	protected function escapeSpecialChars($string) {
		$string = str_replace(array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ð', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', '§', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', '€', 'Ð', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', '§', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ'), array('a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'U', 'U', 'Y', 'Y'), $string);
		$string = preg_replace('/[^a-zA-Z0-9\-\.\,\(\)_]+/', ' ', $string);
		$string = preg_replace('/[\s]+/', ' ', $string);

		return $string;
	}

	/**
	 * Escape special XML characters
	 */
	protected function escapeXml($string) {
		return utf8_encode(str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}

	/**
	 * Unescape special XML characters
	 */
	protected function unescapeXml($string) {
		return str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), utf8_decode($string));
	}

	/**
	 * Generate the fingerprint for a certificate
	 */
	protected function getCertificateFingerprint($certificateFile) {
		if ($fp = fopen($certificateFile, 'r')) {
			$sRawData = fread($fp, 8192);
			fclose($fp);

			$sData = openssl_x509_read($sRawData);

			if (!openssl_x509_export($sData, $sData)) {
				$this->setError('Error in certificate ' . $certificateFile, false, __FILE__, __LINE__);
				return false;
			}
		
			$sData = str_replace('-----BEGIN CERTIFICATE-----', '', $sData);
			$sData = str_replace('-----END CERTIFICATE-----', '', $sData);

			return strtoupper(sha1(base64_decode($sData)));
		} else {
			$this->log('Cannot open certificate file: ' . $certificateFile, 'PaymentIdealAdvanced getCertificateFingerprint()', TL_ERROR);
		}
	}

	/**
	 * Calculate signature of the given message
	 */
	protected function getSignature($message) {
		if ($fp = fopen(TL_ROOT . '/' . $this->ideal_acceptant_key_file, 'r')) {
			$privateKeyFileContents = fread($fp, 8192);
			fclose($fp);

			$signature = '';

			if ($privateKey = openssl_get_privatekey($privateKeyFileContents, $this->ideal_acceptant_key)) {
				if (openssl_sign($this->removeSpaces($message), $signature, $privateKey)) {
					openssl_free_key($privateKey);
					$signature = base64_encode($signature);
				} else {
					$this->log('Error while signing message.', 'PaymentIdealAdvanced getSignature()', TL_ERROR);
				}
			} else {
				$this->log('Invalid password for ' . TL_ROOT . '/' . $this->ideal_acceptant_key_file . ' file.', 'PaymentIdealAdvanced getSignature()', TL_ERROR);
			}

			return $signature;
		} else {
			$this->log('Cannot open private key file: ' . TL_ROOT . '/' . $this->ideal_acceptant_key_file, 'PaymentIdealAdvanced getSignature()', TL_ERROR);
		}
	}

	/**
	 * Validate signature for the given data
	 */
	protected function verifySignature($data, $signature) {
		$status = false;

		if ($fp = fopen(TL_ROOT . '/' . $this->publicCertificate(), 'r')) {
			$publicCertificateFileContents = fread($fp, 8192);
			fclose($fp);

			if ($publicKey = openssl_get_publickey($publicCertificateFileContents)) {
				$status = (openssl_verify($data, $signature, $publicKey) ? true : false);
				openssl_free_key($publicKey);
			} else {
				$this->log('Cannot retrieve key from public certificate file: ' . TL_ROOT . '/' . $this->publicCertificate(), 'PaymentIdealAdvanced verifySignature()', TL_ERROR);
			}
		} else {
			$this->log('Cannot open public certificate file: ' . TL_ROOT . '/' . $this->publicCertificate(), 'PaymentIdealAdvanced verifySignature()', TL_ERROR);
		}

		return $status;
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