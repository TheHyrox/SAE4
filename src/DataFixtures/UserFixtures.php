<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setName('User');
        $admin->setaddress('1 rue de la Paix');
        $admin->setRoles(['ROLE_USER', 'ROLE_PRODUCTEUR', 'ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password123'));
        $manager->persist($admin);

        // Create 10 producer users with different professions
        $professionReferences = [
            TypeProfessionFixtures::PROFESSION_AGRICULTEUR,
            TypeProfessionFixtures::PROFESSION_VIGNERON,
            TypeProfessionFixtures::PROFESSION_MARAICHER,
            TypeProfessionFixtures::PROFESSION_APICULTEUR,
            TypeProfessionFixtures::PROFESSION_ELEVEUR_VOLAILLE,
            TypeProfessionFixtures::PROFESSION_VITICULTEUR,
            TypeProfessionFixtures::PROFESSION_PEPINIERISTE
        ];

        $cities = ['Paris', 'Lyon', 'Marseille', 'Bordeaux', 'Lille', 'Strasbourg', 'Nantes', 'Toulouse', 'Nice', 'Rennes'];

        for ($i = 1; $i <= 10; $i++) {
            $producteur = new User();
            $producteur->setEmail("producteur{$i}@example.com");
            $producteur->setFirstName("Prénom{$i}");
            $producteur->setName("Nom{$i}");
            $producteur->setaddress($cities[$i - 1]);
            $producteur->setRoles(['ROLE_USER', 'ROLE_PRODUCTEUR']);
            $producteur->setPassword($this->passwordHasher->hashPassword($producteur, 'password123'));

            // Assign a profession
            $professionReference = $professionReferences[$i % count($professionReferences)];

            // Use the correct getReference method with the second parameter as the class name
            // The second parameter is the expected class name of the referenced object
            $profession = $this->getReference($professionReference, 'App\Entity\TypeProfession');

            // If your User entity has a setProfession method, use it here
            if (method_exists($producteur, 'setProfession')) {
                $producteur->setProfession($profession);
            } else if (method_exists($producteur, 'setTypeProfession')) {
                $producteur->setTypeProfession($profession);
            }

            $manager->persist($producteur);
        }

        // Create a regular customer user
        $customer = new User();
        $customer->setEmail('customer@example.com');
        $customer->setFirstName('Customer');
        $customer->setName('User');
        $customer->setaddress('1 rue de la Paix');
        $customer->setRoles(['ROLE_USER']);
        $customer->setPassword($this->passwordHasher->hashPassword($customer, 'password123'));
        $manager->persist($customer);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TypeProfessionFixtures::class,
        ];
    }
}