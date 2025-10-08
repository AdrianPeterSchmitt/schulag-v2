<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'remember_token',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[COORDINATOR,TEACHER]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Diese E-Mail-Adresse ist bereits registriert.',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Password Hashing
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash Password vor Insert/Update
     * 
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function isCoordinator(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'COORDINATOR';
    }

    public function isTeacher(int $userId): bool
    {
        $user = $this->find($userId);
        return $user && $user['role'] === 'TEACHER';
    }
}
