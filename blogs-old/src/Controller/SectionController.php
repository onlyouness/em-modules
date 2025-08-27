<?php



namespace Hp\Blogs\Controller;

use Context;
use Hp\Blogs\Entity\Blog;
use Hp\Blogs\Form\BlogType;
use Hp\Blogs\Form\SectionType;
use Hp\Services\Entity\Section as EntitySection;
use Hp\Blogs\Entity\Section;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use PrestaShop\PrestaShop\Adapter\Entity\Category;

use PrestaShop\PrestaShop\Adapter\Entity\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tools;

class SectionController extends FrameworkBundleAdminController

{

    public function indexAction($id = null)
    {
        if ($id === null) {
            // Handle missing ID, e.g., show a default page or redirect.
            throw $this->createNotFoundException('Blog ID is required.');
        }
        // get the sections by the blog id that i pass as a param
        $sections = $this->getDoctrine()->getRepository(Section::class)->findBy(array('blog' => $id));

        // get the blog from that id in params to get the blog title
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $blogName = $blog ? $blog->getTitle() : 'Blog';


        return $this->render('@Modules/blogs/views/templates/admin/sections.html.twig', [
            'id' => $id,
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Blogs', 'Modules.Blogs.Admin'),
            'sections' => $sections,
            'title' => $blogName,
        ]);
    }


    public function createAction(Request $request, Blog $blog)
    {
        $section = new Section();
        $em = $this->getDoctrine()->getManager();

        $position = count($em->getRepository(Section::class)->findAll())+ 1;

        
        $products = $this->getProducts();

        // Create the form
        $form = $this->createForm(SectionType::class, $section, [
            'products' => $products,
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            
            $products = serialize($form->get('products')->getData());
            // Tools::dieObject($form->getData());
            
            $section->setProducts($products);
            $section->setBlog($blog);
            $section->setPosition($position);

            $em->persist($section);
            $em->flush();

            $this->addFlash('success', 'Section created successfully!');

            return $this->redirectToRoute('mm_sections_blogs_index', ['id' => $blog->getId()]);
        }

        return $this->render('@Modules/blogs/views/templates/admin/create_section.html.twig', [
            'form' => $form->createView(),
        ]);
    }





    public function editAction(Request $request, Section $section,$blog_id)
    {

        $em = $this->getDoctrine()->getManager();

        // Fetch categories from PrestaShop
        $categories = $this->getCategories();

        // Create the form
        $form = $this->createForm(SectionType::class, $section, [
            'categories' => $categories,
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $section->setCategoryId($form->get('categoryId')->getData()); 
            $section->setProducts($form->get('products')->getData());
            $em->persist($section);
            $em->flush();

            $this->addFlash('success', 'Section created successfully!');

            return $this->redirectToRoute('mm_sections_blogs_index', ['id' => $blog_id]);
        }

        return $this->render('@Modules/blogs/views/templates/admin/create_section.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Section $section, $blog_id)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($section);
        $em->flush();

        $this->addFlash('success', 'Service has been deleted.');
        return $this->redirectToRoute('mm_sections_blogs_index', ['id' => (int)$blog_id]);
    }

    public function getCategories(){
        // Fetch categories from PrestaShop
        $categoriesArray = Category::getCategories(
            Context::getContext()->language->id,
            true,
            true
        );

        $categories = [];
        foreach ($categoriesArray as $categoryGroup) {
            foreach ($categoryGroup as $categoryData) {
                $infos = $categoryData['infos'];
                $categories[$infos['id_category']] = $infos['name'];
            }
        }
        return $categories;
    }



    public function getProducts()
    {

        $products = \Product::getProducts(Context::getContext()->language->id, 0, 100, 'name', 'ASC');

        $productChoices = [];
        foreach ($products as $product) {
            $productChoices[$product['name']] = $product['id_product'];
        }

        return $productChoices;
    }

    public function positions(Request $request): JsonResponse
    {
        // Decode the JSON body
        $order = json_decode($request->getContent(), true)['order'];
    
        if (empty($order) || !is_array($order)) {
            return new JsonResponse(['error' => 'Invalid data received'], 400);
        }
    
        $entityManager = $this->getDoctrine()->getManager();
    
        try {
            foreach ($order as $position => $id) {
                // Retrieve the Section by its ID
                $section = $entityManager->getRepository(Section::class)->find($id);
    
                if ($section) {
                    $section->setPosition($position + 1); // Add 1 if positions are 1-based
                    $entityManager->persist($section);
                }
            }
            $entityManager->flush();
    
            return new JsonResponse(['success' => 'Positions updated successfully']);
        } catch (\Exception $e) {
            // Log the error for debugging
            $logFilePath = _PS_MODULE_DIR_ . 'your_module_name/logs/logs.txt';
            file_put_contents($logFilePath, $e->getMessage(), FILE_APPEND);
    
            return new JsonResponse(['error' => 'An error occurred while updating positions'], 500);
        }
    }
    
}
