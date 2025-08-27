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
 * Class ColissimoTrackingSimpleRequest
 */
class ColissimoTrackingSimpleRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/tracking-chargeur-cxf/TrackingServiceWS/track';
    const WS_CONTENT_TYPE = 'application/xml;charset=UTF-8';

    /** @var SimpleXMLElement */
    public $xml;

    /**
     * ColissimoTrackingSimpleRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->xml = simplexml_load_file($this->xmlLocation.'track.xml');
        $this->xml->registerXPathNamespace('char', 'http://chargeur.tracking.geopost.com');
        $this->setCredentials();
    }

    /**
     * @return void
     */
    public function setCredentials()
    {
        $track = $this->xml->xpath('soapenv:Body/char:track');
        $track[0]->accountNumber = isset($this->request['contractNumber']) ? $this->request['contractNumber'] : '';
        $track[0]->password = isset($this->request['password']) ? $this->request['password'] : '';
        $track[0]->apiKey = isset($this->request['apikey']) ? $this->request['apikey'] : '';
    }

    public function setSkybillNumber($skybillNumber)
    {
        $track = $this->xml->xpath('soapenv:Body/char:track');
        $track[0]->skybillNumber = $skybillNumber;
    }

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        return;
    }

    /**
     * @param bool $obfuscatePassword
     * @return string
     */
    public function getRequest($obfuscatePassword = false)
    {
        if ($obfuscatePassword) {
            $requestXml = new SimpleXMLElement($this->xml->asXML());
            $requestXml->registerXPathNamespace('char', 'http://chargeur.tracking.geopost.com');
            $track = $requestXml->xpath('soapenv:Body/char:track');
            $track[0]->password = '****';
            $track[0]->accountNumber = '****';
            $requestJsonString = json_encode($track);

            return $requestJsonString;
        }

        return $this->xml->asXML();
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoTrackingSimpleResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
