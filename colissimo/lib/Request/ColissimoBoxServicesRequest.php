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
 * Class ColissimoBoxServicesRequest
 */
class ColissimoBoxServicesRequest extends AbstractColissimoRequest
{
    const WS_TYPE = 'CURL';
    const WS_PATH = '/api-ewe/v1/rest/additionalinformations';
    const WS_CONTENT_TYPE = 'application/json';

    /**
     * ColissimoBoxServicesRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        if (Configuration::get('COLISSIMO_CONNEXION_KEY')) {
            $this->request['credential']['apiKey'] = $this->request['apikey'];
            unset($this->request['apikey']);
        } else {
            $this->request['credential']['login'] = $this->request['contractNumber'];
            $this->request['credential']['password'] = $this->request['password'];
            $this->request['credential']['partnerClientCode'] = $this->request['partnerCode'];
            unset($this->request['partnerCode']);
            unset($this->request['password']);
            unset($this->request['contractNumber']);
        }
        $this->request['token'] = '';
        $this->request['tagInfoPartner'] = '';
    }

    /**
     * @return void
     */
    public function buildRequest()
    {
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
        return ColissimoBoxServicesResponse::buildFromResponse($responseHeader, $responseBody);
    }
}
