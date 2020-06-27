<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function create(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $company = $request->request->get('company');
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $street = $request->request->get('street');
        $zip = $request->request->get('zip');
        $city = $request->request->get('city');
        $country = $request->request->get('country');
        $phone = $request->request->get('phone');
        $email = $request->request->get('email');

        $customer = new Customer();
        $customer->setCompany($company);
        $customer->setFirstname($firstname);
        $customer->setLastname($lastname);
        $customer->setStreet($street);
        $customer->setZip($zip);
        $customer->setCity($city);
        $customer->setCountry($country);
        $customer->setPhone($phone);
        $customer->setEmail($email);

        // Stage query
        $entityManager->persist($customer);

        // Execute query 
        $entityManager->flush();

        return new Response("New customer ID {$customer->getId()} has been created.", Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/update/{id}", name="update_customer")
     */
    public function update($id, CustomerRepository $customerRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customer = $customerRepository->find($id);

        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        $customer->setFirstname('Mark');

        $entityManager->flush();

        return new Response("Customer ID {$customer->getId()} has been updated.", Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/list", name="list_customers")
     */
    public function list(CustomerRepository $customerRepository)
    {
        $customers = $customerRepository->findAll();

        if (!$customers) {
            throw $this->createNotFoundException(
                'No customers found'
            );
        }

        return new Response($this->json($customers));
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

        return new Response($this->json($customer));
    }
}
