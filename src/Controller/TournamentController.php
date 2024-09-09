<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\TournamentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class TournamentController extends AbstractController
{
    private TournamentRepository $tournamentRepository;

    public function __construct(TournamentRepository $tournamentRepository)
    {
        $this->tournamentRepository = $tournamentRepository;
    }
    #[Route('/api/tournaments', methods: ['POST'])]
    public function createTournament(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }

        $data = json_decode($request->getContent(), true);
        $repository = $doctrine->getRepository(Tournament::class);
        // Vérifie si le nom  est déjà utilisé
        if ($repository->findOneBy(['tournamentName' => $data['tournamentName']])) {
            return new Response('tournament already exist', Response::HTTP_BAD_REQUEST);
        }
        $tournament = new Tournament();

        $entityManager = $doctrine->getManager();

        // Charger l'organisateur à partir de l'ID
        $organizer = $entityManager->getRepository(User::class)->find($data['organizer']);
        if (!$organizer) {
            return $this->json(['message' => 'Organisateur non trouvé'], Response::HTTP_BAD_REQUEST);
        }
        $tournament->setOrganizer($organizer);

        // Charger le gagnant à partir de l'ID, si spécifié
        if (isset($data['winner'])) {
            $winner = $entityManager->getRepository(User::class)->find($data['winner']);
            if (!$winner) {
                return $this->json(['message' => 'Gagnant non trouvé'], Response::HTTP_BAD_REQUEST);
            }
            $tournament->setWinner($winner);
        }

        // initialisation d'autres attributs du tournoi
        $tournament->setTournamentName($data['tournamentName']);
        $tournament->setStartDate(new \DateTime($data['startDate']));
        $tournament->setEndDate(new \DateTime($data['endDate']));
        $tournament->setDescription($data['description']);
        $tournament->setSport($data['sport']);
        $tournament->setMaxParticipants($data['maxParticipants']);
        $tournament->setLocation($data['location']);

        // Calculer le status du tournoi
        $startDate = new \DateTime($data['startDate']);
        $endDate = new \DateTime($data['endDate']);
        $now = new \DateTime();
        if ($startDate > $now) {
            $tournament->setStatus('Upcoming');
        } elseif ($endDate <= $now && $endDate >= $now) {
            $tournament->setStatus('Ongoing');
        } elseif ($endDate < $now) {
            $tournament->setStatus('Completed');
        }

        $entityManager->persist($tournament);
        $entityManager->flush();

        return $this->json(['message' => 'Tournoi créé avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/api/tournaments', methods: ['GET'])]
    public function getAllTournaments(SerializerInterface $serializer): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $tournaments = $this->tournamentRepository->findAll();
        $json = $serializer->serialize($tournaments, 'json', ['groups' => 'full']);
        return new Response($json, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    #[Route('/api/tournaments/{id}', methods: ['GET'])]
    public function getTournament(Tournament $tournament): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Access denied');
        }
        $tournamentData = [
            'id' => $tournament->getId(),
            'tournamentName' => $tournament->getTournamentName(),
            'startDate' => $tournament->getStartDate()->format('Y-m-d'),
            'endDate' => $tournament->getEndDate()->format('Y-m-d'),
            'location' => $tournament->getLocation(),
            'description' => $tournament->getDescription(),
            'maxParticipants' => $tournament->getMaxParticipants(),
            'status' => $tournament->getStatus(),
            'sport' => $tournament->getSport(),
            'organizerUserName' => $tournament->getOrganizer()->getUserName(),
            'winnerUserName' => $tournament->getWinner()?->getUserName(),
            // Ajouter ici toutes les autres propriétés que tu souhaites retourner
            // Exclure le créateur et le gagnant
        ];

        return $this->json($tournamentData);
    }
    #[Route('/api/tournaments/{id}', methods: ['DELETE'])]
    public function deleteTournament(Tournament $tournament, ManagerRegistry $doctrine): Response
    {
        // Vérifie si l'utilisateur actuel possède le rôle ADMIN pour supprimer le tournoi
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Retourne un message d'erreur si l'utilisateur n'a pas la permission
            return $this->json(['message' => 'Access denied.'], Response::HTTP_FORBIDDEN);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($tournament);
        $entityManager->flush();

        // Retourne une réponse pour indiquer que la suppression a été réussie
        return $this->json(['message' => 'Tournament deleted successfully.'], Response::HTTP_OK);
    }






    /*#[Route('/tournament', name: 'app_tournament')]
    public function index(): Response
    {
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }*/



}
