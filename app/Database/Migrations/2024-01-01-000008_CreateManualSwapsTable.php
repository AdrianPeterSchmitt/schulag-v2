<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateManualSwapsTable extends Migration
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
            'student_a_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'student_b_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'offer_a_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'offer_b_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addForeignKey('student_a_id', 'schueler', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_b_id', 'schueler', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('offer_a_id', 'club_offers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('offer_b_id', 'club_offers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('manual_swaps');
    }

    public function down()
    {
        $this->forge->dropTable('manual_swaps');
    }
}
