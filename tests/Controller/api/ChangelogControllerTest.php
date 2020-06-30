<?php

namespace App\Tests\Controller\api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\Generator;

class ChangelogControllerTest extends WebTestCase
{
    private $token;

    protected function setUp()
    {
        $generator = new Generator();
        $this->token = $generator->accessToken($_ENV['TEST_USER']);
    }

    public function testListChangelog()
    {
        $client = static::createClient([]);

        $client->request('GET', '/api/changelog', [], [], [], json_encode(['access_token' => $this->token]));

        $this->assertStringContainsString('userID', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAccessTokenIsRequired()
    {
        $client = static::createClient([]);

        $client->request('GET', '/api/changelog');

        $this->assertStringContainsString('required', $client->getResponse()->getContent());
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
    }
}
