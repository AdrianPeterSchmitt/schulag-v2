<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKlassenTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'z.B. "5a"',
            ],
            'jahrgang' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'z.B. 5',
            ],
            'klassenleitung' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('klassen');
    }

    public function down()
    {
        $this->forge->dropTable('klassen');
    }
}
