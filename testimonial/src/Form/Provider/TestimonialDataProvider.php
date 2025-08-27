<?php
declare(strict_types =1);

namespace Hp\Testimonial\Form\Provider;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShopObjectNotFoundException;

class TestimonialDataProvider implements FormDataProviderInterface
{

    public function getData($contactId)
    {
        // $contactObjectModel = new Contact($contactId);
        
        // // check that the element exists in db
        // if (empty($contactObjectModel->id)) {
        //     throw new PrestaShopObjectNotFoundException('Object not found');
        // }

        // return [
        //     'title' => $contactObjectModel->name,
        // ];
        return [
            'title' => 'Testimonial',
        ];
    }

    /**
     * Get default form data.
     *
     * @return mixed
     */
    public function getDefaultData()
    {
        return [
            'name' => '',
            'message' => '',
        ];
    }

}