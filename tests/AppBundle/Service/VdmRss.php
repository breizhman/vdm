<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace Tests\AppBundle\Service;

use Tests\WebTestCase;

/**
 * Test unitaire du service gérant la récupération des articles du flux RSS
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class VdmRssTest extends WebTestCase
{
    public function setUp()
    {
        // on vide la bdd avant les test
        $this->loadFixtures([]);
    }

    public function testRead()
    {
        $service = $this->getContainer()->get('app.vdm.rss');
        $this->assertNotEmpty($service->read());
        $this->assertNotEmpty($service->read(1));
        $this->assertNotEmpty($service->read('test'));
        $this->assertNotEmpty($service->read(null));
    }

    public function testLoad()
    {
        $service = $this->getContainer()->get('app.vdm.rss');

        $posts = $this->getContainer()
                      ->get('doctrine.orm.entity_manager')
                      ->getRepository('AppBundle:Post')
                      ->findAll();

        $this->assertLessThanOrEqual($this->getContainer()->getParameter('vdm.rss.limit_posts'), count($posts));
    }
}
