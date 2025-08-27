<?php
declare (strict_types = 1);

namespace Hp\Groupbanner\Form\Provider;

use Hp\Groupbanner\Repository\BannerRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

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
     * @param int $groupId
     * @return array
     */
    public function getData($groupId)
    {
        $groupData   = [];
        $groupBanner = $this->repository->find($groupId);

        if ($groupBanner) {
            $groupData['group']   = $groupBanner->getGroup();
            $groupData['section'] = $groupBanner->getSection();
            $groupData['active']  = $groupBanner->getActive();
            $groupData['image']   = $groupBanner->getImage();
            $groupData['link']    = $groupBanner->getLink();
            $languages            = \Language::getLanguages(true);
            $title                = [];
            $description          = [];

            foreach ($languages as $index => $language) {
                $groupLang = $this->getGroupBannerLang($groupBanner->getId());
                if ($groupLang) {
                    $title[$language['id_lang']]       = $groupLang[$index]['title'];
                    $description[$language['id_lang']] = $groupLang[$index]['description'];
                } else {
                    $title[$language['id_lang']]       = '';
                    $description[$language['id_lang']] = '';
                }
            }

            $groupData['title']       = $title;
            $groupData['description'] = $description;
        }
        // \Tools::dieObject($groupData);
        return $groupData;
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
        $groups     = $this->getGroups($languageId, true);

        return [
            'title'       => [1 => '', 2 => ''],
            'description' => [1 => '', 2 => ''],
            'group'       => key($groups), // Default to the first group
            'section'     => '',
            'link'        => '',
            'image'       => '',
            'active'      => 1,
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
    public function getGroupBannerLang($groupId)
    {
        $results = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT f.title,f.description
            FROM `' . _DB_PREFIX_ . 'group_banner_lang` f
            Where f.id_banner = ' . $groupId . '
            Order By id_lang
            '

        );

        return $results;
    }
}
