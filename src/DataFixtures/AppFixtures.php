<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Kit d\'hygiène recyclable',
                'price' => 24.99,
                'description' => 'Déodorant Nécessaire, une formule révolutionnaire composée exclusivement d\'ingrédients naturels pour une protection efficace et bienfaisante. ' .
                    'Chaque flacon de 50 ml renferme le secret d\'une fraîcheur longue durée, sans compromettre votre bien-être ni l\'environnement. Conçu avec soin, ce déodorant allie le pouvoir antibactérien des extraits de plantes aux vertus apaisantes des huiles essentielles, assurant une sensation de confort toute la journée. ' .
                    'Grâce à sa formule non irritante et respectueuse de votre peau, Nécessaire offre une alternative saine aux déodorants conventionnels, tout en préservant l\'équilibre naturel de votre corps',
                'shortDescription' => 'Pour une salle de bain éco-friendly',
                'image' => 'Rectangle1.jpg'
            ],
            [
                'name' => 'Shot Tropical',
                'price' => 4.50,
                'description' => 'Un mélange rafraîchissant de fruits tropicaux, pressés à froid pour préserver leurs nutriments et leur saveur naturelle. ' .
                    'Idéal pour un coup de boost énergétique tout au long de la journée.',
                'shortDescription' => 'Fruits frais, pressés à froid',
                'image' => 'Rectangle2.jpg'
            ],
            [
                'name' => 'Gourde en bois',
                'price' => 16.90,
                'description' => 'Une gourde en bois d’olivier de 50cl, élégante et écologique. ' .
                    'Gardez vos boissons fraîches tout en réduisant l\'utilisation du plastique.',
                'shortDescription' => '50cl, bois d’olivier',
                'image' => 'Rectangle3.jpg'
            ],
            [
                'name' => 'Disques Démaquillants x3',
                'price' => 19.90,
                'description' => 'Un ensemble de trois disques démaquillants réutilisables, conçus pour nettoyer votre peau en douceur tout en respectant l\'environnement. ' .
                    'Doux et parfaits pour un usage quotidien.',
                'shortDescription' => 'Solution efficace pour vous démaquiller en douceur ',
                'image' => 'Rectangle4.jpg'
            ],
            [
                'name' => 'Bougie Lavande & Patchouli',
                'price' => 32.60,
                'description' => 'Une bougie naturelle fabriquée à partir de cire éco-responsable, parfumée à la lavande apaisante et au patchouli. ' .
                    'Idéale pour créer une ambiance relaxante à la maison.',
                'shortDescription' => 'Cire naturelle',
                'image' => 'Rectangle5.jpg'
            ],
            [
                'name' => 'Brosse à dent',
                'price' => 5.40,
                'description' => 'Une brosse à dents biodégradable en bois de hêtre rouge, provenant de forêts gérées durablement. ' .
                    'Douce pour vos dents et respectueuse de l\'environnement.',
                'shortDescription' => 'Bois de hêtre rouge issu de forêts gérées durablement',
                'image' => 'Rectangle6.jpg'
            ],
            [
                'name' => 'Kit couvert en bois',
                'price' => 12.30,
                'description' => 'Un set de couverts en bois d’olivier, parfait pour une expérience culinaire éco-responsable. ' .
                    'Livré avec un étui pratique pour plus de commodité.',
                'shortDescription' => 'Revêtement Bio en olivier & sac de transport',
                'image' => 'Rectangle7.jpg'
            ],
            [
                'name' => 'Nécessaire, déodorant Bio',
                'price' => 8.50,
                'description' => 'Un déodorant bio de 50ml au parfum rafraîchissant d’eucalyptus. ' .
                    'Doux pour votre peau, il offre une fraîcheur longue durée sans produits chimiques nocifs.',
                'shortDescription' => '50ml déodorant à l’eucalyptus',
                'image' => 'Rectangle8.jpg'
            ],
            [
                'name' => 'Savon Bio',
                'price' => 18.90,
                'description' => 'Un savon bio fait main, avec un mélange apaisant de thé, orange et girofle. ' .
                    'Idéal pour tous les types de peau et fabriqué à partir d\'ingrédients naturels.',
                'shortDescription' => 'Thé, Orange & Girofle',
                'image' => 'Rectangle9.jpg'
            ],
        ];

        // Create and persist each product
        foreach ($products as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);
            $product->setShortDescription($data['shortDescription']);
            $product->setImage($data['image']);

            // Persist the product
            $manager->persist($product);
        }

        $manager->flush();
    }
}
