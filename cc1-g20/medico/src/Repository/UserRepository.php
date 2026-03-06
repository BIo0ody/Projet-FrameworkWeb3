<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllUser(int $page = 1, int $limit = 10, string $sortField = 'nom', string $sortDirection = 'ASC', ?string $searchTerm = null)
    {
        $qb = $this->createQueryBuilder('u');

        if ($searchTerm) {
            $qb
                ->where('u.nom LIKE :searchTerm OR u.prenom LIKE :searchTerm OR u.ssn LIKE :searchTerm OR u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        $allowedFields = ['nom', 'prenom', 'ssn', 'email'];
        $allowedDirections = ['ASC', 'DESC'];

        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'nom';
        }

        if (!in_array(strtoupper($sortDirection), $allowedDirections)) {
            $sortDirection = 'ASC';
        }

        $qb->orderBy('u.' . $sortField, $sortDirection);

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function countAllUser(?string $searchTerm = null): int
    {
        $qb = $this->createQueryBuilder('u')
                   ->select('COUNT(u.id)');

        if ($searchTerm) {
            $qb->where('u.nom LIKE :searchTerm OR u.prenom LIKE :searchTerm OR u.ssn LIKE :searchTerm OR u.email LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
