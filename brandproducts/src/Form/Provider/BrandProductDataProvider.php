<?php
namespace Hp\Brandproducts\Form\Provider;

use DbQuery;
use Hp\Brandproducts\Repository\BrandProductRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class BrandProductDataProvider implements FormDataProviderInterface
{
    /**
     * @var BrandProductRepository
     */
    private $repository;

    /**
     * @param BrandProductRepository $repository
     */
    public function __construct(BrandProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get data for a specific Brand product.
     * This is used for editing an existing Brand product.
     *
     * @param int $brandProductId
     * @return array
     */
    public function getData($brandProductId)
    {
        $data = [];
        $id_lang = \Context::getContext()->language->id;
        $brands = $this->getBrands($id_lang);
        $categories = $this->getCategories($id_lang);
        $brandProduct = $this->repository->find($brandProductId);
        $brand = $brandProduct->getBrand();
        $category = $brandProduct->getCategory();
        $type = $brandProduct->getType();
        if($brandProduct){
            $data['display_type'] = $type;
            if(!is_null($brand)){
                $foundBrand = array_search($brand,$brands);
                $data['brand'] = $foundBrand;
            }
            if(!is_null($category)){
                $foundCategory = array_search($category,$categories);
                $data['category'] = $foundCategory;
            }
            $data['brands'] = key($brands);
            $data['categories'] = key($categories);
        }
        dump($brandProduct);
        dump($category);
        dump($brand);
        dump($data);
        return $data;
    }
    public function getDefaultData()
    {
        $id_lang = \Context::getContext()->language->id;
        $brands = $this->getBrands($id_lang);
        $categories = $this->getCategories($id_lang);
        return [
            'display_type' => 'brand',
            'brand' => '',
            'category' =>'',
            'brands' => key($brands), 
            'categories' => key($categories), 
        ];
    }

    public function getBrands($id_lang)
    {
        $db = \Db::getInstance();
        $query = new \DbQuery();
        $brands = [];
        $query->select('m.id_manufacturer,m.name')
                ->from('manufacturer','m')
                ->orderBy('m.id_manufacturer DESC')
                ;
        $results = $db->executeS($query);
        foreach ($results as $res) {
            $brands[$res['name']] = $res['id_manufacturer'];
        }
        return $brands;
    }
    public function getCategories($id_lang)
    {
        $db = \Db::getInstance();
        $query = new \DbQuery();
        $categories = [];
        $query->select('c.id_category,cl.name')
                ->from('category','c')
                ->innerJoin('category_lang','cl','cl.id_category = c.id_category')
                ->where('cl.id_lang = '.(int)$id_lang)
                ->orderBy('c.id_category DESC')
                ;
        $results = $db->executeS($query);
        foreach ($results as $res) {
            $categories[$res['name']] = $res['id_category'];
        }
        return $categories;
    }
}