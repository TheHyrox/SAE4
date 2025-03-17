<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250317142211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE administrateur (id INT AUTO_INCREMENT NOT NULL, id_uti_id INT NOT NULL, UNIQUE INDEX UNIQ_32EB52E81F666A98 (id_uti_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, id_statut_id INT NOT NULL, id_prod_id INT NOT NULL, id_uti_id INT NOT NULL, INDEX IDX_6EEAA67D76158423 (id_statut_id), INDEX IDX_6EEAA67DDF559605 (id_prod_id), INDEX IDX_6EEAA67D1F666A98 (id_uti_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contenu (id INT AUTO_INCREMENT NOT NULL, id_commande_id INT NOT NULL, id_produit_id INT NOT NULL, qte_produit_commande INT NOT NULL, num_produit_commande INT NOT NULL, INDEX IDX_89C2003F9AF8E3A3 (id_commande_id), INDEX IDX_89C2003FAABEFE2C (id_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, emetteur_id INT NOT NULL, destinataire_id INT NOT NULL, date_msg DATETIME NOT NULL, date_expi_msg DATETIME NOT NULL, contenu_msg VARCHAR(4096) NOT NULL, INDEX IDX_B6BD307F79E92E8C (emetteur_id), INDEX IDX_B6BD307FA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE producteur (id INT AUTO_INCREMENT NOT NULL, id_uti_id INT NOT NULL, prof_prod VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_7EDBEE101F666A98 (id_uti_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, id_prod_id INT NOT NULL, id_type_produit_id INT NOT NULL, id_unite_stock_id INT NOT NULL, id_unite_prix_id INT NOT NULL, nom_produit VARCHAR(50) NOT NULL, qte_produit INT NOT NULL, prix_produit_unitaire NUMERIC(15, 5) NOT NULL, INDEX IDX_29A5EC27DF559605 (id_prod_id), INDEX IDX_29A5EC27FB696B56 (id_type_produit_id), INDEX IDX_29A5EC277FCE4705 (id_unite_stock_id), INDEX IDX_29A5EC279006B234 (id_unite_prix_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE statut (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE typedeproduit (id INT AUTO_INCREMENT NOT NULL, desc_type_produit VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unite (id INT AUTO_INCREMENT NOT NULL, nom_unite VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, prenom_uti VARCHAR(50) NOT NULL, nom_uti VARCHAR(50) NOT NULL, mail_uti VARCHAR(50) NOT NULL, adr_uti VARCHAR(200) NOT NULL, pwd_uti VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E81F666A98 FOREIGN KEY (id_uti_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D76158423 FOREIGN KEY (id_statut_id) REFERENCES statut (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DDF559605 FOREIGN KEY (id_prod_id) REFERENCES producteur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1F666A98 FOREIGN KEY (id_uti_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE contenu ADD CONSTRAINT FK_89C2003F9AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE contenu ADD CONSTRAINT FK_89C2003FAABEFE2C FOREIGN KEY (id_produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F79E92E8C FOREIGN KEY (emetteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE producteur ADD CONSTRAINT FK_7EDBEE101F666A98 FOREIGN KEY (id_uti_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27DF559605 FOREIGN KEY (id_prod_id) REFERENCES producteur (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FB696B56 FOREIGN KEY (id_type_produit_id) REFERENCES typedeproduit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC277FCE4705 FOREIGN KEY (id_unite_stock_id) REFERENCES unite (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC279006B234 FOREIGN KEY (id_unite_prix_id) REFERENCES unite (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E81F666A98');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D76158423');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DDF559605');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D1F666A98');
        $this->addSql('ALTER TABLE contenu DROP FOREIGN KEY FK_89C2003F9AF8E3A3');
        $this->addSql('ALTER TABLE contenu DROP FOREIGN KEY FK_89C2003FAABEFE2C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F79E92E8C');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E');
        $this->addSql('ALTER TABLE producteur DROP FOREIGN KEY FK_7EDBEE101F666A98');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27DF559605');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27FB696B56');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC277FCE4705');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC279006B234');
        $this->addSql('DROP TABLE administrateur');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE contenu');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE producteur');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE statut');
        $this->addSql('DROP TABLE typedeproduit');
        $this->addSql('DROP TABLE unite');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
