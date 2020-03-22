<?php

namespace App\Repository;

use App\Entity\Employe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Employe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employe::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Employe) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return Employe[] Returns an array of Employe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Employe
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Employe[] Returns an array of Employe objects
     */

    public function findAll()
    {
        return $this->createQueryBuilder('e')
            ->where('e.roles = :role')
            ->setParameter('role', '["ROLE_EMPLOYE"]')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Employe[] Returns an array of Employe objects
     */

    public function verifSuppMetier(int $id)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.metier', 'm')
            ->where('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
            ;
    }


    public function tempsProduction($idEmploye)
    {
        return $this->createQueryBuilder('e')
            ->select('p.nom as projet, e.coutHoraire as coutHoraire, t.heuresTravailles as heuresTravailles, t.dateSaisie as dateSaisie, (e.coutHoraire * t.heuresTravailles) as cout_total')
            ->leftJoin('e.tempsProductions', 't')
            ->leftJoin('t.projet', 'p')
            ->where('e.id = :id')
            ->setParameter('id', $idEmploye)
            ->getQuery()
            ->getResult()
            ;
    }

}
