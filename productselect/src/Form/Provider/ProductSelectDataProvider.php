<?php

namespace Hp\Productselect\Form\Provider;



use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Db;
use Tools;
use DbQuery;


class ProductSelectDataProvider implements FormDataProviderInterface
{
    public function getData($id)

    {
        $section = $this->getProductSelected($id);
        $products = unserialize($section['products']);
        $section['products']  = $products;
        return $section;
    }

    public function getDefaultData()

    {

        $id_lang = \Context::getContext()->language->id;
        $products = $this->getProducts();
        $categories = $this->getCategories();
        return [
            'title'       => '',
            'link'        => '',
            'products'    => [],
            'category_id' => '',
        ];

    

    }

    public function getProducts()

    {

        $contextClass = \Context::getContext();

        $products     = [];
        $query = new \DbQuery();
        $query->select('c.id_product, cl.name,c.reference');
        $query->from('product', 'c');
        $query->innerJoin('product_lang', 'cl', 'c.id_product = cl.id_product');
        $query->orderBy('cl.name DESC');
        $allProducts = \Db::getInstance()->executeS($query);

        foreach ($allProducts as $res) {
            $name = $res['name'];
            
            $products['('.$res['id_product'].') '.$name] = $res['id_product'];
        }
        return $products;

    }

    public function getCategories()

    {

        $contextClass  = \Context::getContext();

        $categories    = [];

        $query = new \DbQuery();
        $query->select('c.id_category, cl.name');
        $query->from('category', 'c');
        $query->innerJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.id_lang = ' . (int) $contextClass->language->id);
        $query->orderBy('cl.name, c.id_category DESC');
        $allCategories = \Db::getInstance()->executeS($query);


        foreach ($allCategories as $res) {
            $categories[$res['name']] = $res['id_category'];
        }
        return $categories;

    }

    public function getProductSelected ($id){
        $db = \Db::getInstance();
        $query = new \DbQuery();
        $query->select('sp.*')
            ->from('select_product','sp')
            ->where('sp.id = '. (int)$id);
        return $db->getRow($query);
    }




}

