<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;

/**
 * ALimente la base de données avec des articles
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class LoadPostData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * chargement des données en base
     *
     * @param      \Doctrine\Common\Persistence\ObjectManager  $manager  The manager
     */
    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setPublicId(1);
        $post->setDate(new \DateTime('2017-06-15'));
        $post->setContent('contenu du premier post');
        $post->setAuthor($this->getReference('author-jon'));
        $manager->persist($post);

        $this->addReference('post-1', $post);

        $post = new Post();
        $post->setPublicId(2);
        $post->setDate(new \DateTime('2017-06-01'));
        $post->setContent('contenu du second post');
        $post->setAuthor($this->getReference('author-jon'));
        $manager->persist($post);

        $this->addReference('post-2', $post);

        $post = new Post();
        $post->setPublicId(3);
        $post->setDate(new \DateTime('2017-05-02 13:51:10'));
        $post->setContent('contenu du troisième post');
        $post->setAuthor($this->getReference('author-jim'));
        $manager->persist($post);

        $this->addReference('post-3', $post);

        $manager->flush();
    }

    /**
     * ordre d'execution des fixtures
     *
     * @return     integer  The order.
     */
    public function getOrder()
    {
        return 3;
    }
}