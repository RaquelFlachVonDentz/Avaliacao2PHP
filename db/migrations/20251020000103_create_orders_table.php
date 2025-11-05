<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrdersTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('orders')
            ->addColumn('client_id', 'integer', ['signed' => false])
            ->addColumn('order_date', 'date')
            ->addColumn('status', 'string', ['limit' => 20])
            ->addForeignKey('client_id', 'clients', 'id', [
                'delete' => 'NO ACTION',
                'update' => 'NO ACTION'
            ])
            ->create();
    }
}
