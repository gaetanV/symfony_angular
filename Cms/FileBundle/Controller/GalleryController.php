<?php
namespace Cms\FileBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Cms\FileBundle\Entity\Gallery;
use Cms\FileBundle\Form\GalleryCreate;
use Cms\FileBundle\Form\GalleryUpdate;
use Cms\FileBundle\Form\FileCreate;
use Cms\FileBundle\Entity\File;

class GalleryController extends Controller {
    
    Const TWIG_GALLERY_UPDATE = "CmsFileBundle:Gallery:update.html.twig";
    Const TWIG_GALLERY_LIST = "CmsFileBundle:Gallery:list.html.twig";
    Const ERROR_GALLERY_BD = "error.BD";

    public function getAllAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository(Gallery::REPOSITORY)->findAll();

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entities,"json") );
    }


    public function getOneAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Gallery::REPOSITORY)->find($id);
           if (!$entity) {
            throw $this->createNotFoundException('Unable to find Gallery entity.');
        }
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
         return $response->setContent($this->get('jms_serializer')->serialize($entity,"json") );
  
    }
      /*     * ************* ACTION ******************* */

    public function addAction(Request $request) {

        $entity = new Gallery();
        $form = $this->createForm(new GalleryCreate(), $entity);
        $form->handleRequest($this->get('Angular')->jsonToRequest($request));


        $mess = new \stdClass;
        $mess->valid = false;

        if ($form->isValid()) {
            try {
                     $em = $this->getDoctrine()->getManager();

                $em->persist($entity);
                $em->flush();
                
         
                  $mess->result=$this->normalizeGallery($entity);
                
                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->private = $e->getMessage();
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_GALLERY_BD);
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
        $mess->error = array();

        $jsonData = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Gallery::REPOSITORY)->find($id);
        $IsformsValid = false;

        if ($entity) {
            $formsIsValid = false;
            //Check law
            if (array_key_exists("GalleryUpdate", $jsonData)) {
                $IsformsValid = false;

                $form = $this->createForm(new GalleryUpdate(), $entity);
                $form->submit($this->get('Angular')->jsonToRequest($request));
                if ($form->isValid()) {
                      $IsformsValid = true;
                } else {
                    $mess->error["UserUpdate"] = $this->get('Angular')->getErrorMessages($form);
                }
            }
            //Check law
            if (array_key_exists("FileCreate", $jsonData)) {
                  $file = new File();
                $IsformsValid = false;
                
                $form = $this->createForm(new FileCreate(), $file);
                $form->submit($this->get('Angular')->jsonToRequest($request));
                if ($form->isValid()) {
                   
                    $em->persist($file);
                    $em->flush();
                    
                    $IsformsValid = true;
                    $entity->addFile($file);
                    
                     $serializer = $this->get('serializer');
                     $mess->result=$serializer->normalize($file);
                    
                } else {
                    $mess->error["UserUpdateMdp"] = $this->get('Angular')->getErrorMessages($form);
                }
            }

            if ($IsformsValid) {
                try {
                    $em->flush();
                    $mess->valid = true;
                } catch (\Exception $e) {
                    $mess->error[0] = $this->get('translator')->trans(self::ERROR_USER_BD);
                }
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }
    
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Gallery::REPOSITORY)->find($id);
        $mess = new \stdClass;
        $mess->valid = false;

        if ($entity) {
            try {
                $em->remove($entity);
                $em->flush();
                $mess->valid = true;
            } catch (\Exception $e) {
                $mess->error[0] = $this->get('translator')->trans(self::ERROR_GALLERY_BD);
            }
        }

        $response = new JsonResponse($mess);
        return $response;
    }
    
    
    public function addTwigAction(Request $request) {
        $entity = new Gallery();
        $form = $this->createForm(new GalleryCreate(), $entity);
        $formView = $this->get('Angular')->createFormView($form);
// $formView =$form->createView();
        
        return $this->render(self::TWIG_GALLERY_LIST, array(
                    'form' => $formView,
        ));
    }
    
    public function updateTwigAction(Request $request) {
            $entity = new Gallery();

            $form = $this->createForm(new GalleryUpdate(), $entity);
  
          $entity = new File();
          $addFileForm = $this->createForm(new FileCreate(), $entity);

        

        return $this->render(self::TWIG_GALLERY_UPDATE, array(
                    'form' => $this->get('Angular')->createFormView($form) ,
                  //    'form' => $form->createView() ,
                    'addFileForm' => $this->get('Angular')->createFormView($addFileForm)
        ));
    }
    
    
}

?>