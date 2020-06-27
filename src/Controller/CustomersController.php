<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customer;
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
}
