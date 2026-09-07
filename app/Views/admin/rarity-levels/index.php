<div class="row align-items-center md-3">
    <div class="col">
        <div class="page-title">Gestion des raretés</div>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title">Nouvelle rareté</div>
                <?=form_open('admin/rarity-level/insert') ?>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon ">
                           <i class="fa-solid fa-tags "></i>
                    </span>
                    <input type="text" name="name" class="form-control" placeholder="Nom de la rareté" title="Nom de la rareté" value="">
                </div>
                <div class="input-icon mb-3 ">
                    <span class="input-icon-addon ">
                          <i class="fa-solid fa-palette "></i>
                    </span>
                    <input type="color" name="color" class="form-control" title="Couleur" value="">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">
                         <i class="fa-solid fa-hand-fist"></i>
                    </span>
                    <input type="number" step="0.01" name="power_multiplier" class="form-control" placeholder="Multiplicateur de puissance" title="Multiplicateur de puissance" value="">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">
                            <i class="fa-solid fa-dollar-sign"></i>
                    </span>
                    <input type="number" step="0.01" name="cost_multiplier" class="form-control" placeholder="Multiplicateur de coût" title="Multiplicateur de coût" value="">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">
                          <i class="fa-solid fa-percent"></i>
                    </span>
                    <input type="number" step="1" name="appearance_rate" class="form-control" placeholder="Taux d'apparition" title="Taux d'apparition" value="">
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary"><i class="fa-regular fa-floppy-disk me-2"></i>Enregistrer</button>
                </div>
                <?=form_close() ?>
            </div>
        </div>
    </div>
        <div class="col-md-9">
            <div class="card h-100">
                <table class="table table-hover table-striped table-sm" data-toggle="table" data-pagination="true" data-page-size="15" data-sortable="true">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Couleur</th>
                        <th>Multi.puissance</th>
                        <th>Multi.coût</th>
                        <th>Taux drop</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rarityLevels as $rarityLevel): ?>
                    <tr>
                        <td><?= $rarityLevel->name?></td>
                        <td>
                            <span style="display:inline-block;width:25px;height:25px;background-color:<?= $rarityLevel->color?>;vertical-align:middle;margin-right:5px;"></span><?= $rarityLevel->color?>
                            </td>
                        <td><?= $rarityLevel->power_multiplier?></td>
                        <td><?= $rarityLevel->cost_multiplier?></td>
                        <td><?= $rarityLevel->appearance_rate?> %</td>
                        <td class="d-flex">
                            <?= form_open('admin/rarity-level/delete') ?>
                            <?= form_hidden('id',(string) $rarityLevel->id) ?>
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            <?= form_close() ?>
                            <span
                                    class="ms-2 btn btn-sm btn-warning openEditModal"
                                    data-name="<?= $rarityLevel->name;?>"
                                    data-color="<?= $rarityLevel->color;?>"
                                    data-power="<?= $rarityLevel->power_multiplier;?>"
                                    data-cost="<?= $rarityLevel->cost_multiplier;?>"
                                    data-rate="<?= $rarityLevel->appearance_rate;?>"
                                    data-id="<?= $rarityLevel->id;?>">
                                <i class="fa-solid fa-pen"></i>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Modification</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <?= form_open('admin/rarity-level/update'); ?>
            <input type="hidden" id="updateId" value="" name="id">
            <div class="modal-body">
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">

                    </span>
                    <input id="updateName" type="text" name="name" class="form-control" placeholder="Name" value="" title="Name">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">

                    </span>
                    <input id="updateColor" type="color" name="color" class="form-control form-control-color w-100" required title="couleur" placeholder="Couleur" value=""  title="Couleur">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">

                    </span>
                    <input id="updatePower" type="number" name="power_multiplier" class="form-control" placeholder="Multi. power" value="" min="1" title="Multi. Power">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">

                    </span>
                    <input id="updateCost" type="number" name="cost_multiplier" class="form-control" placeholder="Multi. cout" value="" min="1" title="Multi. cout">
                </div>
                <div class="input-icon mb-3">
                    <span class="input-icon-addon">

                    </span>
                    <input id="updateRate" type="number" name="appearance_rate" class="form-control" placeholder="Ratio apparition" value="" min="1" title="Ratio apparition">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Sauvegarder</button>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        const modalEdit = new bootstrap.Modal('#editModal');
        $(document).on('click','.openEditModal', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let color = $(this).data('color');
            let power = $(this).data('power');
            let cost = $(this).data('cost');
            let rate = $(this).data('rate');
            $('#updateId').val(id);
            $('#updateName').val(name);
            $('#updateColor').val(color);
            $('#updatePower').val(power);
            $('#updateCost').val(cost);
            $('#updateRate').val(rate);
            modalEdit.show();
        })
    });
</script>