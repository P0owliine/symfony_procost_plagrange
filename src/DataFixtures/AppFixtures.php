<?php

namespace App\DataFixtures;

use App\Entity\Employe;
use App\Entity\Metier;
use App\Entity\Projet;
use App\Entity\TempsProduction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadMetiers();
        $this->loadEmployes();
        $this->loadProjet();
        $this->loadTempsProduction();

        $this->manager->flush();
    }

    private function loadEmployes(): void
    {
        $prenom = ['paul', 'pauline', 'thomas', 'theo', 'jordan', 'diego', 'lucas', 'héli', 'holly', 'julie', 'katy', 'betty'];
        $nom = ['handerson', 'lagrange', 'lagarde', 'fusaro', 'hermann', 'guerriero', 'hernandez', 'schester', 'dory', 'hoking', 'times', 'ugly'];
        $pwd = ['paulo', 'popo', 'toto', 'teo', 'jojo', 'diego', 'luka', 'eli', 'oly', 'july', 'katy', 'betty'];
        $img = ['paul.png', 'pauline.png', 'thomas.png', 'theo.png', 'jordan.png', 'diego.png', 'lucas.png', 'heli.png', 'holly.png', 'julie.png', 'katy.png', 'betty.png'];
        for ($i = 1; $i <= 12; $i++) {
            $employe = new Employe();
            $password = $this->encoder->encodePassword($employe, $pwd[$i - 1]);

            if ($i <= 2) {
                $employe
                    ->setRoles(['ROLE_MANAGER'])
                    ->setMetier($this->getReference('metier'.'0'));
            } else {
                $employe
                    ->setRoles(['ROLE_EMPLOYE'])
                    ->setMetier($this->getReference('metier' . random_int(1, 5)));
            }

            $employe
                ->setEmail(($nom[$i - 1]) . '.' . $prenom[$i - 1] . '@gmail.com')
                ->setPassword($password)
                ->setPrenom($prenom[$i - 1])
                ->setNom($nom[$i - 1])
                ->setImg($img[$i - 1])
                ->setCoutHoraire(rand(10, 100))
                ->setDateEmbauche(new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31)));

            $this->manager->persist($employe);
            $this->addReference('employe' . $i, $employe);
        }
    }

    private function loadMetiers(): void
    {
        $metiers = [
            'Manager',
            'Chef de projet',
            'Developpeur - Back End',
            'Developpeur - Front End',
            'Designer',
            'Marketeur',
        ];

        foreach ($metiers as $key => $name) {
            $metier = (new Metier())->setNom($name);

            $this->manager->persist($metier);
            $this->addReference('metier' . $key, $metier);
        }
    }

    private function loadProjet(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $projet = (new Projet())
                ->setNom('Projet ' . $i)
                ->setDescription('Description projet ' . $i)
                ->setPrix(random_int(100000, 1000000))
                ->setDateCreation(new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31)))
                ->setDateLivraison(null);

            $this->manager->persist($projet);
            $this->addReference('projet' . $i, $projet);
        }

        for ($i = 11; $i <= 15; $i++) {
            $projet = (new Projet())
                ->setNom('Projet ' . $i)
                ->setDescription('Description projet ' . $i)
                ->setPrix(random_int(1000, 10000));

            $dateC = new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31));
            $dateL = new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31));
            while ($dateL < $dateC) {
                $dateL = new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31));
            }
            $projet
                ->setDateCreation($dateC)
                ->setDateLivraison($dateL);

            $this->manager->persist($projet);
            $this->addReference('projet' . $i, $projet);
        }
    }

    private function loadTempsProduction(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $date = new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31));

            $tpsProduction = (new TempsProduction())
                ->setProjet($this->getReference('projet' . random_int(1, 11)))
                ->setEmploye($this->getReference('employe' . random_int(3, 12)))
                ->setHeuresTravailles(random_int(5, 35));
                if($date < new \DateTime()){
                    $tpsProduction ->setDateSaisie(new \DateTime());
                }

            $this->manager->persist($tpsProduction);
            $this->addReference('tpsProduction' . $i, $tpsProduction);
        }

        for ($i = 11; $i <= 40; $i++) {
            $date = new \DateTime('2019/' . random_int(1, 12) . '/' . random_int(1, 31));
            $tpsProduction = (new TempsProduction())
                ->setProjet($this->getReference('projet' . random_int(11, 15)))
                ->setEmploye($this->getReference('employe' . random_int(3, 12)))
                ->setHeuresTravailles(random_int(5, 35));
                if($date < new \DateTime()){
                    $tpsProduction ->setDateSaisie(new \DateTime());
                }

            $this->manager->persist($tpsProduction);
            $this->addReference('tpsProduction' . $i, $tpsProduction);
        }

    }
}
