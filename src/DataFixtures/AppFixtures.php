<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    // les fixtures sont des fausses données qu'on peut charger dans la bdd pour des tests
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        $admin  = new User;
        $admin->setPrenom($faker->firstName());
        $admin->setNom($faker->lastName());
        $admin->setEmail("admin@email.com");
        $password = $this->encoder->hashPassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);


        for ($u = 0; $u < 5; $u++) {
            $user  = new User;
            $user->setPrenom($faker->firstName());
            $user->setNom($faker->lastName());
            $user->setEmail("user$u@mail.com");
            $password = $this->encoder->hashPassword($user, 'user');
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);


            $manager->persist($user);
        }

        $manager->flush();
    }
}
