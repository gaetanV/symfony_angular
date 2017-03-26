<?php

namespace Cms\TaxonomyBundle\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cms\TaxonomyBundle\Entity\Category;
use Cms\TaxonomyBundle\Form\CategoryCreate;
use Cms\TaxonomyBundle\Form\CategoryUpdateParent;
use Cms\TaxonomyBundle\Form\CategoryUpdate;

class CategoryController extends Controller {

    Const TWIG_CATEGORY_LIST = "CmsTaxonomyBundle:Category:list.html.twig";
    Const TWIG_CATEGORY_UPDATE = "CmsTaxonomyBundle:Category:update.html.twig";
    Const ERROR_CATEGORY_BD = "error.BD";

    public function getOneAction(Request $request , $id) {
         /*$langage=false;
        if($request->query->get('langage')){
            $request->setLocale($request->query->get('langage')) ;
            $langage=true;
        }*/
         $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Category::REPOSITORY)->find($id);
        
         if (!$entity) {
            throw $this->createNotFoundException("Unable to find Category entity $id.");
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response->setContent($this->get('jms_serializer')->serialize($entity,"json", \JMS\Serializer\SerializationContext::create()->setGroups( array('Default', 'single') )  ) );
    }
    

   public function getAllAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Category::REPOSITORY)->findByParent(NULL);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $response->setContent($this->get('jms_serializer')->serialize($entities,"json", \JMS\Serializer\SerializationContext::create()->setGroups( array('Default', 'children') )  ) );   
    }

    /*     * ************* ACTION ******************* */

    public function addAction(Request $request) {

        $entity = new Category();
        $form = $this->createForm(new CategoryCreate(), $entity);
        
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
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_CATEGORY_BD);
            }
        } else {
            $mess->error = $this->get('Angular')->getErrorMessages($form);
        }
        $response = new JsonResponse($mess);
        return $response;
    }

    public function updateAction(Request $request, $id) {
        $mess = new \stdClass;
        $mess->valid = false;
       
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Category::REPOSITORY)->find($id);

        $mess->error = array();
       $jsonData = json_decode($request->getContent(), true);
       
        if ($entity) {
              
              if (array_key_exists("CategoryUpdateParent", $jsonData)) {
                $IsformsValid = false;
              
                
                $form = $this->createForm(new CategoryUpdateParent(), $entity);
        
                $form->submit($this->get('Angular')->jsonToRequest($request));
                if ($form->isValid()) {
                      $IsformsValid = true;
                } else {
                     
                    $mess->error["UserUpdate"] = $this->get('Angular')->getErrorMessages($form);
                }
            }
            
            if (array_key_exists("CategoryUpdate", $jsonData)) {
                    $IsformsValid = false;

                    $form = $this->createForm(new CategoryUpdate(), $entity);
                    $form->submit($this->get('Angular')->jsonToRequest($request));
                    if ($form->isValid()) {
                          $IsformsValid = true;
                    } else {
                       
                        $mess->error["UserUpdate"] = $this->get('Angular')->getErrorMessages($form);
                    }
                }

            if ($IsformsValid) {
                 try {
                     $em->flush();
                     $mess->valid = true;
                 } catch (\Exception $e) {
                       $mess->private = $e->getMessage();
                     $mess->error[0] = $this->get('translator')->trans(self::ERROR_USER_BD);
                 }
             }
        }

        $response = new JsonResponse($mess);
        return $response;
    }

    /**     * ************ TEMPLATE ******************* */
    
    public function updateTwigAction(Request $request) {
        $form = $this->createForm(new CategoryUpdate(), new Category());
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_CATEGORY_UPDATE, array(  'form' => $formView  ));
    }
    
    public function listTwigAction(Request $request) {
        $form = $this->createForm(new CategoryCreate(), new Category());
        $formView = $this->get('Angular')->createFormView($form);
        return $this->render(self::TWIG_CATEGORY_LIST, array(  'form' => $formView ));
    }

}

?>