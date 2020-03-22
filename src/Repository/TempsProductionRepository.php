<?php

namespace App\Repository;

use App\Entity\TempsProduction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method TempsProduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method TempsProduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method TempsProduction[]    findAll()
 * @method TempsProduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TempsProductionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TempsProduction::class);
    }

    // /**
    //  * @return TempsProduction[] Returns an array of TempsProduction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TempsProduction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return TempsProduction[] Returns an array of TempsProduction objects
     */

    public function lastTempsProduction()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.dateSaisie', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult()
            ;
    }

    public function tempsProduction()
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(t.heuresTravailles)')
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function bestEmploye()
    {
        return $this->createQueryBuilder('t')
            ->select('e.prenom as prenom, e.nom as nom, e.dateEmbauche as dateEmbauche, m.nom as metier, t.heuresTravailles as heuresTravailles, MAX(e.coutHoraire * t.heuresTravailles) as cout_total')
            ->leftJoin('t.employe', 'e')
            ->leftJoin('e.metier', 'm')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
