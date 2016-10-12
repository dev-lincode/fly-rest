<?php
namespace TagInterativa\RestApi\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TokenController extends Controller
{
    /**
     * Oauth2 Token.
     * Method: GET, url: /oauth/v2/token
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Oauth2 Token",
     *   filters={
     *      {"name"="client_id", "dataType"="string"},
     *      {"name"="client_secret", "dataType"="string"},
     *      {"name"="grant_type", "dataType"="string", "pattern"="client_credentials"}
     *   },
     *   statusCodes = {
     *      200 = "Sucesso",
     *      404 = "Página não encontrada"
     *   }
     * )
     *
     */
    public function tokenAction(Request $request)
    {
        $token = $this->container->get('fos_oauth_server.controller.token');
        return $token->tokenAction($request);
    }
}