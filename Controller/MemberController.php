<?php

namespace Lincode\RestApi\Bundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Lincode\Fly\Bundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
 * Member controller.
 *
 * @Route("/cms/member")
 */
class MemberController extends BaseController
{
    protected $configs = [
        'prefix_route' => 'cms_member',
        'singular_name' => 'Membro',
        'plural_name' => 'Membros',
        'entity' => 'RestApiBundle:Member',
        'entity_class' => 'Lincode\RestApi\Bundle\Entity\Member',
        'entity_form' => 'Lincode\RestApi\Bundle\Form\MemberType',
        'title_field' => 'name',
        'list_fields' => ['name' => 'Nome', 'email' => 'Email'],
        'show_fields' => ['name' => 'Nome', 'email' => 'Email']
    ];

    /**
     * Lists all Member entities.
     *
     * @Route("/", name="cms_member")
     * @Method("GET")
     */

    public function indexAction() {
        return parent::indexAction();
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
    public function showAction($id) {
        return parent::showAction($id);
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
    public function deleteAction($id) {
        return parent::deleteAction($id);
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
        if ($form->getData()->getPassword() != '') {
            $entity->setPassword($this->encondePassword($entity, $form->getData()->getPassword()));
        }
    }

    private function encondePassword($user, $plainPassword){
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

}
