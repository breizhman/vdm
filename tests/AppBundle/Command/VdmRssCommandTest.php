<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace Tests\AppBundle\Command;

use Tests\WebTestCase;

/**
 * Test unitaire de la commande de chargement des articles depuis un flux RSS
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class VdmRssCommandTest extends WebTestCase
{
    public function setUp()
    {
        // on vide la BDD de test
        $this->loadFixtures([]);
    }

    public function testLoad()
    {
        $this->runCommand('vdm:rss:load');

        $posts = $this->getContainer()
                      ->get('doctrine.orm.entity_manager')
                      ->getRepository('AppBundle:Post')
                      ->findAll();

        $this->assertLessThanOrEqual($this->getContainer()->getParameter('vdm.rss.limit_posts'), count($posts));
    }
}
