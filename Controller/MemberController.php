<?php

namespace TagInterativa\RestApi\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use TagInterativa\CMSBundle\Controller\TemplateController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
 * Member controller.
 *
 * @Route("/cms/member")
 */
class MemberController extends TemplateController
{
	public function __construct() {
		$this->singularTitle = "Membro";
		$this->pluralTitle   = "Membros";

		$this->entityClass      = "TagInterativa\RestApi\Bundle\Entity\Member";
		$this->entityForm       = "TagInterativa\RestApi\Bundle\Form\MemberType";
		$this->entityRepository = "RestApiBundle:Member";

		$this->routeBase = "cms_member";

		$fields = '{
			"id": {
				"label" : "Id",
				"col" : 1
			},
			"name": {
				"label" : "Name",
				"col" : 4
			},
			"email": {
				"label" : "Email",
				"col" : 5
			}
		}';
		$this->listFields = json_decode($fields, true);
		$this->persistences = array("create" => true, "read" => true, "update" => true, "delete" => true);
	}

    /**
     * Lists all Member entities.
     *
     * @Route("/", name="cms_member")
     * @Method("GET")
     */

    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * Generate Report.
     *
     * @Route("/report", name="cms_member_report")
     * @Method("GET")
     */

    public function reportAction(Request $request) {
        return parent::reportAction($request);
    }

    /**
     * Displays a form to create a new Member entity.
     *
     * @Route("/new", name="cms_member_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        return parent::newAction($request);
    }

    /**
     * Finds and displays a Member.
     *
     * @Route("/{id}/show", name="cms_member_show")
     * @Method("GET")
     */
    public function showAction(Request $request, $id) {
        return parent::showAction($request, $id);
    }

    /**
     * Displays a form to edit an existing Member entity.
     *
     * @Route("/{id}/edit", name="cms_member_edit")
     * @Method({"GET", "PUT"})
     */
    public function editAction(Request $request, $id) {
        return parent::editAction($request, $id);
    }

    /**
     * Deletes a Member entity.
     *
     * @Route("/{id}/delete", name="cms_member_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id) {
        return parent::deleteAction($request, $id);
    }

    /**
     * Submit a Member entity list.
     *
     * @Route("/", name="cms_member_submit_list")
     * @Method({"POST"})
     */
    public function submitListAction(Request $request) {
		return parent::submitListAction($request);
	}

    protected function beforePersist($entity, $form, $method) {
        if($form->getData()->getPassword()) {
            $entity->setPassword($this->encondePassword($entity, $form->getData()->getPassword()));
        }
    }

    private function encondePassword($user, $plainPassword){
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

}
