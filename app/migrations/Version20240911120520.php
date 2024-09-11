<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240911120520 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_panier ADD panier_id INT NOT NULL, ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_panier ADD CONSTRAINT FK_21691B4F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE ligne_panier ADD CONSTRAINT FK_21691B44584665A FOREIGN KEY (product_id) REFERENCES produits (id)');
        $this->addSql('CREATE INDEX IDX_21691B4F77D927C ON ligne_panier (panier_id)');
        $this->addSql('CREATE INDEX IDX_21691B44584665A ON ligne_panier (product_id)');
        $this->addSql('ALTER TABLE panier ADD client_id INT NOT NULL, ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF219EB6921 FOREIGN KEY (client_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF26BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF219EB6921 ON panier (client_id)');
        $this->addSql('CREATE INDEX IDX_24CC0DF26BF700BD ON panier (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_panier DROP FOREIGN KEY FK_21691B4F77D927C');
        $this->addSql('ALTER TABLE ligne_panier DROP FOREIGN KEY FK_21691B44584665A');
        $this->addSql('DROP INDEX IDX_21691B4F77D927C ON ligne_panier');
        $this->addSql('DROP INDEX IDX_21691B44584665A ON ligne_panier');
        $this->addSql('ALTER TABLE ligne_panier DROP panier_id, DROP product_id');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF219EB6921');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF26BF700BD');
        $this->addSql('DROP INDEX IDX_24CC0DF219EB6921 ON panier');
        $this->addSql('DROP INDEX IDX_24CC0DF26BF700BD ON panier');
        $this->addSql('ALTER TABLE panier DROP client_id, DROP status_id');
    }
}
