<?php

namespace SalarieBundle\Repository;

/**
 * SalarieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SalarieRepository extends \Doctrine\ORM\EntityRepository
{

    public function getCountCDIbyYear($dataEntry, $condition= null, $param= null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.dateEntre <= :dataEntry AND a.typeContrat = :typeContrat'.($condition?' AND ('.$condition.')':''));
        $qb->setParameter('dataEntry', $dataEntry . "-12-31");
        $qb->setParameter('typeContrat', 'CDI');

        if($param && $condition)
            foreach($param as $key => $value)
                $qb->setParameter($key, $value);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function getCountCDIbySexeAndAgeRange($sexe, $min, $max, $condition= null, $param= null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.dateNaissance >= :min AND a.dateNaissance <= :max AND a.sexe = :sexe'.($condition?' AND ('.$condition.')':''));
        $qb->setParameter('max', date('Y-m-d', strtotime('-'.$min.' year')));
        $qb->setParameter('min', date('Y-m-d', strtotime('-'.$max.' year')));
        $qb->setParameter('sexe', $sexe);

        if($param && $condition)
            foreach($param as $key => $value)
                $qb->setParameter($key, $value);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }


    public function getContratCount($typeContrat, $condition= null, $param= null)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.typeContrat = :typeContrat'.($condition?' AND ('.$condition.')':''));
        $qb->setParameter('typeContrat', $typeContrat);

        if($param && $condition)
            foreach($param as $key => $value)
                $qb->setParameter($key, $value);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
