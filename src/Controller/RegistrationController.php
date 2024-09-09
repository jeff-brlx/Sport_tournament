<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Entity\Registration;
use App\Repository\TournamentRepository;
use App\Repository\RegistrationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class RegistrationController extends AbstractController
{
    private TournamentRepository $tournamentRepository;
    private RegistrationRepository $registrationRepository;

    public function __construct(TournamentRepository $tournamentRepository, RegistrationRepository $registrationRepository)
    {
        $this->tournamentRepository = $tournamentRepository;
        $this->registrationRepository = $registrationRepository;
    }

    #[Route('/api/tournaments/{id}/registrations', methods: ['POST'])]
    public function registerForTournament(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $tournament = $this->tournamentRepository->find($id);
        if (!$tournament) {
            return $this->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }
        // Vérifier si l'utilisateur est déjà inscrit à ce tournoi
        $existingRegistration = $this->registrationRepository->findOneBy([
            'player' => $this->getUser(),
            'tournament' => $tournament
        ]);

        if ($existingRegistration) {
            return $this->json(['message' => 'You are already registered for this tournament'], Response::HTTP_BAD_REQUEST);
        }

        $registration = new Registration();
        $registration->setTournament($tournament);
        $registration->setPlayer($this->getUser()); // Assuming getPlayer() retrieves the currently logged-in user
        $registration->setRegistrationDate(new \DateTime());
        $registration->setStatus("Confirmed");
        $entityManager = $doctrine->getManager();
        $entityManager->persist($registration);
        $entityManager->flush();

        return $this->json(['message' => 'Registered successfully'], Response::HTTP_CREATED);
    }
    #[\Symfony\Component\Routing\Attribute\Route('/api/tournaments/{id}/registrations', methods: ['GET'])]
    public function getTournamentRegistrations(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $tournament = $this->tournamentRepository->find($id);
        if (!$tournament) {
            return $this->json(['message' => 'Tournament not found'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer uniquement les noms d'utilisateur des inscrits
        $registrations = $this->registrationRepository->createQueryBuilder('r')
            ->select('u.userName')
            ->innerJoin('r.player', 'u')
            ->where('r.tournament = :tournament')
            ->setParameter('tournament', $tournament)
            ->getQuery()
            ->getResult();

        return $this->json($registrations);
    }
    #[Route('/api/tournaments/{idTournament}/registrations/{idRegistration}', methods: ['DELETE'])]
    public function deleteRegistration(int $idTournament, int $idRegistration, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $registration = $this->registrationRepository->find($idRegistration);
        if (!$registration || $registration->getTournament()->getId() !== $idTournament) {
            return $this->json(['message' => 'Registration not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($registration);
        $entityManager->flush();

        return $this->json(['message' => 'Registration deleted successfully'], Response::HTTP_OK);
    }

}
