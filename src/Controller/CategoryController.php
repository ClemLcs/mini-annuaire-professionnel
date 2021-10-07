<?php

namespace App\Controller;

use App\Entity\CategoryClass;
use App\Entity\Society;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $categoryClass;
    private $em;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */

    public function __construct(EntityManagerInterface $entityManager = null)
    {
        if($entityManager){
            $this->categoryClass = new CategoryClass($entityManager);
            $this->em = $entityManager;
        }
    }

    /*
    * =============================================
    *      Définition des setters de la classe
    * =============================================
    */

    /**
     * @param CategoryClass $categoryClass
     */
    public function setCategoryClass(CategoryClass $categoryClass): void
    {
        $this->categoryClass = $categoryClass;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    /*
     * =============================================
     *      Définition des getters de la classe
     * =============================================
     */

    /**
     * @return mixed
     */
    public function getCategoryClass()
    {
        return $this->categoryClass;
    }

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em;
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
        $category = $this->categoryClass->checkExistCategory(array('name' => $name));

        $categories = $this->categoryClass->getAllCategories();

        if(isset($category)) {
            // Elle existe et nous récupèrons l'ensemble des sociétés selon le numéro de la catégorie
            $societies = $this->em->getRepository(Society::class)->findBy([
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