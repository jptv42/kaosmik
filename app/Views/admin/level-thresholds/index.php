<div class="row align-items-center mb-3">
    <div class="col">
        <div class="page-title">Courbe de niveaux</div>
    </div>
    <div class="col-auto ms-auto d-print-none">
        <div class="btn-list">
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col">
        <div class="card-body h-100">
            <div class="card p-4 h-100">
                <div class="mb-3">Ajouter un niveau</div>
                <?= form_open('admin/level-threshold/insert') ?>
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-n fa-xs"></i>
                            <i class="fa-solid fa-v fa-xs"></i>
                        </span>
                        <input type="number" value="" name="level" class="form-control" placeholder="Niveau" title="Niveau">
                    </div>
                    <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-x fa-xs"></i>
                            <i class="fa-solid fa-p fa-xs"></i>
                        </span>
                        <input type="number" value="" name="experience_required" class="form-control" placeholder="Experience" title="Experience">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <table class="table table-responsive table-sm table-hover table-striped" data-toggle="table" data-pagination="true" data-page-size="10" data-sortable="true">
                <thead>
                <tr>
                    <th>Level</th>
                    <th>Expérience requise</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($levelThresholds as $levelThreshold): ?>
                    <tr>
                        <td><?=$levelThreshold['level']?></td>
                        <td><?=$levelThreshold['experience_required'];?></td>
                        <td class="d-flex">
                            <?= form_open('admin/level-threshold/delete') ?>
                            <?= form_hidden('id', $levelThreshold['id']) ?>
                            <?php if(isset($levelThreshold)) :
                                echo form_hidden('id', (string) $levelThreshold['id']);
                            endif; ?>
                                <button type="submit" class="btn btn-danger btn-icon"><i class="fa-solid fa-trash-can"></i></button>
                            <?= form_close() ?>
                            <span class="ms-2 btn btn-warning openEditModal"
                                  data-level="<?=$levelThreshold['level']?>"
                                  data-exp="<?=$levelThreshold['experience_required']?>"
                                  data-id="<?=$levelThreshold['id']?>">
                                  <i class="fa-solid fa-pen"></i></span>
                        </td>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modale-title fs-5">Modification</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?=form_open('admin/level-threshold/update')?>
            <input type="hidden" id="updateId" value="" name="id">
            <div class="modal-body">
                <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-n fa-xs"></i>
                            <i class="fa-solid fa-v fa-xs"></i>
                        </span>
                    <input id="updateLevel" type="number" value="" name="level" class="form-control" placeholder="Niveau" min="1" title="Niveau">
                </div>
                <div class="input-icon mb-3">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-x fa-xs"></i>
                            <i class="fa-solid fa-p fa-xs"></i>
                        </span>
                    <input id="updateExperience" type="number" value="" name="experience_required" class="form-control" min="1" placeholder="Experience" title="Experience">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Sauvegarder</button>
            </div>
            <?=form_close()?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        const modalEdit = new bootstrap.Modal('#editModal');
        $(document).on('click','.openEditModal',function(){
            let id = $(this).data('id');
            let level = $(this).data('level');
            let exp = $(this).data('exp');
            $('#updateId').val(id);
            $('#updateLevel').val(level);
            $('#updateExperience').val(exp);
            modalEdit.show();
        })
    });
</script>