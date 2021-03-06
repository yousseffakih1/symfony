<?php

namespace OC\PlatformBundle\Repository;

/**
 * advertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class advertRepository extends \Doctrine\ORM\EntityRepository
{
  public function getAdvertWithCategories(array $categoriesName){

      $qb = $this->createQueryBuilder('a');
      $qb->leftJoin('a.categorie','c' )
         ->addSelect( 'c');

      $qb->where($qb->expr()->in('c.name', $categoriesName));

      return $qb->getQuery()
                ->getResult();

  }
}
