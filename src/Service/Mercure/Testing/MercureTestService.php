<?php

namespace App\Service\Mercure\Testing;

use App\Entity\MercureTest;
use App\Repository\MercureTestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MercureTestService
{

    public function __construct(
        private MercureTestRepository $mercureTestRepository,
        private EntityManagerInterface $manager
    )
    {

    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function hasBeenTestedBy(UserInterface $user): bool
    {
        return count($this->mercureTestRepository->findBy([
                'user' => $user
            ])) > 0;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    // Todo make it async => messenger

    public function saveHasBeenTestedBy(UserInterface $user): bool
    {
        $test = new MercureTest();
        $test->setUser($user);
        try {
            $this->manager->persist($test);
            $this->manager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param UserInterface $user
     * @return void
     */
    // Todo make it async => messenger

    public function removeTestsBy(UserInterface $user): void
    {
        $tests = $this->mercureTestRepository->findBy([
            'user' => $user
        ]);

        if ( !$tests || count($tests) === 0 ) {
            return;
        }

        $this->mercureTestRepository->removeAllTestsForUser($user);
    }

}
