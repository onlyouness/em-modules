<?php
declare (strict_types = 1);
namespace Hp\Nosatouts\Controller;

use Hp\Nosatouts\Entity\NosAtout;
use Hp\Nosatouts\Entity\NosAtoutLang;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminNosAtoutController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $em          = $this->getDoctrine()->getManager();
        $context = \Context::getContext();
        $langId = (int) $context->language->id;
        $atoutbanner = $em->getRepository(NosAtout::class)->findAtoutByLang($langId);
        return $this->render('@Modules/nosatouts/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Banner', 'Modules.RegimeBanner.Admin'),
            'banners'=>$atoutbanner,
        ]);
    }
    public function createAction(Request $request)
    {
        $em         = $this->getDoctrine()->getManager();
        $banner     = new NosAtout();
        $languageId = \Context::getContext()->language->id;
        $languages  = \Language::getLanguages(true);

        $formBuilder = $this->get('atout.form.identifiable_object.builder');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $banner->setActive(1);
            $this->handleImageUpload($image, $banner);

            /** @var Lang $language */
            foreach ($languages as $language) {
                $groupLang   = new NosAtoutLang();
                $id_lang     = $language['id_lang'];
                $title       = $form->get('title')->getData();
                $description = $form->get('description')->getData();

                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                $groupLang->setLang($langEntity);

                if (isset($title[$id_lang])) {
                    $groupLang->setTitle($title[$id_lang]);
                } else {
                    $groupLang->setTitle('');
                }
                if (isset($description[$id_lang])) {
                    $groupLang->setDescription($description[$id_lang]);
                } else {
                    $groupLang->setDescription('');
                }
                $banner->addBannerLang($groupLang);
            }

            $em->persist($banner);
            $em->flush();
            $this->addFlash('success', $this->trans('Atout created', 'Modules.RegimeBanner.Admin'));
            return $this->redirectToRoute('mm_atout_banner_index');
        }
        return $this->render('@Modules/nosatouts/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Nos Atouts', 'Modules.RegimeBanner.Admin'),
            'form'          => $form->createView(),

        ]);
    }
    public function editAction(Request $request, NosAtout $banner)
    {
        $em         = $this->getDoctrine()->getManager();
        $languageId = \Context::getContext()->language->id;
        $languages  = \Language::getLanguages(true);

        $formBuilder = $this->get('atout.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $banner->getId());
        $form->handleRequest($request);

        $context  = \Context::getContext();
        $base_dir = $context->shop->getBaseURL(true);

        if ($form->isSubmitted() && $form->isValid()) {
            $newImage = $form->get('image')->getData();

            $wasActive = $banner->getActive();

            $banner->setActive($wasActive); // Preserve the active status

            // Handle image upload only if a new image is submitted
            if ($newImage) {
                $this->handleImageUpload($newImage, $banner);
            }

            /** @var Lang $language */
            foreach ($languages as $language) {
                $id_lang    = $language['id_lang'];
                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                $groupLang = $em->getRepository(NosAtoutLang::class)->findOneBy([
                    'banner' => $banner->getId(),
                    'lang'   => $id_lang,
                ]);

                if (! $groupLang) {
                    $groupLang = new NosAtoutLang();
                    $groupLang->setLang($langEntity);
                    $groupLang->setBanner($banner); // Make sure the relation is set
                }

                $title       = $form->get('title')->getData();
                $description = $form->get('description')->getData();
                $groupLang->setLang($langEntity);

                if (isset($title[$id_lang])) {
                    $groupLang->setTitle($title[$id_lang]);
                } else {
                    $groupLang->setTitle('');
                }
                if (isset($description[$id_lang])) {
                    $groupLang->setDescription($description[$id_lang]);
                } else {
                    $groupLang->setDescription('');
                }
                $banner->addBannerLang($groupLang);
            }
            $em->flush();
            $this->addFlash('success', $this->trans('Banner edited', 'Modules.GroupBanner.Admin'));
            return $this->redirectToRoute('mm_atout_banner_index');
        }

        return $this->render('@Modules/nosatouts/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Group Banner', 'Modules.GroupBanner.Admin'),
            'form'          => $form->createView(),
            'image'         => $banner->getImage(),
            'base_dir'      => $base_dir,
        ]);
    }
    public function deleteAction(NosAtout $banner)
    {
        $context  = \Context::getContext();
        $base_dir = $context->shop->getBaseURL(true);
        $em       = $this->getDoctrine()->getManager();
        // unlink($base_dir . '/modules/groupbanner/img/' . $groupBanner->getImage());
        $em->remove($banner);
        $em->flush();
        $this->addFlash('success', $this->trans("banner deleted", 'Modules.GroupBanner.Admin'));
        return $this->redirectToRoute('mm_atout_banner_index');
    }
    private function handleImageUpload(?UploadedFile $imageFile, NosAtout $banner): void
    {
        if ($imageFile) {
            $uploadDir  = _PS_MODULE_DIR_ . 'nosatouts/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (! $filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name

            $newFilename = time() . "_nosatouts.png";

            if ($imageFile->isValid()) {
                // Proceed with the move
                $imageFile->move($uploadDir, $newFilename);
            } else {
                // Handle the error
                echo "The file is not valid.";
            }

            $banner->setImage($newFilename);
        }
    }
}
