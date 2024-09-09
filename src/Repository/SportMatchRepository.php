<?php

namespace App\Repository;

use App\Entity\SportMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SportMatch>
 *
 * @method SportMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportMatch[]    findAll()
 * @method SportMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportMatchRepository extends ServiceEntityRepository
{
//    /**
//     * @return SportMatch[] Returns an array of SportMatch objects
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

//    public function findOneBySomeField($value): ?SportMatch
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SportMatch::class);
    }

    public function findMatchesWithPlayerDetailsByTournament(int $tournamentId)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.matchDate', 's.status', 's.scorePlayer1', 's.scorePlayer2', 'p1.userName AS player1Name', 'p2.userName AS player2Name')
            ->innerJoin('s.player1', 'p1', Join::WITH, 's.player1 = p1.id')
            ->innerJoin('s.player2', 'p2', Join::WITH, 's.player2 = p2.id')
            ->where('s.tournament = :tournamentId')
            ->setParameter('tournamentId', $tournamentId)
            ->getQuery()
            ->getResult();
    }
    public function findMatchWithDetails(int $tournamentId, int $matchId)
    {
        return $this->createQueryBuilder('s')
            ->select('s.id', 's.matchDate', 's.status', 's.scorePlayer1', 's.scorePlayer2', 'p1.userName AS player1Name', 'p2.userName AS player2Name')
            ->innerJoin('s.player1', 'p1', Join::WITH, 's.player1 = p1.id')
            ->innerJoin('s.player2', 'p2', Join::WITH, 's.player2 = p2.id')
            ->where('s.tournament = :tournamentId')
            ->andWhere('s.id = :matchId')
            ->setParameter('tournamentId', $tournamentId)
            ->setParameter('matchId', $matchId)
            ->getQuery()
            ->getOneOrNullResult();  // Get one or return null if not found
    }

}
