<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // =========================
        // CATÉGORIES
        // =========================
        $categories = [];

        $nomsCategories = [
            'Relaxation',
            'Sommeil',
            'Énergie',
            'Confiance en soi'
        ];

        foreach ($nomsCategories as $nom) {
            $categorie = new Categorie();
            $categorie->setNom($nom);
            $categorie->setDescription('Pack bien-être ' . $nom);

            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // =========================
        // PRODUITS
        // =========================
        for ($i = 1; $i <= 10; $i++) {
            $produit = new Produit();

            $produit->setNom('Pack Bien-être ' . $i);
            $produit->setDescription('Description du pack ' . $i);
            $produit->setPrix(rand(20, 80));
            $produit->setStock(rand(5, 50));
            $produit->setImage('produit' . $i . '.jpg');

            // catégorie aléatoire
            $produit->setCategorie($categories[array_rand($categories)]);

            $manager->persist($produit);
        }

        // =========================
        // UTILISATEUR TEST
        // =========================
        $user = new Utilisateur();
        $user->setEmail('test@test.com');
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setRoles(['ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'password'
        );

        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $manager->flush();
    }
}
