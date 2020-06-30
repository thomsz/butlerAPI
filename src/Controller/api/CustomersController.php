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

        return new Response($this->json(['message' => "New customer ID {$customer->getId()} has been created."]), Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/update/{id}", name="update_customer")
     */
    public function update($id, CustomerManager $customerManager, Request $request): Response
    {
        $updatedCustomer = json_decode($request->getContent());
        $customer = $customerManager->update($id, $updatedCustomer);

        return new Response($this->json($customer), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/list", name="list_customers")
     */
    public function list(CustomerManager $customerManager, Request $request): Response
    {
        $request = json_decode($request->getContent());

        return new Response($this->json($customerManager->list($request)), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/list/{id}", name="show_customer")
     */
    public function list_by_id($id, CustomerManager $customerManager): Response
    {
        return new Response($this->json($customerManager->list_by_id($id)), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/delete", name="delete_customer")
     */
    public function delete(CustomerManager $customerManager, Request $request): Response
    {
        $customer = json_decode($request->getContent());

        if (is_integer($customerManager->delete($customer)))
            return new Response($this->json(['message' => 'Customer has been deleted.']), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        else
            return new Response($this->json(['message' => 'Customer was not found.']), Response::HTTP_NOT_FOUND);
    }
}
