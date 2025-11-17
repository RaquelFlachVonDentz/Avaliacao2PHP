<?php $this->layout('layouts/admin', ['title' => 'Novo Pedido']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="formView">
    <?php $this->insert('partials/admin/form/header', ['title' => 'Novo Pedido']) ?>
    <div class="card-body">
        <form method="post" action="<?= $this->baseUrl('admin/orders/store') ?>" enctype="multipart/form-data" class="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label">Cliente</label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clients as $clientId => $clientName): ?>
                            <option value="<?= $clientId ?>" <?= (($old['client_id'] ?? '') == $clientId ? 'selected' : '') ?>>
                                <?= $this->e($clientName) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['client_id'])): ?>
                        <div class="text-danger"><?= $this->e($errors['client_id']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="order_date" class="form-label">Data do Pedido</label>
                    <input type="date" class="form-control" id="order_date" name="order_date"
                           value="<?= $this->e(($old['order_date'] ?? date('Y-m-d'))) ?>" required>
                    <?php if (!empty($errors['order_date'])): ?>
                        <div class="text-danger"><?= $this->e($errors['order_date']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="">Selecione um status</option>
                        <option value="pendente" <?= (($old['status'] ?? '') === 'pendente' ? 'selected' : '') ?>>Pendente</option>
                        <option value="processando" <?= (($old['status'] ?? '') === 'processando' ? 'selected' : '') ?>>Processando</option>
                        <option value="enviado" <?= (($old['status'] ?? '') === 'enviado' ? 'selected' : '') ?>>Enviado</option>
                        <option value="entregue" <?= (($old['status'] ?? '') === 'entregue' ? 'selected' : '') ?>>Entregue</option>
                        <option value="cancelado" <?= (($old['status'] ?? '') === 'cancelado' ? 'selected' : '') ?>>Cancelado</option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                        <div class="text-danger"><?= $this->e($errors['status']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3"></div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-lg"></i> Salvar
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Limpar
                </button>
                <a href="<?= $this->baseUrl('admin/orders') ?>" class="btn align-self-end">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>

            <?= \App\Core\Csrf::input() ?>
        </form>
    </div>
</div>
<?php $this->stop() ?>



