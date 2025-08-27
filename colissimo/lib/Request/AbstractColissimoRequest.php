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
 * Class AbstractColissimoRequest
 */
abstract class AbstractColissimoRequest
{
    /** @var array */
    protected $request;

    /** @var string */
    protected $xmlLocation;

    /** @var string */
    public $forceEndpoint;

    /**
     * @param mixed $responseHeader
     * @param mixed $responseBody
     * @return mixed
     */
    abstract public function buildResponse($responseHeader, $responseBody);

    /**
     * @return mixed
     */
    abstract public function buildRequest();

    /**
     * AbstractColissimoRequest constructor.
     * @param array $credentials
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        if (Configuration::get('COLISSIMO_CONNEXION_KEY')) {
            if (!isset($credentials['key'])) {
                throw new Exception('Bad credentials.');
            }
        } else {
            if (!isset($credentials['contract_number']) || !isset($credentials['password'])) {
                throw new Exception('Bad credentials.');
            }
        }
        $this->setIdentification($credentials);
        $this->xmlLocation = dirname(__FILE__) . '/../xml/';
    }

    /**
     * @param array $credentials
     */
    private function setIdentification(array $credentials)
    {
        $this->request = [];
        if (Configuration::get('COLISSIMO_CONNEXION_KEY')) {
            $this->request['apikey'] = $credentials['key'];
        } else {
            $this->request['contractNumber'] = $credentials['contract_number'];
            $this->request['password'] = $credentials['password'];
            $this->request['partnerCode'] = $credentials['partner_code'];
        }
        if (isset($credentials['force_endpoint'])) {
            $this->forceEndpoint = $credentials['force_endpoint'];
        }
    }

    /**
     * @param bool $obfuscatePassword
     * @return array|string
     */
    public function getRequest($obfuscatePassword = false)
    {
        if ($obfuscatePassword) {
            $request = $this->request;
            $request['password'] = '*****';
            $request['contractNumber'] = '*****';

            return json_encode($request);
        }

        return json_encode($this->request);
    }
}
