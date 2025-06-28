<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand('app:add:users')]
class CreateUsersCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $manager,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user1 = $this->userRepository->findOneBy([
            'username' => 'user_1'
        ]);

        $user2 = $this->userRepository->findOneBy([
            'username' => 'user_2'
        ]);

        if($user1 && $user2) {
            return Command::SUCCESS;
        }

        if(!$user1) {
            $user1 = new User();
            $password1 = '123456';
            $password = $this->hasher->hashPassword($user1, $password1);
            $user1
                ->setRoles(['ROLE_USER'])
                ->setUsername('user_1')
                ->setPassword($password);
            $this->manager->persist($user1);
        }

        if(!$user2) {
            $user2 = new User();
            $password2 = '123456';
            $password = $this->hasher->hashPassword($user2, $password2);
            $user2
                ->setRoles(['ROLE_USER'])
                ->setUsername('user_2')
                ->setPassword($password);
            $this->manager->persist($user2);
        }

        $this->manager->flush();
        return Command::SUCCESS;
    }
}
