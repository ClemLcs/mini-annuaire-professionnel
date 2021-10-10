<?php

namespace App\Controller;

use App\Entity\CategoryClass;
use App\Entity\Society;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private ?CategoryClass $categoryClass;
    private ?EntityManagerInterface $entityManager;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */

    public function __construct(EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
        $this->categoryClass = new CategoryClass($this->entityManager);
    }

    /*
    * =============================================
    *      Définition des setters de la classe
    * =============================================
    */

    /**
     * @param CategoryClass|null $categoryClass
     */
    public function setCategoryClass(?CategoryClass $categoryClass): void
    {
        $this->categoryClass = $categoryClass;
    }

    /**
     * @param EntityManagerInterface|null $entityManager
     */
    public function setEntityManager(?EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /*
     * =============================================
     *      Définition des getters de la classe
     * =============================================
     */

    /**
     * @return CategoryClass|null
     */
    public function getCategoryClass(): ?CategoryClass
    {
        return $this->categoryClass;
    }

    /**
     * @return EntityManagerInterface|null
     */
    public function getEntityManager(): ?EntityManagerInterface
    {
        return $this->entityManager;
    }

    /*
     * =============================================
     *      Définition des méthodes de la classe
     * =============================================
     */

    /**
     * @Route("/category/read/{name}", name="readOneCategory")
     * @param String $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readOneCategory(String $name = null){
        // Vérifie si la catégorie existe avec ce nom
        $category = $this->categoryClass->checkExistCategory(['name' => $name])['data'];

        $categories = $this->categoryClass->getAllCategories();

        if(isset($category)) {
            // Elle existe et nous récupèrons l'ensemble des sociétés selon le numéro de la catégorie
            $societies = $this->entityManager->getRepository(Society::class)->findBy([
                'category' => $category->id
            ]);
        }else{
            $this->addFlash('error', 'Aucune catégorie n\'à été renseigné avec ce nom en BDD');
            return $this->redirectToRoute('readAllSociety');
        }

        if (isset($societies)){
            return $this->render('Category/readOne.html.twig',[
                'category' => $category,
                'categories' => $categories,
                'societies' => $societies
            ]);
        }else{
            $this->addFlash('error', 'Aucune société n\'à été associée à cette catégorie en BDD');
            return $this->redirectToRoute('readAllSociety');
        }
    }
}