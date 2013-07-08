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
 * @copyright  Ruud Walraven 2010 - 2013
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

use iDEALConnector\iDEALConnector;

use iDEALConnector\Entities\Transaction;

use iDEALConnector\Exceptions\iDEALException;
use iDEALConnector\Exceptions\ValidationException;
use iDEALConnector\Exceptions\SecurityException;
use iDEALConnector\Exceptions\SerializationException;

require_once("ideal/" . IDEAL3_VERSION . "/iDEALConnector.php");
require_once('PaymentIdeal3Configuration.php');
require_once('PaymentIdeal3Log.php');

/**
 * Handle iDEAL advanced/professional/zelfbouw payments
 *
 * @extends Payment
 */
class PaymentIdeal3Advanced extends IsotopePayment
{	
	/**
	 * iDEAL connector (ING code)
	 */
	protected $configuration;
	protected $log;
	protected $connector;


	/**
	 * Initialize the object
	 *
	 * @access public
	 * @param array $arrRow
	 */
	public function __construct($arrRow)
	{
		parent::__construct($arrRow);

		// Reset errormessage
		unset($_SESSION['CHECKOUT_DATA']['paymentResponseMsg']);

		$hasErrors = false;
		
		// Create iDEAL connector
		try
		{
			$this->configuration = new PaymentIdeal3Configuration($arrRow);
			$this->log = new PaymentIdeal3Log($this);
			$this->connector = new iDEALConnector($this->configuration, $this->log);

			$config_data = array();
			$config_data['merchant_return_url'] = $this->Environment->base . $this->addToUrl('step=complete');
			$this->configuration->setConfig($config_data);
		}
		catch (iDEALException $ex)
		{
			$hasErrors = true;
			$errorCode = $ex->getErrorCode();
			$consumerMessage = $ex->getConsumerMessage();
			$errorMsg = $ex->getMessage();
		}
		catch (Exception $ex)
		{
			$hasErrors = true;
			$errorCode = '-1';
			$errorMsg = $ex->getMessage();
		}

		// Log exception and return error message
		if ($hasErrors)
		{
			$this->log('iDEAL connector exception (' . $errorCode . '). Error message: ' . $errorMsg, 'PaymentIdeal3Advanced __construct()', TL_ERROR);
			$_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] = $GLOBALS['TL_LANG']['MSC']['idealTryAgainOrContact'];
		}

		// Check server requirements
		if ($this->ideal_testmode)
		{
			if (version_compare(PHP_VERSION, '5.3.0') < 0)
			{
				$this->log('iDEAL 3.3.1 requires PHP 5.3.0 for SHA256 support in OPENSSL libraries.<br>Server is running PHP ' . PHP_VERSION . '.', 'PaymentIdeal3Advanced __construct()', TL_ERROR);
			}

			// Base encode/decode available
			if (!function_exists('base64_encode') || !function_exists('base64_decode'))
			{
				$this->log('iDEAL 3.3.1 requires function base64_encode and base64_decode.', 'PaymentIdeal3Advanced __construct()', TL_ERROR);
			}

			// cURL loaded
			if (!in_array('curl', get_loaded_extensions()) || !function_exists('curl_exec'))
			{
				$this->log('iDEAL 3.3.1 requires extension cURL to be loaded and function curl_exec.', 'PaymentIdeal3Advanced __construct()', TL_ERROR);
			}

			// SHA256 hash available
			$digests = openssl_get_md_methods();
			if (!in_array ('SHA256', $digests))
			{
				$this->log('iDEAL 3.3.1 requires openssl hash method SHA256 to be available.', 'PaymentIdeal3Advanced __construct()', TL_ERROR);
			}
		}
	}

// Isotope backend
	/**
	 * Return information or advanced features in the backend.
	 *
	 * @access public
	 * @param  int		Order ID
	 * @return string
	 */
	public function backendInterface($orderId)
	{
		$objOrderInfo = $this->Database->prepare("SELECT * FROM tl_iso_orders WHERE id=?")
										   ->limit(1)
										   ->execute($orderId);
				
		$arrOrderInfo = $objOrderInfo->fetchAssoc();

		$arrPaymentInfo = deserialize($arrOrderInfo['payment_data'], true);
		
		$this->fltOrderTotal = $arrOrderInfo['grandTotal'];
				
		//Get the iDEAL advanced configuration data			
		$objAIMConfig = $this->Database->execute("SELECT * FROM tl_iso_payment_modules WHERE type='idealadvanced'");
		if ($objAIMConfig->numRows < 1)
		{
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
									
		if ($this->Input->post('FORM_SUBMIT') == 'be_pos_terminal' && $objOrderInfo->ideal_transaction_id!='')
		{
			// Load / initialize data
			$arrPayment = deserialize($objOrderInfo->payment_data, true);
		
			$arrPayment['POSTSALE'] = $this->statusRequest($objOrderInfo->ideal_transaction_id); // (success, cancelled, failure, open, expired)

			$arrPayment['POSTSALE']['trxid'] = $objOrderInfo->ideal_transaction_id;
			$arrPayment['POSTSALE']['ec'] = $objOrderInfo->ideal_entrance_code;
					
			$this->Database->prepare("UPDATE tl_iso_orders SET payment_data=? WHERE id=?")->execute(serialize($arrPayment), $orderId);
			$arrOrderInfo['payment_data'] = serialize($arrPayment);
			
			$strResponse = '<p class="tl_info">' . $GLOBALS['TL_LANG']['ISO']['ideal_status_reponse'][1] . ' ' . $arrPayment['POSTSALE']['status'] . '</p>';

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
		if ($arrOrderInfo['payment_data']['status'] == 'success')
		{
			$return .= '<div class="info">' . $GLOBALS['TL_LANG']['ISO']['ideal_status_success'] . '</div>';
			$return .= '<table class="details">';
			foreach($arrOrderInfo['payment_data']['POSTSALE'] as $label => $value)
			{
				$return .= '<tr><td style="padding-right: 24px;">' . ($GLOBALS['TL_LANG']['ISO']['payment_data_labels'][$label] ? $GLOBALS['TL_LANG']['ISO']['payment_data_labels'][$label] : '') . '</td><td>' . print_r($value, true) . '</td></tr>';
			}
			$return .= '</table>';
		}
		else
		{
			$return .= '<div class="info">' . $GLOBALS['TL_LANG']['ISO']['ideal_status_no_additional_information'].'</div>';
		}
		$return .= '</div>';
		//$return .= $strOrderDetails;
		$return .= '</div></div>';

		if ($arrOrderInfo['payment_data']['status'] != 'success')
		{
			$return .= '<div class="tl_formbody_submit"><div class="tl_submit_container">';
			$return .= '<input type="submit" class="submit" value="' . $objTemplate->slabel . '" /></div></div>';
		}

		$objTemplate->orderReview = $return;
		$objTemplate->action = $action;
		$objTemplate->rowLast = 'row_' . (count($this->editable) + 1) . ((($i % 2) == 0) ? ' odd' : ' even');						

		return $objTemplate->parse();
	}

// Isotope frontend - payment selection
	/** f
	 * Return a html form for payment data or an empty string.
	 *
	 * @access	public
	 * @param	object	The checkout module object.
	 * @return	string
	 */
	public function paymentForm($objModule)
	{
		$arrPayment = $this->Input->post('payment');
		$this->log->logAPICall("PaymentIdeal3Advanced paymentForm()", $arrPayment);

		$strBuffer = $this->getPaymentForm($objModule);

		if ($this->Input->post('FORM_SUBMIT') == 'iso_mod_checkout_payment' && $objModule->doNotSubmit && $arrPayment['module']==$this->id)
		{
			// Issuer select form was submitted without a valid choice
			if ($arrPayment['idealIssuer'] == '')
			{
				$_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] = $GLOBALS['TL_LANG']['ISO']['ideal3_choose_issuer'];
			}
		}
		elseif ($_SESSION['CHECKOUT_DATA']['payment']['module'] == $this->id && !$this->issuerLookup($_SESSION['CHECKOUT_DATA']['payment']['idealIssuer']))
		{
			// If the chosen issuer became invalid during the checkout proces, or choice was somehow lost
			$_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] = $GLOBALS['TL_LANG']['ERR']['orderPayment'];
		}

		$this->log->logAPIReturn("PaymentIdeal3Advanced paymentForm()", $strBuffer);

		return ($_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] == '' ? '' : '<p class="error message">'. $_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] . '</p>').$strBuffer;
	}

	/** f
	 * Create the iDEAL issuer selection form
	 */
	public function getPaymentForm(&$objModule)
	{
		$strBuffer = '';
		$arrPayment = $this->Input->post('payment');

		// Directory request; lookup banks (issuers)
		$issuerOptions = '';
		
		if (!$response = $this->issuerRequest())
		{
			$objModule->doNotSubmit = true;
			return $strBuffer;
		}

		$acquirerID = $response->getAcquirerID();
		$responseDatetime = $response->getDirectoryDate();

		$i = 0;
		$arrOptions = array();
		$arrOptions[$i]['label'] = $GLOBALS['TL_LANG']['ISO']['ideal3_choose_issuer'];
		$arrOptions[$i]['value'] = '';
		$issuerList = $response->getCountries();
		foreach ($issuerList as $country)
		{
			$i++;
			$arrOptions[$i]['group'] = true;
			$arrOptions[$i]['label'] = $country->getCountryNames();

			foreach ($country->getIssuers() as $issuer)
			{
				$i++;
				$arrOptions[$i]['label'] = $issuer->getName();
				$arrOptions[$i]['value'] = $issuer->getId();
			}
		}

		//Build form fields
		$arrFields = array
		(
			'idealIssuer' => array
			(
				//'label'			=> &$GLOBALS['TL_LANG']['ISO']['ideal3_choose_issuer'],
				'inputType'		=> 'select',
				'options'		=> array('-'),
				'eval'			=> array('mandatory'=>true, 'tableless'=>true)
			)
		);

		foreach ($arrFields as $field => $arrData)
		{
			$strClass = $GLOBALS['TL_FFL'][$arrData['inputType']];

			// Continue if the class is not defined
			if (!$this->classFileExists($strClass))
			{
				continue;
			}

			// The field needs to be in payment array
			$objWidget = new $strClass($this->prepareForWidget($arrData, 'payment['.$field.']'));

			if ($field == 'idealIssuer')
			{
				$objWidget->options = serialize($arrOptions);
			}

			$objWidget->value = $_SESSION['CHECKOUT_DATA']['payment'][$field];

			// Validate input
			if ($this->Input->post('FORM_SUBMIT') == 'payment_form' && $arrPayment['module']==$this->id)
			{
				$objWidget->validate();

				if ($objWidget->hasErrors())
				{
					$objModule->doNotSubmit = true;
				}
			}
			elseif ($objWidget->mandatory && !strlen($objWidget->value))
			{
				$objModule->doNotSubmit = true;
			}

			$strBuffer .= $objWidget->parse();
		}

		return $strBuffer;
	}

// Isotope frontend - review
	/**
	 * Return the checkout review information.
	 *
	 * @access public
	 * @return string
	 */
	public function checkoutReview()
	{
		global $objPage;

		if ($issuerName = $this->issuerLookup($_SESSION['CHECKOUT_DATA']['payment']['idealIssuer']))
		{
			return $this->label . '<br' . ($objPage->outputFormat == 'xhtml' ? ' /' : '') . '>(' . $issuerName . ')';
		}

		return $this->label;
	}

	/** f
	 * Lookup the issuer name
	 * Returns false when issuer not in current list
	 */
	public function issuerLookup($issuerId)
	{
		if ($issuerId && $response = $this->issuerRequest())
		{
			$issuerList = $response->getCountries();
			foreach ($issuerList as $country)
			{
				foreach ($country->getIssuers() as $issuer)
				{
					$arrOptions[$i]['label'] = $issuer->getName();
					if ($issuer->getId() == $issuerId)
					{
						return $issuer->getName();
					}
				}
			}
		}
		
		return false;
	}


// Isotope frontend - payment
	/** f
	 * Return the iDEAL form.
	 * (Issuer request) -> (Transaction request)
	 *
	 * @access public
	 * @return string
	 */
	public function checkoutForm()
	{
		$this->log->logAPICall("PaymentIdeal3Advanced checkoutForm()", '');
		
		$this->import('Isotope');

		// Check the session still contains products
		if (!count($this->Isotope->Cart->getProducts()))
		{
			$this->Template = new FrontendTemplate('mod_message');
			$this->Template->type = 'empty';
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['noItemsInCart'];
			return;
		}

		// Setting order information.
		$objOrder = $this->Database->prepare("SELECT id, order_id, grandTotal FROM tl_iso_orders WHERE cart_id=?")->execute($this->Isotope->Cart->id);
		$orderDescription = htmlspecialchars($this->ideal_order_description ? $this->ideal_order_description : $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'][2]);
		$orderAmount = round($this->Isotope->Cart->grandTotal, 2);

		// Order amount of 0 euro is not possible
		if ($orderAmount == 0.00)
		{
			global $objPage;
			$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'. ($GLOBALS['TL_LANG']['ERR']['orderAmount0'] ? '/reason/'.$GLOBALS['TL_LANG']['ERR']['orderAmount0'] : '')));
		}

		$entranceCode = sha1($objOrder->id . '-' . date('YmdHis'));
		$returnUrl = $this->configuration->getMerchantReturnURL() . '?uid=' . $objOrder->uniqid;

		// Escape [ and ] for ING Bank if they are in the returnUrl
		// $returnUrl = str_replace(array('[', ']'), array('%5B', '%5D'), $returnUrl);
		// TODO: remove forbidden characters from strings
		list($transactionId, $transactionUrl) = $this->transactionRequest(substr($returnUrl, 0, 512)
																		, $objOrder->id
																		, substr($orderDescription, 0, 32)
																		, $orderAmount
																		, $_SESSION['CHECKOUT_DATA']['payment']['idealIssuer']
																		, $entranceCode
																		);

		// Save order information
		$this->Database->execute("UPDATE tl_iso_orders SET status='".$this->new_order_status."', ideal_transaction_id='" . $transactionId . "', ideal_entrance_code='" . $entranceCode . "' WHERE id=" . $objOrder->id);

		$this->log->logAPIReturn("PaymentIdeal3Advanced checkoutForm(). Redirection to iDEAL.", $transactionUrl);

		// Customer will be redirected to issuer website to pay
		$this->redirect($transactionUrl);
		
		// If something went wrong then have the customer click the link
		return '<h1>' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][0] . '</h1><p>' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][1] . ' <a href="' . htmlentities($transactionUrl()) . '">' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'][2] . '</a></p>';		
	}

	/** f
	 * Process payment on confirmation page.
	 *
	 * @access public
	 * @return void
	 */
	public function processPayment()
	{
		global $objPage;

		$this->log->logAPICall("PaymentIdeal3Advanced processPayment()", '');

		// Get the transaction id and entrance code
		$transactionId = $this->Input->get('trxid');
		$entranceCode = $this->Input->get('ec');

		$this->import('Isotope');

		$objOrder = $this->Database->execute("SELECT * FROM tl_iso_orders WHERE ideal_transaction_id='" . $transactionId . "' AND ideal_entrance_code = '" . $entranceCode . "'");

		if (!$objOrder->numRows)
		{
			$this->log('Order with transaction ID "' . $transactionId . '" not found', 'PaymentIdeal3Advanced processPayment()', TL_ERROR);
			$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed' . ($GLOBALS['TL_LANG']['ERR']['orderUnknown'] ? '/reason/'.$GLOBALS['TL_LANG']['ERR']['orderUnknown'] : '')));
		}
		else
		{
			// Load / initialize data
			$arrPayment = deserialize($objOrder->payment_data, true);
			
			// Store request data in order for future references
			$arrData = $objOrder->row();
			$arrData['old_payment_status'] = $arrPayment['status'];
			$status = $this->statusRequest($transactionId); // (success, cancelled, failure, open, expired)
			if (is_array($status))
			{
				$arrData = array_merge($arrData, $status);
			}

			$arrData['new_payment_status'] = $arrPayment['status'];
			$arrPayment['status'] = $status['status'];

			$arrPayment['POSTSALE'] = $status;
			$arrPayment['POSTSALE']['trxid'] = $this->Input->get('trxid');
			$arrPayment['POSTSALE']['ec'] = $this->Input->get('ec');

			switch( $status['status'] )
			{
				case 'success':
					$this->Database->execute("UPDATE tl_iso_orders SET date_payed=" . time() . " WHERE id=" . $objOrder->id);
					$this->Database->prepare("UPDATE tl_iso_orders SET payment_data=? WHERE id=?")->execute(serialize($arrPayment), $objOrder->id);
					$this->log('Ideal (advanced) payment success ' . print_r($_GET, true), 'PaymentIdeal3Advanced processPayment()', TL_GENERAL);

					// Set the current system to the language when the user placed the order.
					// This will result in correct e-mails and payment description.
					$GLOBALS['TL_LANGUAGE'] = $objOrder->language;
					$this->loadLanguageFile('default');
					break;

				case 'cancelled':
					$this->log('Ideal (advanced) payment cancelled by customer ' . print_r($_GET, true), 'PaymentIdeal3Advanced processPayment()', TL_GENERAL);
					$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed' . ($GLOBALS['TL_LANG']['ERR']['orderCancelled'] ? '/reason/'.$GLOBALS['TL_LANG']['ERR']['orderCancelled'] : '')));
					break;

				case 'expired':
					$this->log('Ideal (advanced) payment expired ' . print_r($_GET, true), 'PaymentIdeal3Advanced processPayment()', TL_ERROR);
					$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'));
					break;

				case 'error':
				case 'failure':
				case 'open':
				default:
					$this->log('Ideal (advanced) payment error ' . print_r($_GET, true), 'PaymentIdeal3Advanced processPayment()', TL_ERROR);
					$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'));
					break;
			}
			
			return true;
		}
	}

// iDEAL functions
	/** f
	 * Execute request (Lookup issuer list)
	 */
	public function issuerRequest($renewCache=false)
	{
		$cacheFile = false;

		if (!$renewCache && $this->ideal_cache_path) {
			$cacheFile = $this->ideal_cache_path . '/' . 'issuerrequest.dat';

			if (file_exists($cacheFile) == false) {
				// Attempt to create cache file
				if (@touch($cacheFile)) {
					@chmod($cacheFile, 0777);
				}
			}

			if (file_exists($cacheFile) && is_readable($cacheFile) && is_writable($cacheFile)) {
				if (filemtime($cacheFile) > strtotime('-1 Hours')) {
					// Read data from cache file
					if ($data = file_get_contents($cacheFile)) {
						return unserialize($data);
					}
				}
			} else {
				$cacheFile = false;
			}
		}

		try
		{
			$issuerList = $this->connector->getIssuers();

			// Save data to cachefile
			if ($cacheFile) {
				if ($handle = fopen($cacheFile, 'w')) {
					fwrite($handle, serialize($issuerList));
					fclose($handle);
				}
			}
			
			return $issuerList;
		}
		catch (iDEALException $ex)
		{
			$errorCode = $ex->getErrorCode();
			$consumerMessage = $ex->getConsumerMessage();
			$errorMsg = $ex->getMessage();
		}
		catch (Exception $ex)
		{
			$errorCode = '-1';
			$errorMsg = $ex->getMessage();
		}

		// Log exception and return error message
		$this->log('Issuer request exception (' . $errorCode . '). Error message: ' . $errorMsg, 'PaymentIdeal3Advanced issuerRequest()', TL_ERROR);
		$_SESSION['CHECKOUT_DATA']['paymentResponseMsg'] = $GLOBALS['TL_LANG']['MSC']['idealIssuerRequestError'] . ($consumerMessage ? ' (' . $consumerMessage . ') ' : '');

		return false;
	}

	/** f
	 * Execute request (Setup transaction)
	 */
	public function transactionRequest($returnUrl, $orderId, $orderDescription, $orderAmount, $issuerId, $entranceCode)
	{
		$hasErrors = false;

		try
		{
			$objOrder = new IsotopeOrder();
			if (!$objOrder->findBy('cart_id', $this->Isotope->Cart->id))
			{
				$this->redirect($this->addToUrl('step=failed', true));
			}

			$returnUrl = $this->configuration->getMerchantReturnURL() . '?uid=' . $objOrder->uniqid;
			$response = $this->connector->startTransaction(
							$issuerId,
							new Transaction(
								$orderAmount,
								$orderDescription,
								$entranceCode,
								$this->configuration->getExpirationPeriod(),
								$orderId,
								'EUR',
								'nl'
							),
							$returnUrl
						);

			/* @var $response AcquirerTransactionResponse */
			$acquirerID = $response->getAcquirerID();
			$issuerAuthenticationURL = $response->getIssuerAuthenticationURL();
			$transactionID = $response->getTransactionID();
		}
		catch (SerializationException $ex)
		{
			$hasErrors = true;
			$errorCode = 'SerializationException';
			$errorMsg = $ex->getMessage();
		}
		catch (SecurityException $ex)
		{
			$hasErrors = true;
			$errorCode = 'SecurityException';
			$errorMsg = $ex->getMessage();
		}
		catch(ValidationException $ex)
		{
			$hasErrors = true;
			$errorCode = 'ValidationException';
			$errorMsg = $ex->getMessage();
		}
		catch (iDEALException $ex)
		{
			$hasErrors = true;
			$errorCode = $ex->getErrorCode();
			$errorMsg = $ex->getMessage();
			$consumerMessage = $ex->getConsumerMessage();
		}
		catch (Exception $ex)
		{
			$hasErrors = true;
			$errorCode = '-1';
			$errorMsg = $ex->getMessage();
		}

		// Log exception and return error message
		if ($hasErrors)
		{
			global $objPage;

			$this->log('Transaction request exception (' . $errorCode . '). Error message: ' . $errorMsg, 'PaymentIdeal3Advanced transactionRequest()', TL_ERROR);
			$this->redirect($this->generateFrontendUrl($objPage->row(), '/step/failed'. ($GLOBALS['TL_LANG']['MSC']['idealTryAgainOrContact'] ? '/reason/' . $GLOBALS['TL_LANG']['MSC']['idealTryAgainOrContact'] : '')));
		}

		return array($transactionID, $issuerAuthenticationURL);
	}

	/** f
	 * Get the transaction status
	 */
	public function statusRequest($transactionID)
	{
		$hasErrors = false;

		try
		{
			$response = $this->connector->getTransactionStatus($transactionID);

			/* @var $response AcquirerStatusResponse */
			$status['acquirerID'] = $response->getAcquirerID();
			$status['consumerName'] = $response->getConsumerName();
			$status['consumerIBAN'] = $response->getConsumerIBAN();
			$status['consumerBIC'] = $response->getConsumerBIC();
			$status['amount'] = $response->getAmount();
			$status['currency'] = $response->getCurrency();
			$status['statusDateTime'] = $response->getStatusTimestamp();
			$status['transactionID'] = $response->getTransactionID();
			$status['status'] = strtolower($response->getStatus());
		}
		catch (SerializationException $ex)
		{
			$hasErrors = true;
			$errorCode = 'SerializationException';
			$errorMsg = $ex->getMessage();
		}
		catch (SecurityException $ex)
		{
			$hasErrors = true;
			$errorCode = 'SecurityException';
			$errorMsg = $ex->getMessage();
		}
		catch(ValidationException $ex)
		{
			$hasErrors = true;
			$errorCode = 'ValidationException';
			$errorMsg = $ex->getMessage();
		}
		catch (iDEALException $ex)
		{
			$hasErrors = true;
			$errorCode = $ex->getErrorCode();
			$errorMsg = $ex->getMessage();
			$consumerMessage = $ex->getConsumerMessage();
		}
		catch (Exception $ex)
		{
			$hasErrors = true;
			$errorCode = '-1';
			$errorMsg = $ex->getMessage();
		}

		// Log exception and return error message
		if ($hasErrors)
		{
			$this->log('Transaction request exception (' . $errorCode . '). Error message: ' . $errorMsg, 'PaymentIdeal3Advanced transactionRequest()', TL_ERROR);
		}
		
		return $status;
	}

	/**
	 * Extend the log function to display on errors but only in test mode
	 */
	public function log($strText, $strFunction, $strAction) {
		if ($strAction == TL_ERROR && $this->ideal_testmode) {
			echo '<h1>iDEAL test; an error occurred</h1>'
				. '<p>Function: ' . $strFunction . '</p>'
				. '<p>Details: ' . $strText . '</p>';
			exit;
		}
		parent::log($strText, $strFunction, $strAction);
	}
}