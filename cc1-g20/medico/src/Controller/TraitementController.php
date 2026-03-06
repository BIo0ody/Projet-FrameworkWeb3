<?php

namespace App\Controller;

use App\Entity\Traitement;
use App\Form\TraitementType;
use App\Repository\TraitementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Consultation;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/consults/{consultationId}/traitements')]
final class TraitementController extends AbstractController
{
    
    #[Route(name: 'app_traitement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, int $consultationId, TraitementRepository $traitementRepository, Request $request): Response
    {
        $consultation = $entityManager->getRepository(Consultation::class)->find($consultationId);

        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $finiParam = $request->query->get('fini', null);

        if ($finiParam === '1') {
            $finiFilter = true;
        } elseif ($finiParam === '0') {
            $finiFilter = false;
        } else {
            $finiFilter = null;
        }
        
        $sortDirection = $request->query->get('direction', 'ASC');

        $traitements = $traitementRepository->findTraitementsByConsultationId($consultationId, $page, $limit, $finiFilter, $sortDirection);
        
        $totalTraitements = $traitementRepository->countTraitementByConsultationID($consultationId, $finiFilter);

        $totalPages = max(ceil($totalTraitements / $limit), 1);

        return $this->render('traitement/index.html.twig', [
            'consultation' => $consultation,
            'traitements' => $traitements,
            'currentPage' => $page,
            'limit' => $limit,
            'finiFilter' => $finiFilter,
            'sortDirection' => $sortDirection,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'app_traitement_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function new(Request $request, EntityManagerInterface $entityManager, int $consultationId): Response
    {
        $consultation = $entityManager->getRepository(Consultation::class)->find($consultationId);
        
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MEDECIN')){

            $traitement = new Traitement();
            $traitement->setConsultation($consultation);
            $form = $this->createForm(TraitementType::class, $traitement);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($traitement);
                $entityManager->flush();

                return $this->redirectToRoute('app_traitement_index', [
                'consultationId' => $consultationId]);
            }
            return $this->render('traitement/new.html.twig', [
                'traitement' => $traitement,
                'form' => $form->createView(),
                'consultation' => $consultation,
                ]);
        }
        else{
            return $this->render('traitement/index.html.twig', [
            'consultation' => $consultation,
            'traitements' => $consultation->getTraitements(),
        ]);
        }
    }


    #[Route('/{id}', name: 'app_traitement_show', methods: ['GET'])]
    public function show(Traitement $traitement, int $consultationId, Request $request): Response
    {
        return $this->render('traitement/show.html.twig', [
            'traitement' => $traitement,
            'consultationId' => $consultationId,
            'returnTo' => $request->query->get('returnTo'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_traitement_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function edit(Request $request, Traitement $traitement, EntityManagerInterface $entityManager, int $consultationId, Request $request1): Response
    {
        $form = $this->createForm(TraitementType::class, $traitement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_traitement_index', [
                'consultationId' => $consultationId
            ]);
    }

        return $this->render('traitement/edit.html.twig', [
            'traitement' => $traitement,
            'form' => $form->createView(),
            'consultationId' => $consultationId,
            'returnTo' => $request1->query->get('returnTo'),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_traitement_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function delete(Request $request, Traitement $traitement, EntityManagerInterface $entityManager, int $consultationId, Request $request1): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('delete'.$traitement->getId(), $request->request->get('_token'))) {
                $entityManager->remove($traitement);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_traitement_index', [
                'consultationId' => $consultationId
            ]);
        }

        return $this->render('traitement/confirm_delete.html.twig', [
            'traitement' => $traitement,
            'consultationId' => $consultationId,
            'returnTo' => $request1->query->get('returnTo'),
        ]);
    }

}
