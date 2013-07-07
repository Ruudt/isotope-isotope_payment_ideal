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
 * Payment modules
 */
$GLOBALS['TL_LANG']['PAY']['ideal'] = array('iDEAL Basic', 'Payment using the iDEAL platform. (Basic/Lite connection required)');
$GLOBALS['TL_LANG']['PAY']['idealadvanced'] = array('iDEAL Advanced', 'Payment using the iDEAL advanced platform. (Advanced/Professional connection required)');

/**
 * Isotope module labels
 */
$GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'] = array('Pay with iDEAL', 'If you were not automatically redirected, please click on the "Pay now" link.', 'Pay now');
$GLOBALS['TL_LANG']['ISO']['pay_with_ideal'] = array('Pay with iDEAL', 'You will be redirected to the iDEAL payment website. If you are not automatically redirected, please click on the "Pay now" button.', 'Pay now');
$GLOBALS['TL_LANG']['ISO']['ideal_choose_issuer'] = 'To pay using iDEAL first choose your bank, then press \'' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][2] . '\'.';
$GLOBALS['TL_LANG']['ISO']['ideal3_choose_issuer'] = 'Choose your bank';
$GLOBALS['TL_LANG']['ISO']['ideal_your_issuer'] = 'Your bank'; 
$GLOBALS['TL_LANG']['ISO']['ideal_amount'] = 'Amount';
$GLOBALS['TL_LANG']['ISO']['ideal_description'] = 'Description';

$GLOBALS['TL_LANG']['ISO']['ideal_status_reponse'] = array('Get status', 'iDEAL status response:');
$GLOBALS['TL_LANG']['ISO']['ideal_payment_information'] = 'Payment Information';
$GLOBALS['TL_LANG']['ISO']['ideal_account_city'] = 'Account city:';
$GLOBALS['TL_LANG']['ISO']['ideal_account_name'] = 'Account name:';
$GLOBALS['TL_LANG']['ISO']['ideal_account_number'] = 'Account number:';
$GLOBALS['TL_LANG']['ISO']['ideal_status_no_additional_information'] = 'No payment information available.';

$GLOBALS['TL_LANG']['ISO']['ideal_basic_status_unsafe'] = 'The information below can be false or outdated.<br />Check actual status with your bank or iDEAL basic provider.<br />Switch to iDEAL advanced/professional/zelfbouw or another iDEAL provider for secure feedback.';
$GLOBALS['TL_LANG']['ISO']['ideal_status_success'] = 'Payment received.';
$GLOBALS['TL_LANG']['ISO']['ideal_no_status_success'] = 'Payment not received.';
$GLOBALS['TL_LANG']['ISO']['backendPaymentNoInfoIdealBasic'] = 'Payment status information can be displayed but must be enabled in this payment methods options.';

/**
 * Status labels payment
 */
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['acquirerID'] = 'Bank id customer (Acquirer id)';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerName'] = 'Consumer name';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerIBAN'] = 'Consumer IBAN';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerBIC'] = 'Consumer BIC';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['amount'] = 'Amount';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['currency'] = 'Currency';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['statusDateTime'] = 'Date and time';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['transactionID'] = 'Transaction id';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['status'] = 'Payment status';

/**
 * Messages
 */
$GLOBALS['TL_LANG']['ERR']['orderPayment'] = 'Error while connecting to your bank. Please try again or choose a different payment method.';
$GLOBALS['TL_LANG']['ERR']['orderCancelled'] = 'Payment cancelled. Please try again or choose a different payment method.';
$GLOBALS['TL_LANG']['ERR']['orderAmount0'] = 'iDEAL payments of &euro;0.00 are not possible. Choose another payment method to continue.';
$GLOBALS['TL_LANG']['ERR']['orderUnknown'] = 'Order unknown. Please try again or choose a different payment method.';
$GLOBALS['TL_LANG']['MSC']['idealTryAgainOrContact'] = 'Please try again. Contact us if the problem persists.';
$GLOBALS['TL_LANG']['MSC']['idealIssuerRequestError'] = 'Cannot fetch a list of iDEAL banks.';
