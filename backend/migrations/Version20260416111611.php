<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416111611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE barbershop_bookings (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , service_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , stylist_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , start_time DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , end_time DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , status VARCHAR(255) NOT NULL, customer_name VARCHAR(255) NOT NULL, customer_contact VARCHAR(255) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_1731D1E2ED5CA9E6 FOREIGN KEY (service_id) REFERENCES barbershop_services (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1731D1E24066877A FOREIGN KEY (stylist_id) REFERENCES barbershop_stylists (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1731D1E2ED5CA9E6 ON barbershop_bookings (service_id)');
        $this->addSql('CREATE INDEX IDX_1731D1E24066877A ON barbershop_bookings (stylist_id)');
        $this->addSql('CREATE TABLE barbershop_businesses (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE barbershop_opening_hours (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , business_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , day_of_week INTEGER NOT NULL, open_from VARCHAR(5) NOT NULL, open_to VARCHAR(5) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_2B84B881A89DB457 FOREIGN KEY (business_id) REFERENCES barbershop_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2B84B881A89DB457 ON barbershop_opening_hours (business_id)');
        $this->addSql('CREATE TABLE barbershop_services (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , business_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, duration_minutes INTEGER NOT NULL, price NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_1E860CBEA89DB457 FOREIGN KEY (business_id) REFERENCES barbershop_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_1E860CBEA89DB457 ON barbershop_services (business_id)');
        $this->addSql('CREATE TABLE barbershop_stylists (id CHAR(36) NOT NULL --(DC2Type:uuid)
        , business_id CHAR(36) NOT NULL --(DC2Type:uuid)
        , name VARCHAR(255) NOT NULL, photo_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_D0476444A89DB457 FOREIGN KEY (business_id) REFERENCES barbershop_businesses (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D0476444A89DB457 ON barbershop_stylists (business_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE barbershop_bookings');
        $this->addSql('DROP TABLE barbershop_businesses');
        $this->addSql('DROP TABLE barbershop_opening_hours');
        $this->addSql('DROP TABLE barbershop_services');
        $this->addSql('DROP TABLE barbershop_stylists');
    }
}
