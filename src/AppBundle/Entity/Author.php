<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité de la table des auteurs
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="author", cascade={"persist"})
     */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * récupère le nom de l'auteur
     *
     * @return     string  The name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Modifie le nom de l'auteur
     *
     * @param      string  $name   The name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Récupère la liste des articles de l'auteur courant
     *
     * @return     array The posts.
     */
    public function getPosts()
    {
        return $this->posts;
    }
}