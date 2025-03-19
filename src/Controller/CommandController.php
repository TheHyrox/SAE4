<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\TypeStatusCommand;
use App\Repository\CommandProductRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CommandController extends AbstractController
{
    private string $timezone;
    private CommandProductRepository $commandProductRepository;
    private Connection $connection;

    public function __construct(
        string $appTimezone = 'Europe/Paris',
        EntityManagerInterface $entityManager
    ) {
        $this->timezone = $appTimezone;
        $this->connection = $entityManager->getConnection();
    }

    #[Route('/command/create', name: 'app_command_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get user
        $user = $this->getUser();
        
        // Get cart from session
        $session = $request->getSession();
        $cart = $session->get('cart', []);
        
        // Check if cart is not empty
        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_producteur', [
                'id' => $request->request->get('producer_id')
            ]);
        }
        
        // Create new command
        $command = new Command();
        $now = new \DateTime('now', new \DateTimeZone($this->timezone));
        $command->setCustomer($user);
        $command->setDate($now);
        
        // Set status "En traitement" (ID 1)
        $status = $entityManager->getRepository(TypeStatusCommand::class)->find(1);
        $command->setStatus($status);
        
        // Persist command first to get an ID
        $entityManager->persist($command);
        $entityManager->flush();
        
        // Add products to command with quantities
        foreach ($cart as $productId => $item) {
            $product = $entityManager->getRepository(Product::class)->find($productId);
            if ($product) {
                $command->addProduct($product);
                
                // Insert directly into the junction table with quantity
                $this->connection->executeStatement(
                    'UPDATE command_product SET quantity = :quantity WHERE command_id = :commandId AND product_id = :productId',
                    [
                        'quantity' => $item['quantity'],
                        'commandId' => $command->getId(),
                        'productId' => $product->getId(),
                    ]
                );
            }
        }
        
        // Flush again to ensure all changes are saved
        $entityManager->flush();
        
        // Clear cart
        $session->remove('cart');
        
        // Add success message
        $this->addFlash('success', 'Votre commande a été créée avec succès !');
        
        // Redirect to my commands page
        return $this->redirectToRoute('app_my_commands');
    }
    
    #[Route('/my-commands', name: 'app_my_commands')]
    public function myCommands(EntityManagerInterface $entityManager): Response
    {
        // Get user
        $user = $this->getUser();
        
        // Get user commands
        $commands = $entityManager->getRepository(Command::class)
            ->findBy(['customer' => $user], ['date' => 'DESC']);
        
        // Get quantities for each product in each command
        $commandsData = [];
        foreach ($commands as $command) {
            $productsData = [];
            foreach ($command->getProducts() as $product) {
                // Get quantity from junction table
                $stmt = $this->connection->executeQuery(
                    'SELECT quantity FROM command_product WHERE command_id = :commandId AND product_id = :productId',
                    [
                        'commandId' => $command->getId(),
                        'productId' => $product->getId(),
                    ]
                );
                $quantity = $stmt->fetchOne();
                
                $productsData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product->getPrice() * $quantity
                ];
            }
            
            $commandsData[] = [
                'command' => $command,
                'products' => $productsData
            ];
        }
        
        return $this->render('command/my_commands.html.twig', [
            'commandsData' => $commandsData
        ]);
    }
}