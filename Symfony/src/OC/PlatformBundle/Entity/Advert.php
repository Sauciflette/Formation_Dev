<?php

namespace OC\PlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Advert
 *
 * @ORM\Table(name="oc_advert")
 * @ORM\Entity(repositoryClass="OC\PlatformBundle\Repository\AdvertRepository")
 */

class Advert
{
    /**
    * @ORM\OneToOne(targetEntity="OC\PlatformBundle\Entity\Image", cascade={"persist"})
    */
    private $image;

    /**
    *@ORM\ManyToMany(targetEntity="OC\PlatformBundle\Entity\Category", cascade={"persist"})
    *@ORM\JoinTable(name="oc_advert_category")
    */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="OC\PlatformBundle\Entity\Application", mappedBy="advert")
     */
    private $applications; // Notez le « s », une annonce est liée à plusieurs candidatures

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    public function __construct()
     {
       // Par défaut, la date de l'annonce est la date d'aujourd'hui
       $this->date = new \Datetime();
       $this->categories = new ArrayCollection();
     }

     // Notez le singulier, on ajoute une seule catégorie à la fois
     public function addCategory(Category $category)
     {
         // Ici, on utilise l'ArrayCollection vraiment comme un tableau
         $this->categories[] = $category;
     }

     public function removeCategory(Category $category)
     {
         // Ici on utilise une méthode de l'ArrayCollection, pour supprimer la catégorie en argument
         $this->categories->removeElement($category);
     }

     // Notez le pluriel, on récupère une liste de catégories ici !
     public function getCategories()
     {
     return $this->categories;
     }

    public function setImage(Image $image = null)
    {
    $this->image = $image;
    }

    public function getImage()
    {
    return $this->image;
    }

    public function getId()
    {
        return $this->id;
    }


    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Advert
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Advert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author.
     *
     * @param string $author
     *
     * @return Advert
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Advert
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set published.
     *
     * @param bool $published
     *
     * @return Advert
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published.
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    public function addApplication(Application $application)
    {
      $this->applications[] = $application;

      $application->setAdvert($this);

      return $this
      // Et si notre relation était facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
      // $application->setAdvert(null);
    }

    public function removeApplication(Application $application)
    {
      $this->applications->removeElement($application);
    }

    public function getApplications()
    {
      return $this->applications;
    }
}
