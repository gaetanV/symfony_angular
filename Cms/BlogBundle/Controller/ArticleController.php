<?php

namespace Cms\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cms\BlogBundle\Entity\Article;
use Cms\BlogBundle\Form\ArticleCreate;
use Cms\BlogBundle\Form\ArticleUpdate;


class ArticleController extends Controller {
    Const TWIG_ARTICLE_ADD = "CmsBlogBundle:Article:add.html.twig";
    Const TWIG_ARTICLE_LIST = "CmsBlogBundle:Article:list.html.twig";
    Const TWIG_ARTICLE_UPDATE = "CmsBlogBundle:Article:update.html.twig";
    Const ERROR_ARTICLE_BD = "error.BD";

    /*     * ************* ACTION ******************* */

  public function getAllAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Article::REPOSITORY)->findAll();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entities,"json", \JMS\Serializer\SerializationContext::create()->setGroups( array('Default', 'single') )  ) );   
      //   return $response->setContent($this->get('jms_serializer')->serialize($entities,"json") );
    }

    public function getOneAction($id) {
         $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Article::REPOSITORY)->find($id);
           if (!$entity) {
            throw $this->createNotFoundException("Unable to find Article entity ($id).");
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entity,"json") );
    }
  

    /*     * ************* ACTION ******************* */

    public function updateAction(Request $request, $id) {
        $mess = new \stdClass;
        $mess->valid = false;

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Article::REPOSITORY)->find($id);


        $mess->error = array();
        if ($entity) {
            $form = $this->createForm(new ArticleUpdate(), $entity);
            $form->handleRequest($this->get('Angular')->jsonToRequest($request));


            if ($form->isValid()) {
                $mess->valid = true;
            } else {
                $mess->error["ArticleUpdate"] = $this->get('Angular')->getErrorMessages($form);
            }

            if ($mess->valid) {
                try {
                    $em->flush();
                } catch (\Exception $e) {
                    $mess->error[0] = $this->get('translator')->trans(self::ERROR_ARTICLE_BD);
                }
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Article::REPOSITORY)->find($id);
        $mess = new \stdClass;
        $mess->valid = false;

        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_FILE_BD);
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    public function addAction(Request $request) {

        $entity = new Article();
        $form = $this->createForm(new ArticleCreate(), $entity);

        $form->handleRequest($this->get('Angular')->jsonToRequest($request));

        $mess = new \stdClass;
        $mess->valid = false;

        if ($form->isValid()) {

            try {
                $em = $this->getDoctrine()->getManager();

                $em->persist($entity);

                $em->flush();

                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->private = $e->getMessage();
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_ARTICLE_BD);
            }
        } else {
            $mess->error = $this->get('Angular')->getErrorMessages($form);
        }
        $response = new JsonResponse($mess);
        return $response;
    }

    /**     * ************ TEMPLATE ******************* */
    
      public function addTwigAction(Request $request) {
        $form = $this->createForm(new ArticleCreate(), new Article());
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_ARTICLE_ADD, array('form' => $formView));
    }
    
    
    public function updateTwigAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ArticleUpdate(), new Article());
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_ARTICLE_UPDATE, array('form' => $formView));
    }

    public function listTwigAction(Request $request) {
        return $this->render(self::TWIG_ARTICLE_LIST, array());
    }

}

?>