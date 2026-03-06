<?php

namespace App\Repository;

use App\Entity\Traitement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Traitement>
 */
class TraitementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Traitement::class);
    }

    //    /**
    //     * @return Traitement[] Returns an array of Traitement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Traitement
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByUserRole(User $user, int $page = 1, int $limit = 10, ?bool $finiFilter = null, string $sortDirection = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.consultation', 'c')
            ->addSelect('c');

        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            $qb->andWhere('c.patient = :user')
            ->setParameter('user', $user);

            if ($finiFilter !== null) {

                if ($finiFilter === true) {
                    $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') < :now");
                } else {
                    $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') >= :now");
                }

                $qb->setParameter('now', new \DateTime());
            }

            $allowedDirections = ['ASC', 'DESC'];
        
            if (!in_array(strtoupper($sortDirection), $allowedDirections)) $sortDirection = 'ASC';
            
            $qb->orderBy('t.id', strtoupper($sortDirection));

        } elseif (in_array('ROLE_MEDECIN', $user->getRoles())) {
            $qb->andWhere('c.medecin = :user')
            ->setParameter('user', $user);
        }

        $qb->setFirstResult(($page - 1) * $limit)
       ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countByUserRole(User $user, ?bool $finiFilter = null): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->join('t.consultation', 'c');

        if (in_array('ROLE_PATIENT', $user->getRoles())) {
            $qb->andWhere('c.patient = :user')
            ->setParameter('user', $user);
        } elseif (in_array('ROLE_MEDECIN', $user->getRoles())) {
            $qb->andWhere('c.medecin = :user')
            ->setParameter('user', $user);
        }

        if ($finiFilter !== null) {
            if ($finiFilter === true) {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') < :now");
            } else {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') >= :now");
            }

            $qb->setParameter('now', new \DateTime());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }


    public function findTraitementsByConsultationId(int $consultationId, int $page = 1, int $limit = 10, ?bool $finiFilter = null, string $sortDirection = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.consultation = :consultationId')
            ->setParameter('consultationId', $consultationId);

        if ($finiFilter !== null) {
            $qb->join('t.consultation', 'c');

            if ($finiFilter === true) {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') < :now");
            } else {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') >= :now");
            }

            $qb->setParameter('now', new \DateTime());
        }

        $allowedDirections = ['ASC', 'DESC'];
    
        if (!in_array(strtoupper($sortDirection), $allowedDirections)) $sortDirection = 'ASC';
        
        $qb->orderBy('t.id', strtoupper($sortDirection));

        $qb->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countTraitementByConsultationID(int $consultationId, ?bool $finiFilter = null): int
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.consultation = :consultationId')
            ->setParameter('consultationId', $consultationId);

        if ($finiFilter !== null) {
            $qb->join('t.consultation', 'c');

            if ($finiFilter === true) {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') < :now");
            } else {
                $qb->andWhere("DATE_ADD(c.date, t.duree, 'DAY') >= :now");
            }

            $qb->setParameter('now', new \DateTime());
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

}
