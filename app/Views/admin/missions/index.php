<div class="row">
    <div>
        <h1>Les missions !</h1>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="card-title text-center">
                    Les missions
                </div>
                <table class="table table-hover table-striped table-sm" data-toggle="table" data-pagination="true" data-page-size="1" data-sortable="true">
                    <tr>
                        <th data-sortable="false">Titre</th>
                        <th data-sortable="true">Mission</th>
                        <th data-sortable="true">Niveau requis</th>
                        <th data-sortable="true">Puissance requise</th>
                        <th data-sortable="true">Endurance requise</th>
                        <th data-sortable="true">Taille escouade</th>
                        <th data-sortable="true">Récomp. Crédits</th>
                        <th data-sortable="true">Récomp. Energie</th>
                        <th data-sortable="true">Récomp. XP</th>
                        <th data-sortable="false" class="text-center">Actions</th>
                    </tr>
                    <?php foreach ($missions as $m) : ?>
                    <tr>
                        <td><?=$m['title']?></td>
                        <td><?=$m['description']?></td>
                        <td><?=$m['level_required']?></td>
                        <td><?=$m['power_required']?></td>
                        <td><?=$m['stamina_cost']?></td>
                        <td><?=$m['team_size_max']?></td>
                        <td><?=$m['credits_reward']?></td>
                        <td><?=$m['energy_reward']?></td>
                        <td><?=$m['experience_reward']?></td>
                        <td class="d-flex">
                            <?=form_open('admin/mission/edit/'.$m['id'])?>
                            <button type="submit" class="btn btn-primary me-2"><i class="fa-solid fa-pen"></i></button>
                            <?=form_close()?>
                            <a href="<?=base_url('/admin/mission/delete/').$m['id']?>" class="btn btn-danger"><i class="fa-solid fa-trash" ></i></a>
                        </td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
        </div>
    </div>
</div>