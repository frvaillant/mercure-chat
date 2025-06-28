<?php

namespace App\Repository;

use App\Entity\MercureTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<MercureTest>
 */
class MercureTestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MercureTest::class);
    }

    /**
     * @param UserInterface $user
     * @return void
     */

    public function removeAllTestsForUser(UserInterface $user): void
    {
        $this->createQueryBuilder('t')
            ->delete()
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }


}
