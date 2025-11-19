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

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Itens do Pedido</h5>
        </div>
        <div class="card-body">
            <!-- Formulário para adicionar novo item -->
            <div class="mb-4 p-3 bg-light rounded">
                <h6 class="mb-3">Adicionar Novo Item</h6>
                <form method="post" action="<?= $this->baseUrl('admin/orders/store-item') ?>" class="row g-3">
                    <input type="hidden" name="order_id" value="<?= $this->e($order['id']) ?>">
                    <div class="col-md-4">
                        <label for="product_id" class="form-label">Produto</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Selecione um produto</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                    <?= $this->e($product['name']) ?> - R$ <?= number_format((float)$product['price'], 2, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <label for="unit_price" class="form-label">Preço Unitário</label>
                        <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" min="0.01" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg"></i> Adicionar Item
                        </button>
                    </div>
                    <?= \App\Core\Csrf::input() ?>
                </form>
            </div>

            <!-- Lista de itens -->
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $total = 0;
                    foreach ($items as $item): 
                        $subtotal = (float)$item['quantity'] * (float)$item['unit_price'];
                        $total += $subtotal;
                    ?>
                        <tr id="item-<?= $item['id'] ?>">
                            <td><?= $this->e($item['product_name'] ?? 'N/A') ?></td>
                            <td><?= $this->e($item['quantity']) ?></td>
                            <td>R$ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                            <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" onclick="editItem(<?= htmlspecialchars(json_encode($item)) ?>)">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                                <form class="d-inline" action="<?= $this->baseUrl('admin/orders/delete-item') ?>" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este item?');">
                                    <input type="hidden" name="id" value="<?= $this->e($item['id']) ?>">
                                    <input type="hidden" name="order_id" value="<?= $this->e($order['id']) ?>">
                                    <?= \App\Core\Csrf::input() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Nenhum item encontrado neste pedido. Adicione itens usando o formulário acima.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                    <?php if (!empty($items)): ?>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th>R$ <?= number_format($total, 2, ',', '.') ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para editar item -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?= $this->baseUrl('admin/orders/update-item') ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_item_id">
                        <input type="hidden" name="order_id" value="<?= $this->e($order['id']) ?>">
                        <div class="mb-3">
                            <label for="edit_product_id" class="form-label">Produto</label>
                            <select class="form-select" id="edit_product_id" name="product_id" required>
                                <option value="">Selecione um produto</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                        <?= $this->e($product['name']) ?> - R$ <?= number_format((float)$product['price'], 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_quantity" class="form-label">Quantidade</label>
                            <input type="number" class="form-control" id="edit_quantity" name="quantity" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_unit_price" class="form-label">Preço Unitário</label>
                            <input type="number" step="0.01" class="form-control" id="edit_unit_price" name="unit_price" min="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                    <?= \App\Core\Csrf::input() ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Atualiza preço quando seleciona produto
        document.getElementById('product_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('unit_price').value = selected.dataset.price;
            }
        });

        document.getElementById('edit_product_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                document.getElementById('edit_unit_price').value = selected.dataset.price;
            }
        });

        // Função para editar item
        function editItem(item) {
            document.getElementById('edit_item_id').value = item.id;
            document.getElementById('edit_product_id').value = item.product_id;
            document.getElementById('edit_quantity').value = item.quantity;
            document.getElementById('edit_unit_price').value = item.unit_price;
            
            const modal = new bootstrap.Modal(document.getElementById('editItemModal'));
            modal.show();
        }
    </script>
</div>

<?php $this->stop() ?>



