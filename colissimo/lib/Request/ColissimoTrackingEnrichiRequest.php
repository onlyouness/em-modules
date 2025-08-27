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
 * Class ColissimoTrackingEnrichiRequest
 */
class ColissimoTrackingEnrichiRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/tracking-unified-ws/TrackingUnifiedServiceWSRest/tracking/getTrackingMessagePickupAdressAndDeliveryDate?';
    const WS_CONTENT_TYPE = 'application/json';
    const TRACKING_SUBMISSION_CONTACT = 'TRACKING_PARTNER';
    const WS_HEADER = 1;

    /** @var string */
    protected $lang;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $parcelNumber;

    /**
     * ColissimoTrackingEnrichiRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        if (Configuration::get('COLISSIMO_CONNEXION_KEY')) {
            $this->request['apiKey'] = $this->request['apikey'];
            unset($this->request['apikey']);
        } else {
            $this->request['login'] = $this->request['contractNumber'];
            unset($this->request['contractNumber']);
        }
    }

    /**
     * @param mixed $lang
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @param mixed $ip
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @param mixed $parcelNumber
     * @return ColissimoTrackingEnrichiRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['ip'] = $this->ip;
        $this->request['lang'] = $this->lang;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['profil'] = self::TRACKING_SUBMISSION_CONTACT;

        return;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoTrackingEnrichiResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
