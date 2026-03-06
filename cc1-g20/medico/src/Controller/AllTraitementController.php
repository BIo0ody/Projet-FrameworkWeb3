<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TraitementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/traitements')]
final class AllTraitementController extends AbstractController
{
    #[Route('', name: 'app_traitement_all', methods: ['GET'])]
    public function index(TraitementRepository $traitementRepository, Request $request, string $sortDirection = 'ASC'): Response
    {
        $user = $this->getUser();
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

        $traitements = $traitementRepository->findByUserRole($user, $page, $limit,$finiFilter, $sortDirection);

        $totalTraitements = $traitementRepository->countByUserRole($user, $finiFilter);

        return $this->render('all_traitement/index.html.twig', [
            'traitements' => $traitements,
            'currentPage' => $page,
            'limit' => $limit,
            'totalTraitements' => $totalTraitements,
            'finiFilter' => $finiFilter,
            'sortDirection' => $sortDirection,
        ]);
    }
}
