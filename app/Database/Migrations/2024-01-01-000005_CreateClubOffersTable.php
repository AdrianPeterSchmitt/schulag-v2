<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClubOffersTable extends Migration
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
            'club_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'schoolyear' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'z.B. "2024/2025"',
            ],
            'capacity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 15,
            ],
            'room' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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
        $this->forge->addForeignKey('club_id', 'clubs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['club_id', 'schoolyear']);
        $this->forge->createTable('club_offers');
    }

    public function down()
    {
        $this->forge->dropTable('club_offers');
    }
}
