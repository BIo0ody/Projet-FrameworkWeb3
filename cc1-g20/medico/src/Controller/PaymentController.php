<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Consultation;
use App\Repository\CarteBancaireRepository;
use Doctrine\ORM\EntityManagerInterface; 

final class PaymentController extends AbstractController
{
    #[Route('/payment/{id}', name: 'app_payment')]
    public function index(Consultation $consultation,CarteBancaireRepository $carteRepo,EntityManagerInterface $entityManager, Request $request): Response {

        $patient = $consultation->getPatient();

        $cartes = $carteRepo->findBy([
            'user' => $patient
        ]);

        // Si on clique sur Payer
        $carteId = $request->query->get('carte');

        if ($carteId && !$consultation->isPayer()) {

            $prixTraitements = count($consultation->getTraitements()) * 25;
            $prixDuree = ($consultation->getDuree() ?? 0) * 2;

            $consultation->setPrix($prixTraitements + $prixDuree);
            $consultation->setPayer(true);

            $entityManager->flush();

            return $this->redirectToRoute('app_payment', [
                'id' => $consultation->getId()
            ]);
        }

        $prixTraitements = count($consultation->getTraitements()) * 25;
        $prixDuree = ($consultation->getDuree() ?? 0) * 2;
        $prixTotal = $prixTraitements + $prixDuree;

        return $this->render('payment/index.html.twig', [
            'consultation' => $consultation,
            'cartes' => $cartes,
            'prixTotal' => $prixTotal,
        ]);
    }
}
