<?php

namespace App\DataFixtures;

use App\Entity\TypeProfession;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeProfessionFixtures extends Fixture
{
    public const PROFESSION_AGRICULTEUR = 'profession_agriculteur';
    public const PROFESSION_VIGNERON = 'profession_vigneron';
    public const PROFESSION_MARAICHER = 'profession_maraicher';
    public const PROFESSION_APICULTEUR = 'profession_apiculteur';
    public const PROFESSION_ELEVEUR_VOLAILLE = 'profession_eleveur_volaille';
    public const PROFESSION_VITICULTEUR = 'profession_viticulteur';
    public const PROFESSION_PEPINIERISTE = 'profession_pepinieriste';

    public function load(ObjectManager $manager): void
    {
        $professions = [
            [self::PROFESSION_AGRICULTEUR, 'Agriculteur'],
            [self::PROFESSION_VIGNERON, 'Vigneron'],
            [self::PROFESSION_MARAICHER, 'Maraîcher'],
            [self::PROFESSION_APICULTEUR, 'Apiculteur'],
            [self::PROFESSION_ELEVEUR_VOLAILLE, 'Éleveur de volaille'],
            [self::PROFESSION_VITICULTEUR, 'Viticulteur'],
            [self::PROFESSION_PEPINIERISTE, 'Pépiniériste'],
        ];

        foreach ($professions as [$reference, $name]) {
            $profession = new TypeProfession();
            $profession->setName($name);

            $manager->persist($profession);
            $this->addReference($reference, $profession);
        }

        $manager->flush();
    }
}