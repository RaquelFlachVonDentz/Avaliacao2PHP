<?php $this->layout('layouts/admin', ['title' => 'Pedidos']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="tableView">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">Lista de Pedidos</h5>
        <a href="<?= $this->baseUrl('admin/orders/create') ?>" class="btn btn-primary" id="btnNewOrder">
            <i class="bi bi-plus-lg"></i> Novo Pedido
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data do Pedido</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $this->e($order['id']) ?></td>
                        <td><?= $this->e($order['client_name'] ?? 'N/A') ?></td>
                        <td><?= $this->e($order['order_date']) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $order['status'] === 'pendente' ? 'warning' : 
                                ($order['status'] === 'processando' ? 'info' : 
                                ($order['status'] === 'enviado' ? 'primary' : 
                                ($order['status'] === 'entregue' ? 'success' : 'danger')))
                            ?>">
                                <?= ucfirst($this->e($order['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-secondary btn-view"
                                   href="<?= $this->baseUrl('admin/orders/show?id=' . $this->e($order['id'])) ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a class="btn btn-sm btn-primary btn-edit"
                                   href="<?= $this->baseUrl('admin/orders/edit?id=' . $this->e($order['id'])) ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form class="inline" action="<?= $this->baseUrl('admin/orders/delete') ?>" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este pedido? (Pedido #<?= $this->e($order['id']) ?>)');">
                                    <input type="hidden" name="id" value="<?= $this->e($order['id']) ?>">
                                    <?= \App\Core\Csrf::input() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Nenhum pedido encontrado.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginação -->
<div class="pagination mt-3">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong>[<?= $i ?>]</strong>
        <?php else: ?>
            <a href="<?= $this->baseUrl('admin/orders?page=' . $i) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php $this->stop() ?>



