<?php

namespace App\DataFixtures;

use App\Entity\TypeStatusCommand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeStatusCommandFixtures extends Fixture
{
    public const STATUS_PENDING = 'status_pending';
    public const STATUS_READY = 'status_ready';
    public const STATUS_SHIPPED = 'status_shipped';
    public const STATUS_DELIVERED = 'status_delivered';
    public const STATUS_CANCELLED = 'status_cancelled';

    public function load(ObjectManager $manager): void
    {
        $statusPending = new TypeStatusCommand();
        $statusPending->setName('En traitement');
        $manager->persist($statusPending);
        $this->addReference(self::STATUS_PENDING, $statusPending);

        $statusProcessing = new TypeStatusCommand();
        $statusProcessing->setName('Prête');
        $manager->persist($statusProcessing);
        $this->addReference(self::STATUS_READY, $statusProcessing);

        $statusCompleted = new TypeStatusCommand();
        $statusCompleted->setName('Expédiée');
        $manager->persist($statusCompleted);
        $this->addReference(self::STATUS_SHIPPED, $statusCompleted);

        $statusCancelled = new TypeStatusCommand();
        $statusCancelled->setName('Livrée');
        $manager->persist($statusCancelled);
        $this->addReference(self::STATUS_DELIVERED, $statusCancelled);

        $statusCancelled = new TypeStatusCommand();
        $statusCancelled->setName('Annulée');
        $manager->persist($statusCancelled);
        $this->addReference(self::STATUS_CANCELLED, $statusCancelled);

        $manager->flush();
    }
}