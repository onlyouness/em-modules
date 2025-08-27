<?php
class CategoryController extends CategoryControllerCore
{
    public function initContent()
    {
        parent::initContent();
        $language = $this->context->language;
        $customerId = $this->context->customer->id;
        $customer = new Customer($customerId);
            $id_lang = $language->id;
            $lang_iso = $language->iso_code;
            $address = Address::getFirstCustomerAddressId($customerId);
            $group_id = $customer->id_default_group;
            $countryId = Context::getContext()->cookie->selected_country;
            $country = Country::getNameById($id_lang, $countryId);
            $groupCondition = "";
        
            if ($group_id == 4) {
                $groupCondition = "cg.id_default_group = 4";
            } elseif ($group_id == 5) {
                $groupCondition = "cg.id_default_group = 4 OR cg.id_default_group = 5";
            } 
            if ($group_id == 3) {
                $groupCondition = "cg.id_default_group = 5";
        
                $continent_query = "SELECT c.continent FROM `country` c INNER JOIN `country_translation` ct ON ct.country_id = c.id WHERE ct.name = '" . pSQL($country) . "' ";
                $seller_continent = Db::getInstance()->getValue($continent_query);
        
                $countries_query = "SELECT ct.name FROM `country` c INNER JOIN `country_translation` ct ON ct.country_id = c.id WHERE c.continent = '" . pSQL($seller_continent) ."' " ;
                $continent_countries = Db::getInstance()->executeS($countries_query);
        
                $country_list = [];
                foreach ($continent_countries as $continent_country) {
                    $country_list[] = "'" . pSQL($continent_country['name']) . "'";
                }
        
                $country_condition = empty($country_list) ? "" : "AND cl.name IN (" . implode(",", $country_list) . ")";
        
                $query = "SELECT sp.id_product FROM `ps_seller_product` sp INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer INNER JOIN `ps_country_lang` cl ON cl.name = s.country WHERE (" . $groupCondition . ") " . $country_condition ;
            } else {
                $query = "SELECT sp.id_product FROM `ps_seller_product` sp INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer WHERE " . $groupCondition;
            }
            
            $productIds = Db::getInstance()->executeS($query);
            $productIdList = [];
            foreach ($productIds as $productItem) {
                $productIdList[] = $productItem['id_product'];
            }

    }

}
