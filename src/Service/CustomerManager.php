<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerManager
{
    private $entityManager;
    private $validator;
    private $customerRepository;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator, CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function create($newCustomer)
    {
        $customer = new Customer();
        $customer->setCompany($newCustomer->company);
        $customer->setFirstname($newCustomer->firstname);
        $customer->setLastname($newCustomer->lastname);
        $customer->setStreet($newCustomer->street);
        $customer->setZip($newCustomer->zip);
        $customer->setCity($newCustomer->city);
        $customer->setCountry($newCustomer->country);
        $customer->setPhone($newCustomer->phone);
        $customer->setEmail($newCustomer->email);

        // Validation
        $errors = $this->validator->validate($customer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $errorsString;
        }

        // Stage query
        $this->entityManager->persist($customer);

        // Execute query 
        $this->entityManager->flush();
        return $customer;
    }

    public function delete($customer)
    {
        if ($id = $customer->id ?? false) {
            $customer = $this->customerRepository->find($id);
        } else if ($email = $customer->email ?? false) {
            $customer = $this->customerRepository->findOneByEmail($email);
        }

        if (!$customer) return false;

        $id = $customer->getId();

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return $id;
    }
}
