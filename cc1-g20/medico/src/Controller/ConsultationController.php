<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/consults')]
final class ConsultationController extends AbstractController
{
    #[Route(name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository, Request $request, string $sortField = 'date', string $sortDirection = 'ASC', string $searchTerm = null, string $payerFilter = null): Response
    {
        $user = $this->getUser();
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $sortField = $request->query->get('sort', 'date');
        $sortDirection = $request->query->get('direction', 'ASC');
        $searchTerm = $request->query->get('q',null);

        $payerParam = $request->query->get('payer', null);

        if ($payerParam === '1') {
            $payerFilter = true;
        } elseif ($payerParam === '0') {
            $payerFilter = false;
        } else {
            $payerFilter = null;
        }

        $consultations = $consultationRepository->findByUserRole($user, $page, $limit, $sortField, $sortDirection,$searchTerm, $payerFilter);

        $totalConsultations = $consultationRepository->countByUserRole($user, $searchTerm);


        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultations,
            'currentPage' => $page,
            'limit' => $limit,
            'totalConsultations' => $totalConsultations,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'searchTerm' => $searchTerm,
            'payerFilter' => $payerFilter,
        ]);
    }

    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    #[Route('/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,ConsultationRepository $consultationRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MEDECIN')){
            $consultation = new Consultation();
            $consultation->setDate(new \DateTime());

            $form = $this->createForm(ConsultationType::class, $consultation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($consultation);
                $entityManager->flush();

                return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('consultation/new.html.twig', [
                'consultation' => $consultation,
                'form' => $form,
            ]);
        }
        else{
            return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
        }
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_consultation_delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function delete(Consultation $consultation): Response
    {
        return $this->render('consultation/confirm_delete.html.twig', [
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}/confirm-delete', name: 'app_consultation_confirm_delete', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_MEDECIN')")]
    public function confirmDelete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($consultation);
        $entityManager->flush();;

        return $this->redirectToRoute('app_consultation_index', [], Response::HTTP_SEE_OTHER);
    }
}
