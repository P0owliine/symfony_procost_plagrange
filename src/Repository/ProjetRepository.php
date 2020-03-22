<?php

namespace App\Repository;

use App\Entity\Projet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Projet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Projet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Projet[]    findAll()
 * @method Projet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Projet::class);
    }

     /**
      * @return Projet[] Returns an array of Projet objects
      */

    public function findAllEncours()
    {
        return $this->createQueryBuilder('p')
            ->where('p.date_livraison IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Projet[] Returns an array of Projet objects
     */

    public function findAllLivre()
    {
        return $this->createQueryBuilder('p')
            ->where('p.date_livraison IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }


    public function lastProjets()
    {
        return $this->createQueryBuilder('p')
            ->select('p.id as id, p.nom as nom, p.date_creation as dateCreation, p.prix as prix, p.date_livraison as dateLivraison, SUM(e.coutHoraire * t.heuresTravailles) as cout_total')
            ->leftJoin('p.tempsProductions', 't')
            ->leftJoin('t.employe', 'e')
            ->orderBy('p.date_creation', 'DESC')
            ->groupBy('p.id')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }


    public function coutProjet($idProjet)
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(e.id) as nbEmploye, SUM(e.coutHoraire * t.heuresTravailles) as cout_total')
            ->leftJoin('p.tempsProductions', 't')
            ->leftJoin('t.employe', 'e')
            ->where('p.id = :id')
            ->setParameter('id', $idProjet)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function tempsProduction($idProjet)
    {
        return $this->createQueryBuilder('p')
            ->select('e.prenom as prenom, e.nom as nom, e.coutHoraire as coutHoraire, t.heuresTravailles as heuresTravailles, t.dateSaisie as dateSaisie, (e.coutHoraire * t.heuresTravailles) as cout_total')
            ->leftJoin('p.tempsProductions', 't')
            ->leftJoin('t.employe', 'e')
            ->where('p.id = :id')
            ->setParameter('id', $idProjet)
            ->getQuery()
            ->getResult()
            ;
    }

}
