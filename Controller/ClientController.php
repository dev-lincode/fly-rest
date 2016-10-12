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
	public function __construct() {
		$this->singularTitle = "Cliente API";
		$this->pluralTitle   = "Clientes API";

		$this->entityClass      = "Lincode\RestApi\Bundle\Entity\Client";
		$this->entityForm       = "Lincode\RestApi\Bundle\Form\ClientType";
		$this->entityRepository = "RestApiBundle:Client";

		$this->routeBase = "cms_client";

		$fields = '{
			"id": {
				"label" : "Id",
				"col" : 1
			},
			"name": {
				"label" : "Name",
				"col" : 4
			},
			"publicId": {
				"label" : "Client ID",
				"col" : 4
			},
			"secret": {
				"label" : "Secret ID",
				"col" : 4
			}
		}';
		$this->listFields = json_decode($fields, true);
		$this->persistences = array("create" => true, "read" => false, "update" => false, "delete" => true);
	}

    /**
     * Lists all Member entities.
     *
     * @Route("/", name="cms_client")
     * @Method("GET")
     */

    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * Displays a form to create a new Member entity.
     *
     * @Route("/new", name="cms_client_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {

        $entity = new $this->entityClass;

        $this->checkPermissions();
        $form = $this->getNewForm($entity);
        $form = $this->addSubmitButtonForm($form);
        $form->handleRequest($request);

        if($form->isValid()) {
            if(EntityFormService::validadeForm($form, $this->entityRepository, $entity)) {
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

        return $this->renderNew('CMSBundle:Template:new.html.twig', array(
            'title'             => $this->singularTitle,
            'persistences'      => $this->persistences,
            'routeBase'         => $this->routeBase,
            'form'              => $form->createView(),
            'includes'          => $this->includes,
            'viewDefaultParams' => $this->viewDefaultParams,
            'formThemes'        => $this->formThemes,
        ));

    }

    /**
     * Deletes a Member entity.
     *
     * @Route("/{id}/delete", name="cms_client_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id) {
        return parent::deleteAction($request, $id);
    }

    /**
     * Submit a Member entity list.
     *
     * @Route("/", name="cms_client_submit_list")
     * @Method({"POST"})
     */
    public function submitListAction(Request $request) {
		return parent::submitListAction($request);
	}

}
