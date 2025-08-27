<?php
namespace Hp\Mmreassurances\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class Reassurance
{

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="image", type="string")
     */
    private $image;

    /**
     * @var string
     * @ORM\Column(name="active", type="string")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="Hp\Mmreassurances\Entity\ReassuranceLang", cascade={"persist", "remove"}, mappedBy="banner")
     */
    private $bannerLangs;

    public function __construct()
    {
        $this->bannerLangs = new ArrayCollection();
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }
    public function addBannerLang(ReassuranceLang $bannerLang): self
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
