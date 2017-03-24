<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Validator\Constraint;

/**
 * @reference   : http://symfony.com/doc/current/validation.html
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
    private function getTrans(string $messagePattern, array $param = array()): array {
        $response = [];
        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);
            $response[$lang] = $this->translator->trans($messagePattern, $param, 'validators');
        }
        return $response;
    }

    private function getTransChoice(string $messagePattern, int $number, array $param = array()): array {
        $response = [];
        foreach ($this->languages as $lang) {
            $this->translator->setLocale($lang);
            $response[$lang] = $this->translator->transChoice($messagePattern, $number, $param, 'validators');
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
            case "Email":
                $response->message = $this->getTrans($assert->message);
                break;
            case "Type":
                $response->type = $assert->type;
                $response->message = $this->getTrans($assert->message);
                break;
            case "Range":
                $response->min = $assert->min;
                $response->max = $assert->max;
                $response->minMessage = $this->getTransChoice($assert->minMessage, $assert->min, array('{{ limit }}' => $assert->min));
                $response->maxMessage = $this->getTransChoice($assert->maxMessage, $assert->max, array('{{ limit }}' => $assert->max));
                $response->invalidMessage = $assert->invalidMessage;
            case "Length":
                $response->min = $assert->min;
                $response->max = $assert->max;
                $response->minMessage = $this->getTransChoice($assert->minMessage, $assert->min, array('{{ limit }}' => $assert->min));
                $response->maxMessage = $this->getTransChoice($assert->maxMessage, $assert->max, array('{{ limit }}' => $assert->max));
                if ($assert->min == $assert->max) {
                    $response->exactMessage = $this->getTrans($assert->exactMessage, array('{{ limit }}' => $assert->max));
                }
                break;
            case "Url":
                $response->message = $this->getTrans($assert->message);
                $response->protocols = $this->getTrans($assert->protocols);
                break;
            case "Regex":
                $response->message = $this->getTrans($assert->message);
                $response->htmlPattern = $assert->htmlPattern;
                $response->match = $assert->match;
                break;
            case "Ip":
                $response->version = $assert->version;
                $response->message = $this->getTrans($assert->message);
                break;
            case "Uuid":
                $response->strict = $assert->strict;
                $response->versions = $assert->versions;
                $response->message = $this->getTrans($assert->message);
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
                $response->message = $this->getTrans($assert->message, array('{{ compared_value }}' => $assert->value));
                break;
            case "DateTime":
                $response->format = $assert->format;
                $response->message = $this->getTrans($assert->message);
                break;
            case "Image":
                $response->minRatio = $assert->minRatio;
                $response->maxRatio = $assert->maxRatio;
                $response->allowSquare = $assert->allowSquare;
                $response->allowLandscape = $assert->allowLandscape;
                $response->allowPortrait = $assert->allowPortrait;
                $response->maxWidth = $assert->maxWidth;
                $response->maxWidthMessage = $this->getTrans($assert->maxSizeMessage, array('{{ width }}' => "%width%", '{{ max_width }}' => $assert->maxWidth));
                $response->minWidth = $assert->minWidth;
                $response->minWidthMessage = $this->getTrans($assert->minSizeMessage, array('{{ width }}' => "%width%", '{{ min_width }}' => $assert->minWidth));
                $response->maxHeight = $assert->maxHeight;
                $response->maxHeightMessage = $this->getTrans($assert->maxSizeMessage, array('{{ height }}' => "%height%", '{{ max_height }}' => $assert->maxHeight));
                $response->minHeight = $assert->minHeight;
                $response->minHeightMessage = $this->getTrans($assert->minSizeMessage, array('{{ height }}' => "%height%", '{{ min_height }}' => $assert->minHeight));
            case "File":
                $upload_max_size = ini_get('upload_max_filesize');
                $response->maxSize = $assert->maxSize < $upload_max_size ? $assert->maxSize : $upload_max_size;
                $repsonse->mimeTypes = $assert->mimeTypes;
                $response->disallowEmptyMessage = $this->disallowEmptyMessage;
                $repsonse->uploadErrorMessage = $this->getTrans($assert->uploadErrorMessage);
                $response->uploadFormSizeErrorMessage = $this->getTrans($assert->uploadFormSizeErrorMessage);
                $repsonse->mimeTypesMessage = $this->getTrans($assert->mimeTypesMessage, array('{{ types  }}' => "%type%"));
                $repsonse->maxSizeMessage = $this->getTrans($assert->maxSizeMessage, array('{{ limit }}' => $assert->maxSize, '{{ suffix }}' => "%suffix%"));
                break;
            case "CardScheme ":
                $response->message = $this->getTrans($assert->message);
                $response->schemes = $assert->schemes;
                break;
            case "Isbn":
                $response->type = $assert->type;
                $response->message = $this->getTrans($assert->message);
                $response->isbn10Message = $this->getTrans($assert->isbn10Message);
                $response->isbn13Message = $this->getTrans($assert->isbn13Message);
                $response->sbn10Message = $this->getTrans($assert->sbn10Message);
                break;
            case "Issn":
                $response->caseSensitive = $assert->caseSensitive;
                $response->requireHyphen = $assert->requireHyphen;
                $response->message = $this->getTrans($assert->message);
                break;
            case "Callback":
            case "Expression":
            case "All":
            case "Valid":
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
    public function getAssert(string $propertyName): array {

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

    /**
     * @return \stdClass
     */
    public function getAllAsserts(): \stdClass {

        $response = new \stdClass();
        foreach ($this->meta->properties as $propertyName => $property) {
            $response->$propertyName = $this->getAssert($propertyName);
        }
        return $response;
    }

}