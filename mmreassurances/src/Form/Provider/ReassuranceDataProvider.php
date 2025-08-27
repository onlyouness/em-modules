<?php
declare (strict_types = 1);

namespace Hp\Mmreassurances\Form\Provider;

use DbQuery;
use Hp\Mmreassurances\Repository\ReassuranceRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

class ReassuranceDataProvider implements FormDataProviderInterface
{
    /**
     * @var ReassuranceRepository
     */
    private $repository;

    /**
     * @param ReassuranceRepository $repository
     */
    public function __construct(ReassuranceRepository $repository)
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
    public function getData($id)
    {
        $groupData   = [];
        $atout = $this->repository->find($id);

        if ($atout) {
            $groupData['image'] = $atout->getImage();
            $languages          = \Language::getLanguages(true);
            $title              = [];
            $description        = [];

            foreach ($languages as $index => $language) {
                $groupLang = $this->repository->findReassuranceLangById($atout->getId());
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
        return $groupData;
    }

    /**
     * Get default form data for creating a new Banner.
     * This is used when the form is displayed for creating a new Banner.
     *
     * @return array
     */
    public function getDefaultData()
    {
        $languageId = \Context::getContext()->language->id;
        return [
            'title'       => [1 => '', 2 => ''],
            'description' => [1 => '', 2 => ''],
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

    public function getAtoutLangs($id)
    {
        $query = new DbQuery();
        $query->select('l.title,l.description');
        $query->from('nos_atout_lang', 'l');
        $query->where('l.id_atout = ' . (int) $id);
        return \Db::getInstance()->executeS($query);
    }
}
