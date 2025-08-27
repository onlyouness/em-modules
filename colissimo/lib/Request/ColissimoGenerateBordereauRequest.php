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
 * Class ColissimoGenerateBordereauRequest
 */
class ColissimoGenerateBordereauRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'SOAP';
    const WS_METHOD = 'generateBordereauByParcelsNumbers';
    const WS_CONTENT_TYPE = 'application/xml';

    /** @var SimpleXMLElement */
    public $xml;

    /**
     * ColissimoGenerateBordereauRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        $this->xml = simplexml_load_file($this->xmlLocation . self::WS_METHOD . '.xml');
        $this->xml->registerXPathNamespace('sls', 'http://sls.ws.coliposte.fr');
        $this->setCredentials();
    }

    /**
     * @param array $numbers
     */
    public function setParcelsNumbers(array $numbers)
    {
        $list = $this->xml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers/generateBordereauParcelNumberList');
        foreach ($numbers as $number) {
            $list[0]->addChild('parcelsNumbers', $number);
        }
    }

    /**
     * @return void
     */
    public function setCredentials()
    {
        $parcels = $this->xml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers');
        $parcels[0]->contractNumber = isset($this->request['contractNumber']) ? $this->request['contractNumber'] : '';
        $parcels[0]->password = isset($this->request['password']) ? $this->request['password'] : '';
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
            $requestXml->registerXPathNamespace('sls', 'http://sls.ws.coliposte.fr');
            $parcels = $requestXml->xpath('soapenv:Body/sls:generateBordereauByParcelsNumbers');
            $parcels[0]->password = '****';
            $parcels[0]->contractNumber = '****';
            $requestJsonString = json_encode($parcels);

            return $requestJsonString;
        }

        return $this->xml->asXML();
    }

    /**
     * @return mixed|null
     */
    public function getApiKey()
    {
        return isset($this->request['apikey']) ? $this->request['apikey'] : null;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoGenerateBordereauResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
