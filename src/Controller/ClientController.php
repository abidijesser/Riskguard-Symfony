<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\Client1Type;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;




#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_new')]
    public function new(ManagerRegistry $doctrine, Request $req): Response
    {
        $em= $doctrine->getManager();
        $client = new Client();
        $form = $this->createForm(Client1Type::class, $client);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('app_signin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/new.html.twig', [

            'form' => $form,
        ]);
    }


    #[Route('/update/{id}', name: 'client_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $client= $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException(
                'No client found for id '
            );
        }

        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $cin = $request->request->get('cin');
        $email =$request->request->get('email');
        $telephone = $request->request->get('telephone');
        $adresse_domicile = $request->request->get('adresse_domicile');

        $client->setNom($nom);
        $client->setPrenom($prenom);
        $client->setCin($cin);
        $client->setEmail($email);
        $client->setTelephone($telephone);
        $client->setAdresseDomicile($adresse_domicile);
        $entityManager->flush();

        return $this->redirectToRoute('show_client', ['id'=>$id]);
    }

    #[Route('/delete/{id}', name: 'client_delete')]
    public function delete(ClientRepository $repository, ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $client= $entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            throw $this->createNotFoundException(
                'No client found for id '.$id
            );
        }
        $entityManager->remove($client);
        $entityManager->flush();

        return $this->redirectToRoute('client_allshow');
    }


     #[Route('/{id<\d+>}', name: 'show_client')]
     public function showClient(EntityManagerInterface $entityManager, $id): Response
     {
         $client = $entityManager->getRepository(Client::class)->find($id);
         if (!$client) {
             throw $this->createNotFoundException(
                 'No product found for id '.$id
             );
         }
         return $this->render('client/show.html.twig', ['client' => $client]);
     }
    

    #[Route('/allclients', name: 'client_allshow')]
    public function showAllClients(ManagerRegistry $doctrine): Response
    {
        $clients = $doctrine->getRepository(Client::class)->findAll();

        return $this->render('admindashboard/dashboardAdmin.html.twig', ['clients' => $clients]);
    }

    #[Route('/allclients/{bynom}', name: 'client_allshow_bynom')]
    public function showAllClientByNom(ManagerRegistry $doctrine, $bynom): Response
    {
        $clients = $doctrine->getRepository(Client::class)->findBy([], [$bynom => 'ASC']);

        return $this->render('admindashboard/dashboardAdmin.html.twig', ['clients' => $clients]);
    }
    
    #[Route('/ajouterclient', name: 'client_add')]
    public function addClient(ManagerRegistry $doctrine): Response
    {
        return $this->render('admindashboard/ajoutClient.html.twig');
    }

    

    // #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Client $client, EntityManagerInterface $entityManager, $id): Response
    // {
    //     $form = $this->createForm(Client1Type::class, $client);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('client/edit.html.twig', [
    //         'client' => $client,
    //         'form' => $form,
    //         'id' => $id,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    // public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
    //         $entityManager->remove($client);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    // }

    // official one
    // #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    // public function show(Client $client, $id): Response
    // {
    //     return $this->render('client/show.html.twig', [
    //         'client' => $client,
    //         'id' => $id,

    //     ]);
    // }


    // public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(Client1Type::class, $client);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_client_index', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('client/edit.html.twig', [
    //         'client' => $client,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $client = new Client();
    //     $form = $this->createForm(Client1Type::class, $client);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($client);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('client/new.html.twig', [
    //         'client' => $client,
    //         'form' => $form,
    //     ]);
    // }

    //    #[Route('/allclients', name: 'client_allshow')]
//    public function creationform(Request $request): Response
//    {
//        $form = $this->createForm(Client1Type::class);
//
//        return $this->render('admindashboard/dashboardAdmin.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }

}