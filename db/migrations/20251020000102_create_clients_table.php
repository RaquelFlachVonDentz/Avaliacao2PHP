<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateClientsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('clients')
            ->addColumn('name', 'string', ['limit' => 100])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('phone', 'string', ['limit' => 20])
            ->addColumn('city', 'string', ['limit' => 50])
            ->addColumn('state', 'string', ['limit' => 2])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
