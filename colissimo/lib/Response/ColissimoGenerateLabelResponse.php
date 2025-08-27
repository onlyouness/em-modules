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
 * Class ColissimoGenerateLabelResponse
 */
class ColissimoGenerateLabelResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var string */
    public $label;

    /** @var string */
    public $cn23;

    /** @var string */
    public $parcelNumber;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $jsonResponseParser = new ColissimoResponseParser();
        try {
            $parsedResponse = $jsonResponseParser->parseBody($responseBody);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $generateLabelResponse = new self();
        if (isset($parsedResponse['<jsonInfos>'])) {
            $generateLabelResponse->response = json_decode($parsedResponse['<jsonInfos>'], true);
            $generateLabelResponse->messages = $generateLabelResponse->response['messages'];
        }
        if (isset($generateLabelResponse->response['labelV2Response']['parcelNumber'])) {
            $generateLabelResponse->parcelNumber = $generateLabelResponse->response['labelV2Response']['parcelNumber'];
        }
        if (isset($parsedResponse['<label>'])) {
            $generateLabelResponse->label = base64_encode($parsedResponse['<label>']);
        }
        if (isset($generateLabelResponse->response['tokenV2Response']['parcelNumber'])) {
            $generateLabelResponse->parcelNumber = $generateLabelResponse->response['tokenV2Response']['parcelNumber'];
        }
        if (isset($parsedResponse['<cn23>'])) {
            $generateLabelResponse->cn23 = base64_encode($parsedResponse['<cn23>']);
        }

        return $generateLabelResponse;
    }
}
