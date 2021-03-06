<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

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

    /**
     * List customers by a given customer ID
     */
    public function list_by_id($id)
    {
        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            throw new \Exception(
                'No customer found for id ' . $id
            );
        }

        return $customer;
    }

    /**
     * List all customers. Default items per page is 10
     */
    public function list($request)
    {

        $sortBy = $request->sort_by ?? 'id'; // id, company, firstname, lastname, city, country
        $sortOrder = $request->order ?? 'ASC'; // ASC, DESC
        $page = $request->page ?? '1';

        $customers = $this->customerRepository->findBy([], [$sortBy => $sortOrder]);

        if (!$customers) {
            throw new \Exception(
                'No customers found'
            );
        }

        // Pagination
        $adapter = new ArrayAdapter($customers);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($page);

        return $pagerfanta->getCurrentPageResults();
    }

    /**
     * Update customer by ID
     */
    public function update($id, $updatedCustomer)
    {

        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            throw new \Exception(
                'No customer found for id ' . $id
            );
        }

        $customer->setCompany($updatedCustomer->company);
        $customer->setFirstname($updatedCustomer->firstname);
        $customer->setLastname($updatedCustomer->lastname);
        $customer->setStreet($updatedCustomer->street);
        $customer->setZip($updatedCustomer->zip);
        $customer->setCity($updatedCustomer->city);
        $customer->setCountry($updatedCustomer->country);
        $customer->setPhone($updatedCustomer->phone);
        $customer->setEmail($updatedCustomer->email);

        // Validation
        $errors = $this->validator->validate($customer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
        }

        $this->entityManager->flush();

        return $customer;
    }

    /**
     * Create a new customer
     */
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

    /**
     * Delete a customer by ID or email address
     */
    public function delete($customer)
    {
        if ($id = $customer->id ?? false) {
            $customer = $this->customerRepository->find($id);
        } else if ($email = $customer->email ?? false) {
            $customer = $this->customerRepository->findOneByEmail($email);
        } else {
            throw new \Exception('Please provide user identification. Either a customer ID or email address.');
        }

        if (!$customer) throw new \Exception('Could not find user.');

        $id = $customer->getId();

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return $id;
    }
}
