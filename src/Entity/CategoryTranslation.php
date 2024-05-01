<?php

declare(strict_types=1);

/*
 * Ici au départ laisser les @Annotations à la place des attributs 
 * parce que les SUperClasses de Doctrine utilisent aussi les @annotation
 * Ensuite vider le cache avec cache clear puis remettre les attributs php
 * Si j'utilise les attributs php directement j'ai une erreur...
 * En suivant ces étapes ça fonctionne.
 * 
 * https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/example/app/Entity/CategoryTranslation.php
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

#[ORM\Entity]
#[ORM\Table(name: 'category_translations')]
#[ORM\UniqueConstraint(name: 'lookup_unique_idx', columns: ['locale', 'object_id', 'field'])]
class CategoryTranslation extends AbstractPersonalTranslation
{
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'translations')]
    #[ORM\JoinColumn(name: 'object_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    protected $object;

    /**
     * Convenient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale, $field, $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }
}