<?php

namespace Claroline\CoreBundle\Migrations\pdo_sqlite;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2013/12/09 05:04:25
 */
class Version20131209170424 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            DROP INDEX UNIQ_D902854577153098
        ");
        $this->addSql("
            DROP INDEX UNIQ_D90285452B6FCFB2
        ");
        $this->addSql("
            DROP INDEX IDX_D9028545A76ED395
        ");
        $this->addSql("
            DROP INDEX IDX_D9028545727ACA70
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__claro_workspace AS
            SELECT id,
            parent_id,
            user_id,
            name,
            code,
            is_public,
            displayable,
            guid,
            self_registration,
            self_unregistration,
            discr,
            lft,
            lvl,
            rgt,
            root
            FROM claro_workspace
        ");
        $this->addSql("
            DROP TABLE claro_workspace
        ");
        $this->addSql("
            CREATE TABLE claro_workspace (
                id INTEGER NOT NULL,
                parent_id INTEGER DEFAULT NULL,
                user_id INTEGER DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(255) NOT NULL,
                guid VARCHAR(255) NOT NULL,
                discr VARCHAR(255) NOT NULL,
                lft INTEGER DEFAULT NULL,
                lvl INTEGER DEFAULT NULL,
                rgt INTEGER DEFAULT NULL,
                root INTEGER DEFAULT NULL,
                is_public BOOLEAN NOT NULL,
                displayable BOOLEAN NOT NULL,
                self_registration BOOLEAN NOT NULL,
                self_unregistration BOOLEAN NOT NULL,
                PRIMARY KEY(id),
                CONSTRAINT FK_D9028545727ACA70 FOREIGN KEY (parent_id)
                REFERENCES claro_workspace (id)
                ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_D9028545A76ED395 FOREIGN KEY (user_id)
                REFERENCES claro_user (id)
                ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO claro_workspace (
                id, parent_id, user_id, name, code,
                is_public, displayable, guid, self_registration,
                self_unregistration, discr, lft,
                lvl, rgt, root
            )
            SELECT id,
            parent_id,
            user_id,
            name,
            code,
            is_public,
            displayable,
            guid,
            self_registration,
            self_unregistration,
            discr,
            lft,
            lvl,
            rgt,
            root
            FROM __temp__claro_workspace
        ");
        $this->addSql("
            DROP TABLE __temp__claro_workspace
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_D902854577153098 ON claro_workspace (code)
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_D90285452B6FCFB2 ON claro_workspace (guid)
        ");
        $this->addSql("
            CREATE INDEX IDX_D9028545A76ED395 ON claro_workspace (user_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D9028545727ACA70 ON claro_workspace (parent_id)
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            DROP INDEX UNIQ_D902854577153098
        ");
        $this->addSql("
            DROP INDEX UNIQ_D90285452B6FCFB2
        ");
        $this->addSql("
            DROP INDEX IDX_D9028545A76ED395
        ");
        $this->addSql("
            DROP INDEX IDX_D9028545727ACA70
        ");
        $this->addSql("
            CREATE TEMPORARY TABLE __temp__claro_workspace AS
            SELECT id,
            user_id,
            parent_id,
            name,
            code,
            is_public,
            displayable,
            guid,
            self_registration,
            self_unregistration,
            discr,
            lft,
            lvl,
            rgt,
            root
            FROM claro_workspace
        ");
        $this->addSql("
            DROP TABLE claro_workspace
        ");
        $this->addSql("
            CREATE TABLE claro_workspace (
                id INTEGER NOT NULL,
                user_id INTEGER DEFAULT NULL,
                parent_id INTEGER DEFAULT NULL,
                name VARCHAR(255) NOT NULL,
                code VARCHAR(255) NOT NULL,
                guid VARCHAR(255) NOT NULL,
                discr VARCHAR(255) NOT NULL,
                lft INTEGER DEFAULT NULL,
                lvl INTEGER DEFAULT NULL,
                rgt INTEGER DEFAULT NULL,
                root INTEGER DEFAULT NULL,
                is_public BOOLEAN DEFAULT NULL,
                displayable BOOLEAN DEFAULT NULL,
                self_registration BOOLEAN DEFAULT NULL,
                self_unregistration BOOLEAN DEFAULT NULL,
                PRIMARY KEY(id),
                CONSTRAINT FK_D9028545A76ED395 FOREIGN KEY (user_id)
                REFERENCES claro_user (id)
                ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_D9028545727ACA70 FOREIGN KEY (parent_id)
                REFERENCES claro_workspace (id)
                ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        ");
        $this->addSql("
            INSERT INTO claro_workspace (
                id, user_id, parent_id, name, code,
                is_public, displayable, guid, self_registration,
                self_unregistration, discr, lft,
                lvl, rgt, root
            )
            SELECT id,
            user_id,
            parent_id,
            name,
            code,
            is_public,
            displayable,
            guid,
            self_registration,
            self_unregistration,
            discr,
            lft,
            lvl,
            rgt,
            root
            FROM __temp__claro_workspace
        ");
        $this->addSql("
            DROP TABLE __temp__claro_workspace
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_D902854577153098 ON claro_workspace (code)
        ");
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_D90285452B6FCFB2 ON claro_workspace (guid)
        ");
        $this->addSql("
            CREATE INDEX IDX_D9028545A76ED395 ON claro_workspace (user_id)
        ");
        $this->addSql("
            CREATE INDEX IDX_D9028545727ACA70 ON claro_workspace (parent_id)
        ");
    }
}
