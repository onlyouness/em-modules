<?php
namespace Hp\Filterdproducts;

use Address;
use Context;
use Country;
use Db;

class FilteredProducts
{
    public static $ids = [];
    public function getFilteredProducts($products, $filteredIds)
    {
        $filteredProducts = [];
        foreach ($products as $key => $product) {
            if (in_array($product['id_product'], $filteredIds)) {
                $filteredProducts[] = $product;
            }
        }
        return array_slice($filteredProducts, 0, 10);
    }
    public static function getFilteredIds()
    {
        $context       = Context::getContext();
        $id_lang       = $context->language->id;
        $iso_code      = $context->language->iso_code;
        $customer      = $context->customer;
        $id_customer   = $customer->id;
        $productIdList = [];

         if (!empty(self::$ids)) {
            $address        = Address::getFirstCustomerAddressId($id_customer);
            $group_id       = $customer->id_default_group;
            $countryId      = Context::getContext()->cookie->selected_country;
            $country        = Country::getNameById($id_lang, $countryId);
            $groupCondition = "";

            if ($group_id == 4) {
                $groupCondition = "cg.id_default_group = 4";
            }
            if ($group_id == 5) {
                $groupCondition = "cg.id_default_group = 4 OR cg.id_default_group = 5";
            }
            if ($group_id == 3 || $group_id == 6) {
                $groupCondition = "cg.id_default_group = 5";

                $continent_query = "SELECT c.continent
                            FROM `country` c
                            INNER JOIN `country_translation` ct ON ct.country_id = c.id
                            WHERE ct.name = '" . pSQL($country) . "'";

                $seller_continent = Db::getInstance()->getValue($continent_query);

                $countries_query = "SELECT ct.name
                            FROM `country` c
                            INNER JOIN `country_translation` ct ON ct.country_id = c.id
                            WHERE c.continent = '" . pSQL($seller_continent) . "'";

                $continent_countries = Db::getInstance()->executeS($countries_query);

                $country_list = [];
                foreach ($continent_countries as $continent_country) {
                    $country_list[] = "'" . pSQL($continent_country['name']) . "'";
                }

                $country_condition = empty($country_list) ? "" : "AND cl.name IN (" . implode(",", $country_list) . ")";

                $query = "SELECT sp.id_product
                  FROM `" . _DB_PREFIX_ . "seller_product` sp
                  INNER JOIN `" . _DB_PREFIX_ . "seller` s ON s.id_seller = sp.id_seller
                  INNER JOIN `" . _DB_PREFIX_ . "customer` cg ON cg.id_customer = s.id_customer
                  INNER JOIN `" . _DB_PREFIX_ . "country_lang` cl ON cl.name = s.country
                  WHERE ($groupCondition) $country_condition";
            } else {
                $query = "SELECT sp.id_product
                  FROM `" . _DB_PREFIX_ . "seller_product` sp
                  INNER JOIN `" . _DB_PREFIX_ . "seller` s ON s.id_seller = sp.id_seller
                  INNER JOIN `" . _DB_PREFIX_ . "customer` cg ON cg.id_customer = s.id_customer
                  WHERE $groupCondition";
            }

            $productIds    = Db::getInstance()->executeS($query);
            $productIdList = [];

            foreach ($productIds as $productItem) {
                $productIdList[] = $productItem['id_product'];
            }
        }
        return self::$ids;

    }
}
