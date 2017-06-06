<?php

namespace Lincode\RestApi\Bundle\Controller;

use Lincode\RestApi\Bundle\Errors\FormErrorException;
use Lincode\RestApi\Bundle\Entity\Member;
use Lincode\RestApi\Bundle\Form\MemberType;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Voryx\RESTGeneratorBundle\Controller\VoryxController;

/**
 * Member controller.
 * @RouteResource("Member")
 */
class MemberRESTController extends VoryxController
{
    /**
     * Get a Member entity
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "GET Member",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\Member",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     */

    public function getAction(Member $entity)
    {
        try {
            return ['result' => $entity, 'has_more' => false];
        } catch (\Exception $e) {

            return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get all Member entities.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "GET Members",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\Member",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     * @QueryParam(name="offset", requirements="\d+", nullable=true, description="Deslocamento de inicio da lista de itens.")
     * @QueryParam(name="limit", requirements="\d+", default="20", description="Quantos resultados ira retornar.")
     * @QueryParam(name="order_by", nullable=true, array=true, description="Ordena por campo. Deve ser um array ie. &order_by[nome]=ASC&order_by[descricao]=DESC")
     * @QueryParam(name="filters", nullable=true, array=true, description="Filtra por campos. Deve ser um array ie. &filters[id]=3")
     *
     */

    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        try {
            $offset = $paramFetcher->get('offset');
            $limit = $paramFetcher->get('limit');
            $order_by = $paramFetcher->get('order_by');
            $filters = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('RestApiBundle:Member')->findBy($filters, $order_by, $limit, $offset);
            if ($entities) {

                $entitiesNextPage = $em->getRepository('RestApiBundle:Member')->findBy($filters, $order_by, $limit, $offset + $limit);
                $has_next = false;
                if ($entitiesNextPage) {
                    $has_next = true;
                }
                return ['result' => $entities, 'has_more' => $has_next];
            }
            return FOSView::create(['error' => ['code'=> Codes::HTTP_NO_CONTENT,
                'message' => 'Not Found']], Codes::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a Member entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "POST Member",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   input="TagInterativa\RestApi\Bundle\Form\MemberType",
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\Member",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     */

    public function postAction(Request $request)
    {
        $entity = new Member();
        $form = $this->createForm(new MemberType(), $entity, array("method" => $request->getMethod()));
        $this->removeExtraFields($request, $form);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
            $entity->setPassword($encoder->encodePassword($form['password']->getData(), $entity->getSalt()));
            $entity->setIsActive(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return ['result' => $entity, 'has_more' => false];
        }

        $errorFiltered = new FormErrorException($form);

        return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $errorFiltered->getFormErrors()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Update a Member entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "PUT Member",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   input="TagInterativa\RestApi\Bundle\Form\MemberType",
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\Member",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     */

    public function putAction(Request $request, Member $entity)
    {
        try {
            $oldPass = $entity->getPassword();
            $em = $this->getDoctrine()->getManager();
            $request->setMethod('PATCH'); //Treat all PUTs as PATCH
            $form = $this->createForm(new MemberType(), $entity, array("method" => $request->getMethod()));

            $this->removeExtraFields($request, $form);
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($form->get("password") && $form->get("password")->getData() != $oldPass) {
                    $encoder = $this->container->get('security.encoder_factory')->getEncoder($entity);
                    $entity->setPassword($encoder->encodePassword($form['password']->getData(), $entity->getSalt()));
                }

                $em->flush();

                return ['result' => $entity, 'has_more' => false];
            }

            $errorFiltered = new FormErrorException($form);
            return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $errorFiltered->getFormErrors()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Partial Update to a Member entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "PATCH Member",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   input="TagInterativa\RestApi\Bundle\Form\MemberType",
     *   statusCodes = {
     *      404 = "Página não encontrada"
     *   },
     *   responseMap={
     *      200 = {
     *          "class"="TagInterativa\RestApi\Bundle\Entity\Member",
     *          "parsers"={"Nelmio\ApiDocBundle\Parser\JmsMetadataParser"}
     *     }
     *   }
     * )
     *
     *
     */

    public function patchAction(Request $request, Member $entity)
    {
        return $this->putAction($request, $entity);
    }

    /**
     * Delete a Member entity.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "DELETE Member",
     *   headers={
     *      {
     *          "name"="Authorization",
     *          "description"="Authorization key"
     *      },
     *      {
     *          "name"="Content-Type",
     *          "description"="application/json"
     *      }
     *   },
     *   statusCodes = {
     *      200 = "Sucesso",
     *      404 = "Página não encontrada"
     *   }
     * )
     *
     */

    public function deleteAction(Request $request, Member $entity)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            return ['result' => 'success', 'has_more' => false];
        } catch (\Exception $e) {
            return FOSView::create(['error' => ['code'=> Codes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()]], Codes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
