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

$GLOBALS['TL_LANG']['tl_iso_payment_modules']['config_ideal'] = 'iDEAL settings';
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['config_ideal_basic_status'] = 'iDEAL status settings';

$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_basic_accept_status_risk'] = array('I want to enable the backend payment status information and I understand the risks involved','Although this payment method (iDEAL basic/lite) provides secure payments, it does not provide secure status feedback to the webstore. The feedback provided can be easily faked. You can ONLY securely check payments with your bank or iDEAL provider. For secured feedback use iDEAL advanced/professional/zelfbouw or contact another iDEAL payment provider for their products.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_sub_id'] = array('Sub ID','Fill in if needed, 0 if no other value is specified by iDEAL.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_id'] = array('Acceptant ID','Fill in the received acceptant/merchant ID (ex: 005021234)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key'] = array('Acceptant key','Fill in the secret key set at the iDEAL dashboard (ex: idYCq46j8cpcIhQ2)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key_file'] = array('Private key file','Generated private key file (ex: private.key) - USE A SECURE FOLDER; you can use the Contao file manager to secure a folder.)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_priv_cert_file'] = array('Private key certificate','Certificate generated from the private key (ex: private.cer) - USE A SECURE FOLDER; you can use the Contao file manager to secure a folder.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_cert_file'] = array('Public certificate','Certificate obtained from your bank (ex: ingbank.cer) - USE A SECURE FOLDER; you can use the Contao file manager to secure a folder.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_use_publ_test_cert_file'] = array('Specify a test certificate', 'Use a different certificate for the test environment (not needed for Rabobank and ING).');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_test_cert_file'] = array('Public test certificate','Certificate obtained for the test environment (ex: bankname.test.cer) - USE A SECURE FOLDER; you can use the Contao file manager to secure a folder.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_test'] = array('Test environment','Fill in the URL to the desired iDEAL test environment. (Rabobank: https://idealtest.rabobank.nl/ideal/mpiPayInitRabo.do, ING: ssl://idealtest.secure-ing.com:443/ideal/iDeal, ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Directory.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_production'] = array('Production environment','Fill in the URL to the desired iDEAL production environment. (Rabobank: https://ideal.rabobank.nl/ideal/mpiPayInitRabo.do, ING: ssl://ideal.secure-ing.com:443/ideal/iDeal, ABN Amro: ssl://idealm.abnamro.nl:443/nl/issuerInformation/getIssuerInformation.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_test'] = array('Transaction request test URL','Fill in the URL to the desired iDEAL transaction request test environment. (ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Transaction.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_production'] = array('Transaction request production URL','Fill in the URL to the desired iDEAL transaction request production environment. (ABN Amro: ssl://idealm.abnamro.nl:443/nl/acquirerTrxRegistration/getAcquirerTrxRegistration.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_test'] = array('Status request test URL','Fill in the URL to the desired iDEAL status request test environment. (ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Status.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_production'] = array('Status request production URL','Fill in the URL to the desired iDEAL status request production environment. (ABN Amro: ssl://idealm.abnamro.nl:443/nl/acquirerStatusInquiry/getAcquirerStatusInquiry.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_testmode'] = array('Use testenvironment','Use the testenvironment. (uncheck after completing testing phase)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'] = array('Order description', 'Description displayed to customer that is displayed on transaction transcript. "iDEAL payment" if left empty', 'iDEAL payment');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_service'] = array('Terms and conditions page','URL of the terms and conditions page. Consumer can click a link to this page during the payment process.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_cache_path'] = array('Data cache location', 'Optional location to store cachable data like issuer list. Speeds up response. Cleared every 24 hours');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_unique_urls'] = array('Specify each request URL', 'Some banks use a different URL for each request type (ABN Amro). The existing fields "' . $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_test'][0] . '" and "' . $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_production'][0] . '" are used as the issuer request URLs.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_verbose_logging'] = array('Verbose logging', 'Enable verbose logging.');
