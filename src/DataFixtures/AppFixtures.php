<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Message;
use Faker\Provider\DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $nom = $faker->lastName();
            $prenom = $faker->firstName();
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setEmail("$nom.$prenom@mail.com");
            $password = $this->encoder->hashPassword($user, 'user');
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);


            $manager->persist($user);


            for ($m = 0; $m <= mt_rand(3, 6); $m++) {
                $message  = new Message;
                $message->setUser($user);
                $message->setContent($faker->paragraph());
                $message->setDone(0);
                $message->setCreatedAt($faker->dateTimeBetween('-1 week'));

                $manager->persist($message);
            }
        }

        $manager->flush();
    }
}
