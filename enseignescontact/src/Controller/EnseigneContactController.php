<?php

namespace Developpement\Checkoutinformation\Controller;

use Language;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class EnseigneContactController extends FrameworkBundleAdminController
{
    public function indexAction(Request $request)
    {
        $languages = \Language::getLanguages(true);

        $formBuilder = $this->get('check.form.identifiable_object.builder');
        $form = $formBuilder->getForm();

        $bg_image = \Configuration::get('ENSEIGNE_CONTACT_BG_IMAGE') ?: '';
        $map_image = \Configuration::get('ENSEIGNE_CONTACT_MAP_IMAGE') ?: '';

        
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve form data
            $subtitle = $form->get('subtitle')->getData();
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData();
            $link = $form->get('link')->getData();
            $bg_image = $form->get('bgimage')->getData();
            $map_image = $form->get('mapimage')->getData();

            // Handle image uploads
            $bgImageFile = $this->handleImageUpload($bg_image);
            $mapImageFile = $this->handleImageUpload($map_image);

            // Save the images if they were uploaded
            if ($bgImageFile) {
                \Configuration::updateValue('ENSEIGNE_CONTACT_BG_IMAGE', $bgImageFile);
            }
            if ($mapImageFile) {
                \Configuration::updateValue('ENSEIGNE_CONTACT_MAP_IMAGE', $mapImageFile);
            }
            \Configuration::updateValue('ENSEIGNE_CONTACT_LINK', $link);

            // save the multilang information
            foreach ($languages as $lang) {
                $langId = $lang['id_lang'];
                $langIsoCode = \Tools::strtoupper($lang['iso_code']);
                
                \Configuration::updateValue('ENSEIGNE_CONTACT_SUBTITLE'.$langIsoCode, $subtitle[$langId] ?? '');
                \Configuration::updateValue('ENSEIGNE_CONTACT_TITLE'.$langIsoCode, $title[$langId] ?? '');
                \Configuration::updateValue('ENSEIGNE_CONTACT_DESCRIPTION'.$langIsoCode, $description[$langId] ?? '');
            }
            $this->addFlash('success', $this->trans('Information created', 'Modules.CheckInfo.Admin'));
            return $this->redirectToRoute('mm_ensigne_contact_index');
        }

        return $this->render('@Modules/checkoutinformation/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Information', 'Modules.CheckInfo.Admin'),
            'bg_image' => $bg_image,
            'map_image' => $map_image,
            'form' => $form->createView(),
        ]);
    }
    private function handleImageUpload(?UploadedFile $imageFile)
    {
        if ($imageFile) {
            $uploadDir  = _PS_MODULE_DIR_ . 'nosatouts/img/';
            $filesystem = new Filesystem();
            // Create the directory if it doesn't exist
            if (! $filesystem->exists($uploadDir)) {
                $filesystem->mkdir($uploadDir);
            }
            //creating the img name
            $newFilename = time() . "_checkinfo.png";
            if ($imageFile->isValid()) {
                // Proceed with the move
                $imageFile->move($uploadDir, $newFilename);
            } else {
                return null;
            }
            return $newFilename;
        }
        return null;
    }
}