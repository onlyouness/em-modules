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
 * Class ColissimoMerchantAddress
 */
class ColissimoMerchantAddress
{
    /** @var array */
    private static $types = ['sender', 'return'];

    /** @var string */
    public $type;

    /** @var string */
    public $companyName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $firstName;

    /** @var string */
    public $line0;

    /** @var string */
    public $line1;

    /** @var string */
    public $line2;

    /** @var string */
    public $line3;

    /** @var string */
    public $countryCode;

    /** @var string */
    public $city;

    /** @var string */
    public $zipCode;

    /** @var string */
    public $phoneNumber;

    /** @var string */
    public $email;

    /**
     * ColissimoMerchantAddress constructor.
     * @param string $type
     * @param array $addressArray
     */
    public function __construct($type = 'sender', $addressArray = [])
    {
        if (!in_array($type, self::$types)) {
            $this->type = 'sender';
        } else {
            $this->type = $type;
        }
        if (!$addressArray) {
            $addressString = Configuration::get('COLISSIMO_' . Tools::strtoupper($this->type) . '_ADDRESS');
            $addressArray = (array) json_decode($addressString, true);
        }
        $this->hydrate($addressArray);
    }

    /**
     * @param array $array
     */
    private function hydrate($array)
    {
        $this->companyName = isset($array[$this->type . '_company']) ? $array[$this->type . '_company'] : null;
        $this->lastName = isset($array[$this->type . '_lastname']) ? $array[$this->type . '_lastname'] : null;
        $this->firstName = isset($array[$this->type . '_firstname']) ? $array[$this->type . '_firstname'] : null;
        $this->line0 = isset($array[$this->type . '_address3']) ? $array[$this->type . '_address3'] : null;
        $this->line1 = isset($array[$this->type . '_address4']) ? $array[$this->type . '_address4'] : null;
        $this->line2 = isset($array[$this->type . '_address1']) ? $array[$this->type . '_address1'] : null;
        $this->line3 = isset($array[$this->type . '_address2']) ? $array[$this->type . '_address2'] : null;
        $this->countryCode = isset($array[$this->type . '_country']) ? $array[$this->type . '_country'] : null;
        $this->city = isset($array[$this->type . '_city']) ? $array[$this->type . '_city'] : null;
        $this->zipCode = isset($array[$this->type . '_zipcode']) ? $array[$this->type . '_zipcode'] : null;
        $this->phoneNumber = isset($array[$this->type . '_phone']) ? $array[$this->type . '_phone'] : null;
        $this->email = isset($array[$this->type . '_email']) ? $array[$this->type . '_email'] : null;
    }

    /**
     * @return ColissimoMerchantAddress
     */
    public static function getMerchantReturnAddress()
    {
        $useReturnAddress = Configuration::get('COLISSIMO_USE_RETURN_ADDRESS');
        if ($useReturnAddress) {
            return new self('return');
        } else {
            return new self('sender');
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            $this->type . '_company' => $this->companyName,
            $this->type . '_lastname' => $this->lastName,
            $this->type . '_firstname' => $this->firstName,
            $this->type . '_address1' => $this->line2,
            $this->type . '_address2' => $this->line3,
            $this->type . '_address3' => $this->line0,
            $this->type . '_address4' => $this->line1,
            $this->type . '_country' => $this->countryCode,
            $this->type . '_city' => $this->city,
            $this->type . '_zipcode' => $this->zipCode,
            $this->type . '_phone' => $this->phoneNumber,
            $this->type . '_email' => $this->email,
        ];
    }

    /**
     * @return false|string
     */
    public function toJSON()
    {
        $array = (array) $this->toArray();

        return json_encode($array);
    }
}
