<?php
namespace Hp\Mmbrandbanner\Controller;

use Hp\Mmbrandbanner\Entity\BrandBanner;
use Hp\Mmbrandbanner\Form\BrandBannerType;
use Manufacturer;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AdminBrandController extends FrameworkBundleAdminController
{

    public function indexAction()
    {
        $em      = $this->getDoctrine()->getManager();
        $banners = $this->getDoctrine()->getRepository(BrandBanner::class)->findAll();

        // Fetch manufacturer details for each banner
        $bannersWithManufacturer = [];
        foreach ($banners as $banner) {
            $manufacturerId = $banner->getManufacturer();
            $manufacturer = new Manufacturer($manufacturerId);
            $bannersWithManufacturer[] = [
                'banner' => $banner,
                'manufacturer' => $manufacturer,
            ];
        }
        
        // \Tools::dieObject($bannersWithManufacturer);
        return $this->render('@Modules/mmbrandbanner/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Brand Banners', 'Modules.Mmbrandbanner.Admin'),
            'banners' => $bannersWithManufacturer,
        ]);
    }
    public function handleForm(Request $request, BrandBanner $banner, $action)
    {
        $em            = $this->getDoctrine()->getManager();
        $activeBanners = count($em->getRepository(BrandBanner::class)->findBy(['active' => 1]));
        $currentLangid = \Context::getContext()->language->id;
        $old_banner    = $banner->getImage();
        $brands        = \Manufacturer::getManufacturers($currentLangid);
        
        $brandsChoices = [];
        foreach ($brands as $brandLevel) {
            $brandsChoices[$brandLevel['name']] = $brandLevel['id_manufacturer'];
        }
        $form = $this->createForm(BrandBannerType::class, $banner, [
            'brands' => $brandsChoices,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if (! $image && $action == "updated") {
                $banner->setImage($old_banner);
            } elseif (! $image && $action == 'created') {
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
            return $this->redirectToRoute('mm_brand_banners_index');
        }
        return $this->render('@Modules/mmbrandbanner/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Brand Banners', 'Modules.Mmbanners.Admin'),
            'form'          => $form->createView(),
            'image'         => $old_banner,
        ]);
    }

    public function createAction(Request $request)
    {
        $banner = new BrandBanner();
        return $this->handleForm($request, $banner, 'created');
    }
    public function editAction(Request $request, BrandBanner $banner)
    {
        return $this->handleForm($request, $banner, 'edited');
    }
    public function deleteAction(BrandBanner $banner)
    {
        $em    = $this->getDoctrine()->getManager();
        $image = $banner->getImage();
        if ($image) {
            $imagePath = _PS_MODULE_DIR_ . 'mmbrandbanner/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->remove($banner);
        $em->flush();
        $this->addFlash('success', 'Banner Deleted Successfully ');
        return $this->redirectToRoute('mm_brand_banners_index');
    }

    public function activeAction(BrandBanner $banner)
    {
        $em       = $this->getDoctrine()->getManager();
        $actives  = $this->getDoctrine()->getRepository(BrandBanner::class)->findBy(['active' => 1]);
        $isActive = $banner->getActive();

        if ($isActive == 0) {
            if (count($actives) < 2) {
                $banner->setActive(1);
            } else {
                $this->addFlash('error', 'You have reached the limit of active banners');
                return $this->redirectToRoute('mm_brand_banners_index');
            }
        } else {
            $banner->setActive(0);
        }

        $em->flush();
        $this->addFlash('success', 'Banner has been updated Successfully .');
        return $this->redirectToRoute('mm_brand_banners_index');
    }

    private function handleImageUpload(?UploadedFile $imageFile, BrandBanner $banner): void
    {
        if ($imageFile) {
            $uploadDir  = _PS_MODULE_DIR_ . 'mmbrandbanner/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (! $filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name

            $newFilename = time() . "_mmbrandbanner.png";

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
