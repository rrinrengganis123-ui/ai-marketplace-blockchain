<?php

namespace App\Libraries\DataStructures;

class QueueNode
{
    public array  $data;
    public ?QueueNode $next;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->next = null;
    }
}

class TransactionQueue
{
    private ?QueueNode $front;   
    private ?QueueNode $rear;    
    private int        $size;

    public function __construct()
    {
        $this->front = null;
        $this->rear  = null;
        $this->size  = 0;
    }

    public function enqueue(array $transaction): void
    {
        $node = new QueueNode($transaction);

        if ($this->rear === null) {
            $this->front = $node;
            $this->rear  = $node;
        } else {
            $this->rear->next = $node;
            $this->rear       = $node;
        }

        $this->size++;
    }

    public function dequeue(): ?array
    {
        if ($this->isEmpty()) {
            return null;
        }

        $data        = $this->front->data;
        $this->front = $this->front->next;

        if ($this->front === null) {
            $this->rear = null;
        }

        $this->size--;
        return $data;
    }

    public function peek(): ?array
    {
        return $this->front?->data;
    }

    public function isEmpty(): bool
    {
        return $this->front === null;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function drainAll(): array
    {
        $transactions = [];
        while (!$this->isEmpty()) {
            $transactions[] = $this->dequeue();
        }
        return $transactions;
    }
}

class MerkleTree
{
    private array $leaves;    
    private array $tree;      
    private ?string $root;

    public function __construct(array $transactions)
    {
        $this->leaves = [];
        $this->tree   = [];
        $this->root   = null;

        $this->build($transactions);
    }

    private function hashTransaction(array $tx): string
    {
        $payload = $tx['id'] . $tx['dataset_name'] . $tx['price'] . $tx['buyer_id'];
        return hash('sha256', $payload);
    }

    private function hashPair(string $left, string $right): string
    {
        return hash('sha256', $left . $right);
    }

    private function build(array $transactions): void
    {
        if (empty($transactions)) {
            $this->root = hash('sha256', 'empty_block');
            return;
        }

        $currentLevel = [];
        foreach ($transactions as $tx) {
            $currentLevel[] = $this->hashTransaction($tx);
        }
        $this->tree[] = $currentLevel;
        $this->leaves = $currentLevel;

        while (count($currentLevel) > 1) {
            $nextLevel = [];
            $count     = count($currentLevel);

            for ($i = 0; $i < $count; $i += 2) {
                $left  = $currentLevel[$i];
                
                $right = $currentLevel[$i + 1] ?? $currentLevel[$i];
                $nextLevel[] = $this->hashPair($left, $right);
            }

            $this->tree[]  = $nextLevel;
            $currentLevel  = $nextLevel;
        }

        $this->root = $currentLevel[0];
    }

    public function getRoot(): string
    {
        return $this->root ?? hash('sha256', 'empty');
    }

    public function getLeaves(): array
    {
        return $this->leaves;
    }

    public function getTreeLevels(): array
    {
        return $this->tree;
    }

    public function verify(array $transaction): bool
    {
        $txHash = $this->hashTransaction($transaction);
        return in_array($txHash, $this->leaves, true);
    }
}

class BlockNode
{
    public int     $index;
    public string  $hash;          
    public string  $prevHash;      
    public string  $merkleRoot;    
    public array   $transactions;  
    public int     $timestamp;
    public bool    $isValid;
    public ?BlockNode $next;       

    public function __construct(
        int    $index,
        string $prevHash,
        array  $transactions,
        string $merkleRoot
    ) {
        $this->index        = $index;
        $this->prevHash     = $prevHash;
        $this->transactions = $transactions;
        $this->merkleRoot   = $merkleRoot;
        $this->timestamp    = time();
        $this->isValid      = true;
        $this->next         = null;
        $this->hash         = $this->calculateHash();
    }

    public function calculateHash(): string
    {
        $payload = $this->index
                 . $this->prevHash
                 . $this->merkleRoot
                 . $this->timestamp
                 . json_encode($this->transactions);

        return hash('sha256', $payload);
    }
}

class Blockchain
{
    private ?BlockNode $head;   
    private ?BlockNode $tail;   
    private int        $length;

    public function __construct()
    {
        $this->head   = null;
        $this->tail   = null;
        $this->length = 0;

        $this->createGenesisBlock();
    }

    private function createGenesisBlock(): void
    {
        $genesis = new BlockNode(
            index:        0,
            prevHash:     str_repeat('0', 64),
            transactions: [],
            merkleRoot:   hash('sha256', 'genesis_ai_marketplace')
        );

        $this->head   = $genesis;
        $this->tail   = $genesis;
        $this->length = 1;
    }

    public function addBlock(array $transactions): BlockNode
    {
        $merkleTree = new MerkleTree($transactions);
        $merkleRoot = $merkleTree->getRoot();

        $newBlock = new BlockNode(
            index:        $this->length,
            prevHash:     $this->tail->hash,
            transactions: $transactions,
            merkleRoot:   $merkleRoot
        );

        $this->tail->next = $newBlock;
        $this->tail       = $newBlock;
        $this->length++;

        return $newBlock;
    }

    public function validateChain(): array
    {
        $results  = [];
        $current  = $this->head;
        $previous = null;

        while ($current !== null) {
            $recalculated = $current->calculateHash();
            $hashValid    = ($recalculated === $current->hash);

            $prevHashValid = ($previous === null)
                ? ($current->prevHash === str_repeat('0', 64))
                : ($current->prevHash === $previous->hash);

            $current->isValid = $hashValid && $prevHashValid;

            $results[] = [
                'index'         => $current->index,
                'hash'          => $current->hash,
                'prevHash'      => $current->prevHash,
                'merkleRoot'    => $current->merkleRoot,
                'isValid'       => $current->isValid,
                'hashValid'     => $hashValid,
                'prevHashValid' => $prevHashValid,
                'txCount'       => count($current->transactions),
            ];

            $previous = $current;
            $current  = $current->next;
        }

        return $results;
    }

    public function getAllBlocks(): array
    {
        $blocks  = [];
        $current = $this->head;

        while ($current !== null) {
            $blocks[]= [
                'index'        => $current->index,
                'hash'         => $current->hash,
                'prevHash'     => $current->prevHash,
                'merkleRoot'   => $current->merkleRoot,
                'transactions' => $current->transactions,
                'timestamp'    => $current->timestamp,
                'isValid'      => $current->isValid,
                'txCount'      => count($current->transactions),
            ];
            $current = $current->next;
        }

        return $blocks;
    }

    public function getLength(): int   { return $this->length; }
    public function getHead(): ?BlockNode { return $this->head; }
    public function getTail(): ?BlockNode { return $this->tail; }
}