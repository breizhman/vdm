<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase as BaseWebTestCase;

use Symfony\Component\HttpFoundation\Response;

class WebTestCase extends BaseWebTestCase
{
    /**
     * Checks the success state of a response.
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

    public function assertJsonPostsCount($jsonData, $count)
    {
        $data = json_decode($jsonData, true);
        $this->assertEquals($data['count'], $count);
    }
}
