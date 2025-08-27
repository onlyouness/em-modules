<?php

namespace Hp\Blogs\Controller;

use Hp\Blogs\Entity\Blog;
use Hp\Blogs\Entity\BlogParagraph;
use Hp\Blogs\Form\BlogParagraphType;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ParagraphController extends FrameworkBundleAdminController{
    public function indexAction($id = null){

        
        if ($id === null) {
            // Handle missing ID, e.g., show a default page or redirect.
            throw $this->createNotFoundException('Blog ID is required.');
        }

        $paragraphs = $this->getDoctrine()->getRepository(BlogParagraph::class)->findBy(array('blog' => $id));

        // get the blog from that id in params to get the blog title
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $blogName = $blog ? $blog->getTitle() : 'Blog';

        // \Tools::dieObject($paragraphs);


        return $this->render('@Modules/blogs/views/templates/admin/paragraph.html.twig', [
            'id' => $id,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Blogs', 'Modules.Blogs.Admin'),
            'paragraphs' => $paragraphs,
            'title' => $blogName,
        ]); 

        
    }

    public function createAction(Request $request, Blog $blog)
    {
        $paragraph = new BlogParagraph();
        $em = $this->getDoctrine()->getManager();

        // Create the form
        $form = $this->createForm(BlogParagraphType::class, $paragraph);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();
            if ($image) {
                $this->handleImageUpload($image, $paragraph);
            }
            
            $paragraph->setBlog($blog);

            $em->persist($paragraph);
            $em->flush();

            $this->addFlash('success', 'Paragraph created successfully!');

            return $this->redirectToRoute('mm_paragraphs_blogs_index', ['id' => $blog->getId()]);
        }

        return $this->render('@Modules/blogs/views/templates/admin/create_paragraph.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, Blog $blog,BlogParagraph $paragraph)
    {
        $em = $this->getDoctrine()->getManager();
        $image = $paragraph->getImage();
        // Create the form
        $form = $this->createForm(BlogParagraphType::class, $paragraph);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $imagenew = $form->get('image')->getData();
            if (!$imagenew && $image ) {
                $oldImage = $paragraph->getImage();
                $paragraph->setImage($oldImage);
            } else {
                $this->handleImageUpload($imagenew, $paragraph);
            }
            
            $paragraph->setBlog($blog);

            $em->persist($paragraph);
            $em->flush();

            $this->addFlash('success', 'Paragraph edited successfully!');

            return $this->redirectToRoute('mm_paragraphs_blogs_index', ['id' => $blog->getId()]);
        }

        return $this->render('@Modules/blogs/views/templates/admin/create_paragraph.html.twig', [
            'form' => $form->createView(),
            'image'=>$image,
        ]);
    }


    public function deleteAction(BlogParagraph $paragraph, $blog_id)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($paragraph);
        $em->flush();

        $this->addFlash('success', 'Paragraph deleted successfully!');

        return $this->redirectToRoute('mm_paragraphs_blogs_index', ['id' => $blog_id]);
    }

    private function handleImageUpload(?UploadedFile $imageFile, BlogParagraph $paragraph)
    {
        if ($imageFile) {
            $uploadDir = _PS_MODULE_DIR_ . 'blogs/img/';
            $filesystem = new Filesystem();

            // Create the directory if it doesn't exist
            if (!$filesystem->exists($uploadDir)) {
                try {
                    $filesystem->mkdir($uploadDir, 0755);
                } catch (\Exception $e) {
                    throw new \RuntimeException('Failed to create directory: ' . $uploadDir, 0, $e);
                }
            }

            // Generate a unique filename
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the file
            try {
                $imageFile->move($uploadDir, $newFilename);
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to upload image: ' . $e->getMessage());
            }

            // Update the Blog entity
            $paragraph->setImage($newFilename);
        }
    }
}