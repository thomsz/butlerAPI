<?php

namespace App\Tests\Service;

use App\Service\CustomerManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerManagerTest extends WebTestCase
{
    public function testUserCreation()
    {
        self::bootKernel();

        $customerManager = static::$container->get(CustomerManager::class);

        $newCustomer = (object) [
            'company' => 'Test',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => 'test',
            'zip' => '00000',
            'city' => 'City Test',
            'country' => '',
            'phone' => '+42075463623',
            'email' => 'random@email.com'
        ];

        $customer = $customerManager->create($newCustomer);

        $this->assertEquals($newCustomer->email, $customer->getEmail());

        $customer = (object) ['email' => $customer->getEmail()];

        if (!is_integer($customerManager->delete($customer))) throw new \Exception('Could not delete dummy user.');
    }
}
