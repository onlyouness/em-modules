<?php

namespace Hp\Testimonial\Controller;

use Hp\Testimonial\Filters\TestimonialFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminTestomonial extends FrameworkBundleAdminController
{
    public function indexAction(TestimonialFilters $testimonialFilter) {
        $gridFactory = $this->get('testimonial.grid.grid_factory');
        
        // Get the grid using the filters
        $grid = $gridFactory->getGrid($testimonialFilter);

        // Render the template with the grid data
        return $this->render('@Modules/testimonial/views/templates/admin/testimonials.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Liste Testimonial', 'Modules.OpenArticle.Admin'),
            'testimonialGrid' => $this->presentGrid($grid),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }
    private function getToolbarButtons()
    {
        return [
            'add' => [
                'desc' => $this->trans('New Testimonial', 'Modules.Testimonial.Admin'),
                'icon' => 'add_circle_outline',
                'href' => $this->generateUrl('mm_testimonial_create'),
            ],
            
        ];
    }
    public function createAction (Request $request){
        $formBuilder = $this->get('testimonial.form.identifiable_object.builder');
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        // \Tools::dieObject($form);

        return $this->render('@Modules/testimonial/views/templates/admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
