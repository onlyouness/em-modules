<?php
declare(strict_types = 1);
namespace Hp\Services\Controller;

use Hp\Services\Entity\Section;
use Hp\Services\Entity\Service;
use Hp\Services\Form\SectionType;
use Hp\Services\Form\ServiceFormType;
use Hp\Services\Form\ServiceType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SectionController extends FrameworkBundleAdminController
{

    public function createAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $section = new Section();;
        $form = $this->createForm(SectionType::class,$section);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($section);
            $em->flush();
            $this->addFlash('success', 'Service has been created.');
            return $this->redirectToRoute('oil_service_index');

        }
        return $this->render('@Modules/services/views/templates/admin/create_config.html.twig', [
            'form' => $form->createView(),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Section service', 'Modules.Tasks.Admin'),
        ]);
    }
    public function editAction(Request $request,Section $section){
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SectionType::class,$section);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
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