<?php

declare (strict_types = 1);

namespace Hp\Productselect\Controller;

use Hp\Productselect\Entity\SelectProduct;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminProductSelect extends FrameworkBundleAdminController
{

    public function indexAction()
    {
        // $query = new \DbQuery();
        // $query->select('*')
        //     ->from('select_product_backup_2');
        // \Tools::dieObject(\Db::getInstance()->executeS($query));
        $selection = $this->getDoctrine()->getRepository(SelectProduct::class)->findBy([], ['position' => 'ASC']); // \Tools::dieObject($selection);
        return $this->render(
            '@Modules/productselect/views/templates/admin/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle'   => $this->trans('Selection Management', 'Modules.ProductSelect.Admin'),
                'selection'     => $selection,
            ]
        );

    }

    public function createAction(Request $request)
    {
        $section     = new SelectProduct();
        $em          = $this->getDoctrine()->getManager();
        $products    = $this->getProducts();
        $categories  = $this->getCategories();
        $formBuilder = $this->get('productselect.form.identifiable_object.builder');
        $form        = $formBuilder->getForm([
            'products_options'   => $products,
            'categories_options' => $categories,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $products = serialize($form->get('products')->getData());

            $categoryId = $form->get('category_id')->getData();

            $title = $form->get('title')->getData();

            $link = $form->get('link')->getData();

            // Tools::dieObject($form->getData());

            $count = $em->getRepository(SelectProduct::class)->count([]);
            $section->setProducts($products);
            $section->setCategoryId($categoryId);
            $section->setPosition($count + 1);

            if (! empty($categoryId)) {
                $section->setCategoryId($categoryId);
            } else {
                $section->setCategoryId(null);
                $section->setTitle($title);
                $section->setLink($link);
            }

            $em->persist($section);

            $em->flush();

            $this->addFlash('success', 'created successfully!');

            return $this->redirectToRoute('mm_productselect_index');

        }

        return $this->render('@Modules/productselect/views/templates/admin/create.html.twig', [

            'form' => $form->createView(),

        ]);

    }

    public function editAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository(SelectProduct::class)->find($id);

        $products = $this->getProducts();

        $categories = $this->getCategories();

        $formBuilder = $this->get('productselect.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $section->getId(), [
            'products_options'   => $products,
            'categories_options' => $categories,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $products = serialize($form->get('products')->getData());

            $categoryId = $form->get('category_id')->getData();

            $title = $form->get('title')->getData();

            $link = $form->get('link')->getData();

            // Tools::dieObject($form->getData());

            $section->setProducts($products);

            $section->getCategoryId($categoryId);

            if (! empty($categoryId)) {

                $section->setCategoryId($categoryId);

            } else {

                $section->setCategoryId(null);

                $section->setTitle($title);

                $section->setLink($link);

            }

            $em->persist($section);

            $em->flush();

            $this->addFlash('success', 'created successfully!');

            return $this->redirectToRoute('mm_productselect_index');

        }

        return $this->render('@Modules/productselect/views/templates/admin/create.html.twig', [

            'form' => $form->createView(),

        ]);

    }
    public function deleteAction($id)
    {
        $em      = $this->getDoctrine()->getManager();
        $section = $em->getRepository(SelectProduct::class)->find($id);
        $em->remove($section);
        $em->flush();
        $this->addFlash('success', 'deleted successfully!');
        return $this->redirectToRoute('mm_productselect_index');
    }

    public function getProducts()
    {
        $contextClass = \Context::getContext();
        $products     = [];
        $query        = new \DbQuery();
        $query->select('c.id_product, cl.name,c.reference');
        $query->from('product', 'c');
        $query->innerJoin('product_lang', 'cl', 'c.id_product = cl.id_product');
        $query->orderBy('cl.name DESC');
        $allProducts = \Db::getInstance()->executeS($query);

        foreach ($allProducts as $res) {
            $name = $res['name'];

            $products['(' . $res['id_product'] . ') ' . $name] = $res['id_product'];
        }
        return $products;
    }
    public function positionAction(Request $request)
    {
        $data = json_decode($request->getContent(), true); // Decode JSON manually

        if (! isset($data['order']) || ! is_array($data['order'])) {
            return new JsonResponse(['message' => 'Invalid order format'], 400);
        }

        $order = $data['order'];
        $em    = $this->getDoctrine()->getManager();
        $repo  = $em->getRepository(SelectProduct::class);

        foreach ($order as $position => $id) {
            $item = $em->getRepository(SelectProduct::class)->find($id);
            if ($item) {
                $item->setPosition($position);
                $em->persist($item);
            }
        }

        $em->flush();
        return new JsonResponse(['message' => 'Order updated successfully']);

    }

    public function getCategories()
    {

        $contextClass = \Context::getContext();

        $categories = [];

        $query = new \DbQuery();
        $query->select('c.id_category, cl.name');
        $query->from('category', 'c');
        $query->innerJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.id_lang = ' . (int) $contextClass->language->id);
        $query->orderBy('cl.name, c.id_category DESC');
        $allCategories = \Db::getInstance()->executeS($query);

        foreach ($allCategories as $res) {
            $categories['(' . $res['id_category'] . ') ' . $res['name']] = $res['id_category'];
        }
        return $categories;

    }

}
