<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $producteurs = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_PRODUCTEUR%')
            ->getQuery()
            ->getResult();

        $typeProducts = [
            TypeProductFixtures::TYPE_LEGUME => [
                ['Carotte', 2.50, 'Carottes bio fraîches', 50],
                ['Pomme de terre', 1.80, 'Pommes de terre de montagne', 100],
                ['Tomate', 3.20, 'Tomates grappes', 30],
                ['Salade', 1.50, 'Salade verte', 20],
            ],
            TypeProductFixtures::TYPE_FRUIT => [
                ['Pomme', 2.80, 'Pommes Golden', 80],
                ['Poire', 3.50, 'Poires Williams', 60],
                ['Fraise', 4.90, 'Fraises Gariguette', 25],
                ['Abricot', 4.20, 'Abricots du Roussillon', 35],
            ],
            TypeProductFixtures::TYPE_VIANDE => [
                ['Poulet', 9.90, 'Poulet fermier', 15],
                ['Bœuf', 15.50, 'Bœuf charolais', 20],
                ['Agneau', 18.90, 'Agneau de pré-salé', 10],
            ],
            TypeProductFixtures::TYPE_FROMAGE => [
                ['Comté', 12.50, 'Comté affiné 18 mois', 25],
                ['Chèvre', 3.80, 'Fromage de chèvre frais', 30],
                ['Camembert', 4.50, 'Camembert au lait cru', 20],
            ],
            TypeProductFixtures::TYPE_VIN => [
                ['Bordeaux rouge', 8.50, 'Bordeaux AOP', 50],
                ['Chablis', 12.90, 'Chablis Premier Cru', 30],
                ['Champagne', 25.00, 'Champagne Brut', 20],
            ],
            TypeProductFixtures::TYPE_MIEL => [
                ['Miel de lavande', 9.90, 'Miel de lavande de Provence', 15],
                ['Miel d\'acacia', 8.50, 'Miel d\'acacia des Cévennes', 20],
                ['Miel de châtaignier', 7.80, 'Miel de châtaignier corsé', 18],
            ],
            TypeProductFixtures::TYPE_OEUF => [
                ['Œufs bio', 4.50, 'Œufs bio élevés en plein air', 200],
                ['Œufs fermiers', 3.80, 'Œufs fermiers de poules élevées en liberté', 150],
            ],
            TypeProductFixtures::TYPE_PLANTE => [
                ['Lavande', 6.50, 'Plants de lavande', 40],
                ['Romarin', 4.90, 'Plants de romarin', 35],
                ['Thym', 4.50, 'Plants de thym', 30],
            ],
        ];

        // Associate product types with professions for more realistic data
        $professionProductMapping = [
            TypeProfessionFixtures::PROFESSION_AGRICULTEUR => [
                TypeProductFixtures::TYPE_LEGUME,
                TypeProductFixtures::TYPE_FRUIT
            ],
            TypeProfessionFixtures::PROFESSION_VIGNERON => [
                TypeProductFixtures::TYPE_VIN
            ],
            TypeProfessionFixtures::PROFESSION_MARAICHER => [
                TypeProductFixtures::TYPE_LEGUME,
                TypeProductFixtures::TYPE_FRUIT
            ],
            TypeProfessionFixtures::PROFESSION_APICULTEUR => [
                TypeProductFixtures::TYPE_MIEL
            ],
            TypeProfessionFixtures::PROFESSION_ELEVEUR_VOLAILLE => [
                TypeProductFixtures::TYPE_VIANDE,
                TypeProductFixtures::TYPE_OEUF
            ],
            TypeProfessionFixtures::PROFESSION_VITICULTEUR => [
                TypeProductFixtures::TYPE_VIN
            ],
            TypeProfessionFixtures::PROFESSION_PEPINIERISTE => [
                TypeProductFixtures::TYPE_PLANTE
            ],
        ];

        foreach ($producteurs as $producteur) {
            // Get the profession of the producer
            $profession = null;
            if (method_exists($producteur, 'getTypeProfession')) {
                $profession = $producteur->getTypeProfession();
            } elseif (method_exists($producteur, 'getProfession')) {
                $profession = $producteur->getProfession();
            }

            // If profession is null, assign random product types
            $relevantTypes = [];
            if ($profession) {
                // Find the profession reference name by comparing names
                $professionName = $profession->getName();
                $professionRef = null;
                foreach ([
                             TypeProfessionFixtures::PROFESSION_AGRICULTEUR => 'Agriculteur',
                             TypeProfessionFixtures::PROFESSION_VIGNERON => 'Vigneron',
                             TypeProfessionFixtures::PROFESSION_MARAICHER => 'Maraîcher',
                             TypeProfessionFixtures::PROFESSION_APICULTEUR => 'Apiculteur',
                             TypeProfessionFixtures::PROFESSION_ELEVEUR_VOLAILLE => 'Éleveur de volaille',
                             TypeProfessionFixtures::PROFESSION_VITICULTEUR => 'Viticulteur',
                             TypeProfessionFixtures::PROFESSION_PEPINIERISTE => 'Pépiniériste'
                         ] as $ref => $name) {
                    if ($professionName === $name) {
                        $professionRef = $ref;
                        break;
                    }
                }

                if ($professionRef && isset($professionProductMapping[$professionRef])) {
                    $relevantTypes = $professionProductMapping[$professionRef];
                }
            }

            // If no relevant types found, use all types
            if (empty($relevantTypes)) {
                $relevantTypes = array_keys($typeProducts);
            }

            // Create 3-7 products for each producer
            $numProducts = rand(3, 7);
            for ($i = 0; $i < $numProducts; $i++) {
                // Select a random type from the relevant types
                $typeRef = $relevantTypes[array_rand($relevantTypes)];
                $products = $typeProducts[$typeRef];

                // Select a random product from the type
                $productData = $products[array_rand($products)];
                [$name, $price, $description, $stock] = $productData;

                $product = new Product();
                $product->setName($name);
                $product->setPrice($price + (rand(-50, 100) / 100)); // Add some price variation
                $product->setQuantity($stock + rand(-10, 20)); // Add some stock variation
                $product->setUser($producteur);

                // Get type product reference
                $typeProduct = $this->getReference($typeRef, 'App\Entity\TypeProduct');
                $product->setTypeProduct($typeProduct);

                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            TypeProductFixtures::class
        ];
    }
}