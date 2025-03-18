<?php

namespace App\DataFixtures;

use App\Entity\TypeProduct;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeProductFixtures extends Fixture
{
    public const TYPE_LEGUME = 'type_legume';
    public const TYPE_FRUIT = 'type_fruit';
    public const TYPE_VIANDE = 'type_viande';
    public const TYPE_FROMAGE = 'type_fromage';
    public const TYPE_VIN = 'type_vin';
    public const TYPE_MIEL = 'type_miel';
    public const TYPE_OEUF = 'type_oeuf';
    public const TYPE_PLANTE = 'type_plante';

    public function load(ObjectManager $manager): void
    {
        $types = [
            [self::TYPE_LEGUME, 'Légume'],
            [self::TYPE_FRUIT, 'Fruit'],
            [self::TYPE_VIANDE, 'Viande'],
            [self::TYPE_FROMAGE, 'Fromage'],
            [self::TYPE_VIN, 'Vin'],
            [self::TYPE_MIEL, 'Miel'],
            [self::TYPE_OEUF, 'Œuf'],
            [self::TYPE_PLANTE, 'Plante'],
        ];

        foreach ($types as [$reference, $name]) {
            $type = new TypeProduct();
            $type->setName($name);

            $manager->persist($type);
            $this->addReference($reference, $type);
        }

        $manager->flush();
    }
}