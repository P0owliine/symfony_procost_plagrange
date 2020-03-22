<?php

namespace App\Controller;


use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use App\Repository\TempsProductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projet", name="projet_")
 */
class ProjetController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ProjetRepository */
    private $projetRepository;

    /** @var TempsProductionRepository */
    private $tempsProductionRepository;

    public function __construct(EntityManagerInterface $em, ProjetRepository $projetRepository, TempsProductionRepository $tempsProductionRepository)
    {
        $this->em = $em;
        $this->projetRepository = $projetRepository;
        $this->tempsProductionRepository = $tempsProductionRepository;
    }


    /**
     * @Route("/{page}", name="liste", requirements={"page" = "\d+"})
     */
    public function listProjet(int $page): Response
    {
        $projets = $this->projetRepository->findAll();
        $nb_msg_page = 10;
        if (!empty($projets)){
            $pages=array_chunk($projets, $nb_msg_page);

            $nbpages = sizeof($pages);
            if($page != 0 && $page <= $nbpages){
                $page_actuel = $page;
                $page_actuel = $page_actuel-1;
            }else{
                $page = 1;
                $page_actuel = 0;
            }
            $nb_msg_page = sizeof($pages[$page_actuel]);
        }

        return $this->render('projet/list.html.twig', [
            'i' => $page, 'nb_msg_page'=>$nb_msg_page, 'pages'=>$pages, 'page_actuel'=>$page_actuel, 'nbpages'=>$nbpages
        ]);
    }


    /**
     * @Route("/form/{id}", name="form", requirements={"id" = "\d+"})
     */
    public function projetForm(int $id, Request $request): Response
    {
        if ($id != 0){
            $projet = $this->projetRepository->find($id);
            if (null === $projet) {
                throw new NotFoundHttpException();
            }
        }else{
            $projet = new Projet();
        }

        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($projet);
            $this->em->flush();
            if ($id != 0){
                $this->addFlash('success', 'Le projet a été modifié avec succès');
                $page = 1;
            }else{
                $this->addFlash('success', 'Le projet a été ajouté avec succès');
                $projets = $this->projetRepository->findAll();
                $pages=array_chunk($projets, 10);
                $page = sizeof($pages);
            }



            return $this->redirectToRoute('projet_liste', ["page" => $page]);
        }
        return $this->render('projet/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/detail/{id}/{page}", name="detail", requirements={"id" = "\d+", "page" = "\d+"})
     */
    public function detailProjet(int $id, int $page): Response
    {
        if ($id != 0){
            $projet = $this->projetRepository->find($id);
            if (null === $projet) {
                throw new NotFoundHttpException();
            }
        }

        $tpsProduction = $this->projetRepository->tempsProduction($id);
        $nb_msg_page = 10;
        if (!empty($tpsProduction)){
            $pages=array_chunk($tpsProduction, $nb_msg_page);

            $nbpages = sizeof($pages);
            if($page != 0 && $page <= $nbpages){
                $page_actuel = $page;
                $page_actuel = $page_actuel-1;
            }else{
                $page = 1;
                $page_actuel = 0;
            }
            $nb_msg_page = sizeof($pages[$page_actuel]);
        }

        $infoSupp = $this->projetRepository->coutProjet($id);
        return $this->render('projet/detail.html.twig', [
            'projet' => $projet, 'tpsProduction' => $tpsProduction, 'infoSupp' => $infoSupp,
            'i' => $page, 'nb_msg_page'=>$nb_msg_page, 'pages'=>$pages, 'page_actuel'=>$page_actuel, 'nbpages'=>$nbpages
        ]);
    }

    /**
     * @Route("/livrer/{id}", name="livrer", requirements={"id" = "\d+"})
     */
    public function livrerProjet(int $id): Response
    {
        if ($id != 0){
            $projet = $this->projetRepository->find($id);
            $projet->setDateLivraison(new \DateTime());
            $this->em->flush();
            $this->addFlash('success', 'Le projet a été livré avec succès');
        }

        return $this->redirectToRoute('projet_detail', ["id" => $id, "page" => 1]);
    }
}
