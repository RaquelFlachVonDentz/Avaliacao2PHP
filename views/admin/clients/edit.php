<?php $this->layout('layouts/admin', ['title' => 'Editar Cliente']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="formView">
    <?php $this->insert('partials/admin/form/header', ['title' => 'Editar Cliente']) ?>
    <div class="card-body">
        <form method="post" action="<?= $this->baseUrl('admin/clients/update') ?>" enctype="multipart/form-data" class="">
            <input type="hidden" name="id" value="<?= $this->e($client['id']) ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name"
                           placeholder="Digite o nome completo"
                           value="<?= $this->e(($client['name'] ?? '')) ?>" required>
                    <?php if (!empty($errors['name'])): ?>
                        <div class="text-danger"><?= $this->e($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="Digite o e-mail"
                           value="<?= $this->e(($client['email'] ?? '')) ?>" required>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="text-danger"><?= $this->e($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                           placeholder="(00) 00000-0000"
                           value="<?= $this->e(($client['phone'] ?? '')) ?>" required>
                    <?php if (!empty($errors['phone'])): ?>
                        <div class="text-danger"><?= $this->e($errors['phone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="city" name="city"
                           placeholder="Digite a cidade"
                           value="<?= $this->e(($client['city'] ?? '')) ?>" required>
                    <?php if (!empty($errors['city'])): ?>
                        <div class="text-danger"><?= $this->e($errors['city']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="state" class="form-label">Estado (UF)</label>
                    <input type="text" maxlength="2" class="form-control" id="state" name="state"
                           placeholder="Ex: SP"
                           value="<?= $this->e(($client['state'] ?? '')) ?>" required>
                    <?php if (!empty($errors['state'])): ?>
                        <div class="text-danger"><?= $this->e($errors['state']) ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8 mb-3"></div>
            </div>

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Atualizar
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="bi bi-x-lg"></i> Limpar
                </button>
                <a href="<?= $this->baseUrl('admin/clients') ?>" class="btn align-self-end">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
            </div>

            <?= \App\Core\Csrf::input() ?>
        </form>
    </div>
</div>

<?php $this->stop() ?>
