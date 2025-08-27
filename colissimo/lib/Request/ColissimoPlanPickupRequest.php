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
 * Class ColissimoPlanPickupRequest
 */
class ColissimoPlanPickupRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/planPickup?';
    const WS_CONTENT_TYPE = 'application/json';
    const WS_HEADER = 1;

    /** @var string */
    protected $parcelNumber;

    /** @var string */
    protected $mailboxPickingDate;

    /** @var array */
    protected $senderAddress;

    /**
     * @param string $parcelNumber
     * @return ColissimoPlanPickupRequest
     */
    public function setParcelNumber($parcelNumber)
    {
        $this->parcelNumber = $parcelNumber;

        return $this;
    }

    /**
     * @param string $mailboxPickingDate
     * @return ColissimoPlanPickupRequest
     */
    public function setMailboxPickingDate($mailboxPickingDate)
    {
        $this->mailboxPickingDate = $mailboxPickingDate;

        return $this;
    }

    /**
     * @param array $senderAddress
     * @return ColissimoPlanPickupRequest
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
        $this->request['parcelNumber'] = $this->parcelNumber;
        $this->request['mailBoxPickingDate'] = $this->mailboxPickingDate;
        $this->request['sender'] = $this->senderAddress;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoPlanPickupResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
