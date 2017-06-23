<?php
/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace Tests\AppBundle\Controller;

use Tests\WebTestCase;

/**
 * Test unitaire de l'API REST
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class PostControllerTest extends WebTestCase
{
    /**
     * liste des fixtures lancé pour ce test
     */
    private $fixtures;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\LoadPostData',
            'AppBundle\DataFixtures\ORM\LoadAuthorData',
        ])->getReferenceRepository();
    }

    public function testGetPosts()
    {
        $client = $this->makeClient();

        $client->request('GET', $this->getUrl('get_posts'));

        $this->isSuccessfulJson($client->getResponse());
    }

    public function testGetPostsByAuthor()
    {
        $client = $this->makeClient();

        $client->request(
            'GET',
            $this->getUrl('get_posts', ['author' => 'none'])
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request(
            'GET',
            $this->getUrl('get_posts', ['author' => $this->fixtures->getReference('author-jon')->getName()])
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 2);
    }

    public function testGetPostsByDate()
    {
        $client = $this->makeClient();

        $client->request(
            'GET',
            $this->getUrl('get_posts', ['from' => '2017-06-01', 'to' => '2017-06-30'])
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 2);

        $client->request(
            'GET',
            $this->getUrl('get_posts', ['from' => '2018-06-01', 'to' => '2018-06-30'])
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);
    }

    public function testGetPostsByAuthorAndDate()
    {
        $client = $this->makeClient();

        $client->request(
            'GET',
            $this->getUrl('get_posts', ['author' => 'none', 'from' => '2017-05-01', 'to' => '2017-06-30'])
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request(
            'GET',
            $this->getUrl(
                'get_posts',
                [
                    'author' => $this->fixtures->getReference('author-jim')->getName(),
                    'from' => '2017-06-01',
                    'to' => '2017-06-30',
                ]
            )
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request(
            'GET',
            $this->getUrl(
                'get_posts',
                [
                    'author' => $this->fixtures->getReference('author-jim')->getName(),
                    'from' => '2017-05-01',
                    'to' => '2017-06-30',
                ]
            )
        );
        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 1);
    }

    public function testGetPost()
    {
        $client = $this->makeClient();

        $client->request(
            'GET',
            $this->getUrl('get_post', ['id' => 0])
        );
        $this->isSuccessfulJson($client->getResponse(), false);

        $client->request(
            'GET',
            $this->getUrl(
                'get_post',
                ['id' => $this->fixtures->getReference('post-1')->getPublicId()]
            )
        );
        $this->isSuccessfulJson($client->getResponse());
    }
}
