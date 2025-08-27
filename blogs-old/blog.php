<?php

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

class BlogsBlogModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // Register the stylesheet for the blogs module
        $this->context->controller->registerStylesheet(
            'module-blogs-css',
            'modules/' . $this->module->name . '/views/css/blogs.css',
            ['media' => 'all', 'priority' => 200]
        );

        // Get the blog ID from the URL
        $id_blog = (int) Tools::getValue('id_blog');

        // Validate the blog ID
        if (! $id_blog || ! Validate::isUnsignedId($id_blog)) {
            Tools::redirect('index.php'); // Redirect if invalid
        }

        // Build and execute the SQL query to get the blog and associated sections
        $sql = '
        SELECT
            b.id AS blog_id,
            b.title AS blog_title,
            b.description AS blog_description,
            b.image AS blog_image,
            b.created_at AS blog_created_at,
            bs.id AS section_id,
            bs.products AS products,
            bs.title AS title_section,
            bs.created_at AS section_created_at
        FROM
            `' . _DB_PREFIX_ . 'blogs` b
        LEFT JOIN
            `' . _DB_PREFIX_ . 'blog_sections` bs
        ON
            b.id = bs.blog_id
        WHERE
            b.id = ' . (int) $id_blog . '
        ORDER BY
          bs.position Asc
    ';

        $sections = Db::getInstance()->executeS($sql);

        // Check if the blog data exists
        if (! $sections) {
            Tools::redirect('index.php'); // Redirect if no blog data
        }

                              // Initialize blog details
        $blog = $sections[0]; // Use the first section's blog data

        // Initialize an array for sections
        $blogSections = [];
        foreach ($sections as $section) {
            $productUnserialized = unserialize($section['products']);
            if (! empty($productUnserialized)) {
                $products = [];
                foreach ($productUnserialized as $pr) {
                    $products[] = new Product($pr, false, $this->context->language->id);
                }
                $assembler        = new ProductAssembler($this->context);
                $presenterFactory = new ProductPresenterFactory($this->context);
                $presentationSettings = $presenterFactory->getPresentationSettings();
                $presenter = new ProductListingPresenter(

                    new ImageRetriever(

                        $this->context->link

                    ),

                    $this->context->link,

                    new PriceFormatter(),

                    new ProductColorsRetriever(),

                    $this->context->getTranslator()

                );

                $products_f = [];

                foreach ($products as $rawProduct) {
                                                             // Get product data as an array
                    $productData = $rawProduct->getFields(); // You can adjust this depending on what fields you need

                    // Ensure the data is in the right format for assembleProduct
                    $products_f[] = $presenter->present(
                        $presentationSettings,
                        $assembler->assembleProduct($productData), // Pass the array instead of the object
                        $this->context->language
                    );
                }

                // dump($products_f);

                $blogSections[] = [
                    'section_id'    => $section['section_id'],
                    'products'      => $products_f,
                    'title_product' => $section['title_section'],

                ];
            }
        }

        // Assign blog and sections data to Smarty
        // Tools::dieObject($products_f);
        $this->context->smarty->assign([
            'link'     => $this->context->link,
            'blog'     => $blog, // Blog details (will be the same for all sections)
            'sections' => $blogSections,
        ]);

        // Load the template
        $this->setTemplate('module:blogs/views/templates/front/blogs.tpl');
    }
}
