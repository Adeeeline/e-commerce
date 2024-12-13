<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Générer des catégories fictives
        $categories = [];
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName($faker->unique()->word()); // Nom unique pour la catégorie
            $manager->persist($category);
            $categories[] = $category;
        }

        // Générer des sous-catégories fictives
        $subCategories = [];
        foreach ($categories as $category) {
            for ($i = 0; $i < 3; $i++) { // Chaque catégorie aura 3 sous-catégories
                $subCategory = new SubCategory();
                $subCategory->setName($faker->unique()->word());
                $subCategory->setCategory($category); // Associer à une catégorie
                $manager->persist($subCategory);
                $subCategories[] = $subCategory;
            }
        }

        // Générer des produits fictifs
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->unique()->words(3, true)); // Nom du produit
            $product->setDescription($faker->paragraph); // Description
            $product->setPrice($faker->numberBetween(10, 100)); // Prix
            $product->setStock($faker->numberBetween(1, 500)); // Stock
            $product->setImage('default.jpg'); // Image par défaut
            $product->setReleaseDate($faker->dateTimeBetween('-1 years', 'now')); // Date de sortie aléatoire

            // Ajouter des sous-catégories au produit
            $randomSubCategories = $faker->randomElements($subCategories, $faker->numberBetween(1, 3));
            foreach ($randomSubCategories as $subCategory) {
                $product->addSubCategory($subCategory);
            }

            $manager->persist($product);
        }

        // Sauvegarder toutes les données
        $manager->flush();
    }
}
