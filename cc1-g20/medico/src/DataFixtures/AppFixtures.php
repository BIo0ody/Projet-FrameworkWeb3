<?php

namespace App\DataFixtures;

use App\Entity\CarteBancaire;
use App\Entity\Traitement;
use App\Entity\User;
use App\Entity\Consultation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user1 = new User();
        $user1 ->setEmail("a@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user1,"123"))
                ->setRoles(["ROLE_ADMIN"])
                ->setNom($faker->words(1, true))
                ->setSsn(111111111111111)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Homme");
        $manager->persist($user1);

        $user2 = new User();
        $user2 ->setEmail("m@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user2,"123"))
                ->setRoles(["ROLE_MEDECIN"])
                ->setNom($faker->words(1, true))
                ->setSsn(222222222222222)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Homme");
        $manager->persist($user2);

        $user3 = new User();
        $user3 ->setEmail("p1@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user3,"123"))
                ->setRoles(["ROLE_PATIENT"])
                ->setNom($faker->words(1, true))
                ->setSsn(333333333333333)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Homme");
        $manager->persist($user3);

        $user4 = new User();
        $user4 ->setEmail("p2@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user4,"123"))
                ->setRoles(["ROLE_PATIENT"])
                ->setNom($faker->words(1, true))
                ->setSsn(444444444444444)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Homme");
        $manager->persist($user4);

        $user5 = new User();
        $user5 ->setEmail("p3@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user5,"123"))
                ->setRoles(["ROLE_PATIENT"])
                ->setNom($faker->words(1, true))
                ->setSsn(555555555555555)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Autre");
        $manager->persist($user5);

        $user6 = new User();
        $user6->setEmail("p4@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user6,"123"))
                ->setRoles(["ROLE_PATIENT"])
                ->setNom($faker->words(1, true))
                ->setSsn(666666666666666)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Femme");
        $manager->persist($user6);

        $user7 = new User();
        $user7 ->setEmail("p5@gmail")
                ->setPassword($this->passwordHasher->hashPassword($user7,"123"))
                ->setRoles(["ROLE_PATIENT"])
                ->setNom($faker->words(1, true))
                ->setSsn(777777777777777)
                ->setPrenom($faker->words(1, true))
                ->setGenre("Femme");
        $manager->persist($user7);

        $users = [];
        $users[]=$user3;
        $users[]=$user4;
        $users[]=$user5;
        $users[]=$user6;
        $users[]=$user7;
        
        $cartes = [];
        for ($i = 0; $i < 5; $i++) {
            $carte = new CarteBancaire();
            $carte->setNumero($faker->numberBetween(1000000000000000, 9999999999999999))
                ->setPrenom($faker->words(1, true))
                ->setNom($faker->words(1, true))
                ->setType("visa")
                ->setExpiration($faker->dateTimeBetween('+1 years', '+5 years'))
                ->setCodeSecuriter($faker->numberBetween(100, 999))
                ->setUser($users[$i]);
            $manager->persist($carte);
        }

        $consults = [];
        for ($i = 0; $i < 50; $i++) {
            $consult = new Consultation();
            $consult->setAge($faker->numberBetween(1, 95));
            $consult->setDescription($faker->sentence(10));
            $consult->setDate($faker->dateTimeBetween('-1 year', 'now'));
            $consult->setMedecin($user2);
            $consult->setPayer(false);
            $consult->setDuree($faker->numberBetween(30, 60));


            $manager->persist($consult);
            $consults[] = $consult;
        }

        for ($i = 0; $i < 10; $i++) {
                $consults[$i]->setPatient($user3);
                $manager->persist($consults[$i]);
        }
        for ($i = 10; $i < 20; $i++) {
                $consults[$i]->setPatient($user4);
                $manager->persist($consults[$i]);
        }
        for ($i = 20; $i < 30; $i++) {
                $consults[$i]->setPatient($user5);
                $manager->persist($consults[$i]);
        }
        for ($i = 30; $i < 40; $i++) {
                $consults[$i]->setPatient($user6);
                $manager->persist($consults[$i]);
        }
        for ($i = 40; $i < 50; $i++) {
                $consults[$i]->setPatient($user7);
                $manager->persist($consults[$i]);
        }

        

        $traitement = new Traitement();
        $traitement->setMedicament("morphine")
                ->setQuantite(3)
                ->setContenant("addictive ...")
                ->setDuree(7)
                ->setPosologie($faker->sentence(4))
                ->setConsultation($consults[1]);
        $manager->persist($traitement);
        

        $manager->flush();
    }
}
