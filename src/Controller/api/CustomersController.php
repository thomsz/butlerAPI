<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
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

            return new Response($errorsString);
        }

        // Stage query
        $entityManager->persist($customer);

        // Execute query 
        $entityManager->flush();

        return new Response("New customer ID {$customer->getId()} has been created.", Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/customers/update/{id}", name="update_customer")
     */
    public function update($id, CustomerRepository $customerRepository, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $customer = $customerRepository->find($id);

        if (!$customer) {
            throw $this->createNotFoundException(
                'No customer found for id ' . $id
            );
        }

        $company = $request->request->get('company');
        $firstname = $request->request->get('firstname');
        $lastname = $request->request->get('lastname');
        $street = $request->request->get('street');
        $zip = $request->request->get('zip');
        $city = $request->request->get('city');
        $country = $request->request->get('country');
        $phone = $request->request->get('phone');
        $email = $request->request->get('email');

        if (!is_null($company))
            $customer->setCompany($company);
        if (!is_null($firstname))
            $customer->setFirstname($firstname);
        if (!is_null($lastname))
            $customer->setLastname($lastname);
        if (!is_null($street))
            $customer->setStreet($street);
        if (!is_null($zip))
            $customer->setZip($zip);
        if (!is_null($city))
            $customer->setCity($city);
        if (!is_null($country))
            $customer->setCountry($country);
        if (!is_null($phone))
            $customer->setPhone($phone);
        if (!is_null($email))
            $customer->setEmail($email);

        $entityManager->flush();

        return new Response($this->json($customer));;
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

        return new Response("Customer {$id} has been deleted.");
    }
}
