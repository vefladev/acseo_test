<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Message;
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
        // création d'un faux admin
        $admin  = new User;
        $admin->setPrenom($faker->firstName());
        $admin->setNom($faker->lastName());
        $admin->setEmail("admin@email.com");
        $password = $this->encoder->hashPassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        // création d'un faux jeu de données users avec les données crée par faker
        for ($u = 0; $u < 5; $u++) {
            $user  = new User;
            $nom = $faker->lastName();
            $prenom = $faker->firstName();
            $user->setNom($nom);
            $user->setPrenom($prenom);
            // on met la première lettre en minuscule seulement pour l'email
            $nomM =  lcfirst($nom);
            $prenomM =  lcfirst($prenom);
            $user->setEmail("$nomM.$prenomM@mail.com");
            // on encode le mot de passe
            $password = $this->encoder->hashPassword($user, 'user');
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);


            $manager->persist($user);

            // création d'un faux jeu de données messages par rapport aux users
            for ($m = 0; $m <= mt_rand(3, 6); $m++) {
                $message  = new Message;
                $message->setUser($user);
                $message->setContent($faker->paragraph());
                $message->setDone(0);
                // date entre il y a une semaine et aujourd'hui
                $message->setCreatedAt($faker->dateTimeBetween('-1 week'));

                $manager->persist($message);
            }
        }

        $manager->flush();
    }
}
