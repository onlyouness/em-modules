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
 * Class ColissimoTrackingEnrichiResponse
 */
class ColissimoTrackingEnrichiResponse extends AbstractColissimoResponse implements ColissimoReturnedResponseInterface
{
    /** @var array */
    public $parcel;

    /** @var array */
    public $error;

    /** @var array */
    public $message;

    /** @var string */
    public $code;

    /** @var string */
    public $messageCode;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public static function buildFromResponse($responseHeader, $responseBody)
    {
        $trackingEnrichiResponse = new self();
        $responseArray = json_decode($responseBody, true);
        if (!empty($responseArray)) {
            $trackingEnrichiResponse->response = $responseArray;
            if (isset($responseArray['code'])) {
                $trackingEnrichiResponse->code = $responseArray['code'];
            }
            if (isset($responseArray['messageCode'])) {
                $trackingEnrichiResponse->messageCode = $responseArray['messageCode'];
            }
            if (isset($responseArray['message'])) {
                $trackingEnrichiResponse->message = $responseArray['message'];
            }
            if (isset($responseArray['error'])) {
                $trackingEnrichiResponse->error = $responseArray['error'];
            }
            if (isset($responseArray['parcel'])) {
                $trackingEnrichiResponse->parcel = $responseArray['parcel'];
            }
        }

        return $trackingEnrichiResponse;
    }
}
