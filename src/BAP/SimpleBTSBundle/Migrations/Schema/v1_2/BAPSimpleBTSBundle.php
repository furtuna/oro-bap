<?php

namespace BAP\SimpleBTSBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class BAPSimpleBTSBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createBtsIssuePriorityTable($schema);
        $this->createBtsIssueResolutionTable($schema);
        $this->updateBtsIssueTable($schema);
        $this->addBtsIssueForeignKeys($schema);
    }

    /**
     * Update bts_issue table
     *
     * @param Schema $schema
     */
    protected function updateBtsIssueTable(Schema $schema)
    {
        $table = $schema->getTable('bts_issue');
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->addIndex(['priority_id'], 'IDX_48518651497B19F9', []);
        $table->addIndex(['resolution_id'], 'IDX_4851865112A1C43A', []);
    }

    /**
     * Create bts_issue_priority table
     *
     * @param Schema $schema
     */
    protected function createBtsIssuePriorityTable(Schema $schema)
    {
        $table = $schema->createTable('bts_issue_priority');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 30]);
        $table->addColumn('sort_order', 'integer', []);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create bts_issue_resolution table
     *
     * @param Schema $schema
     */
    protected function createBtsIssueResolutionTable(Schema $schema)
    {
        $table = $schema->createTable('bts_issue_resolution');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 30]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add bts_issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBtsIssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bts_issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue_resolution'),
            ['resolution_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue_priority'),
            ['priority_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }
}
