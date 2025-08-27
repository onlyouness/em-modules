<?php

declare(strict_types=1);

namespace Hp\Collectionimages\Controller;

use Hp\Collectionimages\Form\CollectionType;
use Hp\Collectionimages\Entity\QbCollection;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;

class AdminCollection extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $collections = $this->getDoctrine()->getRepository(QbCollection::class)->findAll();
        // \Tools::dieObject($collections);
        return $this->render(
            '@Modules/collectionimages/views/templates/admin/index.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Collection Management', 'Modules.CollectionImages.Admin'),
                'collections'=>$collections,
            ]
        );
    }
    public function createCollectionAction(Request $request){
    
            $em = $this->getDoctrine()->getManager();
            $collection = new QbCollection();
            $form = $this->createForm(CollectionType::class,$collection);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($collection);
                $em->flush();
                $this->addFlash('success', $this->trans('Collection created', 'Modules.CollectionImages.Admin'));
                return $this->redirectToRoute('qb_collection_index');
            }
            return $this->render('@Modules/collectionimages/views/templates/admin/create_collection.html.twig', [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Collection', 'Modules.CollectionImages.Admin'),
                'form' => $form->createView(),
            ]);
        
    }
    public function editCollectionAction(Request $request,QbCollection $collection){
    
            $em = $this->getDoctrine()->getManager();
            $form = $this->createForm(CollectionType::class,$collection);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($collection);
                $em->flush();
                $this->addFlash('success', $this->trans('Collection updated Successfully', 'Modules.CollectionImages.Admin'));
                return $this->redirectToRoute('qb_collection_index');
            }
            return $this->render('@Modules/collectionimages/views/templates/admin/create_collection.html.twig', [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Collection', 'Modules.CollectionImages.Admin'),
                'form' => $form->createView(),
            ]);
        
    }
    public function deleteCollectionAction(QbCollection $collection){
        $em = $this->getDoctrine()->getManager();
        $em->remove($collection);
        $em->flush();
        $this->addFlash('success', $this->trans('Collection created', 'Modules.CollectionImages.Admin'));
        return $this->redirectToRoute('qb_collection_index');
    }
}
