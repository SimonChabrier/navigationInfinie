<?php

declare(strict_types=1);

namespace App\Entity;

use Gedmo\Loggable\Loggable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;



#[Gedmo\Tree(type: 'nested')]
#[Gedmo\Loggable]
#[ORM\Table(name: 'categories')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[Gedmo\TranslationEntity(class: CategoryTranslation::class)]
class Category implements Loggable
{

    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private $id;

    #[Gedmo\Translatable]
    #[ORM\Column(length: 64)]
    private $title;

    #[Gedmo\Translatable]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private $description;

    #[Gedmo\Translatable]
    #[Gedmo\Slug(fields: ['created', 'title'])]
    #[ORM\Column(length: 64, unique: true)]
    private $slug;

    #[Gedmo\TreeLeft]
    #[ORM\Column(type: Types::INTEGER)]
    private $lft;

    #[Gedmo\TreeRight]
    #[ORM\Column(type: Types::INTEGER)]
    private $rgt;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $parent;

    #[Gedmo\TreeRoot]
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private $root;

    #[Gedmo\TreeLevel]
    #[ORM\Column(name: 'lvl', type: Types::INTEGER)]
    private $level;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $children;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $created;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $updated;

    // #[Gedmo\Blameable(on: 'create')]
    // #[ORM\Column(type: Types::STRING)]
    // private $createdBy;

    // #[Gedmo\Blameable(on: 'update')]
    // #[ORM\Column(type: Types::STRING)]
    // private $updatedBy;

    #[ORM\OneToMany(targetEntity: CategoryTranslation::class, mappedBy: 'object', cascade: ['persist', 'remove'])]
    private $translations;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function addTranslation(CategoryTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getLeft()
    {
        return $this->lft;
    }

    public function getRight()
    {
        return $this->rgt;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    // public function getCreatedBy()
    // {
    //     return $this->createdBy;
    // }

    // public function getUpdatedBy()
    // {
    //     return $this->updatedBy;
    // }
}