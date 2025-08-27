<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
declare (strict_types = 1);
namespace Hp\Groupbanner\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Hp\Groupbanner\Repository\BannerRepository")
 * @ORM\HasLifecycleCallbacks
 */

class GroupBanner
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="group_id", type="string")
     */
    private $group;

    /**
     * @var int
     * @ORM\Column(name="section_id", type="string")
     */
    private $section;

    /**
     * @var int
     * @ORM\Column(name="active", type="integer")
     */
    private int $active = 1;

    /**
     * @var string
     * @ORM\Column(name="image", type="string")
     */
    private $image;

    /**
     * @var string
     * @ORM\Column(name="link", type="string")
     */
    private $link;

    /**
     * @ORM\OneToMany(targetEntity="Hp\Groupbanner\Entity\GroupBannerLang", cascade={"persist", "remove"}, mappedBy="banner")
     */
    private $bannerLangs;

    public function __construct()
    {
        $this->bannerLangs = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroup()
    {
        return $this->group;
    }
    public function setGroup($group): self
    {
        $this->group = $group;
        return $this;
    }

    public function setActive($active): self
    {
        $this->active = $active;
        return $this;
    }
    public function getActive()
    {
        return $this->active;
    }
    public function setSection($section): self
    {
        $this->section = $section;
        return $this;
    }
    public function getSection()
    {
        return $this->section;
    }
    public function setLink($link): self
    {
        $this->link = $link;
        return $this;
    }
    public function getLink()
    {
        return $this->link;
    }
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;
        return $this;
    }

    public function addBannerLang(GroupBannerLang $bannerLang): self
    {
        $bannerLang->setBanner($this);
        $this->bannerLangs->add($bannerLang);
        return $this;
    }
    public function getBannerContent(): string
    {
        if ($this->bannerLangs->count() <= 0) {
            return '';
        }

        $bannerLang = $this->bannerLangs->first();

        return $bannerLang->getFaq();
    }
}
