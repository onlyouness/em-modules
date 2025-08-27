<?php

namespace Hp\Collectionimages\Controller;

use Hp\Collectionimages\Entity\QbCollection;
use Hp\Collectionimages\Entity\QbCollectionImage;
use Hp\Collectionimages\Form\CollectionImageType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCollectionImage extends FrameworkBundleAdminController
{

    public function indexAction($id)
    {
        $images = $this->getDoctrine()->getRepository(QbCollectionImage::class)->findBy(['collection' => $id]);

        if (!$id) {
            throw $this->createNotFoundException('Collection ID not provided or invalid');
        }

        $link = \Context::getContext()->link;
        $baseUrl = $link->getBaseLink();

        // \Tools::dieObject([$images,$id,$baseUrl]);
        return $this->render(
            '@Modules/collectionimages/views/templates/admin/images.html.twig',
            [
                'enableSidebar' => true,
                'layoutTitle' => $this->trans('Images Management', 'Modules.CollectionImages.Admin'),
                'images' => $images,
                'id' => $id,
                'baseUrl' => $baseUrl,
            ]
        );
    }



    public function createAction(Request $request, QbCollection $collection)
    {
        $em = $this->getDoctrine()->getManager();
        $collectionImage = new QbCollectionImage();

        $form = $this->createForm(CollectionImageType::class, $collectionImage);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $this->handleImageUpload($image, $collectionImage);
            } else {
                $this->addFlash('error', $this->trans('The image is required please provide one', 'Modules.CollectionImages.Admin'));
                return $this->redirectToRoute('qb_collectionimage_index', ['id' => $collection->getId()]);
            }
            $collectionImage->setCollection($collection);
            $em->persist($collectionImage);
            $em->flush();
            $this->addFlash('success', $this->trans('Image created', 'Modules.CollectionImages.Admin'));
            return $this->redirectToRoute('qb_collectionimage_index', ['id' => $collection->getId()]);
        }
        return $this->render('@Modules/collectionimages/views/templates/admin/create_collection.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Collection', 'Modules.CollectionImages.Admin'),
            'form' => $form->createView(),

        ]);
    }
    public function editAction(QbCollectionImage $collectionImage, Request $request, QbCollection $collection)
    {
        $em = $this->getDoctrine()->getManager();
        $old_image = $collectionImage->getImage();
        $link = \Context::getContext()->link;
        $baseUrl = $link->getBaseLink();
        $form = $this->createForm(CollectionImageType::class, $collectionImage);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            // dump("collection",$collectionImage);
            if ($image) {
                $this->handleImageUpload($image, $collectionImage);
            } else {
                $old_image = $collectionImage->getImage();
                $collectionImage->setImage($old_image);
                // dump('old image',$old_image);
            }
            $collectionImage->setCollection($collection);
            // \Tools::dieObject($form->getData());
            $em->persist($collectionImage);
            $em->flush();
            $this->addFlash('success', $this->trans('Image updated', 'Modules.CollectionImages.Admin'));
            return $this->redirectToRoute('qb_collectionimage_index', ['id' => $collection->getId()]);
        }
        return $this->render('@Modules/collectionimages/views/templates/admin/create_collection.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Collection', 'Modules.CollectionImages.Admin'),
            'form' => $form->createView(),
            'image' => $old_image,
            'base_dir' => $baseUrl,
        ]);
    }
    public function deleteAction(QbCollectionImage $collectionImage, QbCollection $collection)
    {
        $em = $this->getDoctrine()->getManager();
        // \Tools::dieObject($collectionImage);
        $em->remove($collectionImage);
        $image = $collectionImage->getImage();
        if ($image) {
            $imagePath = _PS_MODULE_DIR_ . 'collectionimages/img/' . $image;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $em->flush();
        $this->addFlash('success', $this->trans('Collection deleted', 'Modules.CollectionImages.Admin'));
        return $this->redirectToRoute('qb_collectionimage_index', ['id' => $collection->getId()]);
    }

    private function handleImageUpload(?UploadedFile $imageFile, QbCollectionImage $image): void
    {
        if ($imageFile) {

            $uploadDir = _PS_MODULE_DIR_ . 'collectionimages/img/';
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
            $image->setImage($newFilename);
        }
    }
}
