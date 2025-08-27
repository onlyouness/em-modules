<?php
declare (strict_types = 1);
namespace Hp\Groupbanner\Controller;

use Hp\Groupbanner\Entity\GroupBanner;
use Hp\Groupbanner\Entity\GroupBannerLang;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Entity\Lang;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminBannerGroupController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        return new Response('hello world');
    }
    public function createAction(Request $request)
    {
        $em          = $this->getDoctrine()->getManager();
        $groupBanner = new GroupBanner();
        $languageId  = \Context::getContext()->language->id;
        $groups      = $this->getGroups($languageId, true);
        $languages   = \Language::getLanguages(true);

        $formBuilder = $this->get('groupbanner.form.identifiable_object.builder');

        $form = $formBuilder->getForm(['groups' => $groups]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $section     = $form->get('section')->getData();
            $group       = $form->get('group')->getData();
            $image       = $form->get('image')->getData();
            $link        = $form->get('link')->getData();
            $activeCheck = $em->getRepository(GroupBanner::class)->findActive((int) $section, (int) $group);
            $groupBanner->setGroup($group);
            $groupBanner->setSection($section);
            $groupBanner->setLink($link);
            if (empty($activeCheck)) {
                $groupBanner->setActive(1);
            } else {
                $groupBanner->setActive(0);
            }
            $this->handleImageUpload($image, $groupBanner);

            /** @var Lang $language */
            foreach ($languages as $language) {
                $groupLang   = new GroupBannerLang();
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
                $groupBanner->addBannerLang($groupLang);
            }

            $em->persist($groupBanner);
            $em->flush();
            $this->addFlash('success', $this->trans('Group Banner created', 'Modules.GroupBanner.Admin'));
            return $this->redirectToRoute('mm_group_banner_index');
        }
        return $this->render('@Modules/groupbanner/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Group Banner', 'Modules.GroupBanner.Admin'),
            'form'          => $form->createView(),

        ]);
    }
    public function editAction(Request $request, GroupBanner $groupBanner)
    {
        $em         = $this->getDoctrine()->getManager();
        $languageId = \Context::getContext()->language->id;
        $groups     = $this->getGroups($languageId, true);
        $languages  = \Language::getLanguages(true);

        $formBuilder = $this->get('groupbanner.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $groupBanner->getId(), ["groups" => $groups]);
        $form->handleRequest($request);

        $context  = \Context::getContext();
        $base_dir = $context->shop->getBaseURL(true);

        if ($form->isSubmitted() && $form->isValid()) {
            $section  = $form->get('section')->getData();
            $group    = $form->get('group')->getData();
            $newImage = $form->get('image')->getData();
            $link     = $form->get('link')->getData();
            // dump($newImage);
            // Preserve the active status if it was active before editing
            $wasActive = $groupBanner->getActive();

            $groupBanner->setGroup($group);
            $groupBanner->setSection($section);
            $groupBanner->setLink($link);
            $groupBanner->setActive($wasActive); // Preserve the active status

            // Handle image upload only if a new image is submitted
            if ($newImage) {
                $this->handleImageUpload($newImage, $groupBanner);
            }

            /** @var Lang $language */
            foreach ($languages as $language) {
                $id_lang    = $language['id_lang'];
                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                $groupLang = $em->getRepository(GroupBannerLang::class)->findOneBy([
                    'banner' => $groupBanner->getId(),
                    'lang'   => $id_lang,
                ]);

                if (! $groupLang) {
                    $groupLang = new GroupBannerLang();
                    $groupLang->setLang($langEntity);
                    $groupLang->setBanner($groupBanner); // Make sure the relation is set
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
                $groupBanner->addBannerLang($groupLang);
            }
            $em->flush();
            $this->addFlash('success', $this->trans('Group Banner edited', 'Modules.GroupBanner.Admin'));
            return $this->redirectToRoute('mm_group_banner_index');
        }

        return $this->render('@Modules/groupbanner/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Group Banner', 'Modules.GroupBanner.Admin'),
            'form'          => $form->createView(),
            'image'         => $groupBanner->getImage(),
            'base_dir'      => $base_dir,
        ]);
    }
    public function deleteAction(GroupBanner $groupBanner)
    {
        $context  = \Context::getContext();
        $base_dir = $context->shop->getBaseURL(true);
        $em       = $this->getDoctrine()->getManager();
        // unlink($base_dir . '/modules/groupbanner/img/' . $groupBanner->getImage());
        $em->remove($groupBanner);
        $em->flush();
        $this->addFlash('success', $this->trans("group banner deleted", 'Modules.GroupBanner.Admin'));
        return $this->redirectToRoute('mm_group_banner_index');
    }
    public function getGroups($id_lang, $creation = false)
    {
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT g.`id_group` AS id, gl.`name`
		FROM `' . _DB_PREFIX_ . 'group` g
		LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = ' . (int) $id_lang . ')
		ORDER BY g.`id_group` ASC');
        $groups = [];
        if (! $creation) {
            $groups = $results;
        } else {
            foreach ($results as $res) {
                $groups[$res['name']] = $res['id'];
            }
        }
        return $groups;
    }
    private function handleImageUpload(?UploadedFile $imageFile, GroupBanner $banner): void
    {
        if ($imageFile) {
            $uploadDir  = _PS_MODULE_DIR_ . 'groupbanner/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (! $filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name

            $newFilename = time() . "_groupbanner.png";

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
