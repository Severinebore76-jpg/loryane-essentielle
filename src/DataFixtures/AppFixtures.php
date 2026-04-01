<?php

namespace App\DataFixtures;

use App\Entity\TypePack;
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
        // TYPE PACK
        // =========================
        $typePacks = [];

        $dataTypePacks = [
            ['Mini Packs - Pépites Bien-Être', 'mini-packs'],
            ['Packs Classiques - Bien-être au Quotidien', 'packs-classiques'],
            ['Moments Précieux', 'moments-précieux'],
            ['Moments festifs', 'moments-festifs'],
            ['Saisons', 'saisons'],
            ['Haut de gamme', 'haut-de-gamme'],
            ['Sur mesure', 'sur-mesure'],
        ];

        foreach ($dataTypePacks as [$nom, $slug]) {
            $typePack = new TypePack();
            $typePack->setNom($nom);
            $typePack->setSlug($slug);

            $manager->persist($typePack);
            $typePacks[] = $typePack;
        }

        // =========================
        // CATÉGORIES (GLOBALES)
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

            // slug UNIQUE global
            $slug = strtolower(str_replace(' ', '-', $nom));
            $categorie->setSlug($slug);

            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        // =========================
        // PRODUITS
        // =========================
        for ($i = 1; $i <= 10; $i++) {

            $produit = new Produit();

            // 👉 nom marketing (important)
            $produit->setNom('Pack Sérénité ' . $i);
            $produit->setDescription('Description du pack ' . $i);
            $produit->setPrix(rand(20, 80));
            $produit->setStock(rand(5, 50));
            $produit->setImage('produit' . $i . '.jpg');

            // 🔹 typePack aléatoire
            $typePack = $typePacks[array_rand($typePacks)];
            $produit->setTypePack($typePack);

            // 🔹 catégorie globale aléatoire
            $produit->setCategorie(
                $categories[array_rand($categories)]
            );

            $manager->persist($produit);
        }

        // =========================
        // UTILISATEUR TEST 1
        // =========================
        $user = new Utilisateur();
        $user->setEmail('test@test.com');
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setRoles(['ROLE_USER']);

        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password')
        );

        $manager->persist($user);

        // =========================
        // UTILISATEUR TEST 2
        // =========================
        $user2 = new Utilisateur();
        $user2->setEmail('test2@test.com');
        $user2->setNom('Second');
        $user2->setPrenom('User');
        $user2->setRoles(['ROLE_USER']);

        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'password')
        );

        $manager->persist($user2);

        $manager->flush();
    }
}
