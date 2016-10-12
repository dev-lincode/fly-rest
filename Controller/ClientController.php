<?php

namespace Lincode\RestApi\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Lincode\Fly\Bundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lincode\Fly\Bundle\Service\EntityFormService;

/**
 * Member controller.
 *
 * @Route("/cms/client")
 */
class ClientController extends BaseController
{
    protected $configs = [
        'prefix_route' => 'cms_client',
        'singular_name' => 'Cliente API',
        'plural_name' => 'Clientes API',
        'entity' => 'RestApiBundle:Client',
        'entity_class' => 'Lincode\RestApi\Bundle\Entity\Client',
        'entity_form' => 'Lincode\RestApi\Bundle\Form\ClientType',
        'title_field' => 'name',
        'list_fields' => ['name' => 'Nome', 'publicId' => 'Client ID', 'secret' => 'Secret ID'],
        'show_fields' => ['name' => 'Nome', 'publicId' => 'Client ID', 'secret' => 'Secret ID']
    ];

    /**
     * Lists all Member entities.
     *
     * @Route("/", name="cms_client")
     * @Method("GET")
     */

    public function indexAction() {
        return parent::indexAction();
    }

    /**
     * Displays a form to create a new Member entity.
     *
     * @Route("/new", name="cms_client_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {

        $controllerService = $this->get('fly.controller.service');
        $entity = $this->getNewEntity();

        $urlResponse = $this->generateUrl($this->configs['prefix_route'] . '_new');
        $form = $controllerService->getForm($this->getNewEntityForm(), $entity, $urlResponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formValidateService = $this->get('fly.form.service');
            if ($formValidateService->validadeForm($form, $this->configs['entity'], $entity)) {
                try {
                    $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
                    $client = $clientManager->createClient();
                    $client->setAllowedGrantTypes(['password', 'refresh_token', 'token', 'client_credentials']);
                    $client->setName($form['name']->getData());
                    $clientManager->updateClient($client);

                    $this->get('session')->getFlashBag()->add('title', $this->singularTitle);
                    $this->get('session')->getFlashBag()->add('message', 'Criação de ' . $this->singularTitle . ' efetuada com sucesso.');

                    if(array_key_exists('_cms_new', $request->request->get($form->getName()))) {
                        return $this->redirect($this->generateUrl($this->routeBase . '_new', array_merge($this->viewDefaultParams, $request->query->all())));
                    } else if(array_key_exists('_cms_list', $request->request->get($form->getName()))) {
                        return $this->redirect($this->generateUrl($this->routeBase, array_merge($this->viewDefaultParams, $request->query->all())));
                    } else {
                        return $this->redirect($this->generateUrl($this->routeBase . '_edit', array_merge($this->viewDefaultParams, array('id' => $entity->getId()), $request->query->all())));
                    }
                } catch(\Exception $e) {
                    if($this->container->getParameter('kernel.environment') == 'dev') {
                        throw $e;
                    }
                }
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'configs' => $this->configs,
            'parent' => $this->parent,
            'permissions' => $this->permissions,
            'userPermissions' => $this->userPermissions
        );

    }

    /**
     * Deletes a Member entity.
     *
     * @Route("/{id}/delete", name="cms_client_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {
        return parent::deleteAction($id);
    }


    /**
     * Deletes a Page entity.
     *
     * @Route("/deleteAll", name="cms_client_delete_all")
     * @Method("POST")
     */
    public function deleteAllAction(Request $request)
    {
        return parent::deleteAllAction($request);
    }

}
