<?php

namespace App\Entity;

use Doctrine\ORM\EntityManagerInterface;

class SocietyClass
{

    public $em;

    /*
     * =============================================
     *      Définition du constructeur de la classe
     * =============================================
     */
    public function __construct(EntityManagerInterface $entityManager = null){
        $this->em = $entityManager;
    }

    /*
     * =============================================
     *      Définition des setters de la classe
     * =============================================
     */

    /**
     * @param EntityManagerInterface $em
     */
    public function setEm(EntityManagerInterface $em)
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
     * Méthode qui contient les sociétés par défaut
     * @return array[]
     */
    public function defaultSociety()
    {
        return [
            0 => [
                'name' => 'Technip',
                'desc' => "Technip est une entreprise d'origine française présente dans le management de projets, 
                    l’ingénierie et la construction pour l’industrie de l’énergie (pétrole, gaz, éolien) 
                    mais aussi accessoirement de la chimie.",
                'img' => 'https://www.technipfmc.com/assets/img/logo.svg',
                'id_category' => 1
            ],
            1 => [
                'name' => 'Altran',
                'desc' => 'Altran (renommée Capgemini Engineering depuis 2021) est une entreprise de conseil en ingénierie.
                    Elle a été fondée en France en 1982 par Alexis Kniazeff et Hubert Martigny.',
                'img' => 'https://www.cycl-op.org/data/sources/users/63/20200615142622-altranpart-of-capgemini.jpg',
                'id_category' => 2
            ],
            2 => [
                'name' => 'Alten',
                'desc' => "ALTEN est une multinationale française d'ingénierie et conseil en technologies et une entreprise
                    de services du numérique (ESN) créée en 1988. Elle est présente dans plus de vingt-huit pays et 
                    emploie 33 800 salariés dont une majorité de consultants, fin 2020.",
                'img' => 'https://www.aerocontact.com/actualite-aeronautique-spatiale/images/AERO20190725124819.gif',
                'id_category' => 2
            ],
            3 => [
                'name' => 'Egis',
                'desc' => "Egis est une entreprise d'ingénierie française présente dans les secteurs de l'aménagement, 
                    des infrastructures de transport, d’eau et du secteur de l'environnement. Egis travaille dans
                    l'exploitation routière et aéroportuaire.",
                'img' => 'https://upload.wikimedia.org/wikipedia/fr/5/5b/Logo-egis.gif',
                'id_category' => 2
            ],
            4 => [
                'name' => 'Assystem',
                'desc' => 'Assystem est un groupe spécialisé en ingénierie et gestion de projets d’infrastructures critiques
                    et complexes, pour de grands groupes industriels mondiaux, principalement dans le domaine du nucléaire. 
                    La société est cotée à la Bourse de Paris.',
                'img' => 'https://www.assystem.com/wp-content/themes/assystem-corpo/dist/images/icons/logo-big.png',
                'id_category' => 2
            ]
        ];
    }

    /**
     * Méthode qui permet de vérifier si une société existe dans la BDD
     * @param Int|null $id
     * @return mixed|object
     */
    public function checkExistSociety(Array $data = null){

        // Recherche sur la table société une société ayant des données déjà existantes
        $society = $this->em
            ->getRepository(Society::class)
            ->findBy([
                'name' => $data['name']
            ]);

        if (!$society) {
            // L'enregistrement n'existe pas
            return 0;
        }

        // L'enregistrement existe
        return 1;
    }

    /**
     * Méthode qui permet d'enregistrer une société en BDD
     * @param array|null $data
     */
    public function saveSociety(Array $data = null){

        if (!$this->checkExistSociety($data)) {
            $timezone = new \DateTimeZone('Europe/Paris');
            $dateTime = new \DateTimeImmutable();

            $society = new Society();

            $society->setName($data['name']);
            $society->setDescription($data['desc']);
            $society->setImg($data['img']);
            $society->setCreatedAt($dateTime->setTimezone($timezone));

            $this->em->persist($society);
            $this->em->flush();

        }else{
            throw new \Exception('Une société existe déjà avec le nom: '. $data['name']);
        }

    }
}