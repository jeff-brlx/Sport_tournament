<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use UnexpectedValueException;

class JwtAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private string $secretKey;
    private LoggerInterface $logger;

    public function __construct(UserRepository $userRepository, string $secretKey, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->secretKey = $secretKey;
        $this->logger = $logger;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): \Symfony\Component\Security\Http\Authenticator\Passport\Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            throw new AuthenticationException('Authorization header is missing.');
        }

        if (!str_starts_with($authorizationHeader, 'Bearer ')) {
            throw new AuthenticationException('Bearer token not found in Authorization header.');
        }

        $token = substr($authorizationHeader, 7);

        try {
            $credentials = JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (ExpiredException $e) {
            throw new AuthenticationException('Token has expired: ' . $e->getMessage());
        } catch (SignatureInvalidException $e) {
            throw new AuthenticationException('Invalid token signature: ' . $e->getMessage());
        } catch (BeforeValidException $e) {
            throw new AuthenticationException('Token is not yet valid: ' . $e->getMessage());
        } catch (UnexpectedValueException $e) {
            throw new AuthenticationException('Token cannot be parsed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new AuthenticationException('Token validation failed: ' . $e->getMessage());
        }

        $userName = $credentials->user ?? null;
        if (!$userName) {
            throw new AuthenticationException('Token does not contain necessary information');
        }

        $user = $this->userRepository->findOneByUserName($userName);
        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        return new SelfValidatingPassport(new UserBadge($userName));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Allow the request to continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->logger->error('Authentication failure', [
            'exception_type' => get_class($exception),
            'exception_message' => $exception->getMessage(),
            'request_uri' => $request->getRequestUri()
        ]);

        $data = [
            'message' => $exception->getMessageKey(),
            'error' => $exception->getMessage()
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
