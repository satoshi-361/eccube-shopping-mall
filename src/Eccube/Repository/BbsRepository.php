<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Repository;

use Eccube\Entity\Bbs;
use Eccube\Entity\Order;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * BbsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BbsRepository extends AbstractRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bbs::class);
    }
    
    public function getMaxCount(Order $Order)
    {
        return (int) $this->createQueryBuilder('b')
        ->select('MAX(b.id) AS last_bbs_id')
        ->where('b.Order = :Order')
        ->setParameter('Order', $Order)
        ->getQuery()
        ->getSingleScalarResult();
    }
    
    public function getNewBbs(Order $Order,int $count)
    {
        return $this->createQueryBuilder('b')
        ->where('b.Order = :Order')
        ->andWhere('b.id > :count')
        ->setParameter('Order', $Order)
        ->setParameter('count', $count)
        ->addOrderBy('b.id', 'ASC')
        ->getQuery()
        ->getResult();
    }
}
