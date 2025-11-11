<?php $this->layout('layouts/admin', ['title' => 'Clientes']) ?>

<?php $this->start('body') ?>
<div class="card shadow-sm" id="tableView">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-semibold">Lista de Clientes</h5>
        <a href="/admin/clients/create" class="btn btn-primary" id="btnNewClient">
            <i class="bi bi-plus-lg"></i> Novo Cliente
        </a>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody id="tableBody">
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= $this->e($client['id']) ?></td>
                        <td><?= $this->e($client['name']) ?></td>
                        <td><?= $this->e($client['email']) ?></td>
                        <td><?= $this->e($client['phone']) ?></td>
                        <td><?= $this->e($client['city']) ?></td>
                        <td><?= $this->e($client['state']) ?></td>
                        <td><?= $this->e($client['created_at'] ?? '') ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-sm btn-secondary btn-view"
                                   href="/admin/clients/show?id=<?= $this->e($client['id']) ?>">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                                <a class="btn btn-sm btn-primary btn-edit"
                                   href="/admin/clients/edit?id=<?= $this->e($client['id']) ?>">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form class="inline" action="/admin/clients/delete" method="post"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este cliente? (<?= $this->e($client['name']) ?>)');">
                                    <input type="hidden" name="id" value="<?= $this->e($client['id']) ?>">
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

                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Nenhum cliente encontrado.
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
            <a href="/admin/clients?page=<?= $i ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php $this->stop() ?>
