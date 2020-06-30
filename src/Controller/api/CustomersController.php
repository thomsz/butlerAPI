<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\CustomerManager;
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
    public function create(Request $request, CustomerManager $customerManager): Response
    {
        $newCustomer = json_decode($request->getContent());

        $customer = $customerManager->create($newCustomer);

        if (is_string($customer)) {
            return new Response($customer, Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
        }

        return new Response("New customer ID {$customer->getId()} has been created.", Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/update/{id}", name="update_customer")
     */
    public function update($id, CustomerManager $customerManager, CustomerRepository $customerRepository, Request $request, ValidatorInterface $validator): Response
    {
        $updatedCustomer = json_decode($request->getContent());
        $customer = $customerManager->update($id, $updatedCustomer);

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
    public function delete(CustomerManager $customerManager, Request $request): Response
    {
        $customer = json_decode($request->getContent());

        if (is_integer($customerManager->delete($customer)))
            return new Response("Customer has been deleted.", Response::HTTP_OK, ['Content-Type' => 'application/json']);
        else
            return new Response('Customer was not found.', Response::HTTP_NOT_FOUND);
    }
}
