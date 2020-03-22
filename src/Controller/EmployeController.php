<?php

namespace App\Controller;


use App\Entity\Employe;
use App\Entity\TempsProduction;
use App\Form\EmployeType;
use App\Form\TempsType;
use App\Repository\EmployeRepository;
use App\Repository\MetierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/employe", name="employe_")
 */
class EmployeController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EmployeRepository */
    private $employeRepository;

    /** @var MetierRepository */
    private $metierRepository;

    public function __construct(EntityManagerInterface $em, EmployeRepository $employeRepository, MetierRepository $metierRepository)
    {
        $this->em = $em;
        $this->employeRepository = $employeRepository;
        $this->metierRepository = $metierRepository;
    }


    /**
     * @Route("/{page}", name="liste", requirements={"page" = "\d+"})
     */
    public function listEmploye(int $page): Response
    {
        $employes = $this->employeRepository->findAll();
        $nb_msg_page = 10;
        if (!empty($employes)){
            $pages=array_chunk($employes, $nb_msg_page);

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

        return $this->render('employe/list.html.twig', [
            'employes' => $employes, 'i' => $page,
            'nb_msg_page'=>$nb_msg_page, 'pages'=>$pages, 'page_actuel'=>$page_actuel, 'nbpages'=>$nbpages
        ]);
    }

    /**
     * @Route("/form/{id}", name="form", requirements={"id" = "\d+"})
     */
    public function employeForm(int $id, Request $request): Response
    {
        if ($id != 0){
            $employe = $this->employeRepository->find($id);
            if (null === $employe) {
                throw new NotFoundHttpException();
            }
        }else{
            $employe = new Employe();
            $employe->setRoles(['ROLE_EMPLOYE']);
        }

        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData()->getPassword();
            $form->getData()->setPassword(password_hash($password, PASSWORD_DEFAULT));

            $this->em->persist($employe);
            $this->em->flush();
            if ($id != 0){
                $this->addFlash('success', 'L\'employé a été modifié avec succès');
                $page = 1;
            }else{
                $this->addFlash('success', 'L\'employé a été ajouté avec succès');
                $employes = $this->employeRepository->findAll();
                $pages=array_chunk($employes, 10);
                $page = sizeof($pages);
            }


            return $this->redirectToRoute('employe_liste', ["page" => $page]);
        }
        return $this->render('employe/form.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    /**
     * @Route("/detail/{id}/{page}", name="detail", requirements={"id" = "\d+", "page" = "\d+"})
     */
    public function detailEmploye(int $id, int $page, Request $request): Response
    {
        if ($id != 0){
            $employe = $this->employeRepository->find($id);
            if (null === $employe) {
                throw new NotFoundHttpException();
            }
        }

        $temps = new TempsProduction();
        $temps->setEmploye($employe);

        $form = $this->createForm(TempsType::class, $temps);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($temps);
            $this->em->flush();

            $this->addFlash('success', 'Temps de travail ajouté avec succès');

            }

        $tpsProduction = $this->employeRepository->tempsProduction($id);
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
        return $this->render('employe/detail.html.twig', [
            'employe' => $employe, 'tpsProduction' => $tpsProduction, 'form' => $form->createView(), 'id' => $id,
            'i' => $page, 'nb_msg_page'=>$nb_msg_page, 'pages'=>$pages, 'page_actuel'=>$page_actuel, 'nbpages'=>$nbpages
        ]);
    }
}
