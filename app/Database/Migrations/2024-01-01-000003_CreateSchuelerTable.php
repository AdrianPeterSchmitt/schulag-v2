<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchuelerTable extends Migration
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
            ],
            'klasse_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'typ_gl' => [
                'type' => 'ENUM',
                'constraint' => ['G', 'LE'],
                'comment' => 'G = Förderschwerpunkt geistige Entwicklung, LE = Förderschwerpunkt Lernen',
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
        $this->forge->addForeignKey('klasse_id', 'klassen', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('schueler');
    }

    public function down()
    {
        $this->forge->dropTable('schueler');
    }
}
