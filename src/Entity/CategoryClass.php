<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;

class CategoryClass extends SocietyClass
{

    /*
     * =============================================
     *      Définition des méthodes de la classe
     * =============================================
     */

    /**
     * Méthode qui contient les catégories par défaut
     * @return \string[][]
     */
    public function defaultCategory(){
        return [
            0 => [
                'name' => 'Ingénierie pétrolière et gazière'
            ],
            1 => [
                'name' => 'Conseil en ingénierie'
            ]
        ];
    }

    /**
     * Méthode qui permet de vérifier si une catégorie existe dans la BDD
     * @param array|null $data
     * @return int
     */
    public function checkExistCategory(Array $data = null){
        // Recherche sur la table category une catégorie ayant des données déjà existantes
        $category = $this->em
            ->getRepository(Category::class)
            ->findBy([
                'name' => $data['name']
            ]);

        if (!$category) {
            // L'enregistrement n'existe pas
            return 0;
        }
        // L'enregistrement existe
        return 1;
    }

    /**
     * Méthode qui permet d'enregister une catégorie en BDD
     * @param array|null $data
     * @throws \Exception
     */
    public function saveCategory(Array $data = null){
        if (!$this->checkExistCategory($data)){
            $category = new Category();

            $category->setName($data['name']);

            $this->em->persist($category);
            $this->em->flush();
        }else{
            throw new \Exception('Une catégorie existe déjà avec le nom: '. $data['name']);
        }
    }

    /**
     * Méthode qui permet de lier une catégorie à une société
     * @param array|null $data
     * @throws \Exception
     */
    public function linkSocietyToCategory(Array $data = null){

        $society = $this->em->getRepository(Society::class)->findOneBy(['name' => $data['name']]);
        $category = $this->em->getRepository(Category::class)->find($data['id_category']);

        if ($society && $category){

            $society->setCategory($category);

            $this->em->persist($society);
            $this->em->flush();
        }else{
            throw new \Exception('Impossible de lier la société ' . $data['name'] . ' à la catégorie n° '. $data['id_category']);
        }

    }


}