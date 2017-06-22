<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Author;

class LoadAuthorData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $author = new Author();
        $author->setName('Jon First');
        $manager->persist($author);

        $this->addReference('author-jon', $author);

        $author = new Author();
        $author->setName('Jim Second');
        $manager->persist($author);

        $this->addReference('author-jim', $author);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}