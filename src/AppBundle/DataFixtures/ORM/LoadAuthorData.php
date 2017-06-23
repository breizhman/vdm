<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Author;

/**
 * ALimente la base de données avec des auteurs
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class LoadAuthorData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * chargement des données en base
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager The manager
     */
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

    /**
     * ordre d'execution des fixtures
     *
     * @return     integer  The order.
     */
    public function getOrder()
    {
        return 2;
    }
}
