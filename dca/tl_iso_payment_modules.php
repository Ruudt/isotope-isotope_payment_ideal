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

/**
 * Table tl_iso_payment_modules
 */

$GLOBALS['TL_DCA']['tl_iso_payment_modules']['palettes'] += array(
    'ideal' => '{type_legend},type,name,label;{note_legend:hide},note;{config_ideal_basic_status},ideal_basic_accept_status_risk;{config_ideal},ideal_sub_id,ideal_acceptant_id,ideal_acceptant_key,ideal_url_test,ideal_url_production,ideal_testmode,ideal_order_description,ideal_url_service;{config_legend},new_order_status,postsale_mail,minimum_total,maximum_total,countries,shipping_modules,product_types;{price_legend:hide},price,tax_class;{enabled_legend},enabled',
    'idealadvanced' => '{type_legend},type,name,label;{note_legend:hide},note;{config_ideal},ideal_sub_id,ideal_acceptant_id,ideal_acceptant_key,ideal_acceptant_key_file,ideal_priv_cert_file,ideal_publ_cert_file,ideal_use_publ_test_cert_file,ideal_url_test,ideal_url_production,ideal_unique_urls,ideal_testmode,ideal_order_description,ideal_url_service,ideal_cache_path;{config_legend},new_order_status,postsale_mail,minimum_total,maximum_total,countries,shipping_modules,product_types;{price_legend:hide},price,tax_class;{enabled_legend},enabled',
    'ideal3advanced' => '{type_legend},type,name,label;{note_legend:hide},note;{config_ideal},ideal_sub_id,ideal_acceptant_id,ideal_acceptant_key,ideal_acceptant_key_file,ideal_priv_cert_file,ideal_publ_cert_file,ideal_use_publ_test_cert_file,ideal_url_test,ideal_url_production,ideal_unique_urls,ideal_testmode,ideal_order_description,ideal_url_service,ideal_cache_path,ideal_verbose_logging;{config_legend},new_order_status,postsale_mail,minimum_total,maximum_total,countries,shipping_modules,product_types;{price_legend:hide},price,tax_class;{enabled_legend},enabled'
);

$GLOBALS['TL_DCA']['tl_iso_payment_modules']['palettes']['__selector__'][] = 'ideal_use_publ_test_cert_file';
$GLOBALS['TL_DCA']['tl_iso_payment_modules']['palettes']['__selector__'][] = 'ideal_unique_urls';

if (!isset($GLOBALS['TL_DCA']['tl_iso_payment_modules']['subpalettes'])) {
	$GLOBALS['TL_DCA']['tl_iso_payment_modules']['subpalettes'] = array();
}

$GLOBALS['TL_DCA']['tl_iso_payment_modules']['subpalettes'] = array(
	'ideal_use_publ_test_cert_file' => 'ideal_publ_test_cert_file',
    'ideal_unique_urls' => 'ideal_transaction_url_test,ideal_transaction_url_production,ideal_status_url_test,ideal_status_url_production',
);

$GLOBALS['TL_DCA']['tl_iso_payment_modules']['fields'] += array(
	'ideal_sub_id' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_sub_id'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'maxlength'=>32),
		'default'				=> '0'
	),
	'ideal_acceptant_id' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_id'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'maxlength'=>9)
	),
	'ideal_acceptant_key' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>64)
	),
	'ideal_acceptant_key_file' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_acceptant_key_file'],
		'exclude'				=> true,
		'inputType'				=> 'fileTree',
		'eval'					=> array('fieldType'=>'radio', 'files'=>true, 'mandatory'=>true, 'tl_class'=>'clr')
	),
	'ideal_priv_cert_file' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_priv_cert_file'],
		'exclude'				=> true,
		'inputType'				=> 'fileTree',
		'eval'					=> array('fieldType'=>'radio', 'files'=>true, 'mandatory'=>true, 'tl_class'=>'clr')
	),
	'ideal_publ_cert_file' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_cert_file'],
		'exclude'				=> true,
		'inputType'				=> 'fileTree',
		'eval'					=> array('fieldType'=>'radio', 'files'=>true, 'mandatory'=>true, 'tl_class'=>'clr')
	),
	'ideal_basic_accept_status_risk' => array (
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_basic_accept_status_risk'],
		'exclude'				=> true,
		'inputType'				=> 'checkbox'
	),
	'ideal_use_publ_test_cert_file' => array (
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_use_publ_test_cert_file'],
		'exclude'				=> true,
		'inputType'				=> 'checkbox',
		'eval'					=> array('submitOnChange'=>true)
	),
	'ideal_publ_test_cert_file' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_publ_test_cert_file'],
		'exclude'				=> true,
		'inputType'				=> 'fileTree',
		'eval'					=> array('fieldType'=>'radio', 'files'=>true, 'mandatory'=>true, 'tl_class'=>'clr')
	),
	'ideal_unique_urls' => array (
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_unique_urls'],
		'exclude'				=> true,
		'inputType'				=> 'checkbox',
		'eval'					=> array('submitOnChange'=>true)
	),
	'ideal_url_test' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_test'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_url_production' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_production'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_transaction_url_test' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_test'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_transaction_url_production' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_transaction_url_production'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_status_url_test' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_test'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_status_url_production' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_status_url_production'],
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'size'=>255,'maxlength'=>255)
	),
	'ideal_testmode' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_testmode'],
		'exclude'				=> true,
		'inputType'				=> 'checkbox',
		'eval'					=> array('tl_class'=>'w100'),
	),
	'ideal_order_description' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_order_description'],
		'exclude'				=> true,
		'inputType'				=> 'text',
		'eval'					=> array('mandatory'=>true, 'maxlength'=>32)
	),
	'ideal_url_service' => array
	(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_url_service'],
		'exclude'				=> true,
		'inputType'				=> 'pageTree',
		'explanation'			=> 'jumpTo',
		'eval'					=> array('fieldType'=>'radio', 'mandatory'=>true)
	),
	'ideal_cache_path' => array(
		'label'    				=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_cache_path'],
		'exclude'  				=> true,
		'inputType'				=> 'fileTree',
		'eval'     				=> array('fieldType'=>'radio', 'mandatory'=>false, 'tl_class'=>'clr')
	),
	'ideal_verbose_logging' => array(
		'label'					=> &$GLOBALS['TL_LANG']['tl_iso_payment_modules']['ideal_verbose_logging'],
		'exclude'				=> true,
		'inputType'				=> 'checkbox',
		'eval'					=> array('tl_class'=>'w100'),
	),
);

