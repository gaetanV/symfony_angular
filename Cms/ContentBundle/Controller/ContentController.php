<?php

namespace Cms\ContentBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/* ContentBundle */
use Cms\ContentBundle\Entity\ContentRich;
use Cms\ContentBundle\Entity\Content;
use Cms\ContentBundle\Entity\Translation;
use Cms\ContentBundle\Entity\TranslationRich;

class ContentController extends Controller {
    /*     * ************* ACTION ******************* */

    Const TRANSLATION_DEFAULT = "__default__";


    private function getContentType($entity) {
        if ($entity instanceof Content)
            return "Content";
        if ($entity instanceof ContentRich)
            return "ContentRich";
        throw new \Exception("Function normalize Content require a entity of type ContentRich or Content , type $className given");
    }

    private function langageKey($langage) {
        $langageList = Translation::$langage_list;
        return $key = \array_search((string) $langage, $langageList);
    }

    public function trans($entity) {
        $contentType = $this->getContentType($entity);
        $em = $this->getDoctrine()->getManager();

        $langage = $this->get('request')->getLocale();

        if ($contentType === "Content")
            $type = Translation::REPOSITORY;
        if ($contentType === "ContentRich")
            $type = TranslationRich::REPOSITORY;

        $query = $em->createQuery("SELECT p.value FROM $type p WHERE p.langage = :langage AND p.content= :content  ")
                ->setParameters(array('langage' => $langage, 'content' => $entity->getId()));
        $result = $query->getResult();
        if ($result)
            return $result[0]["value"];

        /* @Constraint  langage is in langage list */
        if ($this->langageKey($langage) !== false) {

            /* @BD  create  langage in content */
            if ($contentType === "Content")
                $translation = new Translation();
            if ($contentType === "ContentRich")
                $translation = new TranslationRich();

            $translation->setLangage($langage);
            $translation->setValue(self::TRANSLATION_DEFAULT);
            $entity->addTranslation($translation);
            $em->flush();
            return self::TRANSLATION_DEFAULT;
        } else {
            return $this->get('translator')->trans("error.translation");
        }
    }

    public function normalizeContent($entity) {
        $contentType = $this->getContentType($entity);
        $translations = $entity->getTranslations();
        $translations->initialize();
        $object = Array();

        $langageList = Translation::$langage_list;

        /* @Constraint content langages is  in langage list */
        foreach ($translations as $index => $translate) {
            $key = $this->langageKey($translate->getLangage());

            if ($key !== false) {
                $translation = $this->normalizeTranslation($translate);
                // $translation->key = $index;
                $object[$index] = $translation;

                $langageList[$key] = false;
            }
        }

        /* @Constraint content langages is not in langage list */
        $em = $this->getDoctrine()->getManager();
        if (count($langageList) > 0) {
            foreach ($langageList as $key => $value) {
                if ($value) {
                    switch ($contentType) {
                        case "Content":
                            $translation = new Translation();

                            break;
                        case "ContentRich":
                            $translation = new TranslationRich();
                            break;
                    }
                    $translation->setLangage($value);
                    $translation->setValue(self::TRANSLATION_DEFAULT);
                    $translation->setContent($entity);
                    $object[] = $this->normalizeTranslation($translation);
                    $entity->addTranslation($translation);
                }
            }
            $em->flush();
        }

        $translations = new \stdClass();
        $translations->translations = $object;
        return $translations;
    }

}
