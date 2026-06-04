<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    
    protected $table         = 'transactions';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'dataset_name',
        'description',
        'category',
        'price',
        'buyer_id',
        'seller_id',
        'status',       
        'block_id',     
        'tx_hash',      
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'dataset_name' => 'required|min_length[3]|max_length[200]',
        'price'        => 'required|numeric|greater_than[0]',
        'buyer_id'     => 'required|integer',
        'seller_id'    => 'required|integer',
        'category' => 'required|in_list[nlp,vision,tabular,audio,multimodal,reinforcement,generative,timeseries,medical,graph,pointcloud,geospatial,cybersecurity,finance,recommendation,robotics,climate,education,legal,other]',
    ];

    protected $validationMessages = [
        'dataset_name' => ['required' => 'Nama dataset wajib diisi.'],
        'price'        => ['required' => 'Harga wajib diisi.', 'numeric' => 'Harga harus berupa angka.'],
        'category'     => ['in_list'  => 'Kategori tidak valid.'],
    ];

    public function addToQueue(array $data): int|false
    {
        $data['status']  = 'pending';
        $data['tx_hash'] = hash('sha256',
            ($data['dataset_name'] ?? '') .
            ($data['buyer_id'] ?? '') .
            ($data['price'] ?? '') .
            microtime()
        );

        return $this->insert($data, true);
    }

    public function confirmTransactions(array $ids, int $blockId): bool
    {
        return $this->whereIn('id', $ids)
                    ->set([
                        'status'   => 'confirmed',
                        'block_id' => $blockId,
                    ])
                    ->update();
    }

    public function rejectTransaction(int $id, string $reason = ''): bool
    {
        return $this->update($id, [
            'status'      => 'rejected',
            'description' => $reason,
        ]);
    }

    public function saveBlock(array $blockData): int|false
    {
        $db = \Config\Database::connect();

        $data = [
            'block_index'  => $blockData['index'],
            'block_hash'   => $blockData['hash'],
            'prev_hash'    => $blockData['prevHash'],
            'merkle_root'  => $blockData['merkleRoot'],
            'tx_count'     => $blockData['txCount'],
            'is_valid'     => $blockData['isValid'] ? 1 : 0,
            'created_at'   => date('Y-m-d H:i:s', $blockData['timestamp']),
        ];

        $db->table('blocks')->insert($data);
        return $db->insertID() ?: false;
    }

    public function getAllBlocks(): array
    {
        $db = \Config\Database::connect();
        return $db->table('blocks')
                  ->orderBy('block_index', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    public function getBlockWithTransactions(int $blockId): array
    {
        $db    = \Config\Database::connect();
        $block = $db->table('blocks')->where('id', $blockId)->get()->getRowArray();

        if (!$block) return [];

        $block['transactions'] = $this->where('block_id', $blockId)->findAll();
        return $block;
    }

    public function getLatestBlock(): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('blocks')
                  ->orderBy('block_index', 'DESC')
                  ->limit(1)
                  ->get()
                  ->getRowArray() ?: null;
    }

    public function updateBlockValidity(int $blockId, bool $isValid): bool
    {
        $db = \Config\Database::connect();
        return $db->table('blocks')
                  ->where('id', $blockId)
                  ->update(['is_valid' => $isValid ? 1 : 0]);
    }

    public function getTotalValue(): float
{
    $db  = \Config\Database::connect();
    $row = $db->table('transactions')
              ->selectSum('price', 'total')
              ->where('status', 'confirmed')
              ->get()
              ->getRowArray();
    return (float) ($row['total'] ?? 0);
}

public function getRecentTransactions(int $limit = 10): array
{
    $db = \Config\Database::connect();
    return $db->table('transactions')
              ->orderBy('created_at', 'DESC')
              ->limit($limit)
              ->get()
              ->getResultArray();
}

public function getCategoryDistribution(): array
{
    $db = \Config\Database::connect();
    return $db->table('transactions')
              ->select('category, COUNT(*) as total, SUM(price) as revenue')
              ->groupBy('category')
              ->orderBy('total', 'DESC')
              ->get()
              ->getResultArray();
}


public function countBlocks(): int
{
    $db = \Config\Database::connect();
    return (int) $db->table('blocks')->countAll();
}

public function countByStatus(): array
{
    $db     = \Config\Database::connect();
    $result = ['pending' => 0, 'confirmed' => 0, 'rejected' => 0];
    $rows   = $db->table('transactions')
                 ->select('status, COUNT(*) as total')
                 ->groupBy('status')
                 ->get()
                 ->getResultArray();
    foreach ($rows as $row) {
        if (isset($result[$row['status']])) {
            $result[$row['status']] = (int) $row['total'];
        }
    }
    return $result;
}

public function getPendingTransactions(): array
{
    $db = \Config\Database::connect();
    return $db->table('transactions')
              ->where('status', 'pending')
              ->where('block_id IS NULL', null, false)
              ->orderBy('created_at', 'ASC')
              ->get()
              ->getResultArray();
}

public function getPendingForBlock(int $limit = 5): array
{
    $db = \Config\Database::connect();
    return $db->table('transactions')
              ->where('status', 'pending')
              ->where('block_id IS NULL', null, false)
              ->orderBy('created_at', 'ASC')
              ->limit($limit)
              ->get()
              ->getResultArray();
    }
}