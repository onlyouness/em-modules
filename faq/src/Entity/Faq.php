<?php

declare (strict_types = 1);
namespace Hp\Faq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Hp\Faq\Repository\FaqRepository")
 * @ORM\HasLifecycleCallbacks
 */

class Faq
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
     * @ORM\OneToMany(targetEntity="Hp\Faq\Entity\FaqLang", cascade={"persist", "remove"}, mappedBy="Faq")
     */
    private $faqLangs;

    public function __construct()
    {
        $this->faqLangs = new ArrayCollection();
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
    public function addFaqLang(FaqLang $faqLang): self
    {
        $faqLang->setFaq($this);
        $this->faqLangs->add($faqLang);
        return $this;
    }
    public function getFaqContent(): string
    {
        if ($this->faqLangs->count() <= 0) {
            return '';
        }

        $faqLang = $this->faqLangs->first();

        return $faqLang->getFaq();
    }
}
