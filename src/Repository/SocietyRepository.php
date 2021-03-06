<?php

namespace App\Repository;

use App\Entity\Society;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Society|null find($id, $lockMode = null, $lockVersion = null)
 * @method Society|null findOneBy(array $criteria, array $orderBy = null)
 * @method Society[]    findAll()
 * @method Society[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocietyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Society::class);
    }

    /**
     * Méthode permettant de retourner toutes les sociétés sauf une bien précise
     * @param String $societyName
     * @return int|mixed|string
     */
    public function findSocietyExcept(String $societyName){
        return $this->createQueryBuilder('society')
            ->where('society.name != :val')
            ->setParameter('val', $societyName)
            ->getQuery()
            ->getResult();
    }

    /**
     * Méthode qui permet de retourner un ensemble de sociétés selon un schéma
     * @param String $search
     * @return int|mixed|string
     */
    public function findSocietyByPattern(String $search){
        return $this->createQueryBuilder('searchSociety')
            ->where('searchSociety.name LIKE :value')
            ->setParameter('value', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Society[] Returns an array of Society objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Society
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
