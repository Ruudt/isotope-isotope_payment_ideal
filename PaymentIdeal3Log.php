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

class PaymentIdeal3Log
{
	private $objLog;

    function __construct($objLog)
    {
        $this->objLog = $objLog;
    }

	public function logAPICall($method, $request)
	{
		$this->log("Entering[".$method."]", $request);
	}

    public function logAPIReturn($method, $response)
    {
        $this->log("Exiting[".$method."]", $response);
    }

    public function logRequest($xml)
    {
        $this->log("Request", $xml);
    }

    public function logResponse($xml)
    {
        $this->log("Response", $xml);
    }

    public function logErrorResponse($exception)
    {
        $this->log("ErrorResponse", $exception);
    }

    public function logException($exception)
    {
        $this->log("Exception", $exception);
    }

    private function log($message, $value)
    {
		if ($this->objLog->ideal_verbose_logging)
		{
			$this->objLog->log($message . "\n" . serialize($value), 'PaymentIdeal3Log log()', TL_GENERAL);
		}
	}
}
