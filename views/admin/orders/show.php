<?php $this->layout('layouts/admin', ['title' => 'Detalhe do Pedido']) ?>

<?php $this->start('body') ?>
<div class="container mt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalhes do Pedido #<?= $this->e($order['id']) ?></h5>
        </div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>ID:</strong></label>
                        <input type="text" class="form-control" value="<?= $this->e($order['id']) ?>" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Cliente:</strong></label>
                        <input type="text" class="form-control" value="<?= $this->e($order['client_name'] ?? 'N/A') ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Data do Pedido:</strong></label>
                        <input type="text" class="form-control" value="<?= $this->e($order['order_date']) ?>" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Status:</strong></label>
                        <input type="text" class="form-control" value="<?= ucfirst($this->e($order['status'])) ?>" readonly>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <a href="<?= $this->baseUrl('admin/orders/edit?id=' . $this->e($order['id'])) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Pedido
                    </a>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Itens do Pedido</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $total = 0;
                    foreach ($items as $item): 
                        $subtotal = (float)$item['quantity'] * (float)$item['unit_price'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= $this->e($item['product_name'] ?? 'N/A') ?></td>
                            <td><?= $this->e($item['quantity']) ?></td>
                            <td>R$ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Nenhum item encontrado neste pedido.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                    <?php if (!empty($items)): ?>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>R$ <?= number_format($total, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->stop() ?>



