<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Create Faker instance
        $faker = Faker::create();

        // Create and persist 10 fake products
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName($faker->word)
                    ->setPrice($faker->randomFloat(2, 5, 100))  // Price between 5 and 100
                    ->setDescription($faker->text)
                    ->setShortDescription($faker->sentence)
                    ->setImage($faker->imageUrl());

            $manager->persist($product);
        }

        // Flush to save the changes to the database
        $manager->flush();
    }
}
