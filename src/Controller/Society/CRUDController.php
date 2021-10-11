<?php

namespace App\Controller\Society;

use App\Entity\Society;
use App\Entity\SocietyClass;
use App\Form\SocietyFormType;;

use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CRUDController extends AbstractController
{

    private ?SocietyClass $societyClass;
    private EntityManagerInterface $entityManager;
    private $categories;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */

    public function __construct(CategoryRepository $categoryRepository, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->societyClass = new SocietyClass($this->entityManager);
        $this->categories = $categoryRepository->findAll();
    }

    /*
     * =============================================
     *      Définition des setters de la classe
     * =============================================
     */

    /**
     * @param SocietyClass|null $societyClass
     */
    public function setSocietyClass(?SocietyClass $societyClass): void
    {
        $this->societyClass = $societyClass;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param CategoryRepository $categories
     */
    public function setCategories(CategoryRepository $categories): void
    {
        $this->categories = $categories;
    }

    /*
     * =============================================
     *      Définition des getters de la classe
     * =============================================
     */

    /**
     * @return SocietyClass|null
     */
    public function getSocietyClass(): ?SocietyClass
    {
        return $this->societyClass;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return CategoryRepository
     */
    public function getCategories(): CategoryRepository
    {
        return $this->categories;
    }

    /*
     * =============================================
     *      Définition des méthodes de la classe
     * =============================================
     */

    /**
     * Méthode qui retourne le formulaire de création d'une société
     * @Route("/admin/society/create", name="createSociety")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function createSociety(Request $request, SluggerInterface $slugger){

        $society = new Society();
        $form = $this->createForm(SocietyFormType::class, $society);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $picture = $form->get('picture')->getData();

            if ($picture){
                try{
                    $destFolder = $this->getParameter('images_directory');
                    $newSociety =  $this->societyClass->uploadFile($society['data'], $picture, $destFolder, $slugger);

                    $this->societyClass->saveSociety($newSociety);

                    $this->addFlash('success', "La société $name a été créé en BDD");
                    return $this->redirectToRoute('readAllSociety');

                }catch (\Exception $erreur){
                    $this->addFlash('error', $erreur->getMessage());
                    return $this->redirectToRoute('createSociety');
                }
            }

            try {

                $this->societyClass->saveSociety($form->getData());

                $this->addFlash('success', "La société $name a été créé en BDD");
                return $this->redirectToRoute('readAllSociety');

            }catch (\Exception $erreur){

                $this->addFlash('error', $erreur->getMessage());
                return $this->redirectToRoute('createSociety');
            }

        }

        return $this->render('Admin/createSociety.html.twig', [
            'form' =>  $form->createView(),
            'society' => $society
        ]);

    }

    /**
     * Méthode qui permet de mettre à jour les données d'une société
     * @Route("/admin/society/update/{name}", name="updateSociety")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function updateSociety(String $name, Request $request, SluggerInterface $slugger){

        $society = $this->societyClass->checkExistSociety(['name' => $name]);

        if (!is_bool($society)){
            // On vérifie si la société existe avec ce nom
            $form = $this->createForm(SocietyFormType::class, $society['data']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $name = $form->get('name')->getData();
                $picture = $form->get('picture')->getData();
                $id = $form->get('id')->getData();

                if ($picture){
                    try{
                        $destFolder = $this->getParameter('images_directory');
                        $newSociety =  $this->societyClass->uploadFile($society['data'], $picture, $destFolder, $slugger);

                        $this->societyClass->saveSociety($newSociety);

                        $this->addFlash('success', "La société $name a été mise à jour en BDD");
                        return $this->redirectToRoute('readAllSociety');

                    }catch (\Exception $erreur){
                        dd($erreur);
                        $this->addFlash('error', $erreur->getMessage());
                        return $this->redirectToRoute('updateSociety');
                    }
                }

                try {

                    $result = $this->societyClass->checkExistSociety(['id' => $id]);
                    $newSociety = $result['data'];

                    $this->societyClass->saveSociety($newSociety);

                    $this->addFlash('success', "La société $name a été mise à jour en BDD");
                    return $this->redirectToRoute('readAllSociety');

                }catch (\Exception $erreur){

                    $this->addFlash('error', $erreur->getMessage());
                    return $this->redirectToRoute('updateSociety');
                }

            }

            return $this->render('Admin/adminSociety.html.twig', [
                'form' =>  $form->createView(),
                'society' => $society['data']
            ]);
        }else{
            $this->addFlash('error', "Aucune société n'éxiste avec le nom $name");
            return $this->redirectToRoute('readAllSociety');
        }
    }

    /**
     * Méthode permettant d'afficher toutes les sociétés
     * @Route("/", name="readAllSociety")
     * @param SocietyRepository $societyRepository
     * @return Response
     */
    public function readAllSociety(SocietyRepository $societyRepository){
        $societies_reversed = array_reverse($societyRepository->findAll());

        if($societies_reversed){
            return $this->render('Society/readAll.html.twig', [
                'societies' => $societies_reversed,
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function readOneSociety(String $name, SocietyRepository $societyRepository){

        $society = $this->societyClass->checkExistSociety(['name' => $name])['data'];

        if ($society) {

            $randomSocieties = $this->randomSocieties($societyRepository, $society->name);

            return $this->render('Society/readOne.html.twig', [
                'society' => $society,
                'categories' => $this->categories,
                'randomsocieties' => $randomSocieties,
            ]);
        }else{
            $this->addFlash('error', 'Aucune société n\'à été renseignée en BDD avec ce nom');
            return $this->redirectToRoute('readAllSociety');
        }
    }

    /**
     * Méthode qui permet de générer deux autres sociétés à consulter par l'utilisateur
     * @param SocietyRepository $societyRepository
     * @param Int $current_id
     * @return mixed
     */
    public function randomSocieties(SocietyRepository $societyRepository, String $current_name){

        $other_societies = $societyRepository->findSocietyExcept($current_name);

        $nb1 = mt_rand(0, count($other_societies) - 1);
        $nb2 = mt_rand(0, count($other_societies) - 1);

        return array_merge(array($other_societies[$nb1]), array($other_societies[$nb2]));
    }
}