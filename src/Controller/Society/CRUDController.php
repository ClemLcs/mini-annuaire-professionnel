<?php

namespace App\Controller\Society;

use App\Entity\Society;
use App\Entity\SocietyClass;
use App\Form\SearchSocietyType;
use App\Form\SocietyFormType;;

use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Json;

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
        $formSearch = $this->createForm(SearchSocietyType::class, $society);
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
                    return $this->redirectToRoute('readAllSociety', [], 301);

                }catch (\Exception $erreur){
                    $this->addFlash('error', $erreur->getMessage());
                    return $this->redirectToRoute('createSociety', [], 301);
                }
            }

            try {

                $this->societyClass->saveSociety($form->getData());

                $this->addFlash('success', "La société $name a été créé en BDD");
                return $this->redirectToRoute('readAllSociety', [], 301);

            }catch (\Exception $erreur){

                $this->addFlash('error', $erreur->getMessage());
                return $this->redirectToRoute('createSociety', [], 301);
            }

        }

        return $this->render('Admin/createSociety.html.twig', [
            'form' =>  $form->createView(),
            'formSearch' => $formSearch->createView(),
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

                    $destFolder = $this->getParameter('images_directory');
                    $newSociety =  $this->societyClass->uploadFile($society['data'], $picture, $destFolder, $slugger);

                    $this->societyClass->saveSociety($newSociety);

                    $this->addFlash('success', "La société $name a été mise à jour en BDD");
                    return $this->redirectToRoute('readAllSociety', [], 301);

                }

                try {

                    $result = $this->societyClass->checkExistSociety(['id' => $id]);
                    $newSociety = $result['data'];

                    $this->societyClass->saveSociety($newSociety);

                    $this->addFlash('success', "La société $name a été mise à jour en BDD");
                    return $this->redirectToRoute('readAllSociety', [], 301);

                }catch (\Exception $erreur){

                    $this->addFlash('error', $erreur->getMessage());
                    return $this->redirectToRoute('updateSociety', [], 301);
                }

            }

            return $this->render('Admin/adminSociety.html.twig', [
                'form' =>  $form->createView(),
                'society' => $society['data']
            ]);
        }else{
            $this->addFlash('error', "Aucune société n'éxiste avec le nom $name");
            return $this->redirectToRoute('readAllSociety', [], 301);
        }
    }

    /**
     * Méthode permettant de supprimer une société
     * @Route("/admin/society/delete/{name}", name="deleteSociety")
     * @param String $name
     * @param SocietyRepository $societyRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteSociety(String $name, SocietyRepository $societyRepository){
            $society = $societyRepository->findOneBy(['name' => $name]);

            if(!is_null($society)){

                $this->entityManager->remove($society);
                $this->entityManager->flush();

                $this->addFlash('success', "La société $name a bien été supprimé en BDD");
                return $this->redirectToRoute('readAllSociety', [], 301);
            }

            $this->addFlash('error', "Une erreur c'est produite lors de la suppression de la société $name");
            return $this->redirectToRoute('readAllSociety', [], 301);
    }

    /**
     * Méthode permettant d'afficher toutes les sociétés
     * @Route("/", name="readAllSociety")
     * @param SocietyRepository $societyRepository
     * @return Response
     */
    public function readAllSociety(SocietyRepository $societyRepository, Request $request){

        if ($request->query->get('searchSocietyPattern')){

            $search = $request->query->get('searchSocietyPattern');
            $societies_reversed = array_reverse($societyRepository->findSocietyByPattern($search));


        }else{
            $societies_reversed = array_reverse($societyRepository->findAll());
        }


        $society = new Society();
        $formSearch = $this->createForm(SearchSocietyType::class, $society);

        if(!empty($societies_reversed)){
            return $this->render('Society/readAll.html.twig', [
                'societies' => $societies_reversed,
                'categories' => $this->categories,
                'formSearch' => $formSearch->createView()
            ]);
        }else{
            $this->addFlash('warning', 'Aucune résultat n\'à été trouvé en BDD');
            return $this->render('Society/readAll.html.twig',[
                'categories' => $this->categories,
                'formSearch' => $formSearch->createView()
            ]);
        }
    }

    /**
     * Méthode permettant d'afficher une société
     * @Route("/society/read/{name}", name="readOneSociety")
     * @param String $name
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function readOneSociety(String $name, SocietyRepository $societyRepository){

        $society = $this->societyClass->checkExistSociety(['name' => $name]);
        $formSearch = $this->createForm(SearchSocietyType::class, $society);

        if (is_array($society)) {

            $society = $society['data'];

            $randomSocieties = $this->randomSocieties($societyRepository, $society->name);

            return $this->render('Society/readOne.html.twig', [
                'society' => $society,
                'categories' => $this->categories,
                'randomsocieties' => $randomSocieties,
                'formSearch' => $formSearch->createView()
            ]);
        }else{
            $this->addFlash('error', 'Aucune société n\'à été renseignée en BDD avec ce nom');
            return $this->redirectToRoute('readAllSociety', [], 301);
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

    /**
     * Méthode qui permet de rechercher une société par nom
     * @Route("/society/search", name="searchSociety", methods={"POST"})
     * @param Request $request
     */
    public function searchSociety(Request $request, SocietyRepository $societyRepository){

        $society = new Society();
        $formSearch = $this->createForm(SearchSocietyType::class, $society);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()){

            $name = $formSearch->get('name')->getData();

            return $this->redirectToRoute('readAllSociety', [
                'searchSocietyPattern' => $name
            ], 301);

        }

    }
}