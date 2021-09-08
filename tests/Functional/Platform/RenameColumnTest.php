<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Tests\Functional\Platform;

use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Tests\FunctionalTestCase;

use function array_keys;

class RenameColumnTest extends FunctionalTestCase
{
    public function testColumnPositionRetainedAfterRenaming(): void
    {
        $table = new Table('test_rename');
        $table->addColumn('c1', 'string', ['length' => 16]);
        $table->addColumn('c2', 'integer');

        $sm = $this->connection->createSchemaManager();
        $sm->dropAndCreateTable($table);

        $table->dropColumn('c1')
            ->addColumn('c1_x', 'string', ['length' => 16]);

        $diff = $sm->createComparator()
            ->diffTable($sm->listTableDetails('test_rename'), $table);

        self::assertNotNull($diff);
        $sm->alterTable($diff);

        $table = $sm->listTableDetails('test_rename');
        self::assertSame(['c1_x', 'c2'], array_keys($table->getColumns()));
    }
}
