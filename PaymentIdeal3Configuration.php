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

use iDEALConnector\Log\LogLevel;

/**
 * Configuration object, as required by iDEAL Connector
 */
class PaymentIdeal3Configuration
{
    private $certificate = "";
    private $privateKey = "";
    private $passphrase = "";

    private $acquirerCertificate = "";

    private $merchantID = "";
    private $subID = 0;
    private $returnURL = "";

    private $expirationPeriod = 60;
    private $acquirerDirectoryURL = "";
    private $acquirerTransactionURL = "";
    private $acquirerStatusURL = "";
    private $timeout = 10;

    private $proxy = null;
    private $proxyUrl = "";

    private $logFile = "logs/connector.log";
    private $logLevel = LogLevel::Error;

    function __construct($config_data)
    {
       $this->setConfig($config_data);
    }

    public function setConfig($config_data)
    {
        if(isset($config_data['ideal_acceptant_id']))
		{
            $this->merchantID = $config_data['ideal_acceptant_id'];
        }

		if(isset($config_data['ideal_sub_id']))
		{
			$this->subID = intval($config_data['ideal_sub_id']);
        }

		if(isset($config_data['merchant_return_url']))
		{
            $this->returnURL = $config_data['merchant_return_url'];		
		}

        if ($config_data['ideal_testmode'] && $config_data['ideal_url_test'])
        {
			if ($config_data['ideal_unique_urls'])
			{
				$this->acquirerDirectoryURL = $config_data['ideal_url_test'];
				$this->acquirerStatusURL = $config_data['ideal_status_url_test'];
				$this->acquirerTransactionURL = $config_data['ideal_transaction_url_test'];
			}
			else
			{
				$this->acquirerDirectoryURL = $config_data['ideal_url_test'];
				$this->acquirerStatusURL = $config_data['ideal_url_test'];
				$this->acquirerTransactionURL = $config_data['ideal_url_test'];
			}
		}
		elseif ($config_data['ideal_url_production'])
		{
			if ($config_data['ideal_unique_urls'])
			{
				$this->acquirerDirectoryURL = $config_data['ideal_url_production'];
				$this->acquirerStatusURL = $config_data['ideal_status_url_production'];
				$this->acquirerTransactionURL = $config_data['ideal_transaction_url_production'];
			}
			else
			{
				$this->acquirerDirectoryURL = $config_data['ideal_url_production'];
				$this->acquirerStatusURL = $config_data['ideal_url_production'];
				$this->acquirerTransactionURL = $config_data['ideal_url_production'];
			}
		}

        if(isset($config_data['ACQUIRERTIMEOUT']))
		{
            $this->timeout = intval($config_data['ACQUIRERTIMEOUT']);
        }
		
		if(isset($config_data['EXPIRATIONPERIOD']))
        {
            if ($config_data['EXPIRATIONPERIOD'] === "PT1H")
                $this->expirationPeriod = 60;
            else
            {
                $value = substr($config_data['EXPIRATIONPERIOD'], 2, strlen($config_data['EXPIRATIONPERIOD']) - 3);
                if (is_numeric($value))
                    $this->expirationPeriod = intval($value);
            }
        }

		if ($config_data['ideal_testmode'] && $config_data['ideal_use_publ_test_cert_file'] && $config_data['ideal_publ_test_cert_file'])
		{
			$this->acquirerCertificate = $config_data['ideal_publ_test_cert_file'];
		}
		elseif ($config_data['ideal_publ_cert_file'])
		{
            $this->acquirerCertificate = $config_data['ideal_publ_cert_file'];
		}

        if(isset($config_data['ideal_priv_cert_file']))
            $this->certificate = $config_data['ideal_priv_cert_file'];
        if(isset($config_data['ideal_acceptant_key_file']))
            $this->privateKey = $config_data['ideal_acceptant_key_file'];
        if(isset($config_data['ideal_acceptant_key']))
            $this->passphrase = $config_data['ideal_acceptant_key'];

        if(isset($config_data['PROXY']))
            $this->proxy = $config_data['PROXY'];

        if(isset($config_data['PROXYACQURL']))
            $this->proxyUrl = $config_data['PROXYACQURL'];

        if(isset($config_data['LOGFILE']))
            $this->logFile = $config_data['LOGFILE'];

        if(isset($config_data['TRACELEVEL']))
        {
            $level = $config_data['TRACELEVEL'];

            if ($level === "DEBUG")
                $this->logLevel = LogLevel::Debug;
            else if ($level === "ERROR")
                $this->logLevel = LogLevel::Error;
        }
    }

    public function getAcquirerCertificatePath()
    {
        return $this->acquirerCertificate;
    }

    public function getCertificatePath()
    {
        return $this->certificate;
    }

    public function getExpirationPeriod()
    {
        return $this->expirationPeriod;
    }

    public function getMerchantID()
    {
        return $this->merchantID;
    }

    public function getPassphrase()
    {
        return $this->passphrase;
    }

    public function getPrivateKeyPath()
    {
        return $this->privateKey;
    }

    public function getMerchantReturnURL()
    {
        return $this->returnURL;
    }

    public function getSubID()
    {
        return $this->subID;
    }

    public function getAcquirerTimeout()
    {
        return $this->timeout;
    }

    public function getAcquirerDirectoryURL()
    {
        return $this->acquirerDirectoryURL;
    }

    public function getAcquirerStatusURL()
    {
        return $this->acquirerStatusURL;
    }

    public function getAcquirerTransactionURL()
    {
        return $this->acquirerTransactionURL;
    }

    /**
     * @return string
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @return string
     */
    public function getProxyUrl()
    {
        return $this->proxyUrl;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @return int
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }
}
