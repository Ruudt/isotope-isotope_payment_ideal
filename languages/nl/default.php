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
$GLOBALS['TL_LANG']['PAY']['ideal'] = array('iDEAL Basic', 'Betaling via het iDEAL platform. (Basic/Lite connectie vereist)');
$GLOBALS['TL_LANG']['PAY']['idealadvanced'] = array('iDEAL Advanced', 'Betaling via het iDEAL advanced platform. (Advanced/Professional connectie vereist)');

/**
 * Isotope module labels
 */
$GLOBALS['TL_LANG']['ISO']['pay_with_ideal_after_error'] = array('Betaal met iDEAL', 'Mocht u niet automatisch doorverwezen zijn, klikt u dan op de "Betaal nu" link.', 'Betaal nu');
$GLOBALS['TL_LANG']['ISO']['pay_with_ideal'] = array('Betaal met iDEAL', 'U wordt doorgestuurd naar de iDEAL betaalsite. Mocht u niet automatisch doorgestuurd worden, klikt u dan op de "Betaal nu" knop.', 'Betaal nu');
$GLOBALS['TL_LANG']['ISO']['ideal_choose_issuer'] = 'Kies uw bank om via iDEAL te betalen en druk op \'' . $GLOBALS['TL_LANG']['ISO']['pay_with_ideal'][2] . '\'.';
$GLOBALS['TL_LANG']['ISO']['ideal3_choose_issuer'] = 'Kies uw bank';
$GLOBALS['TL_LANG']['ISO']['ideal_your_issuer'] = 'Uw bank'; 
$GLOBALS['TL_LANG']['ISO']['ideal_amount'] = 'Bedrag';
$GLOBALS['TL_LANG']['ISO']['ideal_description'] = 'Omschrijving';

$GLOBALS['TL_LANG']['ISO']['ideal_status_reponse'] = array('Status ophalen', 'iDEAL status antwoord:');
$GLOBALS['TL_LANG']['ISO']['ideal_payment_information'] = 'Betalingsgegevens';
$GLOBALS['TL_LANG']['ISO']['ideal_account_city'] = 'Plaatsnaam:';
$GLOBALS['TL_LANG']['ISO']['ideal_account_name'] = 'Naam rekeninghouder:';
$GLOBALS['TL_LANG']['ISO']['ideal_account_number'] = 'Rekeningnummer:';
$GLOBALS['TL_LANG']['ISO']['ideal_status_no_additional_information'] = 'Geen betalingsgegevens beschikbaar.';

$GLOBALS['TL_LANG']['ISO']['ideal_basic_status_unsafe'] = 'Onderstaande informatie is mogelijk juist of verouderd.<br />Controleer de juiste status bij uw bank of iDEAL basic provider.<br />Gebruik iDEAL advanced/professional/zelfbouw of een andere iDEAL provider voor beveiligde feedback.';
$GLOBALS['TL_LANG']['ISO']['ideal_status_success'] = 'Betaling ontvangen.';
$GLOBALS['TL_LANG']['ISO']['ideal_no_status_success'] = 'Betaling niet ontvangen.';
$GLOBALS['TL_LANG']['ISO']['backendPaymentNoInfoIdealBasic'] = 'Betalings status kan weergegeven worden maar moet aangevinkt worden in de opties voor deze betaalmethode.';

/**
 * Status labels payment
 */
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['acquirerID'] = 'Banknr van klant (Acquirer id)';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerName'] = 'Naam klant';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerIBAN'] = 'IBAN klant';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['consumerBIC'] = 'BIC klant';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['amount'] = 'Bedrag';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['currency'] = 'Valuta';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['statusDateTime'] = 'Datum en tijd';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['transactionID'] = 'Transactienr';
$GLOBALS['TL_LANG']['ISO']['payment_data_labels']['status'] = 'Status betaling';

/**
 * Messages
 */
$GLOBALS['TL_LANG']['ERR']['orderPayment'] = 'Fout bij bereiken bank. Kies opnieuw uw betaalmethode.';
$GLOBALS['TL_LANG']['ERR']['orderCancelled'] = 'Betaling geannuleerd. Probeer het opnieuw of kies een andere betaalmethode.';
$GLOBALS['TL_LANG']['ERR']['orderAmount0'] = 'iDEAL betalingen van &euro;0,00 zijn niet mogelijk, kies een andere betaalmethode.';
$GLOBALS['TL_LANG']['ERR']['orderUnknown'] = 'Bestelling onbekend. Probeer het opnieuw of kies een andere betaalmethode.';
$GLOBALS['TL_LANG']['MSC']['idealTryAgainOrContact'] = 'Probeert u het nogmaals, of neem a.u.b. contact met ons op als het probleem blijft bestaan.';
$GLOBALS['TL_LANG']['MSC']['idealIssuerRequestError'] = 'Kan momenteel geen lijst met beschikbare banken ophalen.';