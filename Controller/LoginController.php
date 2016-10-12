<?php

namespace TagInterativa\RestApi\Bundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use TagInterativa\CMSBundle\Controller\TemplateController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Member controller.
 *
 * @Route("/api/v1/login")
 */
class LoginController extends FOSRestController
{
    /**
     * User Login.
     *
     * REST User Login.
     * Method: POST, url: /api/v1/login/
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "User Login",
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *     {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="Username"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password"}
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\User",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     * @Route("/", name="user_login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {

        if ($request->getMethod() == "POST") {
            $loginService = $this->container->get('taginterativa.user.login_service');
            $repository = $this->container->getParameter('oauth2_entity_repository');
            return $loginService->login($request, $repository);
        }

        return new JsonResponse(['error' => ['code'=> JsonResponse::HTTP_BAD_REQUEST,
            'message' => 'Method not permited']], JsonResponse::HTTP_BAD_REQUEST);

    }
}