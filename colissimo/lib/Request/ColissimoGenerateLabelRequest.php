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
 * Class ColissimoGenerateLabelRequest
 */
class ColissimoGenerateLabelRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/sls-ws/SlsServiceWSRest/2.0/generateLabel?';
    const WS_CONTENT_TYPE = 'application/json';
    const WS_HEADER = 1;

    /** @var array */
    protected $output;

    /** @var array */
    protected $senderAddress;

    /** @var array */
    protected $addresseeAddress;

    /** @var array */
    protected $shipmentOptions;

    /** @var array */
    protected $shipmentServices;

    /** @var array */
    protected $customsOptions;

    /** @var array */
    protected $fields;

    /**
     * @param array $output
     * @return ColissimoGenerateLabelRequest
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param array $senderAddress
     * @return ColissimoGenerateLabelRequest
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * @param array $addresseeAddress
     * @return ColissimoGenerateLabelRequest
     */
    public function setAddresseeAddress($addresseeAddress)
    {
        $this->addresseeAddress = $addresseeAddress;

        return $this;
    }

    /**
     * @param array $shipmentOptions
     * @return ColissimoGenerateLabelRequest
     */
    public function setShipmentOptions($shipmentOptions)
    {
        $this->shipmentOptions = $shipmentOptions;

        return $this;
    }

    /**
     * @param array $shipmentServices
     * @return ColissimoGenerateLabelRequest
     */
    public function setShipmentServices($shipmentServices)
    {
        $this->shipmentServices = $shipmentServices;

        return $this;
    }

    /**
     * @param array $customsOptions
     * @return ColissimoGenerateLabelRequest
     */
    public function setCustomsOptions($customsOptions)
    {
        $this->customsOptions = $customsOptions;

        return $this;
    }

    /**
     * @param array $customField
     * @return ColissimoGenerateLabelRequest
     */
    public function addCustomField($customField)
    {
        foreach ($customField as $key => $field) {
            $this->fields['customField'][$key] = [
                'key' => $field['key'],
                'value' => $field['value'],
            ];
        }

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function buildRequest()
    {
        $this->request['outputFormat'] = $this->output;
        $this->request['letter']['service'] = $this->shipmentServices;
        $this->request['letter']['parcel'] = $this->shipmentOptions;
        if (!empty($this->customsOptions)) {
            $this->request['letter']['customsDeclarations'] = $this->customsOptions;
        }
        $this->request['letter']['sender'] = $this->senderAddress;
        $this->request['letter']['addressee'] = $this->addresseeAddress;
        $this->request['fields'] = $this->fields;
    }

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     * @throws Exception
     */
    public function buildResponse($responseHeader, $responseBody)
    {
        return ColissimoGenerateLabelResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
