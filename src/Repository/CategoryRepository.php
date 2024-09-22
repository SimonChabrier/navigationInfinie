<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @template-extends NestedTreeRepository<Category>
 */
final class CategoryRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, $em->getClassMetadata(Category::class));
    }

    public function getTreeNavLinks(): array
    {
        $categories = $this->getTreeNavObject();
        $navLinks = [];
        foreach ($categories as $category) {

            $navLinks[] = [
                'title' => $category['title'],
                'slug' => $category['slug'],
                'level' => $category['level'],
                'children' => $category['__children'],
            ];
        }
        return $navLinks;
    }

    public function getTreeNavObject(): array
    {
        return $this->childrenHierarchy();
    }

    /**
     * Retourne le QueryBuilder positionné sur les Category triées pour le formulaire de création
     * @return QueryBuilder<Category>
     */
    public function getTreeOrderedPageCategoriesQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.root', 'ASC')
            ->addOrderBy('p.lft', 'ASC');
    }
}
