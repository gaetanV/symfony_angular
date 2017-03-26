<?php

namespace Tools\JsBundle\Component\Entity;

use Symfony\Component\Validator\Constraint;
use Tools\JsBundle\Component\Entity\EntityReflection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @reference   : http://symfony.com/doc/current/validation.html
 * @depreciated : Validator Groupe 
 * @TODO        : Recursive collection
 */
final class EntityMapping extends EntityCheck {

    protected $entity;
    protected $languages;
    protected $strict = false;

    const TRANS_ERROR_NAMESPACE = "JsBundle.Component.Entity.EntityMapping";
    const ERROR_CONSTRAINT_NOT_SUPPORTED = 1;
    const ERROR_CONSTRAINT_DEPRECIATED = 2;
    const ERROR_CONSTRAINT_DUPLICATED = 3;

    /**
     * @param EntityReflection $entity
     * @param array $languages
     */
    public function __construct(EntityReflection $entity, array $languages, bool $strict = false) {
        $this->strict = $strict;
        $this->entity = $entity;
        $this->languages = $languages;
        parent::__construct($entity);
    }

    /**
     * @param int $id
     * @param array $params
     * @return string
     */
    private function translateError(int $id, array $params = array()): string {
        return $this->entity->translator->trans(EntityMapping::TRANS_ERROR_NAMESPACE . "." . $id, $params, 'errors');
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
            $this->entity->translator->setLocale($lang);
            $response[$lang] = $this->entity->translator->trans($messagePattern, $param, 'validators');
        }
        return $response;
    }

    private function getTransChoice(string $messagePattern, int $number, array $param = array()): array {
        $response = [];
        foreach ($this->languages as $lang) {
            $this->entity->translator->setLocale($lang);
            $response[$lang] = $this->entity->translator->transChoice($messagePattern, $number, $param, 'validators');
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
        $name = $func->getShortName();
        $definition = new \stdClass();
        switch ($name) {
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
                $definition->message = $this->getTrans($assert->message);
                break;
            case "Type":
                $definition->type = $assert->type;
                $definition->message = $this->getTrans($assert->message);
                break;
            case "Range":
                $definition->min = $assert->min;
                $definition->max = $assert->max;
                $definition->minMessage = $this->getTransChoice($assert->minMessage, $assert->min, array('{{ limit }}' => $assert->min));
                $definition->maxMessage = $this->getTransChoice($assert->maxMessage, $assert->max, array('{{ limit }}' => $assert->max));
                $definition->invalidMessage = $assert->invalidMessage;
                break;
            case "Length":
                $definition->min = $assert->min;
                $definition->max = $assert->max;
                $definition->minMessage = $this->getTransChoice($assert->minMessage, $assert->min, array('{{ limit }}' => $assert->min));
                $definition->maxMessage = $this->getTransChoice($assert->maxMessage, $assert->max, array('{{ limit }}' => $assert->max));
                if ($assert->min == $assert->max) {
                    $definition->exactMessage = $this->getTrans($assert->exactMessage, array('{{ limit }}' => $assert->max));
                }
                break;
            case "Url":
                $definition->message = $this->getTrans($assert->message);
                $definition->protocols = $this->getTrans($assert->protocols);
                break;
            case "Regex":
                $definition->message = $this->getTrans($assert->message);
                $definition->htmlPattern = $assert->htmlPattern;
                $definition->pattern = $assert->pattern;
                $definition->match = $assert->match;
                break;
            case "Ip":
                $definition->version = $assert->version;
                $definition->message = $this->getTrans($assert->message);
                break;
            case "Uuid":
                $definition->strict = $assert->strict;
                $definition->versions = $assert->versions;
                $definition->message = $this->getTrans($assert->message);
                break;
            case "EqualTo":
            case "NotEqualTo":
            case "IdenticalTo":
            case "NotIdenticalTo":
            case "LessThan":
            case "LessThanOrEqual":
            case "GreaterThan":
            case "GreaterThanOrEqual":
                $definition->value = $assert->value;
                $definition->message = $this->getTrans($assert->message, array('{{ compared_value }}' => $assert->value));
                break;
            case "DateTime":
                $definition->format = $assert->format;
                $definition->message = $this->getTrans($assert->message);
                break;
            case "Image":
                $definition->minRatio = $assert->minRatio;
                $definition->maxRatio = $assert->maxRatio;
                $definition->allowSquare = $assert->allowSquare;
                $definition->allowLandscape = $assert->allowLandscape;
                $definition->allowPortrait = $assert->allowPortrait;
                $definition->maxWidth = $assert->maxWidth;
                $definition->maxWidthMessage = $this->getTrans($assert->maxSizeMessage, array('{{ width }}' => "%width%", '{{ max_width }}' => $assert->maxWidth));
                $definition->minWidth = $assert->minWidth;
                $definition->minWidthMessage = $this->getTrans($assert->minSizeMessage, array('{{ width }}' => "%width%", '{{ min_width }}' => $assert->minWidth));
                $definition->maxHeight = $assert->maxHeight;
                $definition->maxHeightMessage = $this->getTrans($assert->maxSizeMessage, array('{{ height }}' => "%height%", '{{ max_height }}' => $assert->maxHeight));
                $definition->minHeight = $assert->minHeight;
                $definition->minHeightMessage = $this->getTrans($assert->minSizeMessage, array('{{ height }}' => "%height%", '{{ min_height }}' => $assert->minHeight));
            case "File":
                $upload_max_size = ini_get('upload_max_filesize');
                $definition->maxSize = $assert->maxSize < $upload_max_size ? $assert->maxSize : $upload_max_size;
                $definition->mimeTypes = $assert->mimeTypes;
                $definition->disallowEmptyMessage = $this->disallowEmptyMessage;
                $definition->uploadErrorMessage = $this->getTrans($assert->uploadErrorMessage);
                $definition->uploadFormSizeErrorMessage = $this->getTrans($assert->uploadFormSizeErrorMessage);
                $definition->mimeTypesMessage = $this->getTrans($assert->mimeTypesMessage, array('{{ types  }}' => "%type%"));
                $definition->maxSizeMessage = $this->getTrans($assert->maxSizeMessage, array('{{ limit }}' => $assert->maxSize, '{{ suffix }}' => "%suffix%"));
                break;
            case "CardScheme ":
                $definition->message = $this->getTrans($assert->message);
                $definition->schemes = $assert->schemes;
                break;
            case "Isbn":
                $definition->type = $assert->type;
                $definition->message = $this->getTrans($assert->message);
                $definition->isbn10Message = $this->getTrans($assert->isbn10Message);
                $definition->isbn13Message = $this->getTrans($assert->isbn13Message);
                $definition->sbn10Message = $this->getTrans($assert->sbn10Message);
                break;
            case "Issn":
                $definition->caseSensitive = $assert->caseSensitive;
                $definition->requireHyphen = $assert->requireHyphen;
                $definition->message = $this->getTrans($assert->message);
                break;
            case "Callback":
            case "Expression":
            case "All":
            case "Valid":
                throw new \Exception($this->translateError(EntityMapping::ERROR_CONSTRAINT_DEPRECIATED, array('{{ constraint }}' => $name)));
            default :
                throw new \Exception($this->translateError(EntityMapping::ERROR_CONSTRAINT_NOT_SUPPORTED, array('{{ constraint }}' => $name)));
        }
        $response = new \stdClass();
        $response->name = $name;
        $response->definition = $definition;
        return $response;
    }
    
       
    
    
    public function exportAsserts(string $propertyName): \stdClass {
        $response = new \stdClass;
        $property = $this->entity->getPropertyAsserts($propertyName);
        foreach ($property->constraints as $name => $assert) {
            $assert = $this->parseAssert($assert);
            switch ($assert->name) {
                case "Regex":
                    if (!isset($response->{$assert->name})) {
                        $response->{$assert->name} = [];
                    }

                    $response->{$assert->name}[] = $assert->definition;
                    break;
                default:
                    if (isset($response->{$assert->name})) {
                        throw new \Exception($this->translateError(EntityMapping::ERROR_CONSTRAINT_DUPLICATED, array('{{ constraint }}' => $assert->name)));
                    }
                    $response->{$assert->name} = $assert->definition;
            }
        }
        if ($this->strict) {
            $this->checkAsserts($propertyName);
            $case1 = isset($response->Length);
            $case2 = isset($response->Type);
            if (!$case1 || !$case2) {
                $metadata = $this->entity->getPropertyMetaData($propertyName);
                if (!$case1) {
                    $assert = $this->parseAssert(new Length(array('max' => $metadata["length"],'min' => 0)));
                    $response->{$assert->name} = $assert->definition;
                }
                if (!$case2) {
                    $assert = $this->parseAssert(new Type(array('type' => $metadata["type"])));
                    $response->{$assert->name} = $assert->definition;
                }
            }
        }
        return $response;
    }

    /**
     * @return \stdClass
     */
    public function exportAllAsserts(): \stdClass {
        $response = new \stdClass();

        foreach ($this->entity->getAsserts()->properties as $propertyName => $property) {
            $response->$propertyName = $this->exportAsserts($propertyName);
        }

        return $response;
    }

}
