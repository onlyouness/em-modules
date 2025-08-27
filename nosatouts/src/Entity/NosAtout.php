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
namespace Hp\Nosatouts\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use NosAtouts;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Hp\Nosatouts\Repository\AtoutRepository")
 * @ORM\HasLifecycleCallbacks
 */

class NosAtout
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
     * @ORM\Column(name="active", type="integer")
     */
    private int $active = 1;

    /**
     * @var string
     * @ORM\Column(name="image", type="string")
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity="Hp\Nosatouts\Entity\NosAtoutLang", cascade={"persist", "remove"}, mappedBy="banner")
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


    public function setActive($active): self
    {
        $this->active = $active;
        return $this;
    }
    public function getActive()
    {
        return $this->active;
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

    public function addBannerLang(NosAtoutLang $bannerLang): self
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

        return $bannerLang->getBanner();
    }
}
