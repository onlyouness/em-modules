<?php
declare (strict_types = 1);

namespace Hp\Groupbanner\Form\Provider;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Hp\Groupbanner\Repository\BannerRepository;

class BannerDataProvider implements FormDataProviderInterface
{
    /**
     * @var BannerRepository
     */
    private $repository;

    /**
     * @param BannerRepository $repository
     */
    public function __construct(BannerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get data for a specific Banner.
     * This is used for editing an existing Banner.
     *
     * @param int $faqId
     * @return array
     */
    public function getData($faqId)
    {
        // $faqData = [];
        // $faq = $this->repository->find($faqId);
        
        // if ($faq) {
        //     $faqData['group'] = $faq->getGroup();
        //     $faqData['section'] = $faq->getSection();
        //     $faqData['active'] = $faq->getActive();
        //     $languages = \Language::getLanguages(true);
        //     $question = [];
        //     $response = [];

        //     foreach ($languages as $index=>$language) {
        //         $faqLang = $this->getFaqLang($faq->getId());
        //         if ($faqLang) {
        //             $question[$language['id_lang']] = $faqLang[$index]['question'];
        //             $response[$language['id_lang']] = $faqLang[$index]['response'];
        //         } else {
        //             // Provide fallback if no translation exists
        //             $question[$language['id_lang']] = '';
        //             $response[$language['id_lang']] = '';
        //         }
        //     }

        //     $faqData['question'] = $question;
        //     $faqData['response'] = $response;
        // }
        // // Tools::dieObject($faqData);
        // return $faqData;
    }

    /**
     * Get default form data for creating a new FAQ.
     * This is used when the form is displayed for creating a new FAQ.
     *
     * @return array
     */
    public function getDefaultData()
    {
        $languageId = \Context::getContext()->language->id;
        $groups = $this->getGroups($languageId, true);

        return [
            'title' => [1 => '', 2 => ''],
            'description' => [1 => '', 2 => ''],
            'group' => key($groups), // Default to the first group
            'section' => '',
            'image'=>'',
            'active' => 1,
        ];
    }

    /**
     * Get the list of available groups.
     *
     * @param int $id_lang
     * @param bool $creation
     * @return array
     */
    public function getGroups($id_lang, $creation = false)
    {
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT DISTINCT g.`id_group` AS id, gl.`name`
            FROM `' . _DB_PREFIX_ . 'group` g
            LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = ' . (int) $id_lang . ')
            ORDER BY g.`id_group` ASC
        ');
        $groups = [];

        foreach ($results as $res) {
            $groups[$res['name']] = $res['id'];
        }

        return $groups;
    }
    public function getFaqLang($faqId)
    {
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT f.question,f.response
            FROM `' . _DB_PREFIX_ . 'faq_lang` f
            Where f.id_faq = '.$faqId.'
            Order By id_lang
            '
            
            );

        return $results;
    }
}
