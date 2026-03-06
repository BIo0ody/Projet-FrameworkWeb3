<?php

namespace App\Repository;

use App\Entity\Consultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Consultation>
 */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    //    /**
    //     * @return Consultation[] Returns an array of Consultation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Consultation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByUserRole(User $user, int $page = 1, int $limit = 10, string $sortField = 'date', string $sortDirection = 'ASC', string $searchTerm = null, bool $payerFilter = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.date', 'DESC');

        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            $qb->andWhere('c.patient = :user')
                ->setParameter('user', $user);

            if ($payerFilter !== null) {
                $qb->andWhere('c.payer = :payer')
                    ->setParameter('payer', $payerFilter);
            }

            $allowedFields = ['date', 'nom'];
            $allowedDirections = ['ASC', 'DESC'];

            if (!in_array($sortField, $allowedFields)) $sortField = 'date';
            if (!in_array(strtoupper($sortDirection), $allowedDirections)) $sortDirection = 'ASC';

            if ($sortField === 'nom') {
                $qb->join('c.medecin', 'm')
                    ->orderBy('m.nom', strtoupper($sortDirection));
            } else {
                $qb->orderBy('c.' . $sortField, strtoupper($sortDirection));
            }


        } elseif (in_array('ROLE_MEDECIN', $user->getRoles())) {
            $qb->andWhere('c.medecin = :user')
                ->setParameter('user', $user);

            if ($searchTerm) {
                $qb->join('c.patient', 'p')
                    ->andWhere('p.nom LIKE :searchTerm OR p.prenom LIKE :searchTerm OR p.ssn LIKE :searchTerm OR c.date LIKE :searchTerm')
                    ->setParameter('searchTerm', '%' . $searchTerm . '%');
            }

            $allowedFields = ['nom', 'prenom', 'ssn', 'date'];
            $allowedDirections = ['ASC', 'DESC'];

            if (!in_array($sortField, $allowedFields)) $sortField = 'date';
            if (!in_array(strtoupper($sortDirection), $allowedDirections)) $sortDirection = 'ASC';

            if (in_array($sortField, ['nom', 'prenom', 'ssn'])) {
                $qb->join('c.patient', 'p')
                    ->orderBy('p.' . $sortField, strtoupper($sortDirection));
            } else {
                $qb->orderBy('c.' . $sortField, strtoupper($sortDirection));
            }
        }

        

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByUserRole(User $user, string $searchTerm = null, bool $payerFilter = null): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            $qb->andWhere('c.patient = :user')
            ->setParameter('user', $user);
        } elseif (in_array('ROLE_MEDECIN', $user->getRoles())) {
            $qb->andWhere('c.medecin = :user')
            ->setParameter('user', $user);
        }

        if ($payerFilter !== null) {
            $qb->andWhere('c.payer = :payer')
                ->setParameter('payer', $payerFilter);
        }

        if ($searchTerm) {
            $qb->join('c.patient', 'p')
            ->andWhere('p.nom LIKE :searchTerm OR p.prenom LIKE :searchTerm OR p.ssn LIKE :searchTerm OR c.date LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findPatientsByDoctor(User $medecin, int $page = 1, int $limit = 10, string $sortField = 'nom', string $sortDirection = 'ASC', string $searchTerm = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.patient', 'p')
            ->addSelect('p')
            ->where('c.medecin = :medecin')
            ->setParameter('medecin', $medecin)
            ->groupBy('p.id');

        if ($searchTerm) {
        $qb->andWhere('p.nom LIKE :searchTerm OR p.prenom LIKE :searchTerm OR p.ssn LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $allowedFields = ['nom', 'prenom', 'ssn'];
        $allowedDirections = ['ASC', 'DESC'];

        if (!in_array($sortField, $allowedFields)) $sortField = 'nom';
        if (!in_array(strtoupper($sortDirection), $allowedDirections)) $sortDirection = 'ASC';

        $qb->orderBy('p.' . $sortField, strtoupper($sortDirection));

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findDoctorsByPatient(User $patient, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('c')
            ->join('c.medecin', 'm')
            ->addSelect('m')
            ->where('c.patient = :patient')
            ->setParameter('patient', $patient)
            ->groupBy('m.id')                 
            ->orderBy('m.nom', 'ASC');

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByRole(string $role, User $user): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');

        if ($role == 'ROLE_PATIENT') {
            $qb->join('c.patient', 'p')
                ->andWhere('p = :user')
                ->setParameter('user', $user);
        } elseif ($role == 'ROLE_MEDECIN') {
            $qb->join('c.medecin', 'm')
                ->andWhere('m = :user')
                ->setParameter('user', $user);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

}
