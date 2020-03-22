<?php

namespace App\Controller;


use App\Entity\Employe;
use App\Entity\TempsProduction;
use App\Repository\EmployeRepository;
use App\Repository\MetierRepository;
use App\Repository\ProjetRepository;
use App\Repository\TempsProductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var MetierRepository */
    private $metierRepository;

    /** @var EmployeRepository */
    private $employeRepository;

    /** @var ProjetRepository */
    private $projetRepository;

    /** @var TempsProductionRepository */
    private $tempsProductionRepository;
    public function __construct(EntityManagerInterface $em, MetierRepository $metierRepository, EmployeRepository $employeRepository, ProjetRepository $projetRepository, TempsProductionRepository $tempsProductionRepository)
    {
        $this->em = $em;
        $this->metierRepository = $metierRepository;
        $this->employeRepository = $employeRepository;
        $this->projetRepository = $projetRepository;
        $this->tempsProductionRepository = $tempsProductionRepository;

    }


    /**
     * @Route("/home", name="main_homepage")
     */
    public function homepage(): Response
    {
        $nbTotal = sizeof($this->projetRepository->findAll());
        $projetsLivres = $this->projetRepository->findAllLivre();
        $nbLivres = sizeof($projetsLivres);
        $nbEncours = $nbTotal - $nbLivres;
        $nbemployes = sizeof($this->employeRepository->findAll());
        $heuresProduction = implode('', $this->tempsProductionRepository->tempsProduction());
        $tauxLivraison = round($nbLivres/$nbTotal * 100,2);

        $nbrentable = 0;
        foreach($projetsLivres as $projet) {
            $id = $projet->getId();
            $cout = $this->projetRepository->coutProjet($id);
            if ($projet->getPrix() > $cout['cout_total']) {
                $nbrentable += 1;
            }
        }
        $tauxRentabilite = $nbrentable / $nbLivres * 100;

        $nombres = array(
            "projetsEncours" => $nbEncours,
            "projetsLivres" => $nbLivres,
            "nbemployes" => $nbemployes,
            "heuresProduction" => $heuresProduction,
            "nbRentable" => $nbrentable,
            "nonRentable" => $nbLivres - $nbrentable,
            "tauxRentabilite" => $tauxRentabilite,
            "tauxLivraison" => $tauxLivraison
    );
        $bestEmploye = $this->tempsProductionRepository->bestEmploye();
        $derniersProjets = $this->projetRepository->lastProjets();
        $derniersTpsProduction = $this->tempsProductionRepository->lastTempsProduction();

        return $this->render('main/index.html.twig', [
            'nombres' => $nombres,
            'bestEmploye' => $bestEmploye,
            'derniersProjets' => $derniersProjets,
            'derniersTpsProduction' => $derniersTpsProduction
        ]);
    }


}
