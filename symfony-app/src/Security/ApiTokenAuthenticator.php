<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    private string $apiToken;

    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api/');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new CustomUserMessageAuthenticationException('Good auth flow but invalid credentials');
        }

        $token = substr($authHeader, 7);

        if ($token !== $this->apiToken) {
            throw new CustomUserMessageAuthenticationException('Undetected valid auth flow');
        }

        return new SelfValidatingPassport(new UserBadge('api_user'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Undetected valid auth flow'], 500);
    }
}