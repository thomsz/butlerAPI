<?php

namespace App\Tests\Controller\api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\Generator;

class ChangelogControllerWebTest extends WebTestCase
{
    private $token;
    private $client;

    protected function setUp()
    {
        $generator = new Generator();
        $this->token = $generator->accessToken($_ENV['TEST_USER']);

        $this->client = static::createClient([]);
    }

    public function testListChangelog()
    {
        $this->client->request('GET', '/api/changelog', [], [], [], json_encode(['access_token' => $this->token]));

        $this->assertStringContainsString('userID', $this->client->getResponse()->getContent());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAccessTokenIsRequired()
    {
        $this->client->request('GET', '/api/changelog');

        $this->assertStringContainsString('required', $this->client->getResponse()->getContent());
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }
}
