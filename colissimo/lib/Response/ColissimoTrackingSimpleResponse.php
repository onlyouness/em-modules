<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ColissimoTrackingSimpleResponse
 */
class ColissimoTrackingSimpleResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    public $errorMessage;
    public $errorCode;
    public $eventCode;
    public $eventDate;
    public $eventLibelle;
    public $eventSite;
    public $recipientCity;
    public $recipientCountryCode;
    public $recipientZipCode;
    public $skybillNumber;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $trackingSimpleResponse = new self();
        $xml = new SimpleXMLElement($responseBody);
        $xml->registerXPathNamespace('ns1', 'http://chargeur.tracking.geopost.com/');
        $return = $xml->xpath('soap:Body/ns1:trackResponse/return');
        $array = json_decode(json_encode($return[0]), true);
        $trackingSimpleResponse->errorMessage = isset($array['errorMessage']) ? $array['errorMessage'] : null;
        $trackingSimpleResponse->errorCode = isset($array['errorCode']) ? $array['errorCode'] : null;
        $trackingSimpleResponse->eventCode = isset($array['eventCode']) ? $array['eventCode'] : null;
        $trackingSimpleResponse->eventDate = isset($array['eventDate']) ? $array['eventDate'] : null;
        $trackingSimpleResponse->eventLibelle = isset($array['eventLibelle']) ? $array['eventLibelle'] : null;
        $trackingSimpleResponse->eventSite = isset($array['eventSite']) ? $array['eventSite'] : null;
        $trackingSimpleResponse->recipientCity = isset($array['recipientCity']) ? $array['recipientCity'] : null;
        $trackingSimpleResponse->recipientCountryCode = isset($array['recipientCountryCode']) ? $array['recipientCountryCode'] : null;
        $trackingSimpleResponse->recipientZipCode = isset($array['recipientZipCode']) ? $array['recipientZipCode'] : null;
        $trackingSimpleResponse->skybillNumber = isset($array['skybillNumber']) ? $array['skybillNumber'] : null;

        return $trackingSimpleResponse;
    }
}
