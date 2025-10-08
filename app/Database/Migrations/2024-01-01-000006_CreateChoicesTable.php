<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChoicesTable extends Migration
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
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'offer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['1', '2', '3', 'no_participation'],
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
        $this->forge->addForeignKey('student_id', 'schueler', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('offer_id', 'club_offers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addUniqueKey(['student_id', 'priority']);
        $this->forge->createTable('choices');
    }

    public function down()
    {
        $this->forge->dropTable('choices');
    }
}
