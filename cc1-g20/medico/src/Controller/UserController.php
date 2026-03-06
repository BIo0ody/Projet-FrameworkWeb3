<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ConsultationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/users')]
final class UserController extends AbstractController
{
    #[Route('/index', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request, string $sortField = 'nom', string $sortDirection = 'ASC', string $searchTerm = null): Response
{
    $user = $this->getUser();

    if (!in_array('ROLE_ADMIN', $user->getRoles())) {
        throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
    }

    $page = $request->query->getInt('page', 1);
    $limit = 10;

    $sortField = $request->query->get('sort', 'nom');
    $sortDirection = $request->query->get('direction', 'ASC');
    $searchTerm = $request->query->get('q',null);

    $users = $userRepository->findAllUser($page, $limit, $sortField, $sortDirection, $searchTerm);

    $totalUsers = $userRepository->countAllUser($searchTerm);

    return $this->render('user/index.html.twig', [
        'users' => $users,
        'sortField' => $sortField,
        'sortDirection' => $sortDirection,
        'searchTerm' => $searchTerm,
        'totalUsers' => $totalUsers,
            'limit' => $limit,
            'currentPage' => $page,
    ]);
    }
    

    #[Route('/patients', name: 'app_user_patients', methods: ['GET'])]
    public function patients(ConsultationRepository $consultationRepository, Request $request, string $sortField = 'date', string $sortDirection = 'ASC', string $searchTerm = null): Response
    {
        $user = $this->getUser();

        
        if (in_array('ROLE_MEDECIN', $user->getRoles())) {

            $page = $request->query->getInt('page', 1);
            $limit = 10;

            $sortField = $request->query->get('sort', 'date');
            $sortDirection = $request->query->get('direction', 'ASC');
            $searchTerm = $request->query->get('q',null);

            $patients = $consultationRepository->findPatientsByDoctor($user, $page, $limit, $sortField, $sortDirection, $searchTerm);

            $nbPatients = $consultationRepository->countByRole('ROLE_MEDECIN', $user);
        }
        else {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        return $this->render('user/patients.html.twig', [
            'patients' => $patients,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'searchTerm' => $searchTerm,
            'nbPatients' => $nbPatients,
            'limit' => $limit,
            'currentPage' => $page,
        ]);
    }

    #[Route('/medecins', name: 'app_user_medecins', methods: ['GET'])]
    public function medecins(ConsultationRepository $consultationRepository, Request $request): Response
    {
        $user = $this->getUser();

        
        if (in_array('ROLE_PATIENT', $user->getRoles())) {

            $page = $request->query->getInt('page', 1);
            $limit = 10;

            $medecins = $consultationRepository->findDoctorsByPatient($user, $page, $limit);

            $nbMedecins = $consultationRepository->countByRole('ROLE_PATIENT', $user);
        }
        else {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        return $this->render('user/medecins.html.twig', [
            'medecins' => $medecins,
            'nbMedecins' => $nbMedecins,
            'limit' => $limit,
            'currentPage' => $page,
        ]);
    }

    
}
