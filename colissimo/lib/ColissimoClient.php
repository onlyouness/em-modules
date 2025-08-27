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
 * Class ColissimoClient
 */
class ColissimoClient
{
    const BASE_URL_PRODUCTION = 'https://ws.colissimo.fr';
    const BASE_URL_TEST = 'https://ws.colissimo.fr';
    const COLISSIMO_WSDL = 'https://ws.colissimo.fr/sls-ws/SlsServiceWS/2.0?wsdl';

    /** @var string */
    protected $baseUrl;

    /** @var AbstractColissimoRequest */
    private $request;

    /**
     * ColissimoClient constructor.
     * @param int $mode
     * @param array $urls
     */
    public function __construct($mode = 0, $urls = [])
    {
        if (is_array($urls) && isset($urls['test']) && isset($urls['production'])) {
            $this->baseUrl = (1 === $mode) ? $urls['production'] : $urls['test'];
        } else {
            $this->baseUrl = (1 === $mode) ? self::BASE_URL_PRODUCTION : self::BASE_URL_TEST;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function request()
    {
        $wsType = constant(get_class($this->request) . '::WS_TYPE');
        switch ($wsType) {
            case 'SOAP':
                $method = constant(get_class($this->request) . '::WS_METHOD');
                $apikey = $this->request->getApiKey();
                $headers = [];
                if ($apikey) {
                    $headers[] = 'apikey: ' . $apikey;
                }
                $context = stream_context_create([
                    'http' => [
                        'header' => implode("\r\n", $headers)
                    ]
                ]);
                $soapClient = new SoapClient(
                    self::COLISSIMO_WSDL, [
                        'exceptions' => 0,
                        'wsdl_cache' => 0,
                        'trace' => 1,
                        'soap_version' => SOAP_1_1,
                        'encoding' => 'UTF-8',
                        'stream_context' => $context
                    ]
                );
                $responseBody = $soapClient->__doRequest(
                    $this->request->getRequest(),
                    self::COLISSIMO_WSDL,
                    $method,
                    '2.0',
                    0
                );
                $responseHeader = $soapClient->__getLastResponseHeaders();
                preg_match("/HTTP\/\d\.\d\s*\K[\d]+/", $responseHeader, $matches);
                $httpCode = $matches[0];

                break;
            case 'CURL':
                if ($this->request->forceEndpoint) {
                    $url = $this->request->forceEndpoint;
                } else {
                    $url = $this->baseUrl . constant(get_class($this->request) . '::WS_PATH');
                }
                $contentType = [];
                if (defined(get_class($this->request) . '::WS_CONTENT_TYPE')) {
                    $contentType = ['Content-Type: ' . constant(get_class($this->request) . '::WS_CONTENT_TYPE')];
                }
                $body = $this->request->getRequest();

                if (defined(get_class($this->request) . '::WS_HEADER')) {
                    $bodyArray = json_decode($body, true);
                    $headerParams = [
                        'apiKey: ' . (isset($bodyArray['apikey']) ? $bodyArray['apikey'] : ''),
                        'login: ' . (isset($bodyArray['contractNumber']) ? $bodyArray['contractNumber'] : ''),
                        'password: ' . (isset($bodyArray['password']) ? $bodyArray['password'] : ''),
                    ];
                    $contentType = array_merge($headerParams, $contentType);
                    if (isset($bodyArray['file'])) {
                        $bodyArray['file'] = new CURLFILE($bodyArray['file']);
                    }
                    if (defined(get_class($this->request) . '::WS_HEADER_DOCUMENT')) {
                        $body = $bodyArray;
                    }
                }
                $curl = curl_init();
                curl_setopt_array(
                    $curl,
                    [
                        CURLOPT_URL => $url,
                        CURLOPT_POSTFIELDS => $body,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_HEADER => true,
                        CURLOPT_VERBOSE => true,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => $contentType,
                    ]
                );
                $response = curl_exec($curl);
                if ($response === false) {
                    throw new Exception('Empty Response.');
                }
                $curlInfo = curl_getinfo($curl);
                $curlError = curl_errno($curl);
                $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $responseHeader = substr($response, 0, $headerSize);
                $responseBody = substr($response, $headerSize);
                curl_close($curl);
                $httpCode = $curlInfo['http_code'];
                if ($curlError) {
                    throw new Exception('cURL error: ' . $curlError);
                }
                break;
            default:
                throw new Exception('Wrong WS call.');
                break;
        }
        if (!in_array($httpCode, [200, 201, 400])) {
            throw new Exception('Bad HTTP code: ' . $httpCode);
        }

        return $this->request->buildResponse($responseHeader, $responseBody);
    }

    /**
     * @param AbstractColissimoRequest $request
     */
    public function setRequest(AbstractColissimoRequest $request)
    {
        $this->request = $request;
    }
}
