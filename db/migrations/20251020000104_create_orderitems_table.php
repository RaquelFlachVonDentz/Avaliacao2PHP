<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderItemsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('order_items')
            ->addColumn('order_id', 'integer', ['signed' => false])
            ->addColumn('product_id', 'integer', ['signed' => false])
            ->addColumn('quantity', 'integer')
            ->addColumn('unit_price', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('order_id', 'orders', 'id', [
                'delete' => 'NO ACTION',
                'update' => 'NO ACTION'
            ])
            ->addForeignKey('product_id', 'products', 'id', [
                'delete' => 'NO ACTION',
                'update' => 'NO ACTION'
            ])
            ->create();
    }
}
