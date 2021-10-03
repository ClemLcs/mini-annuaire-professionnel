<?php


namespace App\Controller;


use App\Entity\CategoryClass;
use App\Entity\SocietyClass;
use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SocietyController extends AbstractController
{
    private $categories;
    private $societyClass;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */

    public function __construct(CategoryRepository $categoryRepository = null, EntityManagerInterface $entityManager =null) {
        ($categoryRepository->findAll())
            ? $this->categories = $categoryRepository->findAll()
            : '';
        $this->societyClass = new SocietyClass($entityManager);
    }

    /*
    * =============================================
    *      Définition des setters de la classe
    * =============================================
    */

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function setCategories(CategoryRepository $categoryRepository)
    {
        $this->categories = $categoryRepository;
    }

    /**
     * @param SocietyClass $societyClass
     */
    public function setSocietyClass(SocietyClass $societyClass)
    {
        $this->societyClass = $societyClass;
    }

    /*
     * =============================================
     *      Définition des getters de la classe
     * =============================================
     */
    /**
     * @return \App\Entity\Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return SocietyClass
     */
    public function getSocietyClass(): SocietyClass
    {
        return $this->societyClass;
    }

    /*
     * =============================================
     *      Définition des méthodes de la classe
     * =============================================
     */
    /**
     * Méthode permettant d'afficher toutes les sociétés
     * @Route("/", name="readAllSociety")
     * @param SocietyRepository $societyRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readAllSociety(SocietyRepository $societyRepository, CategoryRepository $categoryRepository){
        $societies = $societyRepository->findAll();

        if($societies){
            return $this->render('Society/readAll.html.twig', [
                'societies' => $societies,
                'categories' => $this->categories
            ]);
        }else{
            $this->addFlash('warning', 'Aucune société n\'à été renseignée en BDD');
            return $this->render('Society/readAll.html.twig');
        }
    }

    /**
     * Méthode permettant d'afficher une société
     * @Route("/society/read/{name}", name="readOneSociety")
     * @param String $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function readOneSociety(String $name){

        $data = [ 'name' => $name];
        $society = $this->societyClass->checkExistSociety($data);

        if ($society) {
            return $this->render('Society/readOne.html.twig', ['society' => $society, 'categories' => $this->categories]);
        }else{
            $this->addFlash('error', 'Aucune société n\'à été renseignée en BDD avec ce nom');
            return $this->redirectToRoute('readAllSociety');
        }
    }
}