<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddEstoqueToProductsTable extends AbstractMigration
{
    public function change(): void
    {
        $this->table('products')
            ->addColumn('estoque', 'integer', ['default' => 0, 'after' => 'price'])
            ->update();
    }
}

