<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class CustomersController extends AbstractController
{
    /**
     * @Route("/customers", name="customers")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CustomersController.php',
        ]);
    }

    /**
     * @Route("/customers/create", name="create_customer")
     */
    public function create(Request $request, ValidatorInterface $validator): Response
    {
        $newCustomer = json_decode($request->getContent());
        $entityManager = $this->getDoctrine()->getManager();

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
        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
        }

        // Stage query
        $entityManager->persist($customer);

        // Execute query 
        $entityManager->flush();

        return new Response("New customer ID {$customer->getId()} has been created.", Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/update/{id}", name="update_customer")
     */
    public function update($id, CustomerRepository $customerRepository, Request $request, ValidatorInterface $validator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customer = $customerRepository->find($id);

        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        $updatedCustomer = json_decode($request->getContent());

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
        $errors = $validator->validate($customer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
        }

        $entityManager->flush();

        return new Response($this->json($customer), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/list", name="list_customers")
     */
    public function list(CustomerRepository $customerRepository, Request $request): Response
    {
        $request = json_decode($request->getContent());

        $sortBy = $request->sort_by ?? 'id'; // id, company, firstname, lastname, city, country
        $sortOrder = $request->order ?? 'ASC'; // ASC, DESC
        $page = $request->page ?? '1';

        $customers = $customerRepository->findBy([], [$sortBy => $sortOrder]);

        if (!$customers) {
            throw $this->createNotFoundException(
                'No customers found'
            );
        }

        // Pagination
        $adapter = new ArrayAdapter($customers);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setCurrentPage($page);

        return new Response($this->json($pagerfanta->getCurrentPageResults()), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/list/{id}", name="show_customer")
     */
    public function list_by_id($id, CustomerRepository $customerRepository): Response
    {
        $customer = $customerRepository->find($id);

        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        return new Response($this->json($customer), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/delete", name="delete_customer")
     */
    public function delete(CustomerRepository $customerRepository, Request $request): Response
    {
        $customer = json_decode($request->getContent());

        if ($id = $customer->id ?? false) {
            $customer = $customerRepository->find($id);
        } else if ($email = $customer->email ?? false) {
            $customer = $customerRepository->findOneByEmail($email);
        }

        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found.'
            );
        }

        $id = $customer->getId();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($customer);
        $entityManager->flush();

        return new Response("Customer {$id} has been deleted.", Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
