<?php

namespace App\Tests\Controller\api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\Generator;

class CustomersControllerWebTest extends WebTestCase
{
    private $token;

    protected function setUp()
    {
        $generator = new Generator();
        $this->token = $generator->accessToken($_ENV['TEST_USER']);
    }

    public function testShowListOfAllCustomers()
    {
        $client = static::createClient([]);

        $client->request('GET', '/api/customers/list', [], [], [], json_encode(['access_token' => $this->token]));

        $this->assertStringContainsString('company', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
