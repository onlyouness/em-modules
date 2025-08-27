<?php

declare (strict_types = 1);

namespace Developpement\Checkoutinformation\Form\Provider;

use Hp\Faq\Entity\Faq;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class EnseigneContactDataProvider implements FormDataProviderInterface
{

    public function getData($id)
    {
        $data = [
            'subtitle' => '',
            'title' => '',
            'description' => '',
            'link' => '',
            'bgimage' => '',
            'mapimage' => '',
        ];
        return $data;
    }
    /**

     * Get default form data for creating a new FAQ.

     * This is used when the form is displayed for creating a new FAQ.

     *

     * @return array

     */

    public function getDefaultData()
    {

        return [
            'subtitle'    => [1 => '', 2 => ''],
            'title'       => [1 => '', 2 => ''],
            'description' => [1 => '', 2 => ''],
            'link'    => '',
            'bgimage'    => '',
            'mapimage'   =>'',
        ];
    }

}
