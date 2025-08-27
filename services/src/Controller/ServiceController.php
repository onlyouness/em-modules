<?php

declare(strict_types=1);

namespace Hp\Services\Controller;

use Hp\Services\Entity\Section;
use Hp\Services\Entity\Service;
use Hp\Services\Entity\ServiceLang;
use Hp\Services\Form\SectionType;
use Hp\Services\Form\ServiceType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;

class ServiceController extends FrameworkBundleAdminController
{

    public function indexAction(): Response
    {
        $services = $this->getDoctrine()->getRepository(Service::class)->findAll();
        $section = $this->getDoctrine()->getRepository(Section::class)->find(1);
        if (empty($section)) {
            $em = $this->getDoctrine()->getManager();
            $section = new Section();
            $section->setTitle('NOS SERVICES');
            $section->setDescription('consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ut aliquip ex ea commodo consequat. ');
            $section->setShortDescription('Exclusive Offer -20% Off This Week');
            $em->persist($section);
            $em->flush();
        }

        return $this->render('@Modules/services/views/templates/admin/index.html.twig', [
            'services' => $services,
            'section' => $section,
            'dir_module' => _PS_MODULE_DIR_,
        ]);
    }

    public function createAction(Request $request): Response
    {
        $service = new Service();
        return $this->handleForm($request, $service, 'created');
    }

    public function editAction(Request $request, Service $service): Response
    {
        
        return $this->handleForm($request, $service, 'updated');
        
    }

    private function handleForm(Request $request, Service $service, string $action): Response
    {

        $form = $this->createForm(ServiceType::class, $service, [
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id'   => 'service',
        ]);

        //getting the image and its base url
        $old_image = $service->getImage();
        $link = \Context::getContext()->link;
        $baseUrl = $link->getBaseLink();

        //get the active banners count 
        $em = $this->getDoctrine()->getManager();
        $activeServices = count($em->getRepository(Service::class)->findBy(['active' => 1]));
        // Tools::dieObject($activeServices);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if (!$image && $action == "updated") {
                $service->setImage($old_image);
            } elseif (!$image && $action == 'created') {
                $this->addFlash('error', 'The image is required.');
                return $this->redirectToRoute('oil_service_create');
            } else {
                $this->handleImageUpload($image, $service);
            }

            //active handling:
            if ($activeServices >= 3) {
                $service->setActive(0);
            } else {
                $service->setActive(1);
            }

            $em->persist($service);
            $em->flush();
            $this->addFlash('success', sprintf('Service has been %s.', $action));
            return $this->redirectToRoute('oil_service_index');
        }

        return $this->render('@Modules/services/views/templates/admin/create.html.twig', [
            'form' => $form->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Services', 'Modules.Tasks.Admin'),
            'image'=>$old_image,
            'baseUrl'=>$baseUrl,
        ]);
    }

    private function handleImageUpload(?UploadedFile $imageFile, Service $service): void
    {
        if ($imageFile) {
            $uploadDir = _PS_MODULE_DIR_ . 'services/img/';
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
            $service->setImage($newFilename);
        }
    }

    public function deleteAction(Service $service): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $image = $service->getImage();
        if ($image) {
            $imagePath = _PS_MODULE_DIR_ . 'services/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->flush();

        $this->addFlash('success', 'Service has been deleted.');
        return $this->redirectToRoute('oil_service_index');
    }
    public function activeAction(Service $service)
    {
        $em = $this->getDoctrine()->getManager();
        $actives = $this->getDoctrine()->getRepository(Service::class)->findBy(['active' => 1]);
        $isActive = $service->getActive();

        if ($isActive == 0) {
            if (count($actives) < 3) {
                $service->setActive(1);
            } else {
                $this->addFlash('error', 'You have reached the limit of active services');
                return $this->redirectToRoute('oil_service_index');
            }
        } else {
            $service->setActive(0);
            // dump($isActive);
        }
        // Tools::dieObject(count($actives));
        $em->flush();
        $this->addFlash('success', 'Service has been deleted.');
        return $this->redirectToRoute('oil_service_index');
    }
    public function editSectionAction(Request $request, Section $section)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Service has been updated.');
            return $this->redirectToRoute('oil_service_index');
        }
        return $this->render('@Modules/services/views/templates/admin/create_config.html.twig', [
            'form' => $form->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Tasks', 'Modules.Tasks.Admin'),
        ]);
    }
}
