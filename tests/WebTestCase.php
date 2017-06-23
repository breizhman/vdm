<?php

/*
 * This file is part of the API REST VDM
 *
 * (c) Sylvain Lacot <sylvain.lacot@gmail.com>
 */
namespace Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Customisation des cas de tests
 *
 * @author Sylvain Lacot <sylvain.lacot@gmail.com>
 */
class WebTestCase extends BaseWebTestCase
{
    /**
     * Vérifie que la reponse HTTP est OK et que le contenu est bien en JSON
     *
     * @param Response $response Response object
     * @param bool     $success  to define whether the response is expected to be successful
     * @param string   $type
     */
    public function isSuccessfulJson(Response $response, $success = true, $type = 'text/html')
    {
        $this->isSuccessful($response, $success, $type);

        static::assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    /**
     * Compare le nombre d'articles d'une liste de données JSON à autre nombre
     *
     * @param string  $jsonData The json data
     * @param integer $count    The count
     */
    public function assertJsonPostsCount($jsonData, $count)
    {
        $data = json_decode($jsonData, true);
        $this->assertEquals($data['count'], $count);
    }
}
