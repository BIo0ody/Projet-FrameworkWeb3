<?php

namespace App\Controller;

use App\Entity\CarteBancaire;
use App\Form\CarteBancaireType;
use App\Repository\CarteBancaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carte/bancaire')]
final class CarteBancaireController extends AbstractController
{
    #[Route(name: 'app_carte_bancaire_index', methods: ['GET'])]
    public function index(CarteBancaireRepository $carteBancaireRepository): Response
    {
        return $this->render('carte_bancaire/index.html.twig', [
            'carte_bancaires' => $carteBancaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_carte_bancaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $carteBancaire = new CarteBancaire();
        $carteBancaire->setUser($this->getUser());

        $form = $this->createForm(CarteBancaireType::class, $carteBancaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($carteBancaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carte_bancaire/new.html.twig', [
            'carte_bancaire' => $carteBancaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carte_bancaire_show', methods: ['GET'])]
    public function show(CarteBancaire $carteBancaire): Response
    {
        return $this->render('carte_bancaire/show.html.twig', [
            'carte_bancaire' => $carteBancaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carte_bancaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CarteBancaire $carteBancaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CarteBancaireType::class, $carteBancaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('carte_bancaire/edit.html.twig', [
            'carte_bancaire' => $carteBancaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carte_bancaire_delete', methods: ['POST'])]
    public function delete(Request $request, CarteBancaire $carteBancaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carteBancaire->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($carteBancaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_carte_bancaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
