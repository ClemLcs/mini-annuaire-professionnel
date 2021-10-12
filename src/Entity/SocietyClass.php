<?php

namespace App\Entity;

use App\Repository\SocietyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class SocietyClass
{

    public ?EntityManagerInterface $entityManager;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */
    public function __construct(EntityManagerInterface $entityManager = null){
        $this->entityManager = $entityManager;
    }

    /*
     * =============================================
     *      Définition des setters de la classe
     * =============================================
     */

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
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }


    /*
     * =============================================
     *      Définition des méthodes de la classe
     * =============================================
     */

    /**
     * Méthode qui contient les sociétés par défaut
     * @return array[]
     */
    public function defaultSociety()
    {
        return [
            0 => [
                'name' => 'Technip',
                'description' => "Technip est une entreprise d'origine française présente dans le management de projets, 
                    l’ingénierie et la construction pour l’industrie de l’énergie (pétrole, gaz, éolien) 
                    mais aussi accessoirement de la chimie.",
                'picture' => 'assets/media/img/Technip.svg',
                'id_category' => 1
            ],
            1 => [
                'name' => 'Altran',
                'description' => 'Altran (renommée Capgemini Engineering depuis 2021) est une entreprise de conseil en ingénierie.
                    Elle a été fondée en France en 1982 par Alexis Kniazeff et Hubert Martigny.',
                'picture' => 'assets/media/img/Altran.png',
                'id_category' => 2
            ],
            2 => [
                'name' => 'Alten',
                'description' => "ALTEN est une multinationale française d'ingénierie et conseil en technologies et une entreprise
                    de services du numérique (ESN) créée en 1988. Elle est présente dans plus de vingt-huit pays et 
                    emploie 33 800 salariés dont une majorité de consultants, fin 2020.",
                'picture' => 'assets/media/img/Alten.png',
                'id_category' => 2
            ],
            3 => [
                'name' => 'Egis',
                'description' => "Egis est une entreprise d'ingénierie française présente dans les secteurs de l'aménagement, 
                    des infrastructures de transport, d’eau et du secteur de l'environnement. Egis travaille dans
                    l'exploitation routière et aéroportuaire.",
                'picture' => 'assets/media/img/Egis.png',
                'id_category' => 2
            ],
            4 => [
                'name' => 'Assystem',
                'description' => 'Assystem est un groupe spécialisé en ingénierie et gestion de projets d’infrastructures critiques
                    et complexes, pour de grands groupes industriels mondiaux, principalement dans le domaine du nucléaire. 
                    La société est cotée à la Bourse de Paris.',
                'picture' => 'assets/media/img/Assystem.png',
                'id_category' => 2
            ]
        ];
    }

    /**
     * Méthode qui permet de vérifier si une société existe dans la BDD
     * @param Int|null $id
     * @return mixed|object
     */
    public function checkExistSociety(Array $data){

        // Recherche sur la table société une société ayant des données déjà existantes

        foreach ($data as $key => $value){

            $society = $this->entityManager
                ->getRepository(Society::class)
                ->findOneBy([
                    $key => $value
                ]);

            if ($society){
                return [
                    'data' => $society,
                    'msg' => "Une société $value est déjà enregistrée en BDD"
                ];
            }

            return True;

        }
    }

    /**
     * Méthode qui permet d'enregistrer une société en BDD
     * Utilisée dans le CRUD Controller
     * @param array|null $data
     * @throws \Exception
     */
    public function saveSociety(Society $data){
        $timezone = new \DateTimeZone('Europe/Paris');
        $dateTime = new \DateTimeImmutable();

        $result = $this->checkExistSociety(['name' => $data->name]);

        $data->setName($data->name);
        $data->setDescription($data->description);
        $data->setCategory($data->category);
        $data->setCreatedAt($dateTime->setTimezone($timezone));

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * Méthode qui permet d'enregistrer une société en BDD
     * Utilisé dans la classe AutoModelCommand
     * @param array $data
     * @throws \Exception
     */
    public function saveSocietyAsArray(Array $data){

        $timezone = new \DateTimeZone('Europe/Paris');
        $dateTime = new \DateTimeImmutable();

        $result = $this->checkExistSociety(['name' => $data['name']]);

        if(!is_array($result)){
            //On enregistre les données que si cette donnée n'héxiste pas avec le même nom en BDD
            $society = new Society();

            $society->setName($data['name']);
            $society->setDescription($data['description']);
            $society->setPicture($data['picture']);
            $society->setCreatedAt($dateTime->setTimezone($timezone));

            $this->entityManager->persist($society);
            $this->entityManager->flush();
        }else{
            throw new \Exception($result['msg']);
        }
    }

    /**
     * Méthode qui permet d'envoyer sur le server et insérer en BDD une image
     * @param Society $society
     * @param $picture
     * @param String $folder
     * @param SluggerInterface $slugger
     * @return Society|void
     * @throws \Exception
     */
    public function uploadFile(Society $society, $picture, String $folder, SluggerInterface $slugger){
        if ($picture) {
            $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$picture->guessExtension();

            // Nous déplaçons le fichier dans le répertoire où sont stockées les images sous un nouveau nom.
            try {

                $picture->move($folder, $newFilename);
                $society->setPicture('assets/media/img/'.$newFilename);
                return $society;

            } catch (FileException) {
                throw new \Exception("Une erreur c'est produit pendant l'envoie du formulaire");
            }
        }
    }
}