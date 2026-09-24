<div class="row align-items-center">
    <div class="col">
        <div class="page-title">Liste des missions</div>
    </div>
    <div class="col-auto ms-auto d-print-none">
        <div class="btn-liste">
            <a href="<?=base_url('admin/mission/new')?>" class="btn btn-primary">Créer une nouvelle mission</a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" data-toggle="table">
                        <tbody>
                        <tr>
                            <th scope="col" class="fw-bold bg-light" style="width:240px">Missions</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center align-top"><?=$m->title?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row"  style="font-weight: bold;">Description</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-muted small" style="min-width: 200px;"><?=$m->description?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Niveau requis</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->level_required?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">P. min</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->power_required_min?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">P. max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->power_required_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Stam. min</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->stamina_cost_min?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Stam. max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->stamina_cost_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Team size max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->team_size_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Credits reward min</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->credits_reward_min?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Credits reward max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->credits_reward_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Energie reward min</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->energy_reward_min?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Energie reward max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->energy_reward_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Experience reward min</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->experience_reward_min?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr class=>
                            <th scope="row" style="font-weight: bold;">Experience reward max</th>
                            <?php foreach($missions as $m):?>
                            <td class="text-center"><?=$m->experience_reward_max?></td>
                            <?php endforeach;?>
                        </tr>
                        <tr>
                            <th scope="row" style="font-weight: bold;">Actions</th>
                            <?php foreach($missions as $m):?>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-5">
                                        <a href="<?=base_url('admin/mission/edit/').$m->id?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                                        <a href="<?=base_url('admin/mission/delete/').$m->id?>" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></a>
                                    </div>
                                </td>
                            <?php endforeach;?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
