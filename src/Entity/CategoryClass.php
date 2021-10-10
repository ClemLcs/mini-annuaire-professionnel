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
    public function defaultCategory()
    {
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
     * @return array|bool|void
     */
    public function checkExistCategory(Array $data = null){
        // Recherche sur la table category une catégorie ayant des données déjà existantes

        foreach ($data as $key => $value){
            $category = $this->entityManager
                ->getRepository(Category::class)
                ->findOneBy([
                    $key => $value
                ]);

            if ($category){
                return [
                    'data' => $category,
                    'msg' => "La catégorie $value est déjà enregistrée en BDD"
                ];
            }

            return True;

        }


    }

    /**
     * Méthode qui permet d'enregister une catégorie en BDD
     * @param array|null $data
     * @throws \Exception
     */
    public function saveCategory(Array $data = null){

        $result = $this->checkExistCategory($data);

        if ($result === True){
            $category = new Category();

            $category->setName($data['name']);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }else{
            throw new \Exception($result['msg']);
        }



    }

    /**
     * Méthode qui permet de lier une catégorie à une société
     * @param array|null $data
     * @throws \Exception
     */
    public function linkSocietyToCategory(Array $data = null){

        $society = $this->entityManager->getRepository(Society::class)->findOneBy(['name' => $data['name']]);
        $category = $this->entityManager->getRepository(Category::class)->find($data['id_category']);

        if ($society && $category){

            $society->setCategory($category);

            $this->entityManager->persist($society);
            $this->entityManager->flush();
        }else{
            throw new \Exception('Impossible de lier la société ' . $data['name'] . ' à la catégorie n° '. $data['id_category']);
        }

    }

    public function getAllCategories(){
        return $this->entityManager->getRepository(Category::class)->findAll();
    }


}