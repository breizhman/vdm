<?php
namespace Tests\AppBundle\Command;

use Tests\WebTestCase;

class VdmRssCommandTest extends WebTestCase
{
    public function setUp()
    {
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
