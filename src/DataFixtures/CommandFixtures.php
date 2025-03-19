<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\TypeStatusCommand;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Persistence\ObjectManager;

class CommandFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Get all products
        $products = $manager->getRepository(Product::class)->findAll();

        if (empty($products)) {
            throw new \Exception("No products found. Make sure ProductFixtures has been loaded first.");
        }

        // Group products by seller
        $producerProducts = [];
        foreach ($products as $product) {
            $userId = $product->getUser()->getId();
            if (!isset($producerProducts[$userId])) {
                $producerProducts[$userId] = [];
            }
            $producerProducts[$userId][] = $product;
        }

        if (empty($producerProducts)) {
            throw new \Exception("No producer products found. Check that product users are properly set.");
        }

        // Get all regular users with the ROLE_USER role without the ROLE_ADMIN role and without the findByRole method
        $customers = $manager->getRepository(User::class)->findByRole('ROLE_USER');

        if (empty($customers)) {
            throw new \Exception("No customers found. Make sure UserFixtures has been loaded with ROLE_CLIENT users.");
        }

        // Get status references
        $statusPending = $this->getReference(TypeStatusCommandFixtures::STATUS_PENDING, TypeStatusCommand::class);
        $statusReady = $this->getReference(TypeStatusCommandFixtures::STATUS_READY, TypeStatusCommand::class);
        $statusDelivered = $this->getReference(TypeStatusCommandFixtures::STATUS_DELIVERED, TypeStatusCommand::class);

        $statuses = [$statusPending, $statusReady, $statusDelivered];

        // Generate 30 commands
        for ($i = 0; $i < 30; $i++) {
            $command = new Command();

            // Select a random customer
            $customer = $customers[array_rand($customers)];
            $command->setCustomer($customer);  // Changed from setCustomer to setUser

            // Select a random producer
            $producerIds = array_keys($producerProducts);
            $producerId = $producerIds[array_rand($producerIds)];
            $availableProducts = $producerProducts[$producerId];

            // Select 1-5 random products from this producer
            $numProducts = rand(1, min(5, count($availableProducts)));
            $selectedIndices = array_rand($availableProducts, $numProducts);

            // If only one product is selected, convert to array
            if (!is_array($selectedIndices)) {
                $selectedIndices = [$selectedIndices];
            }

            // Add products to the command
            foreach ($selectedIndices as $index) {
                $product = $availableProducts[$index];
                $command->addProduct($product);
            }

            // Set command creation date (between 3 months ago and now)
            $date = new \DateTime();
            $date->modify('-' . rand(0, 90) . ' days');
            $command->setDate($date);  // Changed from setDate to setCreatedAt

            // Set random status with weighted probability
            $daysAgo = (new \DateTime())->diff($date)->days;
            if ($daysAgo > 45) {
                // Older commands are more likely to be delivered
                $status = $statuses[rand(0, 10) < 8 ? 2 : rand(0, 1)];
            } elseif ($daysAgo > 15) {
                // Medium-age commands are mixed
                $status = $statuses[rand(0, 2)];
            } else {
                // Recent commands are more likely to be new
                $status = $statuses[rand(0, 10) < 7 ? 0 : rand(1, 2)];
            }

            $command->setStatus($status);

            $manager->persist($command);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProductFixtures::class,
            TypeStatusCommandFixtures::class
        ];
    }
}