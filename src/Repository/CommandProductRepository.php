<?php

namespace App\Repository;

use App\Entity\Command;
use App\Entity\Product;
use Doctrine\DBAL\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, null);
    }
    
    public function getProductQuantity(Command $command, Product $product): ?int
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            SELECT quantity
            FROM command_product
            WHERE command_id = :commandId
            AND product_id = :productId
        ';
        
        $result = $conn->executeQuery($sql, [
            'commandId' => $command->getId(),
            'productId' => $product->getId(),
        ]);
        
        $data = $result->fetchAssociative();
        
        return $data ? (int)$data['quantity'] : null;
    }
    
    public function updateProductQuantity(Command $command, Product $product, int $quantity): void
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $sql = '
            UPDATE command_product
            SET quantity = :quantity
            WHERE command_id = :commandId
            AND product_id = :productId
        ';
        
        $conn->executeStatement($sql, [
            'quantity' => $quantity,
            'commandId' => $command->getId(),
            'productId' => $product->getId(),
        ]);
    }
}