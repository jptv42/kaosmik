<div class ="row">
    <div class="col">
        <div class="page-title">
            <?= isset($m) ? "Modification de la mission ".$m->title : "Nouvelle mission"?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <?php echo form_open_multipart('admin/mission/create-update');
                if(isset($m)) :
                    echo form_hidden('id',(string)$m->id);
                endif;?>
                <div class="mb-3">
                    <label class="form-label">Nom de la mission</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-tag"></i>
                        </span>
                        <input type="text" name="title" class="form-control" placeholder="Titre" title="Titre" value="<?=isset($m) ? esc($m->title) : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-tag"></i>
                        </span>
                        <input type="text" name="description" class="form-control" placeholder="Description" title="Description" value="<?=isset($m) ? esc($m->description) : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Niveau requis</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-tag"></i>
                        </span>
                        <input type="number" name="level_required" class="form-control" placeholder="Niveau_requis" title="Niveau_requis" value="<?=isset($m) ? $m->level_required : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Puissance minimum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-hand-fist"></i>
                        </span>
                        <input type="number" name="power_required_min" class="form-control" placeholder="Puissance minimum" title="Puissance minimum" value="<?=isset($m) ? $m->power_required_min : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Puissance maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-hand-fist"></i>
                        </span>
                        <input type="number" name="power_required_max" class="form-control" placeholder="Puissance maximum" title="Puissance maximum" value="<?=isset($m) ? (string)$m->power_required_max : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Endurance minimum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-bolt"></i>
                        </span>
                        <input type="number" name="stamina_cost_min" class="form-control" placeholder="Endurance minimum" title="Endurance minimum" value="<?=isset($m) ? (string)$m->stamina_cost_min : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Endurance maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-bolt"></i>
                        </span>
                        <input type="number" name="stamina_cost_max" class="form-control" placeholder="Endurance maximum" title="Endurance maximum" value="<?=isset($m) ? (string)$m->stamina_cost_max : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Taille de l'équipe maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-people-group"></i>
                        </span>
                        <input type="number" name="team_size_max" class="form-control" placeholder="Taille de l'équipe maximum" title="Taille de l'équipe maximum" value="<?=isset($m) ? $m->team_size_max : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense en crédits minimum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-cent-sign"></i>
                        </span>
                        <input type="number" name="credits_reward_min" class="form-control" placeholder="Récompense en crédits minimum" title="Récompense en crédits minimum" value="<?=isset($m) ? $m->credits_reward_min : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense en crédits maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-cent-sign"></i>
                        </span>
                        <input type="number" name="credits_reward_max" class="form-control" placeholder="Récompense en crédits maximum" title="Récompense en crédits maximum" value="<?=isset($m) ? $m->credits_reward_max : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense énergie minimum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-atom"></i>
                        </span>
                        <input type="number" name="energy_reward_min" class="form-control" placeholder="Récompense énergie minimum" title="Récompense énergie minimum" value="<?=isset($m) ? $m->energy_reward_min : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense énergie maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-atom"></i>
                        </span>
                        <input type="number" name="energy_reward_max" class="form-control" placeholder="Récompense énergie maximum" title="Récompense énergie maximum" value="<?=isset($m) ? $m->energy_reward_max : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense expérience minimum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-x fa-xs"></i>
                            <i class="fa-solid fa-p fa-xs"></i>
                        </span>
                        <input type="number" name="experience_reward_min" class="form-control" placeholder="Récompense expérience minimum" title="Récompense expérience minimum" value="<?=isset($m) ? $m->experience_reward_min : '';?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Récompense expérience maximum</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fa-solid fa-x fa-xs"></i>
                            <i class="fa-solid fa-p fa-xs"></i>
                        </span>
                        <input type="number" name="experience_reward_max" class="form-control" placeholder="Récompense expérience maximum" title="Récompense expérience maximum" value="<?=isset($m) ? $m->experience_reward_max : '';?>">
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($m) ? "Modifier" : "Créer"; ?>
                    </button>
                </div>
                <?=form_close();?>
            </div>
        </div>
    </div>
</div>