<?php



namespace Hp\Blogs\Entity;



use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;



/**

 * @ORM\Entity

 * @ORM\Table(name="mm_blogs")

 */

class Blog

{

    /**

     * @ORM\Id

     * @ORM\GeneratedValue

     * @ORM\Column(type="integer")

     */

    private int $id;



    /**

     * @ORM\Column(type="string", length=255)

     */

    private string $title;



    /**

     * @ORM\Column(type="text")

     */

    private string $description;

    /**

     * @ORM\Column(type="text",name="short_description")

     */

    private string $shortDescription;


    /**

     * @ORM\Column(type="string", length=255)

     */

    private string $image;



    /**

     * @ORM\Column(type="datetime")

     */

    private \DateTime $createdAt;



    /**
     * @ORM\OneToMany(targetEntity="Hp\Blogs\Entity\Section", mappedBy="blog", cascade={"persist", "remove"})
     */
    private $sections;





    public function __construct()

    {

        $this->createdAt = new \DateTime();

        $this->sections = new ArrayCollection();
    }

    public function getId(): int

    {

        return $this->id;
    }



    public function setTitle(string $title): self

    {

        $this->title = $title;

        return $this;
    }



    public function getTitle(): string

    {

        return $this->title;
    }



    public function setDescription(string $description): self

    {

        $this->description = $description;

        return $this;
    }



    public function getDescription(): string

    {

        return $this->description;
    }
    public function setShortDescription(string $shortDescription): self

    {

        $this->shortDescription = $shortDescription;

        return $this;
    }



    public function getShortDescription(): string

    {

        return $this->shortDescription;
    }



    public function setImage(string $image): self

    {

        $this->image = $image;

        return $this;
    }



    public function getImage(): string

    {

        return $this->image;
    }



    public function setCreatedAt(\DateTime $createdAt): self

    {

        $this->createdAt = $createdAt;

        return $this;
    }



    public function getCreatedAt(): \DateTime

    {

        return $this->createdAt;
    }

    public function getSections()
    {

        return $this->sections;
    }

    public function addSections(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setBlog($this);
        }
        return $this;
    }

    public function removeSections(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
            if ($section->getBlog() === $this) {
                $section->setBlog(null);
            }
        }
        return $this;
    }


    public function __toString()

    {

        return $this->getTitle();
    }
}
