<?php
namespace App\Controller;

use App\Entity\SportMatch;
use App\Entity\User;
use App\Repository\SportMatchRepository;
use App\Repository\TournamentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SportMatchController extends AbstractController
{
    private SportMatchRepository $sportMatchRepository;
    private TournamentRepository $tournamentRepository;

    public function __construct(SportMatchRepository $sportMatchRepository, TournamentRepository $tournamentRepository)
    {
        $this->sportMatchRepository = $sportMatchRepository;
        $this->tournamentRepository = $tournamentRepository;
    }

    #[Route('/api/tournaments/{id}/sport-matchs', methods: ['GET'])]
    public function getMatchDetails(int $id, SportMatchRepository $repository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $this->denyAccessUnlessGranted('ROLE_USER');
        $matchDetails = $repository->findMatchesWithPlayerDetailsByTournament($id);
        if (!$matchDetails) {
            return $this->json(['message' => 'No matches found for this tournament'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($matchDetails);
    }

    #[Route('/api/tournaments/{id}/sport-matchs', methods: ['POST'])]
    public function createMatch(Request $request, int $id,ManagerRegistry $doctrine): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $tournament = $this->tournamentRepository->find($id);
        if (!$tournament) {
            return $this->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $player1 = $doctrine->getRepository(User::class)->find($data['player1Id']);
        $player2 = $doctrine->getRepository(User::class)->find($data['player2Id']);

        if (!$player1 || !$player2) {
            return $this->json(['message' => 'One or both players not found'], Response::HTTP_BAD_REQUEST);
        }

        $match = new SportMatch();
        $match->setTournament($tournament);
        $match->setPlayer1($player1);
        $match->setPlayer2($player2);
        $match->setStatus("Pending");
        $match->setMatchDate(new \DateTime()); // Example: assume immediate start

        $entityManager = $doctrine->getManager();
        $entityManager->persist($match);
        $entityManager->flush();

        return $this->json(['message' => 'Match created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/api/tournaments/{idTournament}/sport-matchs/{idSportMatch}', methods: ['GET'])]
    public function getMatchDetail(int $idTournament, int $idSportMatch, SportMatchRepository $repository): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $matchDetail = $repository->findMatchWithDetails($idTournament, $idSportMatch);
        if (!$matchDetail) {
            return $this->json(['message' => 'Match not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($matchDetail);
    }

    #[Route('/api/tournaments/{idTournament}/sport-matchs/{idSportMatch}', methods: ['PUT'])]
    public function updateMatch(Request $request, int $idTournament, int $idSportMatch,ManagerRegistry $doctrine): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        if (!$this->tournamentRepository->find($idTournament)) {
            return $this->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        $match = $this->sportMatchRepository->findOneBy(['id' => $idSportMatch, 'tournament' => $idTournament]);
        if (!$match) {
            return $this->json(['message' => 'Match not found'], Response::HTTP_NOT_FOUND);
        }

        // Ensure that only involved players or an admin can update the match
        if (!$this->isGranted('ROLE_ADMIN') &&
            $this->getUser()->getId() !== $match->getPlayer1()->getId() &&
            $this->getUser()->getId() !== $match->getPlayer2()->getId()) {
            throw new AccessDeniedException('You are not allowed to update this match.');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['scorePlayer1'])) {
            $match->setScorePlayer1($data['scorePlayer1']);

        }
        if (isset($data['scorePlayer2'])) {
            $match->setScorePlayer2($data['scorePlayer2']);
        }
        $match->updateStatus();
        $entityManager = $doctrine->getManager();
        $entityManager->persist($match);
        $entityManager->flush();
        return $this->json(['message' => 'Match updated successfully'], Response::HTTP_OK);
    }

    #[Route('/api/tournaments/{idTournament}/sport-matchs/{idSportMatch}', methods: ['DELETE'])]
    public function deleteMatch(int $idTournament, int $idSportMatch,ManagerRegistry $doctrine): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        if (!$this->tournamentRepository->find($idTournament)) {
            return $this->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        $match = $this->sportMatchRepository->findOneBy(['id' => $idSportMatch, 'tournament' => $idTournament]);
        if (!$match) {
            return $this->json(['message' => 'Match not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($match);
        $entityManager->flush();

        return $this->json(['message' => 'Match deleted successfully'], Response::HTTP_OK);
    }
}
