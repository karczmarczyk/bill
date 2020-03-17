<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('mateusz.karczmarczyk@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'power1'
        ));
        $user->setRoles(['USER_ROLE']);

        $manager->persist($user);
        $manager->flush();
    }
}
