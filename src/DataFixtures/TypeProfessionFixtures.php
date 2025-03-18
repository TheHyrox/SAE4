<?php
namespace App\DataFixtures;

use App\Entity\TypeProfession;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;

class TypeProfessionFixtures extends Fixture
{
    // Constants
    public const PROFESSION_AGRICULTEUR = 'profession_agriculteur';
    public const PROFESSION_VIGNERON = 'profession_vigneron';
    public const PROFESSION_MARAICHER = 'profession_maraicher';
    public const PROFESSION_APICULTEUR = 'profession_apiculteur';
    public const PROFESSION_ELEVEUR_VOLAILLE = 'profession_eleveur_volaille';
    public const PROFESSION_VITICULTEUR = 'profession_viticulteur';
    public const PROFESSION_PEPINIERISTE = 'profession_pepinieriste';

    public function load(ObjectManager $manager): void
    {
        // Configure to use assigned IDs instead of auto-increment
        $metadata = $manager->getClassMetadata(TypeProfession::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());

        $professions = [
            [self::PROFESSION_AGRICULTEUR, 'Agriculteur', 1],
            [self::PROFESSION_VIGNERON, 'Vigneron', 2],
            [self::PROFESSION_MARAICHER, 'Maraîcher', 3],
            [self::PROFESSION_APICULTEUR, 'Apiculteur', 4],
            [self::PROFESSION_ELEVEUR_VOLAILLE, 'Éleveur de volaille', 5],
            [self::PROFESSION_VITICULTEUR, 'Viticulteur', 6],
            [self::PROFESSION_PEPINIERISTE, 'Pépiniériste', 7],
        ];

        foreach ($professions as [$reference, $name, $id]) {
            $profession = new TypeProfession();
            $profession->setId($id); // You'll need to add this method
            $profession->setName($name);

            $manager->persist($profession);
            $this->addReference($reference, $profession);
        }

        $manager->flush();
    }
}