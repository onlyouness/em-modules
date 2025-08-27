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
 * Class ColissimoGenerateBordereauResponse
 */
class ColissimoGenerateBordereauResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string */
    public $bordereau;

    /** @var array */
    public $bordereauHeader;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $soapResponseParser = new ColissimoResponseParser();
        try {
            $parsedResponse = $soapResponseParser->parseBody($responseBody);
            $parsedHeaders = $soapResponseParser->parseHeaders($responseHeader);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $generateBordereauResponse = new self();
        $contentType = $soapResponseParser->parseContentType($parsedHeaders);
        if (isset($parsedResponse[$contentType])) {
            $xml = new SimpleXMLElement($parsedResponse[$contentType]);
            $xml->registerXPathNamespace('ns2', 'http://sls.ws.coliposte.fr');
            $xml->registerXPathNamespace('xop', 'http://www.w3.org/2004/08/xop/include');
            $bordereau = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/bordereau/bordereauDataHandler/xop:Include');
            if (!empty($bordereau)) {
                $cidBordereau = substr($bordereau[0]->attributes()->href->__toString(), 4);
                if (isset($parsedResponse['<' . $cidBordereau . '>'])) {
                    $generateBordereauResponse->bordereau = base64_encode($parsedResponse['<' . $cidBordereau . '>']);
                }
            }
            $bordereauHeader = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/bordereau/bordereauHeader');
            if (!empty($bordereauHeader)) {
                $generateBordereauResponse->bordereauHeader = json_decode(json_encode($bordereauHeader[0]), true);
                $generateBordereauResponse->response['bordereauHeader'] = $generateBordereauResponse->bordereauHeader;
            }
            $messages = $xml->xpath('soap:Body/ns2:generateBordereauByParcelsNumbersResponse/return/messages');
            $generateBordereauResponse->messages = json_decode(json_encode($messages), true);
            $generateBordereauResponse->response['messages'] = $generateBordereauResponse->messages;
        }

        return $generateBordereauResponse;
    }
}
