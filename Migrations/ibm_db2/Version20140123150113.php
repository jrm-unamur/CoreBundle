<?php

namespace Claroline\CoreBundle\Migrations\ibm_db2;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated migration based on mapping information: modify it with caution
 *
 * Generation date: 2014/01/23 03:01:15
 */
class Version20140123150113 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE claro_user 
            ADD COLUMN is_mail_notified SMALLINT NOT NULL WITH DEFAULT
        ");
    }

    public function down(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE claro_user 
            DROP COLUMN is_mail_notified
        ");
        $this->addSql("
            CALL SYSPROC.ADMIN_CMD ('REORG TABLE claro_user')
        ");
    }
}