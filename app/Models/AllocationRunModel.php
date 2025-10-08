<?php

namespace App\Models;

use CodeIgniter\Model;

class AllocationRunModel extends Model
{
    protected $table = 'allocation_runs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'schoolyear',
        'run_date',
        'total_students',
        'total_assigned',
        'total_waitlist',
        'total_rest_waitlist',
        'total_offers',
        'total_capacity',
        'algorithm_version',
        'metadata',
        'created_by',
    ];

    // Timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'schoolyear' => 'required|min_length[9]',
        'total_students' => 'required|integer',
        'total_assigned' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get den neuesten Run
     * 
     * @return array<string, mixed>|null
     */
    public function getLatest(): ?array
    {
        return $this->orderBy('run_date', 'DESC')->first();
    }

    /**
     * Get den neuesten Run für ein Schuljahr
     * 
     * @return array<string, mixed>|null
     */
    public function getLatestForSchoolyear(string $schoolyear): ?array
    {
        return $this->where('schoolyear', $schoolyear)
                    ->orderBy('run_date', 'DESC')
                    ->first();
    }

    /**
     * Get die letzten N Runs
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->orderBy('run_date', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get alle Runs für ein Schuljahr
     * 
     * @return array<int, array<string, mixed>>
     */
    public function getForSchoolyear(string $schoolyear): array
    {
        return $this->where('schoolyear', $schoolyear)
                    ->orderBy('run_date', 'DESC')
                    ->findAll();
    }

    /**
     * Erstelle einen neuen Run mit allen Statistiken
     * 
     * @param array<string, mixed> $stats
     * @return int|bool Run ID oder false bei Fehler
     */
    public function createRun(array $stats)
    {
        $data = [
            'schoolyear' => $stats['schoolyear'] ?? getCurrentSchoolyear(),
            'run_date' => date('Y-m-d H:i:s'),
            'total_students' => $stats['total_students'] ?? 0,
            'total_assigned' => $stats['total_assigned'] ?? 0,
            'total_waitlist' => $stats['total_waitlist'] ?? 0,
            'total_rest_waitlist' => $stats['total_rest_waitlist'] ?? 0,
            'total_offers' => $stats['total_offers'] ?? 0,
            'total_capacity' => $stats['total_capacity'] ?? 0,
            'algorithm_version' => $stats['algorithm_version'] ?? 'v1.0',
            'metadata' => isset($stats['metadata']) ? json_encode($stats['metadata']) : null,
            'created_by' => $stats['created_by'] ?? session()->get('user')['id'] ?? null,
        ];

        return $this->insert($data);
    }

    /**
     * Get Run mit dekodierter Metadata
     * 
     * @return array<string, mixed>|null
     */
    public function getWithMetadata(int $runId): ?array
    {
        $run = $this->find($runId);
        
        if ($run && isset($run['metadata'])) {
            $run['metadata'] = json_decode($run['metadata'], true);
        }
        
        return $run;
    }

    /**
     * Vergleiche zwei Runs
     * 
     * @return array<string, mixed>
     */
    public function compareRuns(int $runId1, int $runId2): array
    {
        $run1 = $this->find($runId1);
        $run2 = $this->find($runId2);

        if (!$run1 || !$run2) {
            return ['error' => 'Einer oder beide Runs nicht gefunden'];
        }

        return [
            'run1' => $run1,
            'run2' => $run2,
            'differences' => [
                'total_students' => $run2['total_students'] - $run1['total_students'],
                'total_assigned' => $run2['total_assigned'] - $run1['total_assigned'],
                'total_waitlist' => $run2['total_waitlist'] - $run1['total_waitlist'],
                'total_rest_waitlist' => $run2['total_rest_waitlist'] - $run1['total_rest_waitlist'],
            ],
            'improvement' => [
                'assigned_rate_run1' => $run1['total_students'] > 0 ? round(($run1['total_assigned'] / $run1['total_students']) * 100, 2) : 0,
                'assigned_rate_run2' => $run2['total_students'] > 0 ? round(($run2['total_assigned'] / $run2['total_students']) * 100, 2) : 0,
            ],
        ];
    }
}

