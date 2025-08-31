<?php



declare (strict_types = 1);

namespace Hp\Productselect\Entity;



use Doctrine\ORM\Mapping as ORM;



/**

 * @ORM\Table()

 * @ORM\Entity()

 * @ORM\HasLifecycleCallbacks

 */



class SelectProduct

{

    /**

     * @ORM\Id

     * @ORM\GeneratedValue

     * @ORM\Column(type="integer")

     */

    private int $id;



    /**

     *  @ORM\Column(type="string" ,name="products") 

     */

    private $products;



    /**

     * @ORM\Column(type="string", name="title", nullable=true)

     */

    private ?string $title = '';



    /**

     * @ORM\Column(type="string", name="link" ,nullable=true)

     */

    private ?string $link = '';



    /**

     * @ORM\Column(type="integer",name="category_id" ,nullable=true)

     */

    private ?int $categoryId = null;

    /**
     * @ORM\Column(type="integer",name="position" ,nullable=true)
     */

    private ?int $position = null;



    /**

     * @ORM\Column(type="datetime")

     */

    private \DateTimeInterface $createdAt;



    public function __construct()

    {

        $this->createdAt = new \DateTimeImmutable();

    }



    // Getters and setters



    public function getId(): int

    {

        return $this->id;

    }



    public function getProducts()

    {

        return $this->products;

    }



    public function setProducts($products): self

    {

        $this->products = $products;

        return $this;

    }



    public function getCategoryId()

    {

        return $this->categoryId;

    }

    public function getTitle()

    {

        return $this->title;

    }

    public function setTitle(string $title): self

    {

        $this->title = $title;

        return $this;

    }



    public function getLink()

    {

        return $this->link;

    }

    public function setLink(string $link): self

    {

        $this->link = $link;

        return $this;

    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
    public function setPosition(?int $position): self
    {
        $this->position = $position;
        return $this;
    }



    public function setCategoryId(?int $categoryId): self

    {

        $this->categoryId = $categoryId;

        return $this;

    }



    public function getCreatedAt(): \DateTimeInterface

    {

        return $this->createdAt;

    }



    public function setCreatedAt(\DateTimeInterface $createdAt): self

    {

        $this->createdAt = $createdAt;

        return $this;

    }

}

