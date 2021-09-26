<?php


namespace App\Controller;


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
    public function readAllSociety(SocietyRepository $societyRepository){
        $societies = $societyRepository->findAll();

        return $this->render('Society/readAll.html.twig', [
            'societies' => $societies
        ]);
    }
}