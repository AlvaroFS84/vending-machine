<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204120554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inserted_coin DROP FOREIGN KEY FK_65B59888A13C630');
        $this->addSql('DROP INDEX UNIQ_65B59888A13C630 ON inserted_coin');
        $this->addSql('ALTER TABLE inserted_coin CHANGE coin_id_id coin_id INT NOT NULL');
        $this->addSql('ALTER TABLE inserted_coin ADD CONSTRAINT FK_65B5988884BBDA7 FOREIGN KEY (coin_id) REFERENCES coin (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65B5988884BBDA7 ON inserted_coin (coin_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inserted_coin DROP FOREIGN KEY FK_65B5988884BBDA7');
        $this->addSql('DROP INDEX UNIQ_65B5988884BBDA7 ON inserted_coin');
        $this->addSql('ALTER TABLE inserted_coin CHANGE coin_id coin_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE inserted_coin ADD CONSTRAINT FK_65B59888A13C630 FOREIGN KEY (coin_id_id) REFERENCES coin (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65B59888A13C630 ON inserted_coin (coin_id_id)');
    }
}
