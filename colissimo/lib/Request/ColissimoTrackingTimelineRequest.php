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
 * Class ColissimoTrackingTimelineRequest
 */
class ColissimoTrackingTimelineRequest extends ColissimoTrackingEnrichiRequest
{
    const WS_PATH = '/tracking-timeline-ws/rest/tracking/timelineCompany';
    const TRACKING_PROFILE = 'TRACKING_BNUM';

    /** @var string */
    protected $lang;

    /** @var string */
    protected $ip;

    /** @var string */
    protected $parcelNumber;

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['ip'] = $this->ip;
        $this->request['lang'] = $this->lang;
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['profil'] = self::TRACKING_PROFILE;

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
        return ColissimoTrackingTimelineResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
