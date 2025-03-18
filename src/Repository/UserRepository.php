<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    /**
     * Search users by email, name or firstName
     */
    public function searchUsers(string $query, int $currentUserId): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.email LIKE :query')
            ->orWhere('u.name LIKE :query')
            ->orWhere('u.firstName LIKE :query')
            ->andWhere('u.id != :currentUserId')
            ->setParameter('query', '%' . $query . '%')
            ->setParameter('currentUserId', $currentUserId)
            ->orderBy('u.email', 'ASC')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }
}