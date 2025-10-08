<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllocationRunsTable extends Migration
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
            'schoolyear' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
                'comment' => 'Schuljahr z.B. 2024/2025',
            ],
            'run_date' => [
                'type' => 'DATETIME',
                'comment' => 'Zeitpunkt des Durchlaufs',
            ],
            'total_students' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Anzahl Schüler gesamt',
            ],
            'total_assigned' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Anzahl zugewiesener Schüler',
            ],
            'total_waitlist' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Anzahl Schüler auf normaler Warteliste',
            ],
            'total_rest_waitlist' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Anzahl Schüler auf Rest-Warteliste',
            ],
            'total_offers' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Anzahl aktiver AGs',
            ],
            'total_capacity' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Gesamt-Kapazität aller AGs',
            ],
            'algorithm_version' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'v1.0',
                'comment' => 'Version des Algorithmus',
            ],
            'metadata' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON mit zusätzlichen Informationen',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User-ID des Durchführenden',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('schoolyear');
        $this->forge->addKey('run_date');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('allocation_runs');
    }

    public function down()
    {
        $this->forge->dropTable('allocation_runs');
    }
}

