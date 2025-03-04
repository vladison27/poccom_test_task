<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Sell;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sell>
 */
class SellRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sell::class);
    }

    public function findPopularAuthors(DateTime $periodFrom, DateTime $periodTo, ?int $genreId, int $limit): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('a.id, a.name, a.birth_date, SUM(s.count) as totalSold')
            ->join('s.book', 'b')
            ->join('b.authors', 'a')
            ->where('s.created_at BETWEEN :periodFrom AND :periodTo')
            ->groupBy('a.id')
            ->orderBy('totalSold', 'DESC')
            ->setParameter('periodFrom', $periodFrom)
            ->setParameter('periodTo', $periodTo)
            ->setMaxResults($limit);

        if ($genreId !== null) {
            $qb->join('b.genres', 'g')
                ->andWhere('g.id = :genreId')
                ->setParameter('genreId', $genreId);
        }

        return $qb->getQuery()->getResult();
    }

    public function largestPriceDate($bookId): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s.created_at')
            ->where('s.book = :bookId')
            ->orderBy('s.count * s.price_per_unit', 'DESC')
            ->setParameter('bookId', $bookId)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    //    /**
    //     * @return Sell[] Returns an array of Sell objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sell
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
