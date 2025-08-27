<?php
namespace Hp\Mmreassurances\Controller;

use Hp\Mmreassurances\Entity\Reassurance;
use Hp\Mmreassurances\Entity\ReassuranceLang;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PrestaShopBundle\Entity\Lang;

class AdminReassurances extends FrameworkBundleAdminController
{

    public function indexAction(): Response
    {
        $langId = (int) \Context::getContext()->language->id;
        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository(Reassurance::class)->findReassuranceByLang($langId);
        return $this->render('@Modules/mmreassurances/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Reassurances', 'Modules.MmReassurance.Admin'),
            'reassurances'  => $res,
        ]);
    }
    public function createAction(Request $request)
    {
        $em          = $this->getDoctrine()->getManager();
        $reassurance = new Reassurance();
        $languages   = \Language::getLanguages(true);
        $formBuilder = $this->get('reassurance.form.identifiable_object.builder');

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $reassurance->setActive(1);
            $this->handleImageUpload($image, $reassurance);

            /** @var Lang $language */
            foreach ($languages as $language) {
                $reassuranceLang = new ReassuranceLang();
                $id_lang         = $language['id_lang'];
                $title           = $form->get('title')->getData();
                $description     = $form->get('description')->getData();

                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                $reassuranceLang->setLang($langEntity);

                if (isset($title[$id_lang])) {
                    $reassuranceLang->setTitle($title[$id_lang]);
                } else {
                    $reassuranceLang->setTitle('');
                }
                if (isset($description[$id_lang])) {
                    $reassuranceLang->setDescription($description[$id_lang]);
                } else {
                    $reassuranceLang->setDescription('');
                }
                $reassurance->addBannerLang($reassuranceLang);
            }

            $em->persist($reassurance);
            $em->flush();
            $this->addFlash('success', $this->trans('Reassurance created', 'Modules.Reassurance.Admin'));
            return $this->redirectToRoute('mm_reassurances_index');
        }

        return $this->render('@Modules/mmreassurances/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Reassurances', 'Modules.MmReassurance.Admin'),
            'form'          => $form->createView(),
        ]);
    }
    private function handleImageUpload(?UploadedFile $imageFile, Reassurance $reassurance): void
    {
        if ($imageFile) {
            $uploadDir  = _PS_MODULE_DIR_ . 'mmreassurances/img/';
            $filesystem = new Filesystem();

            if (! $filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }

            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move($uploadDir, $newFilename);

            // Store the path in the database (relative path)
            $reassurance->setImage($newFilename);
        }
    }
    public function activeAction(Reassurance $mmreassurance)
    {
        $em       = $this->getDoctrine()->getManager();
        $actives  = $this->getDoctrine()->getRepository(Reassurance::class)->findBy(['active' => 1]);
        $isActive = $mmreassurance->getActive();

        if ($isActive == 0) {
            if (count($actives) < 5) {
                $mmreassurance->setActive(1);
            } else {
                $this->addFlash('error', 'You have reached the limit of active reassurances');
                return $this->redirectToRoute('mm_reassurances_index');
            }
        } else {
            $mmreassurance->setActive(0);
        }

        $em->flush();
        $this->addFlash('success', 'Reassurances has been deleted.');
        return $this->redirectToRoute('mm_reassurances_index');

    }

    public function deleteAction(Reassurance $mmreassurance)
    {
        $em    = $this->getDoctrine()->getManager();
        $image = $mmreassurance->getImage();
        if ($image) {
            $imagePath = _PS_MODULE_DIR_ . 'mmreassurances/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->remove($mmreassurance);
        $em->flush();
        $this->addFlash('success', 'Reassurances has been deleted.');
        return $this->redirectToRoute('mm_reassurances_index');

    }

    public function editAction(Request $request, Reassurance $mmreassurance)
    {
        $em          = $this->getDoctrine()->getManager();
        $reassurance = new Reassurance();
        $languages   = \Language::getLanguages(true);
        $formBuilder = $this->get('reassurance.form.identifiable_object.builder');
        $form        = $formBuilder->getFormFor((int) $reassurance->getId());

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $reassurance->setActive(1);
            $this->handleImageUpload($image, $reassurance);

            /** @var Lang $language */
            foreach ($languages as $language) {
                $reassuranceLang = new ReassuranceLang();
                $id_lang         = $language['id_lang'];
                $title           = $form->get('title')->getData();
                $description     = $form->get('description')->getData();

                $langEntity = $em->getRepository(Lang::class)->find($id_lang);
                if (! $langEntity) {
                    throw new \Exception('Language not found for ID ' . $id_lang);
                }

                $reassuranceLang->setLang($langEntity);

                if (isset($title[$id_lang])) {
                    $reassuranceLang->setTitle($title[$id_lang]);
                } else {
                    $reassuranceLang->setTitle('');
                }
                if (isset($description[$id_lang])) {
                    $reassuranceLang->setDescription($description[$id_lang]);
                } else {
                    $reassuranceLang->setDescription('');
                }
                $reassurance->addBannerLang($reassuranceLang);
            }
            $em->flush();
            $this->addFlash('success', $this->trans('Reassurance created', 'Modules.Reassurance.Admin'));
            return $this->redirectToRoute('mm_reassurances_index');
        }

        return $this->render('@Modules/mmreassurances/views/templates/admin/create.html.twig', [
            'enableSidebar' => true,
            'layoutTitle'   => $this->trans('Reassurances', 'Modules.MmReassurance.Admin'),
            'form'          => $form->createView(),
        ]);
    }

}
