<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClubsTable extends Migration
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
            'titel' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'beschreibung_kurz' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'min_grade' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Mindestjahrgang',
            ],
            'max_grade' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Maximaljahrgang',
            ],
            'allowed_types_gl' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'z.B. "G,LE" oder "G"',
            ],
            'max_teilnehmer' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 6,
            ],
            'zweite_lehrkraft_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'zweite_lehrkraft_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'zweite_lehrkraft_telefon' => [
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
        $this->forge->createTable('clubs');
    }

    public function down()
    {
        $this->forge->dropTable('clubs');
    }
}
