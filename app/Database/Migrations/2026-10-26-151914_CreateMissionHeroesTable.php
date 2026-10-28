<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMissionHeroesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'auto_increment'=>true
            ],
            'missions_resolution_id'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'null'=>false,
            ],
            'hero_id'=>[
                'type'=>'INT',
                'constraint'=>11,
                'unsigned'=>true,
                'null'=>false,
            ],
            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ],
            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('missions_resolution_id','mission_resolutions','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('hero_id','heroes','id','CASCADE','CASCADE');
        $this->forge->createTable('mission_heroes');
    }

    public function down()
    {
        $this->forge->dropTable('mission_heroes');
    }
}
