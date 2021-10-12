# mini-annuaire-professionnel
TP Final Module Symfony EPSI Bordeaux B2 2020-2021

Monsieur,

Comme convenu avec vous lors de notre dernier cours, vous trouverez sur le repos GitHub de mon projet Symfony afin de réaliser un mini-annuaire professionnel.

Afin de vous faciliter la pris en main de l'ajout des données en BDD. J'ai rajouté une commande supplémentaire permettant de remplir automatiquement cette dernière avec des données par défaut.  ( php bin/console insert:database )

En en souhaitant bonne réception et restant à votre disposition.

Bien à vous.

#Objectif : créer un mini-annuaire professionnel (3 pages) et un espace admin pour le CRUD d'une société
MCD : 
- société : nom de la société, date de création, image principale (picture), description,
- catégorie : nom
Une société est liée à une catégorie, une catégorie est liée à plusieurs sociétés.
1ère page : la page principale de l'annuaire doit contenir :
- header :
 - des liens vers les différentes catégories de société (cf 2ème page)
 - un système de recherche de société par son nom
- une liste de toutes les sociétés, avec : nom de la société, une image principale + lien vers une page détaillant la société
2ème page : la page d'une catégorie doit contenir :
- header :
 - des liens vers les différentes catégories de société
 - un système de recherche de société par son nom
- la liste des sociétés de la catégorie (titre + image)
3ème page : la page d'une société doit contenir :
- header :
 - des liens vers les différentes catégories de société
 - un système de recherche de société par son nom
- le nom de la société
- l'image principale
- la date de création
- la description de la société
- un lien retour vers la page principale de l'annuaire
