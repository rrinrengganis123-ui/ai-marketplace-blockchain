# AI Marketplace Blockchain
Platform transaksi dataset untuk kebutuhan riset AI berbasis Blockchain.

## Teknologi
- CodeIgniter 4.7.3
- PHP 8.2
- MySQL

## Struktur Data
- **Queue (FIFO)** - Mengelola antrian transaksi
- **Singly Linked List** - Rantai blok dengan prevHash
- **Merkle Tree** - Verifikasi transaksi

## Fitur
- Enqueue & Dequeue transaksi dataset AI
- Mine Block - proses transaksi ke blok
- Validasi Chain - cek integritas rantai
- Tamper Block - simulasi manipulasi data
- Merkle Tree Visual - pohon verifikasi
- Verifikasi Transaksi - Merkle Proof

## Kategori Dataset
NLP, Computer Vision, Audio, Tabular, Multimodal,
Reinforcement Learning, Generative AI, Medical, Finance, dll.

## Cara Install
1. Clone repository
2. Import database `ai_marketplace.sql`
3. Konfigurasi `.env` sesuai database lokal
4. Jalankan `php spark serve`
5. Akses `http://localhost:8080/blockchain`

## Author
Nama: [Ririn]
NIM: [105250036]