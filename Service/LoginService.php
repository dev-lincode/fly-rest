<?php

namespace Lincode\RestApi\Bundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Repository\RepositoryFactory;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LoginService
{
    private $em;
    private $encoderService;
    private $serializer;

    public function __construct(EntityManager $em, $encoderService, $serializer)
    {
        $this->em = $em;
        $this->encoderService = $encoderService;
        $this->serializer = $serializer;
    }

    public function login(Request $request, $repository){

        if (!$request->headers->contains("Content-Type", "application/json")){
            return new JsonResponse(['error' => ['code'=> JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Content-Type must be application/json']], JsonResponse::HTTP_BAD_REQUEST);
        }

        $username = $request->request->get('username',NULL);
        $password = $request->request->get('password',NULL);

        if (!isset($username) || !isset($password)){
            return new JsonResponse(['error' => ['code'=> JsonResponse::HTTP_BAD_REQUEST,
                'message' => 'Preencha os campos de usuario e senha']], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository($repository)->findOneBy(array('email' => $username, 'isActive' => 1));

        if (!$user){
            return new JsonResponse(['error' => ['code'=> JsonResponse::HTTP_UNAUTHORIZED,
                'message' => 'Usuário não encontrado']], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $encoder = $this->encoderService->getEncoder($user);
        $encoded_pass = $encoder->encodePassword($password, $user->getSalt());

        if ($encoded_pass != $user->getPassword()) {
            return new JsonResponse(['error' => ['code'=> JsonResponse::HTTP_UNAUTHORIZED,
                'message' => 'Usuário e/ou Senha inválido']], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$user->getApiKey()){
            $user->generateApiKey();
            $this->em->persist($user);
            $this->em->flush();
        }

        $userContent = $this->serializer->serialize($user, 'json');

        return new JsonResponse(['result' => json_decode($userContent, true), 'has_more'=> false]);
    }
}