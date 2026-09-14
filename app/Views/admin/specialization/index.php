<div class="row mb-3">
    <div class="col">
        <div class="page-title">Spécialisations des héros</div>
    </div>
</div>

<div class="row mb-3">
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title">Ajouter une spécialisation</div>
                <div class="card-body">
                    <?=form_open('admin/specialization/insert') ?>
                    <div>
                        <input class="form-control mb-3 w-25" type="text" name="name" placeholder="Nom" value="">
                    </div>
                    <div>
                        <textarea
                                class="form-control auto-expand mb-3"
                                name="description"
                                placeholder="Description"
                                title="Description"
                                rows="2"
                                style="resize: none; overflow-y: hidden;"
                        ></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Ajouter la spécialisation</button>
                    </div>
                    <?=form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mb-3">
    <div class="col">
        <div class="card h-100">
            <div class="card-body">
                <table class="table table-hover table-striped" data-toggle="table" data-search="true" data-show-columns="true">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($specializations as $spe):?>
                        <tr>
                            <td><?= $spe['id']?></td>
                            <td><?= $spe['name']?></td>
                            <td><?= $spe['description']?></td>
                            <td class="d-flex">
                                <div class="d-flex justify-content-end gap-1">
                                    <span
                                            class="ms-2 btn btn-sm btn-warning openEditModal"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal"
                                            data-id="<?=$spe['id'] ?>"
                                            data-name="<?=$spe['name'] ?>"
                                            data-description="<?=$spe['description']?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <?= form_open('admin/specialization/delete/' . $spe['id']) ?>
                                    <?= form_hidden('id', $spe['id']);?>
                                    <?php if($spe['id'] != 1) :?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <?php endif;?>
                                    <?= form_close() ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Modification de la spécialisation</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?=form_open('admin/specialization/edit/' . $spe['id'])?>
            <input type="hidden" id="updateId" value="" name="id">
            <div class="modal-body">
                <div>
                    <input id="updateName" type="text" class="form-control mb-3" name="name" placeholder="Nom" title="Nom">
                </div>
                <div>
                    <textarea
                            id="updateDescription"
                            class="form-control auto-expand"
                            name="description"
                            placeholder="Description"
                            title="Description"
                            rows="2"
                            style="resize: none; overflow-y: hidden;"
                    ></textarea>
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
        document.getElementById('editModal').addEventListener('show.bs.modal',function(event){
            let button = event.relatedTarget;
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');
            let description = button.getAttribute('data-description');
            $('#updateId').val(id);
            $('#updateName').val(name);
            $('#updateDescription').val(description)
        })
    });
</script>
