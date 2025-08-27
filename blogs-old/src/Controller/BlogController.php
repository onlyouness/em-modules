<?php



namespace Hp\Blogs\Controller;

use Category as GlobalCategory;
use Context;
use Hp\Blogs\Entity\Blog;
use Hp\Blogs\Entity\Section;
use Hp\Blogs\Form\BlogType;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use PrestaShop\PrestaShop\Adapter\Entity\Category;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tools;

class BlogController extends FrameworkBundleAdminController

{

    public function indexAction()
    {
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        return $this->render('@Modules/blogs/views/templates/admin/index.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Blogs', 'Modules.Blogs.Admin'),
            'blogs' => $blogs
        ]);
    }



    public function createAction(Request $request)

    {
        $em = $this->getDoctrine()->getManager();
        $blog = new Blog();

        // Fetch PrestaShop categories
        $categoriesArray  = Category::getCategories(
            Context::getContext()->language->id,
            true,
            true,
        );

        $categories = [];
        foreach ($categoriesArray as $categoryGroup) {
            foreach ($categoryGroup as $categoryData) {
                $infos = $categoryData['infos'];
                $categories[$infos['id_category']] = $infos['name'];
            }
        }

        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {

                $this->handleImageUpload($image, $blog);
            }
            // Save blog and sections

            $em->persist($blog);

            $em->flush();

            $this->addFlash('success', 'Blog created successfully!');

            return $this->redirectToRoute('mm_blogs_index');
        }



        return $this->render('@Modules/blogs/views/templates/admin/blog_form.html.twig', [

            'form' => $form->createView(),

        ]);
    }

    public function editAction(Request $request, Blog $blog)
    {

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(BlogType::class, $blog);
        $image = $blog->getImage();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle image upload
            // Tools::dieObject($request);

            $image = $form->get('image')->getData();
            if (!$image) {
                $oldImage = $blog->getImage();
                $blog->setImage($oldImage);
            } else {
                $this->handleImageUpload($image, $blog);
            }

            $em->flush();

            $this->addFlash('success', 'Blog created successfully!');

            return $this->redirectToRoute('mm_blogs_index');
        }


        return $this->render('@Modules/blogs/views/templates/admin/blog_form.html.twig', [

            'form' => $form->createView(),
            'image' => $image,

        ]);
    }
    public function deleteAction(Blog $blog)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();

        $this->addFlash('success', 'Blog has been deleted.');
        return $this->redirectToRoute('mm_blogs_index');
    }


    private function handleImageUpload(?UploadedFile $imageFile, Blog $blog)
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
            $blog->setImage($newFilename);
        }
    }
    public function fetchProducts(Request $request): JsonResponse
    {
        $categoryId = $request->query->get('category_id');
        $products = Product::getProducts(Context::getContext()->language->id, 0, 100, 'name', 'ASC', $categoryId);

        $productChoices = [];
        foreach ($products as $product) {
            $productChoices[$product['name']] = $product['id_product'];
        }

        return new JsonResponse($productChoices);
    }
}
