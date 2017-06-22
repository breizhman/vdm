<?php
namespace Tests\AppBundle\Controller;

use Tests\WebTestCase;

class PostControllerTest extends WebTestCase
{
    private $fixtures;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadPostData',
            'AppBundle\DataFixtures\ORM\LoadAuthorData'
        ))->getReferenceRepository();
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
        $client->request('GET',  $this->getUrl('get_posts', ['author' => 'none']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());

        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request('GET',  $this->getUrl('get_posts', ['author' => $this->fixtures->getReference('author-jon')->getName()]));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 2);
    }

    public function testGetPostsByDate()
    {
        $client = $this->makeClient();
        $client->request('GET',  $this->getUrl('get_posts', ['from' => '2017-06-01', 'to' => '2017-06-30']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 2);

        $client->request('GET',  $this->getUrl('get_posts', ['from' => '2018-06-01', 'to' => '2018-06-30']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);
    }

    public function testGetPostsByAuthorAndDate()
    {
        $client = $this->makeClient();

        $client->request('GET',  $this->getUrl('get_posts', ['author' => 'none', 'from' => '2017-05-01', 'to' => '2017-06-30']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request('GET',  $this->getUrl('get_posts', ['author' => $this->fixtures->getReference('author-jim')->getName(), 'from' => '2017-06-01', 'to' => '2017-06-30']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 0);

        $client->request('GET',  $this->getUrl('get_posts', ['author' => $this->fixtures->getReference('author-jim')->getName(), 'from' => '2017-05-01', 'to' => '2017-06-30']));

        $this->isSuccessfulJson($client->getResponse());
        $this->assertJson($client->getResponse()->getContent());
        $this->assertJsonPostsCount($client->getResponse()->getContent(), 1);
    }

    public function testGetPost()
    {
        $client = $this->makeClient();

        $client->request('GET',  $this->getUrl('get_post', ['id' => 0]));

        $this->isSuccessfulJson($client->getResponse(), false);

        $client->request('GET',  $this->getUrl('get_post', ['id' => $this->fixtures->getReference('post-1')->getPublicId()]));
        $this->isSuccessfulJson($client->getResponse());
    }
}
