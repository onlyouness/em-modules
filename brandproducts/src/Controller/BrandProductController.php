<?php

declare (strict_types = 1);
namespace Hp\Brandproducts\Controller;

use Hp\Brandproducts\Entity\BrandProduct;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

class BrandProductController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $allSections = $em->getRepository(BrandProduct::class)->findAll();
        $sections = [];
        $langId = \Context::getContext()->language->id;
        $brands = $this->getBrands($langId);
        $categories = $this->getCategories($langId);
        foreach($allSections as $row){
            $rowData = [];
            $brand = $row->getBrand();
            $category = $row->getCategory();
            if(!is_null($brand)){
                $foundBrand = array_search($brand,$brands);
                $rowData['brand'] = $foundBrand;
                $rowData['category'] = null;
            }
            if(!is_null($category)){
                $foundCategory = array_search($category,$categories);
                $rowData['category'] = $foundCategory;
                $rowData['brand'] = null;
            }
            $rowData['type'] = $row->getType();
            $rowData['id'] = $row->getId();
            $sections[] = $rowData;
        }
        // \Tools::dieObject($sections);

        return $this->render('@Modules/brandproducts/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Brand Or Category Section', 'Modules.BrandProduct.Admin'),
            'sections'=>$sections
        ]);
    }
    public function createAction(Request $request)
    {
        $em           = $this->getDoctrine()->getManager();
        $brandProduct = new BrandProduct();
        $languageId   = \Context::getContext()->language->id;
        $brands       = $this->getBrands($languageId);
        $categories   = $this->getCategories($languageId);
        $languages    = \Language::getLanguages(true);
        $formBuilder  = $this->get('brandproduct.form.identifiable_object.builder');
        $form         = $formBuilder->getForm(['brands' => $brands, 'categories' => $categories]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $brand    = $form->get('brand')->getData();
            $category = $form->get('category')->getData();
            dump($brand);
            dump($category);
            $brandProduct->setType($form->get('display_type')->getData());
            if (! is_null($brand)) {
                $brandProduct->setBrand($brand);
            }
            if (! is_null($category)) {
                $brandProduct->setCategory($category);
            }
            $em->persist($brandProduct);
            $em->flush();
            $this->addFlash('success', $this->trans('Section Created', 'Modules.BrandProduct.Admin'));
            return $this->redirectToRoute('mm_product_brand_index');
        }
        return $this->render('@Modules/brandproducts/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Brand Products', 'Modules.BrandProducts.Admin'),
            'form'          => $form->createView(),

        ]);
    }
    public function editAction(BrandProduct $brandProduct, Request $request)
    {
        $em          = $this->getDoctrine()->getManager();
        $languageId  = \Context::getContext()->language->id;
        $brands      = $this->getBrands($languageId);
        $categories  = $this->getCategories($languageId);
        $formBuilder = $this->get('brandproduct.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $brandProduct->getId(), ['brands' => $brands, 'categories' => $categories]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $brand    = $form->get('brand')->getData();
            $category = $form->get('category')->getData();
            dump($brand);
            dump($category);
            // \Tools::dieObject($form->getData());
            $brandProduct->setType($form->get('display_type')->getData());
            if (! is_null($brand)) {
                $brandProduct->setBrand($brand);
                $brandProduct->setCategory(null);
            }
            if (! is_null($category)) {
                $brandProduct->setCategory($category);
                $brandProduct->setBrand(null);
            }
            $em->persist($brandProduct);
            $em->flush();
            $this->addFlash('success', $this->trans('Section Edited', 'Modules.BrandProduct.Admin'));
            return $this->redirectToRoute('mm_product_brand_index');
        }
        return $this->render('@Modules/brandproducts/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Brand Products', 'Modules.BrandProducts.Admin'),
            'form'          => $form->createView(),

        ]);
    }
    public function deleteAction(BrandProduct $brandProduct)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($brandProduct);
        $em->flush();
        $this->addFlash('success', $this->trans('Section Deleted', 'Modules.BrandProduct.Admin'));
        return $this->redirectToRoute('mm_product_brand_index');
    }
    public function getBrands($id_lang)
    {
        $db     = \Db::getInstance();
        $query  = new \DbQuery();
        $brands = [];
        $query->select('m.id_manufacturer,m.name')
            ->from('manufacturer', 'm')
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
        $db         = \Db::getInstance();
        $query      = new \DbQuery();
        $categories = [];
        $query->select('c.id_category,cl.name')
            ->from('category', 'c')
            ->innerJoin('category_lang', 'cl', 'cl.id_category = c.id_category')
            ->where('cl.id_lang = ' . (int) $id_lang)
            ->where('c.id_parent != 0')
            ->orderBy('cl.name,c.id_category DESC')
        ;
        $results = $db->executeS($query);
        foreach ($results as $res) {
            $categories[$res['name']] = $res['id_category'];
        }
        return $categories;
    }
}
