<?php

namespace App\Controller;

use App\Entity\Metier;
use App\Form\MetierType;
use App\Repository\EmployeRepository;
use App\Repository\MetierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/metier", name="metier_")
 */
class MetierController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var MetierRepository */
    private $metierRepository;

    /** @var EmployeRepository */
    private $employeRepository;

    public function __construct(EntityManagerInterface $em,  MetierRepository $metierRepository, EmployeRepository $employeRepository)
    {
        $this->em = $em;
        $this->metierRepository = $metierRepository;
        $this->employeRepository = $employeRepository;
    }


    /**
     * @Route("/{page}", name="liste", requirements={"page" = "\d+"})
     */
    public function listMetier(int $page): Response
    {

        $metiers = $this->metierRepository->findAll();
        $nb_msg_page = 10;
        if (!empty($metiers)){
            $pages=array_chunk($metiers, $nb_msg_page);

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

        return $this->render('metier/list.html.twig', [
            'metiers' => $metiers, 'i' => $page,
            'nb_msg_page'=>$nb_msg_page, 'pages'=>$pages, 'page_actuel'=>$page_actuel, 'nbpages'=>$nbpages
        ]);
    }


    /**
     * @Route("/form/{id}", name="form", requirements={"id" = "\d+"})
     */
    public function formMetier(int $id, Request $request): Response
    {
        if ($id != 0){
            $metier = $this->metierRepository->find($id);
            if (null === $metier) {
                throw new NotFoundHttpException();
            }
        }else{
            $metier = new Metier();
        }

        $form = $this->createForm(MetierType::class, $metier);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($metier);
            $this->em->flush();
            if ($id != 0){
                $this->addFlash('success', 'Le metier a été modifié avec succès');
                $page = 1;
            }else{
                $this->addFlash('success', 'Le metier a été ajouté avec succès');
                $metiers = $this->metierRepository->findAll();
                $pages=array_chunk($metiers, 5);
                $page = sizeof($pages);
            }


            return $this->redirectToRoute('metier_liste', ["page" => $page]);
        }
        return $this->render('metier/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/suppression/{id}", name="suppression", requirements={"id" = "\d+"})
     */
    public function SuppMetier(int $id, Request $request): Response
    {
        if ($id != 0){
            $size = sizeof($this->employeRepository->verifSuppMetier($id));
            if($size == 0){
                $metier = $this->metierRepository->find($id);
                $this->em->remove($metier);
                $this->em->flush();
                $this->addFlash('success', 'Le metier a été supprimé avec succès');
            }else{
                $this->addFlash('danger', 'Vous ne pouvez pas supprimer ce metier.');
            }
        }

        return $this->redirectToRoute('metier_liste', ["page" => 1]);
        }
}
