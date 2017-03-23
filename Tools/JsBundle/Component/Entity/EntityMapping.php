<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraint;

/**
 * @reference http://symfony.com/doc/current/validation.html
 * @TODO        : finish constraints list ( All , Valid )
 * @TODO        : Recursive collection
 * @depreciated : Validator Groupe 
 */

final class EntityMapping {

    private $meta;
    private $languages;
    private $translator;

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.EntityMapping";
    const ERROR_PROPERTY_NOT_FOUND = 1;
    const ERROR_GROUPE_NOT_FOUND = 2;
    const ERROR_ENTITY_NOT_FOUND = 3;
    const ERROR_CONSTRAINT_NOT_SUPPORTED = 4;
    const ERROR_CONSTRAINT_DEPRECIATED = 5;

    /**
     * @param string $entityAlias
     * @param RecursiveValidator $validator
     * @param DataCollectorTranslator $translator
     * @param array $languages
     * @throws \Exception
     */
    public function __construct(string $entityAlias, RecursiveValidator $validator, DataCollectorTranslator $translator, array $languages) {

        if (!class_exists($entityAlias)) {
            throw new \Exception($this->translateError(SELF::ERROR_ENTITY_NOT_FOUND, array('{{ entity }}' => $entityAlias)));
        }
        $entity = new $entityAlias();
        $this->meta = $validator->getMetadataFor($entity);
        $this->translator = $translator;
        $this->languages = $languages;
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->translator->trans(EntityMapping::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
    }

    /**
     * 
     * @param string $messagePattern
     * @param array $param
     * @return array
     */
    private function getTranslations(string $messagePattern, array $param = array()): array {
        $response = [];
        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);
            $response[$lang] = $this->translator->trans($messagePattern, $param, 'validators');
        }
        return $response;
    }

    /**
     * 
     * @param Constraint $assert
     * @return \stdClass
     * @throws \Exception
     */
    private function parseAssert(Constraint $assert): \stdClass {
        $func = new \ReflectionClass($assert);
        $type = $func->getShortName();
        $response = new \stdClass();
        $response->name = $type;
        switch ($type) {
            case "NotBlank":
            case "Blank":
            case "NotNull":
            case "IsNull":
            case "IsTrue":
            case "IsFalse":
            case "Date":
            case "Time":
            case "Bic":
            case "Currency":
            case "Luhn":
            case "Iban":
            case "UserPassword":
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Type":
                $response->type = $assert->type;
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Email":
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Length":
                $response->min = $assert->min;
                $response->max = $assert->max;
                $response->minMessage = $this->getTranslations($assert->minMessage, array('{{ limit }}' => $assert->min));
                $response->maxMessage = $this->getTranslations($assert->maxMessage, array('{{ limit }}' => $assert->max));
                if ($assert->min == $assert->max) {
                    $response->exactMessage = $this->getTranslations($assert->exactMessage, array('{{ limit }}' => $assert->max));
                }
                break;
            case "Url":
                $response->message = $this->getTranslations($assert->message);
                $response->protocols = $this->getTranslations($assert->protocols);
                break;
            case "Regex":
                $response->message = $this->getTranslations($assert->message);
                $response->htmlPattern = $assert->htmlPattern;
                $response->match = $assert->match;
                break;
            case "Ip":
                $response->version = $assert->version;
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Uuid":
                $response->strict = $assert->strict;
                $response->versions = $assert->versions;
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Range":
                $response->min = $assert->min;
                $response->max = $assert->max;
                $response->minMessage = $this->getTranslations($assert->minMessage, array('{{ limit }}' => $assert->min));
                $response->maxMessage = $this->getTranslations($assert->maxMessage, array('{{ limit }}' => $assert->max));
                break;
            case "EqualTo":
            case "NotEqualTo":
            case "IdenticalTo":
            case "NotIdenticalTo":
            case "LessThan":
            case "LessThanOrEqual":
            case "GreaterThan":
            case "GreaterThanOrEqual":
                $response->value = $assert->value;
                $response->message = $this->getTranslations($assert->message, array('{{ compared_value }}' => $assert->value));
                break;
            case "DateTime":
                $response->format = $assert->$format;
                $response->message = $this->getTranslations($assert->message);
                break;
            case "file":
                $upload_max_size = ini_get('upload_max_filesize');
                $response->maxSize = $assert->maxSize < $upload_max_size ? $assert->maxSize : $upload_max_size;
                $repsonse->mimeTypes = $assert->mimeTypes;
                $response->disallowEmptyMessage = $this->disallowEmptyMessage;
                $repsonse->uploadErrorMessage = $this->getTranslations($assert->uploadErrorMessage);
                $response->uploadFormSizeErrorMessage = $this->getTranslations($assert->uploadFormSizeErrorMessage);
                $repsonse->mimeTypesMessage = $this->getTranslations($assert->mimeTypesMessage, array('{{ types  }}' => "%type%"));
                $repsonse->maxSizeMessage = $this->getTranslations($assert->maxSizeMessage, array('{{ limit }}' => $assert->maxSize, '{{ suffix }}' => "%suffix%"));
                break;
            case "Image":
                $response->maxSize = $assert->maxSize < $upload_max_size ? $assert->maxSize : $upload_max_size;
                $repsonse->mimeTypes = $assert->mimeTypes;
                $response->disallowEmptyMessage = $this->disallowEmptyMessage;
                $repsonse->uploadErrorMessage = $this->getTranslations($assert->uploadErrorMessage);
                $response->uploadFormSizeErrorMessage = $this->getTranslations($assert->uploadFormSizeErrorMessage);
                $repsonse->mimeTypesMessage = $this->getTranslations($assert->mimeTypesMessage, array('{{ types  }}' => "%type%"));
                $repsonse->maxSizeMessage = $this->getTranslations($assert->maxSizeMessage, array('{{ limit }}' => $assert->maxSize, '{{ suffix }}' => "%suffix%"));
                $response->minRatio = $assert->minRatio;
                $response->maxRatio = $assert->maxRatio;
                $response->allowSquare = $assert->allowSquare;
                $response->allowLandscape = $assert->allowLandscape;
                $response->allowPortrait = $assert->allowPortrait;
                $response->maxWidth = $assert->maxWidth;
                $response->maxWidthMessage = $this->getTranslations($assert->maxSizeMessage, array('{{ width }}' => "%width%", '{{ max_width }}' => $assert->maxWidth));
                $response->minWidth = $assert->minWidth;
                $response->minWidthMessage = $this->getTranslations($assert->minSizeMessage, array('{{ width }}' => "%width%", '{{ min_width }}' => $assert->minWidth));
                $response->maxHeight = $assert->maxHeight;
                $response->maxHeightMessage = $this->getTranslations($assert->maxSizeMessage, array('{{ height }}' => "%height%", '{{ max_height }}' => $assert->maxHeight));
                $response->minHeight = $assert->minHeight;
                $response->minHeightMessage = $this->getTranslations($assert->minSizeMessage, array('{{ height }}' => "%height%", '{{ min_height }}' => $assert->minHeight));
                break;
            case "CardScheme ":
                $response->message = $this->getTranslations($assert->message);
                $response->schemes = $assert->schemes;
            case "Isbn":
                $response->type = $assert->type;
                $response->message = $this->getTranslations($assert->message);
                $response->isbn10Message = $this->getTranslations($assert->isbn10Message);
                $response->isbn13Message = $this->getTranslations($assert->isbn13Message);
                $response->sbn10Message = $this->getTranslations($assert->sbn10Message);
                break;
            case "Issn":
                $response->caseSensitive = $assert->caseSensitive;
                $response->requireHyphen = $assert->requireHyphen;
                $response->message = $this->getTranslations($assert->message);
                break;
            case "Callback":
            case "Expression":
                throw new \Exception($this->translateError(SELF::ERROR_CONSTRAINT_DEPRECIATED, array('{{ constraint }}' => $type)));
            default :
                throw new \Exception($this->translateError(SELF::ERROR_CONSTRAINT_NOT_SUPPORTED, array('{{ constraint }}' => $type)));
        }
        return $response;
    }

    /**
     * @param string $propertyName
     * @param string $groupe
     * @return array
     * @throws \Exception
     */
    public function getAsserts(string $propertyName): array {

        $response = [];
        if (!isset($this->meta->properties[$propertyName])) {
            throw new \Exception($this->translateError(SELF::ERROR_PROPERTY_NOT_FOUND, array('{{ property }}' => $propertyName)));
        }
        $property = $this->meta->properties[$propertyName];


        foreach ($property->constraints as $assert) {
            $response[] = $this->parseAssert($assert);
        }
        return $response;
    }
    

    public function getAllAsserts(): \stdClass {

        $response =  new \stdClass();
        foreach ($this->meta->properties as $propertyName => $property) {
            $response->$propertyName = $this->getAsserts($propertyName);
        }
        return $response;
    }
}
