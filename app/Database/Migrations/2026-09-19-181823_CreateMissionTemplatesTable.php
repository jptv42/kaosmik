<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMissionTemplatesTable extends Migration
{
    public function up()
    {
        $this->forge->addfield([
            'id'=>[
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,],
            'title'=>[
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'description'=>[
                'type' => 'TEXT',
                'null' => true,
            ],
            'level_required'=>[
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'power_required'=>[
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'stamina_cost'=>[
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'team_size_max'=>[
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'credits_reward'=>[
                'type' => 'INT',
                'constraint' => 11,
            ],
            'energy_reward'=>[
                'type' => 'INT',
                'constraint' => 11,
            ],
            'experience_reward'=>[
                'type' => 'INT',
                'constraint' => 11,
            ],
            'created_at'=>[
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'=>[
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at'=>[
                'type' => 'DATETIME',
                'null' => true,
            ],

        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('mission_templates');
    }
    public function down()
    {
        $this->forge->dropTable('mission_templates');
    }
}
