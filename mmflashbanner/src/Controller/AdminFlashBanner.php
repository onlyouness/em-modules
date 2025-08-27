<?php

namespace Hp\MmFlashBanner\Controller;

use Hp\MmFlashBanner\Entity\Banner;
use Hp\MmFlashBanner\Entity\Section;
use Hp\MmFlashBanner\Form\BannerType;
use Hp\MmFlashBanner\Form\SectionType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AdminFlashBanner extends FrameworkBundleAdminController
{

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $banners = $this->getDoctrine()->getRepository(Banner::class)->findAll();
        $header = $this->getDoctrine()->getRepository(Section::class)->find(1);
        if (!$header) {
            $header = new Section();
            $header->setTitle('Default Title');
            $header->setDescription('Default Description');
            $header->setShortDescription('Default Short Description');
            $em->persist($header);
            $em->flush();
        }

        return $this->render('@Modules/mmflashbanner/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Flash Banners', 'Modules.Mmbanners.Admin'),
            'banners' => $banners,
            'header' => $header,
        ]);
    }
    public function handleForm(Request $request, Banner $banner, $action)
    {
        $em = $this->getDoctrine()->getManager();
        $activeBanners = count($em->getRepository(Banner::class)->findBy(['active' => 1]));
        $currentLangid = \Context::getContext()->language->id;
        $old_banner = $banner->getImage();
        $categories = \Category::getCategories($currentLangid);

        $categoriesChoices = [];
        foreach ($categories as $categoryLevel) {
            foreach ($categoryLevel as $categoryData) {
                if (isset($categoryData['infos'])) {
                    $categoriesChoices[$categoryData['infos']['name']] = $categoryData['infos']['id_category'];
                }
            }
        }
    
        $form = $this->createForm(BannerType::class, $banner, [
            'categories' => $categoriesChoices,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if (!$image && $action == "updated") {
                $banner->setImage($old_banner);
            } elseif (!$image && $action == 'created') {
                $this->addFlash('error', 'The image is required.');
                return $this->redirectToRoute('mm_flash_banners_create');
            } else {
                $this->handleImageUpload($image, $banner);
            }


            if ($image) {
                $this->handleImageUpload($image, $banner);
            } else {
                $old_banner = $banner->getImage();
                $banner->setImage($old_banner);
            }

            if ($activeBanners > 2) {
                $banner->setActive(0);
            } else {
                $banner->setActive(1);
            }

            $em->persist($banner);
            $em->flush();

            $this->addFlash('success', 'Banner ' . $action . ' Successfully ');
            return $this->redirectToRoute('mm_flash_banners_index');
        }
        return $this->render('@Modules/mmflashbanner/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Flash Banner', 'Modules.Mmbanners.Admin'),
            'form' => $form->createView(),
            'image'=>$old_banner
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
            $imagePath = _PS_MODULE_DIR_ . 'mmflashbanner/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->remove($banner);
        $em->flush();
        $this->addFlash('success', 'Banner Deleted Successfully ');
        return $this->redirectToRoute('mm_flash_banners_index');
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
                return $this->redirectToRoute('mm_flash_banners_index');
            }
        } else {
            $banner->setActive(0);
        }

        $em->flush();
        $this->addFlash('success', 'Banner has been updated Successfully .');
        return $this->redirectToRoute('mm_flash_banners_index');
    }

    public function editHeaderAction(Request $request, Section $section)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectionType::class, $section);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($section);
            $em->flush();
            $this->addFlash('success', 'Header has been updated Successfully .');
            return $this->redirectToRoute('mm_flash_banners_index');
        }
        return $this->render('@Modules/mmflashbanner/views/templates/admin/edit_header.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Flash Banner', 'Modules.Mmbanners.Admin'),
            'form' => $form->createView(),
        ]);
    }

    private function handleImageUpload(?UploadedFile $imageFile, Banner $banner): void
    {
        if ($imageFile) {
            $uploadDir = _PS_MODULE_DIR_ . 'mmflashbanner/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (!$filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the uploaded file to the 'img' directory
            $imageFile->move($uploadDir, $newFilename);

            // Store the path in the database (relative path)
            $banner->setImage($newFilename);
        }
    }

    
}
