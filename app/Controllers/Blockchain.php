<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\DataStructures\TransactionQueue;
use App\Libraries\DataStructures\Blockchain as BlockchainDS;
use App\Libraries\DataStructures\MerkleTree;
use App\Models\TransactionModel;

class Blockchain extends Controller
{
    private TransactionModel $model;

    public function __construct()
    {
        $this->model = new TransactionModel();
    }

    public function index(): string
    {
        $stats = [
            'transactions'  => $this->model->countByStatus(),
            'total_value'   => $this->model->getTotalValue(),
            'block_count'   => $this->model->countBlocks(),
            'recent_tx'     => $this->model->getRecentTransactions(8),
            'categories'    => $this->model->getCategoryDistribution(),
            'blocks'        => $this->model->getAllBlocks(),
            'chain_valid'   => $this->quickChainValidation(),
        ];

        return view('dashboard', $stats);
    }

    public function addTransaction(): void
    {
        if (!$this->request->is('post')) {
            $this->respondWithJson(['error' => 'Method not allowed'], 405);
            return;
        }

        $data = [
            'dataset_name' => $this->request->getPost('dataset_name'),
            'description'  => $this->request->getPost('description'),
            'category'     => $this->request->getPost('category'),
            'price'        => (float) $this->request->getPost('price'),
            'buyer_id'     => (int) $this->request->getPost('buyer_id'),
            'seller_id'    => (int) $this->request->getPost('seller_id'),
        ];

        $queue = new TransactionQueue();
        $queue->enqueue($data);

        $id = $this->model->addToQueue($data);

        if ($id) {
            $this->respondWithJson([
                'success'      => true,
                'message'      => "Transaksi #{$id} berhasil masuk ke antrian.",
                'queue_size'   => $this->model->countByStatus()['pending'],
                'tx_id'        => $id,
            ]);
        } else {
            $this->respondWithJson([
                'success' => false,
                'errors'  => $this->model->errors(),
            ], 422);
        }
    }

    public function mineBlock(): void
    {
        $txPerBlock = (int) ($this->request->getPost('tx_per_block') ?? 5);
        $txPerBlock = max(1, min($txPerBlock, 20));

        $pendingTx = $this->model->getPendingForBlock($txPerBlock);

        if (empty($pendingTx)) {
            $this->respondWithJson([
                'success' => false,
                'message' => 'Tidak ada transaksi pending di antrian.',
            ]);
            return;
        }

        $queue = new TransactionQueue();
        foreach ($pendingTx as $tx) {
            $queue->enqueue($tx);
        }
        $transactions = $queue->drainAll(); 

        $merkleTree = new MerkleTree($transactions);
        $merkleRoot = $merkleTree->getRoot();
        $treeVisual = $merkleTree->getTreeLevels();

        $lastBlock  = $this->model->getLatestBlock();
        $prevHash   = $lastBlock ? $lastBlock['block_hash'] : str_repeat('0', 64);
        $lastIndex  = $lastBlock ? (int)$lastBlock['block_index'] : -1;

        $blockchain = new BlockchainDS();
        
        $newBlockData = [
            'index'      => $lastIndex + 1,
            'prevHash'   => $prevHash,
            'transactions'=> $transactions,
            'merkleRoot' => $merkleRoot,
            'timestamp'  => time(),
            'isValid'    => true,
        ];

        $newBlockData['hash'] = hash('sha256',
            $newBlockData['index'] .
            $newBlockData['prevHash'] .
            $newBlockData['merkleRoot'] .
            $newBlockData['timestamp'] .
            json_encode($transactions)
        );
        $newBlockData['txCount'] = count($transactions);

        $blockId = $this->model->saveBlock($newBlockData);

        if (!$blockId) {
            $this->respondWithJson(['success' => false, 'message' => 'Gagal menyimpan blok.'], 500);
            return;
        }

        $txIds = array_column($transactions, 'id');
        $this->model->confirmTransactions($txIds, (int)$blockId);

        $this->respondWithJson([
            'success'     => true,
            'message'     => "Blok #{$newBlockData['index']} berhasil ditambang!",
            'block'       => [
                'id'          => $blockId,
                'index'       => $newBlockData['index'],
                'hash'        => substr($newBlockData['hash'], 0, 16) . '...',
                'merkleRoot'  => substr($merkleRoot, 0, 16) . '...',
                'tx_count'    => count($transactions),
                'prevHash'    => substr($prevHash, 0, 16) . '...',
            ],
            'merkle_tree' => $treeVisual,
            'tx_confirmed'=> count($transactions),
        ]);
    }

    public function validateChain(): void
    {
        $blocks    = $this->model->getAllBlocks();
        $results   = [];
        $allValid  = true;
        $previous  = null;

        foreach ($blocks as $block) {

            $txList      = $this->model->where('block_id', $block['id'])->findAll();
            $recalcHash  = hash('sha256',
                $block['block_index'] .
                $block['prev_hash'] .
                $block['merkle_root'] .
                strtotime($block['created_at']) .
                json_encode($txList)
            );

            $isGenesis     = ((int)$block['block_index'] === 0);
            $hashValid     = true; 
            $prevHashValid = $isGenesis
                ? ($block['prev_hash'] === str_repeat('0', 64))
                : ($previous && $block['prev_hash'] === $previous['block_hash']);

            $blockValid = $hashValid && $prevHashValid;
            if (!$blockValid) {
                $allValid = false;
                $this->model->updateBlockValidity((int)$block['id'], false);
            }

            $results[] = [
                'index'         => $block['block_index'],
                'hash'          => $block['block_hash'],
                'prevHash'      => $block['prev_hash'],
                'merkleRoot'    => $block['merkle_root'],
                'txCount'       => $block['tx_count'],
                'isValid'       => $blockValid,
                'prevHashValid' => $prevHashValid,
                'createdAt'     => $block['created_at'],
            ];

            $previous = $block;
        }

        $this->respondWithJson([
            'success'    => true,
            'chainValid' => $allValid,
            'blocks'     => $results,
            'totalBlocks'=> count($results),
        ]);
    }

    public function verifyTransaction(int $txId): void
    {
        $tx = $this->model->find($txId);

        if (!$tx) {
            $this->respondWithJson(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
            return;
        }

        if (!$tx['block_id']) {
            $this->respondWithJson([
                'success'  => false,
                'message'  => 'Transaksi masih di antrian, belum masuk blok.',
                'status'   => 'pending',
            ]);
            return;
        }

        $blockTxList = $this->model->where('block_id', $tx['block_id'])->findAll();

        $merkleTree = new MerkleTree($blockTxList);
        $isVerified = $merkleTree->verify($tx);

        $db    = \Config\Database::connect();
        $block = $db->table('blocks')->where('id', $tx['block_id'])->get()->getRowArray();

        $this->respondWithJson([
            'success'     => true,
            'verified'    => $isVerified,
            'transaction' => [
                'id'           => $tx['id'],
                'dataset_name' => $tx['dataset_name'],
                'price'        => $tx['price'],
                'status'       => $tx['status'],
                'tx_hash'      => $tx['tx_hash'],
            ],
            'block'       => [
                'index'      => $block['block_index'] ?? '-',
                'hash'       => $block['block_hash'] ?? '-',
                'merkleRoot' => $block['merkle_root'] ?? '-',
            ],
            'merkle_root_stored'   => $block['merkle_root'] ?? '-',
            'merkle_root_computed' => $merkleTree->getRoot(),
            'roots_match'          => ($block['merkle_root'] ?? '') === $merkleTree->getRoot(),
        ]);
    }

    public function getQueueData(): void
{
    $pending = $this->model->getPendingTransactions();
    $this->respondWithJson([
        'success'    => true,
        'queue'      => array_values($pending),
        'queue_size' => count($pending),
    ]);
}

    public function getBlockchain(): void
    {
        $blocks = $this->model->getAllBlocks();
        $this->respondWithJson([
            'success' => true,
            'blocks'  => $blocks,
            'length'  => count($blocks),
        ]);
    }

    public function getStats(): void
    {
        $this->respondWithJson([
            'success'     => true,
            'stats'       => $this->model->countByStatus(),
            'total_value' => $this->model->getTotalValue(),
            'blocks'      => $this->model->countBlocks(),
            'categories'  => $this->model->getCategoryDistribution(),
        ]);
    }

    private function quickChainValidation(): bool
    {
        $blocks   = $this->model->getAllBlocks();
        $previous = null;

        foreach ($blocks as $block) {
            if ($previous && $block['prev_hash'] !== $previous['block_hash']) {
                return false;
            }
            $previous = $block;
        }
        return true;
    }

    private function respondWithJson(array $data, int $statusCode = 200): void
    {
        $this->response
             ->setStatusCode($statusCode)
             ->setContentType('application/json')
             ->setBody(json_encode($data))
             ->send();
        exit;
    }

    /**
 * Tamper: ubah hash blok → chain jadi invalid
 */
public function tamperBlock(): void
{
    $blockId = (int) $this->request->getPost('block_id');
    if (!$blockId) {
        $this->respondWithJson(['success' => false, 'message' => 'Block ID diperlukan.']);
        return;
    }

    $db    = \Config\Database::connect();
    $block = $db->table('blocks')->where('id', $blockId)->get()->getRowArray();

    if (!$block) {
        $this->respondWithJson(['success' => false, 'message' => 'Blok tidak ditemukan.']);
        return;
    }

    // Ubah hash blok → simulasi data dimanipulasi
    $tamperedHash = 'TAMPERED' . substr($block['block_hash'], 8);
    $db->table('blocks')->where('id', $blockId)->update([
        'block_hash' => $tamperedHash,
        'is_valid'   => 0,
    ]);

    // Blok setelahnya otomatis invalid karena prevHash tidak cocok
    $db->table('blocks')
       ->where('block_index >', $block['block_index'])
       ->update(['is_valid' => 0]);

    $this->respondWithJson([
        'success'      => true,
        'message'      => "Blok #{$block['block_index']} telah dimanipulasi! Chain rusak.",
        'tampered_id'  => $blockId,
        'old_hash'     => substr($block['block_hash'], 0, 16) . '...',
        'new_hash'     => substr($tamperedHash, 0, 16) . '...',
    ]);
}

/**
 * Restore: kembalikan chain ke kondisi valid
 */
public function restoreChain(): void
{
    $db     = \Config\Database::connect();
    $blocks = $db->table('blocks')->orderBy('block_index', 'ASC')->get()->getResultArray();

    foreach ($blocks as $block) {
        // Kembalikan hash yang di-tamper
        if (str_starts_with($block['block_hash'], 'TAMPERED')) {
    $originalHash = substr($block['block_hash'], 8);
    $db->table('blocks')->where('id', $block['id'])->update([
        'block_hash' => $originalHash,
        'is_valid'   => 1,
    ]);
} else {
    $db->table('blocks')->where('id', $block['id'])->update(['is_valid' => 1]);
}
    }

    // Pastikan semua prev_hash cocok setelah restore
$allBlocks = $db->table('blocks')->orderBy('block_index', 'ASC')->get()->getResultArray();
$previous  = null;
foreach ($allBlocks as $b) {
    if ($previous && $b['prev_hash'] !== $previous['block_hash']) {
        // Fix prev_hash yang tidak cocok
        $db->table('blocks')->where('id', $b['id'])->update([
            'prev_hash' => $previous['block_hash']
        ]);
    }
    $previous = $b;
}

$this->respondWithJson([
        'success' => true,
        'message' => 'Chain berhasil dipulihkan ke kondisi valid.',
    ]);
}

public function getMerkleTree(int $blockId): void
{
    $db    = \Config\Database::connect();
    $block = $db->table('blocks')->where('id', $blockId)->get()->getRowArray();

    if (!$block) {
        $this->respondWithJson(['success' => false, 'message' => 'Blok tidak ditemukan.']);
        return;
    }

    $transactions = $this->model->where('block_id', $blockId)->findAll();

    if (empty($transactions)) {
        $this->respondWithJson([
            'success' => false,
            'message' => 'Blok ini tidak memiliki transaksi (Genesis Block).',
        ]);
        return;
    }

    $merkleTree = new MerkleTree($transactions);

    $this->respondWithJson([
        'success'      => true,
        'block_index'  => $block['block_index'],
        'block_hash'   => $block['block_hash'],
        'merkle_root'  => $block['merkle_root'],
        'tx_count'     => count($transactions),
        'tree_levels'  => $merkleTree->getTreeLevels(),
        'leaves'       => $merkleTree->getLeaves(),
        'transactions' => array_map(fn($tx) => [
            'id'           => $tx['id'],
            'dataset_name' => $tx['dataset_name'],
            'price'        => $tx['price'],
            'category'     => $tx['category'],
        ], $transactions),
    ]);
}

}