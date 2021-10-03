<?php


namespace App\Controller;


use App\Repository\CategoryRepository;
use App\Repository\SocietyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SocietyController extends AbstractController
{
    /**
     * Méthode permettant d'afficher toutes les sociétés
     * @Route("/", name="readAllSociety")
     * @param SocietyRepository $societyRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readAllSociety(SocietyRepository $societyRepository, CategoryRepository $categoryRepository){
        $societies = $societyRepository->findAll();
        $categories = $categoryRepository->findAll();

        return $this->render('Society/readAll.html.twig', [
            'societies' => $societies,
            'categories' => $categories
        ]);
    }
}