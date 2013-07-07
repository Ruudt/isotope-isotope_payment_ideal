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
 * @copyright  Ruud Walraven 2010-2013
 * @author     Ruud Walraven <ruud.walraven@gmail.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

$GLOBALS['TL_LANG']['tl_iso_payment_modules']['config_ideal'] = 'Instellingen iDEAL';

$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_basic_accept_status_risk'] = array('Toon betalings status in het backend. Ik accepteer de risico\'s die hierbij horen.','Deze betaalmethode (iDEAL basic/lite) biedt beveiligde online betaling, maar geen beveiligde terugkoppeling van de status naar de webwinkel. De terugkoppeling kan makkelijk vervalst worden. Betalingsstatus veilig ophalen kan UITSLUITEND bij uw bank of iDEAL provider. Voor een beveiligde terugkoppeling kunt u iDEAL advanced/professional/zelfbouw gebruiken of neem contact op met een andere iDEAL provider voor hun producten.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_sub_id'] = array('Sub ID','Vul in als opgegeven door iDEAL. Anders 0');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_id'] = array('Acceptant ID','Vul het van iDEAL ontvangen acceptant/merchant ID in (bijv: 005021234)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key'] = array('Acceptant key','Vul de geheime sleutel in verkregen in het iDEAL dashboard (bijv: idYCq46j8cpcIhQ2)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key_file'] = array('Private key','Gegenereerd private key bestand (bijv: private.key) - GEBRUIK EEN BEVEILIGDE MAP; Contao bestandsbeheer kan mappen beveiligen.)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_priv_cert_file'] = array('Private key certificaat','Certificaat gegenereerd met de private key (bijv: private.cer) - GEBRUIK EEN BEVEILIGDE MAP; Contao bestandsbeheer kan mappen beveiligen.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_cert_file'] = array('Publiek certificaat','Certificaat verkregen van de bank (bijv: ingbank.cer) - GEBRUIK EEN BEVEILIGDE MAP; Contao bestandsbeheer kan mappen beveiligen.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_use_publ_test_cert_file'] = array('Specificeer publiek certificaat testomgeving', 'Gebruik een ander certificaat voor de testomgeving (niet nodig voor Rabobank en ING).');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_test_cert_file'] = array('Publiek testcertificaat','Certificaat verkregen voor testomgeving (bijv: banknaam.test.cer) - GEBRUIK EEN BEVEILIGDE MAP; Contao bestandsbeheer kan mappen beveiligen.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_test'] = array('Testomgeving','Vul de URL in van de gewenste iDEAL testomgeving. (Rabobank: https://idealtest.rabobank.nl/ideal/mpiPayInitRabo.do, ING: ssl://idealtest.secure-ing.com:443/ideal/iDeal, ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Directory.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_production'] = array('Productieomgeving','Vul de URL in van de gewenste iDEAL productieomgeving. (Rabobank: https://ideal.rabobank.nl/ideal/mpiPayInitRabo.do, ING: ssl://ideal.secure-ing.com:443/ideal/iDeal, ABN Amro: ssl://idealm.abnamro.nl:443/nl/issuerInformation/getIssuerInformation.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_test'] = array('Transactiecode test URL','Vul de URL in van de gewenste iDEAL transactiecode testomgeving. (ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Transaction.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_production'] = array('Transactiecode productie URL','Vul de URL in van de gewenste iDEAL transactiecode productieomgeving. (ABN Amro: ssl://idealm.abnamro.nl:443/nl/acquirerTrxRegistration/getAcquirerTrxRegistration.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_test'] = array('Status test URL','Vul de URL in van de gewenste iDEAL status test testomgeving. (ABN Amro: ssl://itt.idealdesk.com:443/ITTEmulatorAcquirer/Status.aspx)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_production'] = array('Status productie URL','Vul de URL in van de gewenste iDEAL status productieomgeving. (ABN Amro: ssl://idealm.abnamro.nl:443/nl/acquirerStatusInquiry/getAcquirerStatusInquiry.xml)');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'] = array('Order omschrijving', 'Omschrijving die klant te zien krijgt tijdens iDEAL betaalproces en tevens verschijnt op de afschrijving. "iDEAL betaling" wanneer niet ingevuld.', 'iDEAL betaling');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_service'] = array('Pagina algemene voorwaarden','URL die verwijst naar de Algemene Voorwaarden van de webwinkel. Deze URL kan de consument tijdens het betaalproces benaderen door te klikken op de hyperlink "Algemene voorwaarden".');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_cache_path'] = array('Gegevens cache', 'Optionele locatie voor tijdelijke gegevens zoals een issuer lijst. Versnelt het verwerken en wordt minimaal iedere 24 uur vernieuwd.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_unique_urls'] = array('Specificeer aparte URL per stap', 'Bij sommige banken is de URL bij iedere stap in het proces verschillend (ABN Amro). De twee bovenstaande velden "' . $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_test'][0] . '" en "' . $GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_production'][0] . '" worden gebruikt als issuer request URLs.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_paid_order_status'] = array('Status van betaalde orders', 'Kies de status voor orders die betaald zijn.');
$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_verbose_logging'] = array('Uitgebreid info', 'Houdt uitgebreide info bij in het systeemlog.');
