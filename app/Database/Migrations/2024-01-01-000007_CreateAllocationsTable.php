<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAllocationsTable extends Migration
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
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['ASSIGNED', 'WAITLIST', 'REST_WAITLIST', 'MANUAL'],
                'default' => 'ASSIGNED',
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
        $this->forge->createTable('allocations');
    }

    public function down()
    {
        $this->forge->dropTable('allocations');
    }
}
