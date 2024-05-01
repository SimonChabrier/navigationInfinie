<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavController extends AbstractController
{   

    public function __construct(
        private EntityManagerInterface $em,
    )
    {
        //
    }
    
    #[Route('/{slug?}', name: 'app_home')]
    public function index(?string $slug): Response
    {
        // Récupérer l'entité Category correspondant au slug            
        return $this->render('home/index.html.twig', [
                'tree' => $this->getTreeNavLinks(),
                'categories' => $this->getTreeNavObject(),
                'navArray' => $this->getTreeNavArray(),
                'currentCategory' =>  $this->em->getRepository(Category::class)->findOneBy(['id' => 1]),
            ]);

    }

    #[Route('/page/{slug}', name: 'app_page')]
    public function page(string $slug, EntityManagerInterface $em): Response
    {            
        return $this->render('home/index.html.twig', [
                'tree' => $this->getTreeNavLinks(),
                'categories' => $this->getTreeNavObject(),
                'navArray' => $this->getTreeNavArray(),
                'currentCategory' => $em->getRepository(Category::class)->findOneBy(['slug' => $slug])
            ]);

    }

    #[Route('/create/nav', name: 'app_create_nav')]
    public function createNav(): Response
    {   
        //TODO gèrer created by et updated by
        $em = $this->em;

        // Création des catégories racines
        for ($i = 1; $i <= 4; $i++) {
            $rootCategory = new Category();
            $rootCategory->setTitle("Root Cat $i");
            $em->persist($rootCategory);

            // Création des enfants de premier niveau pour chaque catégorie racine
            for ($j = 1; $j <= rand(1, 5); $j++) {
                $childLevel1 = new Category();
                $childLevel1->setTitle("Child Level 1 - $i.$j");
                $childLevel1->setParent($rootCategory);
                $em->persist($childLevel1);

                // Création des enfants de deuxième niveau pour chaque enfant de premier niveau
                for ($k = 1; $k <= rand(0, 2); $k++) {
                    $childLevel2 = new Category();
                    $childLevel2->setTitle("Child Level 2 - $i.$j.$k");
                    $childLevel2->setParent($childLevel1);
                    $em->persist($childLevel2);

                    // Création des enfants de troisième niveau pour chaque enfant de deuxième niveau
                    for ($l = 1; $l <= 4; $l++) {
                        $childLevel3 = new Category();
                        $childLevel3->setTitle("Child Level 3 - $i.$j.$k.$l");
                        $childLevel3->setParent($childLevel2);
                        $em->persist($childLevel3);
                    }
                }
            }
        }

        // Enregistrement des changements dans la base de données
        $em->flush();

        // redirect to home page
        return $this->redirectToRoute('app_home');
        
    }

    #[Route('/create/cat', name: 'app_create_category')]
    public function createCategory(Request $request): Response
    {
        //render form 
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // TODO à mettre dans un service

     // retourne une liste de nav fomaté en ul li
     function getTreeNavLinks() :string
     {
         $repo = $this->em->getRepository(Category::class);
         $options = [
             'decorate' => true,
             'rootOpen' => '<ul>',
             'rootClose' => '</ul>',
             'childOpen' => '<li>',
             'childClose' => '</li>',
             'nodeDecorator' => function($node) {
                 return '<a href="/page/'.$node['slug'].'">'.$node['title'].'</a>';
             }
         ];
         $tree = $repo->childrenHierarchy(
             null, /* starting from root nodes */
             false, /* true: load only root false load all childres */
             $options
         );
 
         $this->em->clear();
 
         return $tree;
 
     }
 
     // retourne un tableau php
     function getTreeNavArray() :array
     {
         $this->em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');
         $repo = $this->em->getRepository(Category::class);
         $tree = $repo->createQueryBuilder('tree')->getQuery()
             ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)           
             ->getResult('tree');
 
         $tree = $repo->childrenHierarchy(
             null, /* starting from root nodes */
             false, /* true: load only root false load all childres */
             []
         );  
         
         return $tree;
     }
 
     // retour une collection d'objets Category
     function getTreeNavObject() : array
     {
         //* retourne un tableau d'objet pour faire un template twig plus affiné
         $this->em->getConfiguration()->addCustomHydrationMode('tree', 'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator');
         
         $repo = $this->em->getRepository(Category::class);
         $tree = $repo->createQueryBuilder('tree')->getQuery()
             ->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)           
             ->getResult('tree');
         
        $this->em->clear();
 
         return $tree;
     }

}
