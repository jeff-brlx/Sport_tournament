<?php

namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Firebase\JWT\JWT;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }
    #[Route('/welcome', "test_connectivity")]
    public function testConnectivity(): Response
    {
        return new Response('Server is up and running!', Response::HTTP_OK);
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {
        $data = json_decode($request->getContent(), true);
        $repository = $doctrine->getRepository(User::class);

        // Vérifie si l'email est déjà utilisé
        if ($repository->findOneBy(['emailAddress' => $data['emailAddress']])) {
            return new Response('Email already used', Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUserName($data['userName']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmailAddress($data['emailAddress']);
        $user->setStatus('Active');
        // Vérifie si c'est le premier utilisateur à enregistrer
        $countUsers = $repository->count([]);
        if ($countUsers === 0) {
            // Le premier utilisateur est administrateur
            $user->setRoles(['ROLE_ADMIN']);
        } else {
            // Les autres utilisateurs sont normaux
            $user->setRoles(['ROLE_USER']);
        }


        // Hashing the password
        $hashedPassword = $hasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('User registered successfully', Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'user_login', methods: ['POST'])]
    public function login(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {
    $data = json_decode($request->getContent(), true);
    $repository = $doctrine->getRepository(User::class);
    $user = $repository->findOneBy(['emailAddress' => $data['emailAddress']]);

    if (!$user) {
        return new Response('User not found', Response::HTTP_UNAUTHORIZED);
    }


    // Vérifie si le mot de passe est correct
    if (!$hasher->isPasswordValid($user, $data['password'])) {
        return new Response('Invalid credentials', Response::HTTP_UNAUTHORIZED);
    }

    // Création du JWT
    $payload = [
        "user" => $user->getUserName(),
        "roles"=>$user->getRoles(),
        /*"exp"  => (new \DateTime())->modify("+3 minute")->getTimestamp(),*/
        "exp" => time() + (500 * 60) //expire dans 50minutes

    ];

        $jwt = JWT::encode($payload, $this->getParameter('jwt_secret_key'), 'HS256');

    return new Response(json_encode(['token' => $jwt]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    #[Route('/api/players', methods: ['GET'])]
    public function getAllPlayers(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Access denied');
        }

        $players = $this->userRepository->findAll();
        return $this->json($players);
    }
    #[Route('/api/players/{id}', methods: ['GET'])]
    public function getPlayer(User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException('Access denied');
        }

        return $this->json($user);
    }
    #[Route('/api/players/{id}', methods: ['POST'])]
    public function updatePlayer(Request $request, ManagerRegistry $doctrine, User $user,UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException('Access denied');
        }
        // Récupérer les données envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }
        if (isset($data['emailAddress'])) {
            $user->setEmailAddress($data['emailAddress']);
        }
        if (isset($data['password'])) {
            // Hasher le nouveau mot de passe
            $hashedPassword = $hasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);
        }
       // Assurez-vous que Doctrine suit bien l'entité
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user); // Ajoutez cette ligne
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur mis à jour avec succès'], Response::HTTP_OK);
    }
    #[Route('/api/players/{id}', methods: ['DELETE'])]
    public function deletePlayer(User $user,ManagerRegistry $doctrine): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedException('Access denied');
        }

        $entityManager = $doctrine->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['status' => 'User deleted successfully']);
    }
}
