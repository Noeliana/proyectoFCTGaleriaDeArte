<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250508000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea tabla subcategory y su relación con category e image';
    }

    public function up(Schema $schema): void
    {
        // Crear tabla subcategory
        $this->addSql('
            CREATE TABLE subcategory (
                id INT AUTO_INCREMENT NOT NULL,
                category_id INT DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                INDEX IDX_SUBCATEGORY_CATEGORY (category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');

        // Añadir clave foránea a category
        $this->addSql('
            ALTER TABLE subcategory 
            ADD CONSTRAINT FK_SUBCATEGORY_CATEGORY 
            FOREIGN KEY (category_id) 
            REFERENCES category (id)
        ');

        // Añadir columna sub_category_id a la tabla image
        $this->addSql('
            ALTER TABLE image 
            ADD sub_category_id INT DEFAULT NULL,
            ADD CONSTRAINT FK_IMAGE_SUBCATEGORY 
            FOREIGN KEY (sub_category_id) 
            REFERENCES subcategory (id),
            ADD INDEX IDX_IMAGE_SUBCATEGORY (sub_category_id)
        ');
    }

    public function down(Schema $schema): void
    {
        // Eliminar relaciones
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_IMAGE_SUBCATEGORY');
        $this->addSql('ALTER TABLE subcategory DROP FOREIGN KEY FK_SUBCATEGORY_CATEGORY');

        // Eliminar columna de image
        $this->addSql('ALTER TABLE image DROP INDEX IDX_IMAGE_SUBCATEGORY');
        $this->addSql('ALTER TABLE image DROP sub_category_id');

        // Eliminar tabla
        $this->addSql('DROP TABLE subcategory');
    }
}
