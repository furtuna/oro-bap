<?php

namespace BAP\SimpleBTSBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtension;
use Oro\Bundle\NoteBundle\Migration\Extension\NoteExtensionAwareInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class BAPSimpleBTSBundleInstaller implements Installation, NoteExtensionAwareInterface
{
    /** @var NoteExtension */
    protected $noteExtension;

    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_1';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createBtsIssueTable($schema);
        $this->createBtsIssue2CollaboratorTable($schema);
        $this->createBtsIssue2IssueTable($schema);
        $this->createBtsIssuePriorityTable($schema);
        $this->createBtsIssueResolutionTable($schema);

        /** Foreign keys generation **/
        $this->addBtsIssueForeignKeys($schema);
        $this->addBtsIssue2CollaboratorForeignKeys($schema);
        $this->addBtsIssue2IssueForeignKeys($schema);

        $this->noteExtension->addNoteAssociation($schema, 'bts_issue');
    }

    /**
     * Create bts_issue table
     *
     * @param Schema $schema
     */
    protected function createBtsIssueTable(Schema $schema)
    {
        $table = $schema->createTable('bts_issue');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('assignee_id', 'integer', ['notnull' => false]);
        $table->addColumn('reporter_id', 'integer', ['notnull' => false]);
        $table->addColumn('summary', 'string', ['length' => 255]);
        $table->addColumn('code', 'string', ['length' => 10]);
        $table->addColumn('description', 'text', []);
        $table->addColumn('type', 'string', ['length' => 50]);
        $table->addColumn('createdAt', 'datetime', []);
        $table->addColumn('updatedAt', 'datetime', []);
        $table->addColumn('resolution_id', 'integer', ['notnull' => false]);
        $table->addColumn('priority_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['reporter_id'], 'IDX_48518651E1CFE6F5', []);
        $table->addIndex(['assignee_id'], 'IDX_4851865159EC7D60', []);
        $table->addIndex(['parent_id'], 'IDX_48518651727ACA70', []);
        $table->addIndex(['updatedAt'], 'bts_issue_updated_at_idx', []);
        $table->addIndex(['createdAt'], 'bts_issue_created_at_idx', []);
        $table->addIndex(['summary'], 'bts_issue_summary_idx', []);
        $table->addUniqueIndex(['code'], 'bts_issue_code_idx');
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
     * Create bts_issue2collaborator table
     *
     * @param Schema $schema
     */
    protected function createBtsIssue2CollaboratorTable(Schema $schema)
    {
        $table = $schema->createTable('bts_issue2collaborator');
        $table->addColumn('issue_id', 'integer', []);
        $table->addColumn('collaborator_id', 'integer', []);
        $table->setPrimaryKey(['issue_id', 'collaborator_id']);
        $table->addIndex(['issue_id'], 'IDX_1C52F1B15E7AA58C', []);
        $table->addIndex(['collaborator_id'], 'IDX_1C52F1B130098C8C', []);
    }

    /**
     * Create bts_issue2issue table
     *
     * @param Schema $schema
     */
    protected function createBtsIssue2IssueTable(Schema $schema)
    {
        $table = $schema->createTable('bts_issue2issue');
        $table->addColumn('issue_source', 'integer', []);
        $table->addColumn('issue_target', 'integer', []);
        $table->setPrimaryKey(['issue_source', 'issue_target']);
        $table->addIndex(['issue_source'], 'IDX_D8B3F9F9AD7AF554', []);
        $table->addIndex(['issue_target'], 'IDX_D8B3F9F9B49FA5DB', []);
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
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue'),
            ['parent_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['assignee_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['reporter_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add bts_issue2collaborator foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBtsIssue2CollaboratorForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bts_issue2collaborator');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['collaborator_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue'),
            ['issue_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }

    /**
     * Add bts_issue2issue foreign keys.
     *
     * @param Schema $schema
     */
    protected function addBtsIssue2IssueForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('bts_issue2issue');
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue'),
            ['issue_target'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('bts_issue'),
            ['issue_source'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setNoteExtension(NoteExtension $noteExtension)
    {
        $this->noteExtension = $noteExtension;
    }
}
