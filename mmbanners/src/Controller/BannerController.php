<?php

namespace Hp\Mmbanners\Controller;

use Hp\Mmbanners\Entity\Banner;
use Hp\Mmbanners\Entity\Position;
use Hp\Mmbanners\Form\BannerType;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BannerController extends FrameworkBundleAdminController
{

    public function indexAction()
    {
        $banners = $this->getDoctrine()->getRepository(Banner::class)->findAll();

        return $this->render('@Modules/mmbanners/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Banners', 'Modules.Mmbanners.Admin'),
            'banners' => $banners,
        ]);
    }
    public function handleForm(Request $request, Banner $banner, $action)
    {
        $em = $this->getDoctrine()->getManager();
        $activeBanners = count($em->getRepository(Banner::class)->findBy(['active' => 1]));
        //getting the image and its base url
        $old_image = $banner->getImage();
        $link = \Context::getContext()->link;
        $baseUrl = $link->getBaseLink();
        $products = $this->getDiscountProducts();
        $productsChoices = [];
        foreach ($products as $product) {
            $productsChoices[$product['name']] = $product['id_product'];
        }

        $form = $this->createForm(BannerType::class, $banner, [
            'products' => $productsChoices,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if (!$image && $action == "updated") {
                $banner->setImage($old_image);
            } elseif (!$image && $action == 'created') {
                $this->addFlash('error', 'The image is required.');
                return $this->redirectToRoute('mm_banners_create');
            } else {
                $this->handleImageUpload($image, $banner);
            }

            if ($image) {
                $this->handleImageUpload($image, $banner);
            } else {
                $old_banner = $banner->getImage();
                $banner->setImage($old_banner);
            }
            
            if ($activeBanners >= 2) {
                $banner->setActive(0);
            } else {
                $banner->setActive(1);
            }

            $em->persist($banner);
            $em->flush();

            $this->addFlash('success', 'Banner ' . $action . ' Successfully ');
            return $this->redirectToRoute('mm_banners_index');
        }
        return $this->render('@Modules/mmbanners/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Banners', 'Modules.Mmbanners.Admin'),
            'form' => $form->createView(),
        ]);
    }

    public function createAction(Request $request)
    {
        $banner = new Banner();
        return $this->handleForm($request, $banner, 'created');
    }
    public function editAction(Request $request, Banner $banner)
    {
        return $this->handleForm($request, $banner, 'edited');
    }
    public function deleteAction(Banner $banner)
    {
        $em = $this->getDoctrine()->getManager();
        $image = $banner->getImage();
        if ($image) {
            $imagePath = _PS_MODULE_DIR_ . 'mmbanners/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->remove($banner);
        $em->flush();
        $this->addFlash('success', 'Banner Deleted Successfully ');
        return $this->redirectToRoute('mm_banners_index');
    }

    public function activeAction(Banner $banner)
    {
        $em = $this->getDoctrine()->getManager();
        $actives = $this->getDoctrine()->getRepository(Banner::class)->findBy(['active' => 1]);
        $isActive = $banner->getActive();

        if ($isActive == 0) {
            if (count($actives) < 2) {
                $banner->setActive(1);
            } else {
                $this->addFlash('error', 'You have reached the limit of active banners');
                return $this->redirectToRoute('mm_banners_index');
            }
        } else {
            $banner->setActive(0);
        }

        $em->flush();
        $this->addFlash('success', 'Banner has been updated Successfully .');
        return $this->redirectToRoute('mm_banners_index');
    }
    public function getDiscountProducts()
    {
        $sql = 'SELECT pl.name,pl.id_product
        FROM ' . _DB_PREFIX_ . 'product p
        INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON p.id_product = pl.id_product';
        return \Db::getInstance()->executeS($sql);
    }
    private function handleImageUpload(?UploadedFile $imageFile, Banner $banner): void
    {
        if ($imageFile) {
            $uploadDir = _PS_MODULE_DIR_ . 'mmbanners/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (!$filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name
         
            $newFilename = time()."_banner.png";


            if ($imageFile->isValid()) {
                // Proceed with the move
                $imageFile->move($uploadDir, $newFilename);
            } else {
                // Handle the error
                echo "The file is not valid.";
            }

            // Store the path in the database (relative path)
            $banner->setImage($newFilename);
        }
    }
}
