<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Marketplace — Blockchain Data</title>
<style>
  
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg:        #0a0e1a;
    --surface:   #111827;
    --card:      #1a2235;
    --border:    #1e3a5f;
    --accent:    #00d4ff;
    --accent2:   #7c3aed;
    --green:     #10b981;
    --yellow:    #f59e0b;
    --red:       #ef4444;
    --text:      #e2e8f0;
    --muted:     #64748b;
    --font:      'Courier New', monospace;
  }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font);
    font-size: 13px;
    min-height: 100vh;
  }

  .header {
    background: linear-gradient(135deg, #0a0e1a 0%, #0d1b2e 50%, #0a0e1a 100%);
    border-bottom: 1px solid var(--border);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky; top: 0; z-index: 100;
  }
  .logo { display: flex; align-items: center; gap: 12px; }
  .logo-icon {
    width: 36px; height: 36px; border-radius: 8px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
  }
  .logo-text { font-size: 16px; font-weight: 700; color: var(--accent); letter-spacing: 1px; }
  .logo-sub  { font-size: 10px; color: var(--muted); }
  .header-right { display: flex; align-items: center; gap: 16px; }
  .chain-badge {
    padding: 4px 12px; border-radius: 20px; font-size: 11px;
    background: rgba(16,185,129,.15); border: 1px solid var(--green);
    color: var(--green);
  }
  .chain-badge.invalid {
    background: rgba(239,68,68,.15); border-color: var(--red); color: var(--red);
  }

  .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px; margin-bottom: 24px;
  }
  .stat-card {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 12px; padding: 20px;
    position: relative; overflow: hidden;
    transition: border-color .2s, transform .2s;
  }
  .stat-card:hover { border-color: var(--accent); transform: translateY(-2px); }
  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, var(--accent), var(--accent2));
  }
  .stat-label { font-size: 10px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }
  .stat-value { font-size: 28px; font-weight: 700; color: var(--accent); margin: 8px 0 4px; }
  .stat-sub   { font-size: 11px; color: var(--muted); }
  .stat-icon  { position: absolute; right: 16px; top: 16px; font-size: 24px; opacity: .3; }

  .tabs { display: flex; gap: 4px; margin-bottom: 20px; border-bottom: 1px solid var(--border); }
  .tab {
    padding: 10px 20px; border: none; background: none;
    color: var(--muted); font-family: var(--font); font-size: 12px;
    cursor: pointer; letter-spacing: .5px; border-bottom: 2px solid transparent;
    transition: all .2s;
  }
  .tab:hover { color: var(--text); }
  .tab.active { color: var(--accent); border-bottom-color: var(--accent); }

  .tab-content { display: none; }
  .tab-content.active { display: block; }

  .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
  @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

  .panel {
    background: var(--card); border: 1px solid var(--border);
    border-radius: 12px; overflow: hidden;
  }
  .panel-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(0,212,255,.03);
  }
  .panel-title { font-size: 12px; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; }
  .panel-body  { padding: 20px; }

  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .form-group { display: flex; flex-direction: column; gap: 6px; }
  .form-group.full { grid-column: 1 / -1; }
  label { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; }
  input, select, textarea {
    background: var(--surface); border: 1px solid var(--border);
    color: var(--text); font-family: var(--font); font-size: 13px;
    padding: 10px 12px; border-radius: 8px; outline: none;
    transition: border-color .2s;
  }
  input:focus, select:focus, textarea:focus { border-color: var(--accent); }
  textarea { resize: vertical; min-height: 70px; }

  .btn {
    padding: 10px 20px; border-radius: 8px; border: none;
    font-family: var(--font); font-size: 12px; cursor: pointer;
    letter-spacing: .5px; text-transform: uppercase; transition: all .2s;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .btn-primary {
    background: linear-gradient(135deg, var(--accent), #0099bb);
    color: #000; font-weight: 700;
  }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 20px rgba(0,212,255,.3); }
  .btn-success {
    background: linear-gradient(135deg, var(--green), #059669);
    color: #fff; font-weight: 700;
  }
  .btn-success:hover { box-shadow: 0 4px 20px rgba(16,185,129,.3); }
  .btn-outline {
    background: transparent; border: 1px solid var(--border);
    color: var(--text);
  }
  .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
  .btn:disabled { opacity: .4; cursor: not-allowed; transform: none !important; }

  .table-wrap { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  th {
    padding: 10px 14px; text-align: left; font-size: 10px;
    color: var(--muted); text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1px solid var(--border); background: rgba(0,0,0,.2);
  }
  td { padding: 12px 14px; border-bottom: 1px solid rgba(30,58,95,.4); }
  tr:hover td { background: rgba(0,212,255,.03); }
  tr:last-child td { border-bottom: none; }

  .badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    font-size: 10px; text-transform: uppercase; letter-spacing: .5px;
  }
  .badge-pending  { background: rgba(245,158,11,.15); color: var(--yellow); border: 1px solid rgba(245,158,11,.3); }
  .badge-confirmed{ background: rgba(16,185,129,.15);  color: var(--green);  border: 1px solid rgba(16,185,129,.3); }
  .badge-rejected { background: rgba(239,68,68,.15);   color: var(--red);    border: 1px solid rgba(239,68,68,.3); }
  .badge-valid    { background: rgba(16,185,129,.15);  color: var(--green);  border: 1px solid var(--green); }
  .badge-invalid  { background: rgba(239,68,68,.15);   color: var(--red);    border: 1px solid var(--red); }

  .chain-visual { display: flex; flex-direction: column; gap: 0; }
  .block-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 10px; padding: 16px; position: relative;
    transition: border-color .2s;
  }
  .block-card.invalid-block { border-color: var(--red); }
  .block-card.genesis { border-color: var(--accent2); }
  .block-connector {
    display: flex; align-items: center; padding: 6px 0;
    color: var(--muted); font-size: 11px; gap: 8px;
  }
  .connector-line {
    flex: 1; height: 1px;
    background: repeating-linear-gradient(90deg, var(--border) 0, var(--border) 6px, transparent 6px, transparent 12px);
  }
  .connector-arrow { color: var(--accent); font-size: 14px; }
  .block-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
  .block-index {
    width: 36px; height: 36px; border-radius: 8px;
    background: linear-gradient(135deg, var(--accent2), #4c1d95);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700; color: #fff; flex-shrink: 0;
  }
  .block-index.genesis-idx { background: linear-gradient(135deg, var(--accent), #0099bb); }
  .block-meta { flex: 1; }
  .block-hash-display { font-size: 11px; color: var(--accent); font-family: monospace; }
  .block-time  { font-size: 10px; color: var(--muted); margin-top: 2px; }
  .block-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 11px; }
  .block-field label { color: var(--muted); display: block; margin-bottom: 2px; font-size: 10px; }
  .block-field span  { color: var(--text); font-family: monospace; }
  .hash-short { color: var(--yellow); }

  .merkle-container { font-family: monospace; font-size: 11px; }
  .merkle-level { display: flex; justify-content: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap; }
  .merkle-node {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 6px; padding: 6px 10px;
    color: var(--text); font-size: 10px; max-width: 120px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    transition: border-color .2s;
  }
  .merkle-node.root  { border-color: var(--accent);  color: var(--accent);  font-weight: 700; }
  .merkle-node.leaf  { border-color: var(--accent2); color: #a78bfa; }
  .merkle-level-label { text-align: center; color: var(--muted); font-size: 10px; margin-bottom: 4px; }

  .queue-visual {
    display: flex; align-items: center; gap: 0;
    overflow-x: auto; padding-bottom: 8px;
  }
  .queue-item {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 8px; padding: 12px; min-width: 140px;
    flex-shrink: 0; text-align: center;
    position: relative;
  }
  .queue-item:not(:last-child)::after {
    content: '→'; position: absolute; right: -14px; top: 50%;
    transform: translateY(-50%); color: var(--accent); font-size: 16px; z-index: 1;
  }
  .queue-item + .queue-item { margin-left: 20px; }
  .queue-front { border-color: var(--green); }
  .queue-rear  { border-color: var(--yellow); }
  .queue-label { font-size: 9px; color: var(--muted); text-transform: uppercase; margin-bottom: 6px; }
  .queue-name  { font-size: 12px; color: var(--text); margin-bottom: 4px; }
  .queue-price { font-size: 13px; font-weight: 700; color: var(--accent); }
  .queue-empty { color: var(--muted); text-align: center; padding: 20px; font-size: 12px; }

  .alert {
    padding: 12px 16px; border-radius: 8px; font-size: 12px;
    margin-bottom: 16px; display: flex; align-items: center; gap: 10px;
  }
  .alert-success { background: rgba(16,185,129,.1); border: 1px solid var(--green); color: var(--green); }
  .alert-error   { background: rgba(239,68,68,.1);  border: 1px solid var(--red);   color: var(--red); }
  .alert-info    { background: rgba(0,212,255,.1);  border: 1px solid var(--accent); color: var(--accent); }
  .alert { display: none; }
  .alert.show { display: flex; }

  .spinner {
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.2);
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin .6s linear infinite;
    display: inline-block;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  .mono   { font-family: monospace; }
  .text-accent { color: var(--accent); }
  .text-muted  { color: var(--muted); }
  .text-green  { color: var(--green); }
  .text-yellow { color: var(--yellow); }
  .text-red    { color: var(--red); }
  .mt-16 { margin-top: 16px; }
  .empty-state { text-align: center; padding: 40px; color: var(--muted); }
  .empty-state .icon { font-size: 40px; margin-bottom: 12px; }

  .progress { background: var(--surface); border-radius: 4px; height: 6px; overflow: hidden; }
  .progress-bar { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--accent), var(--accent2)); transition: width .4s; }
</style>
</head>
<body>

<div class="header">
  <div class="logo">
    <div class="logo-icon">🤖</div>
    <div>
      <div class="logo-text">AI MARKETPLACE</div>
      <div class="logo-sub">Platform transaksi dataset untuk riset AI</div>
    </div>
  </div>
  <div class="header-right">
    <div class="chain-badge <?= isset($chain_valid) && !$chain_valid ? 'invalid' : '' ?>" id="chainStatus">
      <?= isset($chain_valid) && $chain_valid ? '✓ CHAIN VALID' : '⚠ CHAIN INVALID' ?>
    </div>
    <div class="text-muted" style="font-size:11px" id="headerClock"></div>
  </div>
</div>

<div class="container">

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">⏳</div>
      <div class="stat-label">Antrian (Queue)</div>
      <div class="stat-value" id="statPending"><?= $transactions['pending'] ?? 0 ?></div>
      <div class="stat-sub">Menunggu diproses</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-label">Terkonfirmasi</div>
      <div class="stat-value" style="color:var(--green)" id="statConfirmed"><?= $transactions['confirmed'] ?? 0 ?></div>
      <div class="stat-sub">Di dalam blok</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🧱</div>
      <div class="stat-label">Total Blok</div>
      <div class="stat-value" style="color:var(--accent2)" id="statBlocks"><?= $block_count ?? 0 ?></div>
      <div class="stat-sub">Linked List nodes</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-label">Total Nilai</div>
      <div class="stat-value" style="color:var(--yellow);font-size:20px" id="statValue">
        Rp <?= number_format($total_value ?? 0, 0, ',', '.') ?>
      </div>
      <div class="stat-sub">Transaksi confirmed</div>
    </div>
  </div>

  <div class="tabs">
    <button class="tab active" onclick="switchTab('queue')">📥 Queue &amp; Transaksi</button>
    <button class="tab" onclick="switchTab('blockchain')">🔗 Linked List</button>
    <button class="tab" onclick="switchTab('merkle')">🌿 Merkle Tree</button>
    <button class="tab" onclick="switchTab('verify')">🔍 Verifikasi</button>
  </div>

  <div id="tab-queue" class="tab-content active">
    <div class="two-col">

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">➕ Enqueue Transaksi Baru</span>
        </div>
        <div class="panel-body">
          <div id="alertForm" class="alert"></div>
          <div class="form-grid">
            <div class="form-group full">
              <label>Nama Dataset</label>
              <input type="text" id="fDataset" placeholder="contoh: ImageNet-AI-2024" />
            </div>
            <div class="form-group">
              <label>Kategori</label>
              <select id="fCategory">
                <option value="nlp">NLP</option>
                <option value="vision">Computer Vision</option>
                <option value="tabular">Tabular</option>
                <option value="audio">Audio</option>
                <option value="multimodal">Multimodal</option>
                <option value="reinforcement">Reinforcement Learning</option>
<option value="generative">Generative AI</option>
<option value="timeseries">Time Series</option>
<option value="medical">Medical/Healthcare</option>
<option value="graph">Graph/Network</option>
<option value="pointcloud">3D/Point Cloud</option>
<option value="geospatial">Geospatial</option>
<option value="cybersecurity">Cybersecurity</option>
<option value="finance">Finance</option>
<option value="recommendation">Recommendation</option>
<option value="robotics">Robotics</option>
<option value="climate">Climate/Environment</option>
<option value="education">Education</option>
<option value="legal">Legal/Law</option>
                <option value="other">Lainnya</option>
              </select>
            </div>
            <div class="form-group">
              <label>Harga (Rp)</label>
              <input type="number" id="fPrice" placeholder="500000" min="1" />
            </div>
            <div class="form-group">
              <label>ID Pembeli</label>
              <input type="number" id="fBuyer" placeholder="1" min="1" value="1" />
            </div>
            <div class="form-group">
              <label>ID Penjual</label>
              <input type="number" id="fSeller" placeholder="2" min="1" value="2" />
            </div>
            <div class="form-group full">
              <label>Deskripsi</label>
              <textarea id="fDesc" placeholder="Deskripsi singkat dataset..."></textarea>
            </div>
          </div>
          <div class="mt-16">
            <button class="btn btn-primary" onclick="enqueueTransaction()" id="btnEnqueue">
              <span class="spinner" id="spinnerEnqueue" style="display:none"></span>
              📥 Enqueue
            </button>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">⛏ Proses Blok (Mine)</span>
        </div>
        <div class="panel-body">
          <div id="alertMine" class="alert"></div>
          <p class="text-muted" style="font-size:12px;margin-bottom:16px;line-height:1.6">
            Proses ini melakukan <strong class="text-accent">dequeue</strong> semua transaksi pending,
            membangun <strong class="text-accent">Merkle Tree</strong> untuk menghitung root hash,
            lalu membuat <strong class="text-accent">blok baru</strong> yang disambungkan ke rantai via <code>prevHash</code>.
          </p>
          <div class="form-group" style="margin-bottom:16px">
            <label>Jumlah TX per Blok</label>
            <input type="number" id="txPerBlock" value="5" min="1" max="20" style="width:120px" />
          </div>

          <div style="background:var(--surface);border-radius:8px;padding:14px;margin-bottom:16px;font-size:11px;border:1px solid var(--border)">
            <div style="color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;font-size:10px">Alur Proses</div>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <span style="color:var(--yellow)">Queue (FIFO)</span>
              <span class="text-muted">→</span>
              <span style="color:var(--accent2)">dequeue()</span>
              <span class="text-muted">→</span>
              <span style="color:var(--green)">MerkleTree</span>
              <span class="text-muted">→</span>
              <span style="color:var(--accent)">addBlock()</span>
              <span class="text-muted">→</span>
              <span style="color:var(--text)">Linked List</span>
            </div>
          </div>

          <button class="btn btn-success" onclick="mineBlock()" id="btnMine">
            <span class="spinner" id="spinnerMine" style="display:none"></span>
            ⛏ Mine Block
          </button>

          <div id="mineResult" style="display:none;margin-top:16px;background:var(--surface);border-radius:8px;padding:14px;border:1px solid var(--green);font-size:11px">
            <div style="color:var(--green);margin-bottom:8px;font-weight:700">✅ Blok Berhasil Ditambang!</div>
            <div id="mineResultContent"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel mt-16">
      <div class="panel-header">
        <span class="panel-title">📥 Visualisasi Queue (FIFO)</span>
        
      </div>
      <div class="panel-body">
        <div style="display:flex;gap:16px;margin-bottom:12px;font-size:11px">
          <span><span style="color:var(--green)">▌</span> FRONT (Dequeue pertama)</span>
          <span><span style="color:var(--yellow)">▌</span> REAR (Enqueue terakhir)</span>
        </div>
        <div class="queue-visual" id="queueVisual">
          <div class="queue-empty">Memuat antrian...</div>
        </div>
      </div>
    </div>

    <div class="panel mt-16">
      <div class="panel-header">
        <span class="panel-title">📋 Transaksi Terbaru</span>
      </div>
      <div class="panel-body" style="padding:0">
        <div class="table-wrap">
          <table>
            <table style="table-layout:fixed;width:100%">

            <thead>
              <tr>
                <th style="width:40px">ID</th>
                <th style="min-width:180px">Dataset</th>
                <th style="width:120px">Kategori</th>
                <th style="width:150px">Harga</th>
                <th style="width:120px;text-align:center">Status</th>
                <th style="width:110px;text-align:center">Blok</th>
                <th style="width:120px">Waktu</th>
              </tr>
            </thead>
            <tbody id="txTable">
              <?php if (!empty($recent_tx)): ?>
                <?php foreach ($recent_tx as $tx): ?>
                <tr>
                  <td class="text-muted"><?= $tx['id'] ?></td>
                  <td><?= esc($tx['dataset_name']) ?></td>
                  <td><span style="color:var(--accent2)"><?= esc($tx['category']) ?></span></td>
                  <td class="text-yellow">Rp <?= number_format($tx['price'],0,',','.') ?></td>
                  
                  <td style="text-align:center">
  <?php if($tx['status'] === 'confirmed'): ?>
    <span class="badge badge-confirmed">CONFIRMED</span>
  <?php elseif($tx['status'] === 'rejected'): ?>
    <span class="badge badge-rejected">REJECTED</span>
  <?php else: ?>
    <span class="badge badge-pending">PENDING</span>
  <?php endif; ?>
</td>
                  <td style="text-align:center;color:var(--accent);white-space:nowrap"><?= $tx['block_id'] ? $tx['block_id'] : '<span style="color:var(--muted)">-</span>' ?></td>
                  <td class="text-muted"><?= date('d/m H:i', strtotime($tx['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="empty-state"><div class="icon">📭</div>Belum ada transaksi</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="tab-blockchain" class="tab-content">
    <div style="display:flex;gap:12px;margin-bottom:20px;align-items:center">
      <button class="btn btn-outline" onclick="validateChain()" id="btnValidate">
        <span class="spinner" id="spinnerValidate" style="display:none"></span>
        🔍 Validasi Chain
      </button>
      <div style="display:flex;align-items:center;gap:8px">
    <input type="number" id="tamperBlockId" placeholder="ID Blok" min="1" style="width:100px;padding:8px">
    <button class="btn" style="background:rgba(239,68,68,.2);border:1px solid var(--red);color:var(--red)" onclick="tamperBlock()">
      💥 Tamper Block
    </button>
  </div>
  <button class="btn" style="background:rgba(16,185,129,.15);border:1px solid var(--green);color:var(--green)" onclick="restoreChain()">
    🔄 Restore Chain
  </button>
      <div id="chainValidMsg" style="font-size:12px"></div>
    </div>

    <div class="chain-visual" id="chainVisual">
      <?php if (!empty($blocks)): ?>
        <?php foreach ($blocks as $i => $block): ?>
          <?php if ($i > 0): ?>
          <div class="block-connector">
            <div class="connector-line"></div>
            <div class="connector-arrow">↑</div>
            <span style="font-size:10px;color:var(--muted)">prevHash: <?= substr($block['prev_hash'],0,12) ?>...</span>
            <div class="connector-arrow">↑</div>
            <div class="connector-line"></div>
          </div>
          <?php endif; ?>

          <div class="block-card <?= (int)$block['block_index'] === 0 ? 'genesis' : '' ?> <?= !$block['is_valid'] ? 'invalid-block' : '' ?>">
            <div class="block-header">
              <div class="block-index <?= (int)$block['block_index'] === 0 ? 'genesis-idx' : '' ?>">
                <?= $block['block_index'] ?>
              </div>
              <div class="block-meta">
                <div class="block-hash-display mono"><?= substr($block['block_hash'],0,32) ?>...</div>
                <div class="block-time"><?= $block['created_at'] ?></div>
              </div>
              <span class="badge badge-<?= $block['is_valid'] ? 'valid' : 'invalid' ?>">
                <?= $block['is_valid'] ? '✓ Valid' : '✗ Invalid' ?>
              </span>
            </div>
            <div class="block-fields">
              <div class="block-field">
                <label>prevHash</label>
                <span class="hash-short"><?= substr($block['prev_hash'],0,16) ?>...</span>
              </div>
              <div class="block-field">
                <label>Merkle Root</label>
                <span style="color:var(--green)"><?= substr($block['merkle_root'],0,16) ?>...</span>
              </div>
              <div class="block-field">
                <label>Transaksi</label>
                <span><?= $block['tx_count'] ?> tx</span>
              </div>
              <div class="block-field">
                <label>Index</label>
                <span class="text-accent">#<?= $block['block_index'] ?></span>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <div class="icon">🔗</div>
          <div>Belum ada blok. Mine transaksi pertama Anda!</div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div id="tab-merkle" class="tab-content">
  <div class="two-col">
    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">🌿 Pilih Blok</span>
      </div>
      <div class="panel-body">
        <div id="alertMerkle" class="alert"></div>
        <p class="text-muted" style="font-size:12px;margin-bottom:16px;line-height:1.6">
          Pilih ID blok untuk melihat Merkle Tree yang dibangun dari transaksi di dalamnya.
        </p>
        <div class="form-group" style="margin-bottom:12px">
          <label>Block ID</label>
          <input type="number" id="merkleBlockId" placeholder="contoh: 2" min="1" style="width:120px" />
        </div>
        <button class="btn btn-primary" onclick="loadMerkleTree()">
          🌿 Tampilkan Merkle Tree
        </button>

        <div id="merkleBlockInfo" style="display:none;margin-top:16px;font-size:11px">
          <div style="background:var(--surface);border-radius:8px;padding:14px;border:1px solid var(--border)">
            <div style="margin-bottom:8px;color:var(--muted);text-transform:uppercase;font-size:10px">Info Blok</div>
            <div style="margin-bottom:4px">Index: <span class="text-accent" id="mInfo_index"></span></div>
            <div style="margin-bottom:4px">TX Count: <span class="text-green" id="mInfo_txcount"></span></div>
            <div style="margin-bottom:4px">Merkle Root:<br><code style="color:var(--green);font-size:10px" id="mInfo_root"></code></div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <span class="panel-title">ℹ️ Cara Kerja Merkle Tree</span>
      </div>
      <div class="panel-body" style="font-size:12px;line-height:1.8;color:var(--muted)">
        <div style="margin-bottom:8px"><span class="text-accent">1.</span> Setiap transaksi di-hash → <em>leaf node</em></div>
        <div style="margin-bottom:8px"><span class="text-accent">2.</span> Pasang leaf di-hash bersama → <em>internal node</em></div>
        <div style="margin-bottom:8px"><span class="text-accent">3.</span> Proses naik sampai tersisa 1 hash → <em>Root Hash</em></div>
        <div style="margin-bottom:8px"><span class="text-accent">4.</span> Root Hash disimpan di header blok</div>
        <div><span class="text-green">✓</span> Verifikasi 1 TX tanpa baca seluruh blok <strong>O(log n)</strong></div>
      </div>
    </div>
  </div>

  <!-- Visualisasi Tree -->
  <div class="panel mt-16" id="merkleTreePanel" style="display:none">
    <div class="panel-header">
      <span class="panel-title">🌲 Merkle Tree — Blok <span id="merkleBlockTitle"></span></span>
    </div>
    <div class="panel-body">
      <!-- Transaksi (Leaves) -->
      <div style="margin-bottom:20px">
        <div style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">Transaksi dalam Blok</div>
        <div id="merkleTxList" style="display:flex;gap:8px;flex-wrap:wrap"></div>
      </div>
      <!-- Tree Visual -->
      <div id="merkleTreeVis" class="merkle-container"></div>
    </div>
  </div>
</div>

  <div id="tab-verify" class="tab-content">
    <div class="two-col">
      <div class="panel">
        <div class="panel-header">
          <span class="panel-title">🔍 Verifikasi Transaksi</span>
        </div>
        <div class="panel-body">
          <div id="alertVerify" class="alert"></div>
          <p class="text-muted" style="font-size:12px;margin-bottom:16px;line-height:1.6">
            Masukkan ID transaksi untuk memverifikasi keberadaannya di dalam rantai blok
            menggunakan <strong class="text-accent">Merkle Proof</strong>.
          </p>
          <div class="form-group" style="margin-bottom:16px">
            <label>ID Transaksi</label>
            <input type="number" id="verifyTxId" placeholder="1" min="1" style="width:160px" />
          </div>
          <button class="btn btn-primary" onclick="verifyTransaction()" id="btnVerify">
            <span class="spinner" id="spinnerVerify" style="display:none"></span>
            🔍 Verifikasi
          </button>
        </div>
      </div>

      <div class="panel" id="verifyResultPanel" style="display:none">
        <div class="panel-header">
          <span class="panel-title">📊 Hasil Verifikasi</span>
        </div>
        <div class="panel-body" id="verifyResultContent" style="font-size:12px"></div>
      </div>
    </div>
  </div>

</div>

<script>
const BASE = '<?= base_url() ?>';

function switchTab(name) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  event.target.classList.add('active');
  if (name === 'queue') refreshQueue();
}

function showAlert(id, msg, type) {
  const el = document.getElementById(id);
  el.className = 'alert alert-' + type + ' show';
  el.innerHTML = (type === 'success' ? '✅ ' : type === 'error' ? '❌ ' : 'ℹ️ ') + msg;
  setTimeout(() => el.classList.remove('show'), 5000);
}

function setLoading(btnId, spinnerId, loading) {
  const btn = document.getElementById(btnId);
  const sp  = document.getElementById(spinnerId);
  if (loading) { btn.disabled = true; sp.style.display = 'inline-block'; }
  else          { btn.disabled = false; sp.style.display = 'none'; }
}

async function apiPost(url, data) {
  const fd = new FormData();
  Object.entries(data).forEach(([k, v]) => fd.append(k, v));
  const res = await fetch(BASE + url, { method: 'POST', body: fd });
  return res.json();
}

async function apiGet(url) {
  const res = await fetch(BASE + url);
  return res.json();
}

async function enqueueTransaction() {
  const name   = document.getElementById('fDataset').value.trim();
  const price  = document.getElementById('fPrice').value;
  const buyer  = document.getElementById('fBuyer').value;
  const seller = document.getElementById('fSeller').value;
  const cat    = document.getElementById('fCategory').value;
  const desc   = document.getElementById('fDesc').value;

  if (!name || !price) {
    showAlert('alertForm', 'Nama dataset dan harga wajib diisi.', 'error');
    return;
  }

  setLoading('btnEnqueue', 'spinnerEnqueue', true);
  try {
    const res = await apiPost('blockchain/addTransaction', {
      dataset_name: name, price, buyer_id: buyer, seller_id: seller, category: cat, description: desc
    });

    if (res.success) {
      showAlert('alertForm', res.message, 'success');
      document.getElementById('fDataset').value = '';
      document.getElementById('fPrice').value   = '';
      document.getElementById('fDesc').value    = '';
      refreshQueue();
      refreshStats();
    } else {
      showAlert('alertForm', JSON.stringify(res.errors ?? res.message), 'error');
    }
  } catch(e) {
    showAlert('alertForm', 'Gagal terhubung ke server.', 'error');
  }
  setLoading('btnEnqueue', 'spinnerEnqueue', false);
}

async function mineBlock() {
  const txPerBlock = document.getElementById('txPerBlock').value;
  setLoading('btnMine', 'spinnerMine', true);
  document.getElementById('mineResult').style.display = 'none';

  try {
    const res = await apiPost('blockchain/mineBlock', { tx_per_block: txPerBlock });

    if (res.success) {
      showAlert('alertMine', res.message, 'success');
      const b = res.block;
      document.getElementById('mineResultContent').innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div><span class="text-muted">Index:</span> <strong class="text-accent">#${b.index}</strong></div>
          <div><span class="text-muted">TX Confirmed:</span> <strong class="text-green">${res.tx_confirmed}</strong></div>
          <div><span class="text-muted">Hash:</span> <code style="color:var(--yellow)">${b.hash}</code></div>
          <div><span class="text-muted">Merkle Root:</span> <code style="color:var(--green)">${b.merkleRoot}</code></div>
          <div><span class="text-muted">prevHash:</span> <code class="text-muted">${b.prevHash}</code></div>
        </div>
      `;
      document.getElementById('mineResult').style.display = 'block';
      refreshQueue();
      refreshStats();
    } else {
      showAlert('alertMine', res.message, 'error');
    }
  } catch(e) {
    showAlert('alertMine', 'Gagal terhubung ke server.', 'error');
  }
  setLoading('btnMine', 'spinnerMine', false);
}

async function refreshQueue() {
  try {
    const res = await apiGet('blockchain/getQueueData');
    const el  = document.getElementById('queueVisual');

    if (!res.queue || res.queue.length === 0) {
      el.innerHTML = '<div class="queue-empty">📭 Antrian kosong — semua transaksi sudah diproses</div>';
      return;
    }

    el.innerHTML = res.queue.map((tx, i) => `
      <div class="queue-item ${i === 0 ? 'queue-front' : i === res.queue.length-1 ? 'queue-rear' : ''}">
        <div class="queue-label">${i === 0 ? '← FRONT (dequeue pertama)' : i === res.queue.length-1 ? 'REAR → (enqueue terakhir)' : `posisi #${i+1}`}</div>
        <div class="queue-name">${tx.dataset_name.substring(0,16)}${tx.dataset_name.length>16?'...':''}</div>
        <div class="queue-price">Rp ${Number(tx.price).toLocaleString('id')}</div>
        <div style="font-size:10px;color:var(--muted);margin-top:4px">${tx.category} · ID#${tx.id}</div>
      </div>
    `).join('');
  } catch(e) {
    document.getElementById('queueVisual').innerHTML = '<div class="queue-empty">⚠ Gagal memuat antrian</div>';
  }
}

async function validateChain() {
  setLoading('btnValidate', 'spinnerValidate', true);
  try {
    const res = await apiGet('blockchain/validateChain');
    const msg = document.getElementById('chainValidMsg');
    const badge = document.getElementById('chainStatus');

    if (res.chainValid) {
      msg.innerHTML   = '<span class="text-green">✅ Semua ' + res.totalBlocks + ' blok valid</span>';
      badge.textContent = '✓ CHAIN VALID';
      badge.className   = 'chain-badge';
    } else {
      msg.innerHTML   = '<span class="text-red">⚠ Ditemukan blok tidak valid!</span>';
      badge.textContent = '⚠ CHAIN INVALID';
      badge.className   = 'chain-badge invalid';
    }
  } catch(e) {}
  setLoading('btnValidate', 'spinnerValidate', false);
}

async function tamperBlock() {
  const blockId = document.getElementById('tamperBlockId').value;
  if (!blockId) {
    alert('Masukkan ID Blok yang ingin di-tamper!');
    return;
  }

  const res = await apiPost('blockchain/tamperBlock', { block_id: blockId });
  const msg = document.getElementById('chainValidMsg');
  const badge = document.getElementById('chainStatus');

  if (res.success) {
    msg.innerHTML = `<span class="text-red">💥 ${res.message}</span>`;
    badge.textContent = '⚠ CHAIN INVALID';
    badge.className = 'chain-badge invalid';
    setTimeout(() => location.reload(), 1500);
  }
}

async function restoreChain() {
  const res = await apiPost('blockchain/restoreChain', {});
  const msg = document.getElementById('chainValidMsg');
  const badge = document.getElementById('chainStatus');

  if (res.success) {
    msg.innerHTML = `<span class="text-green">✅ ${res.message}</span>`;
    badge.textContent = '✓ CHAIN VALID';
    badge.className = 'chain-badge';
    setTimeout(() => {
    window.location.href = window.location.href.split('?')[0] + '?restored=1';
}, 1500);
  }
}

async function loadMerkleTree() {
  const blockId = document.getElementById('merkleBlockId').value;
  if (!blockId) {
    showAlert('alertMerkle', 'Masukkan ID blok terlebih dahulu.', 'error');
    return;
  }

  try {
    const res = await apiGet(`blockchain/getMerkleTree/${blockId}`);

    if (!res.success) {
      showAlert('alertMerkle', res.message, 'error');
      return;
    }

    // Info blok
    document.getElementById('mInfo_index').textContent   = '#' + res.block_index;
    document.getElementById('mInfo_txcount').textContent = res.tx_count + ' transaksi';
    document.getElementById('mInfo_root').textContent    = res.merkle_root;
    document.getElementById('merkleBlockInfo').style.display = 'block';
    document.getElementById('merkleBlockTitle').textContent  = '#' + res.block_index;

    // Daftar transaksi (leaf)
    const txList = document.getElementById('merkleTxList');
    txList.innerHTML = res.transactions.map(tx => `
      <div style="background:var(--surface);border:1px solid var(--accent2);border-radius:8px;padding:10px;min-width:160px;font-size:11px">
        <div style="color:var(--accent2);margin-bottom:4px">TX #${tx.id}</div>
        <div style="color:var(--text);margin-bottom:2px">${tx.dataset_name}</div>
        <div style="color:var(--yellow)">Rp ${Number(tx.price).toLocaleString('id')}</div>
        <div style="color:var(--muted);font-size:10px">${tx.category}</div>
      </div>
    `).join('');

    // Render tree levels
    const vis = document.getElementById('merkleTreeVis');
    const levels = res.tree_levels;
    let html = '';

    // Tampilkan dari root ke bawah (reverse)
    const reversed = [...levels].reverse();
    reversed.forEach((level, i) => {
      const isRoot = (i === 0);
      const isLeaf = (i === reversed.length - 1);
      const label  = isRoot ? '🌿 Root Hash' : isLeaf ? '🍃 Leaf Nodes (Hash TX)' : `🔗 Level ${reversed.length - 1 - i}`;

      html += `<div class="merkle-level-label" style="margin-top:${i>0?'4px':'0'}">${label}</div>`;
      html += `<div class="merkle-level">`;
      level.forEach((hash, j) => {
        html += `
          <div class="merkle-node ${isRoot?'root':''} ${isLeaf?'leaf':''}"
               title="${hash}"
               style="cursor:pointer"
               onclick="navigator.clipboard.writeText('${hash}')">
            ${hash.substring(0,14)}...
          </div>`;
      });
      html += `</div>`;

      if (!isLeaf) {
        html += `<div style="text-align:center;color:var(--border);font-size:16px;margin:2px 0">↓</div>`;
      }
    });

    vis.innerHTML = html;
    document.getElementById('merkleTreePanel').style.display = 'block';

  } catch(e) {
    showAlert('alertMerkle', 'Gagal memuat data.', 'error');
  }
}

function renderMerkleTree(levels) {
  if (!levels || levels.length === 0) return;

  const panel = document.getElementById('merkleTreePanel');
  const vis   = document.getElementById('merkleTreeVis');
  panel.style.display = 'block';

  const levelLabels = ['Leaf (TX Hashes)', 'Level 1', 'Level 2', 'Level 3', 'Root'];
  let html = '';

  levels.forEach((level, i) => {
    const isRoot = (i === levels.length - 1);
    const isLeaf = (i === 0);
    const label  = isRoot ? 'Root Hash' : isLeaf ? 'Leaf Nodes' : `Level ${i}`;

    html += `<div class="merkle-level-label">${label}</div>`;
    html += `<div class="merkle-level">`;
    level.forEach(hash => {
      html += `<div class="merkle-node ${isRoot?'root':''} ${isLeaf?'leaf':''}" title="${hash}">${hash.substring(0,12)}...</div>`;
    });
    html += `</div>`;

    if (!isRoot) {
      html += `<div class="merkle-level-label" style="color:var(--border)">↑ hash pairs ↑</div>`;
    }
  });

  vis.innerHTML = html;
}

async function verifyTransaction() {
  const txId = document.getElementById('verifyTxId').value;
  if (!txId) { showAlert('alertVerify', 'Masukkan ID transaksi.', 'error'); return; }

  setLoading('btnVerify', 'spinnerVerify', true);
  try {
    const res = await apiGet(`blockchain/verifyTransaction/${txId}`);
    const panel   = document.getElementById('verifyResultPanel');
    const content = document.getElementById('verifyResultContent');
    panel.style.display = 'block';

    if (!res.success) {
      content.innerHTML = `<div class="text-red">❌ ${res.message}</div>`;
    } else {
      const tx = res.transaction;
      const b  = res.block;
      content.innerHTML = `
        <div style="margin-bottom:16px">
          <div style="font-size:18px;font-weight:700;${res.verified?'color:var(--green)':'color:var(--red)'}">
            ${res.verified ? '✅ TRANSAKSI TERVERIFIKASI' : '❌ VERIFIKASI GAGAL'}
          </div>
        </div>
        <div style="display:grid;gap:8px">
          <div><span class="text-muted">Dataset:</span> ${tx.dataset_name}</div>
          <div><span class="text-muted">Harga:</span> <span class="text-yellow">Rp ${Number(tx.price).toLocaleString('id')}</span></div>
          <div><span class="text-muted">Status:</span> <span class="badge badge-${tx.status}">${tx.status}</span></div>
          <div><span class="text-muted">TX Hash:</span> <code style="font-size:10px;color:var(--accent)">${tx.tx_hash?.substring(0,24)}...</code></div>
          <hr style="border-color:var(--border);margin:8px 0">
          <div><span class="text-muted">Blok #:</span> ${b.index}</div>
          <div><span class="text-muted">Merkle Root (stored):</span><br><code style="font-size:10px;color:var(--green)">${res.merkle_root_stored?.substring(0,32)}...</code></div>
          <div><span class="text-muted">Merkle Root (computed):</span><br><code style="font-size:10px;color:var(--green)">${res.merkle_root_computed?.substring(0,32)}...</code></div>
          <div><span class="text-muted">Root Match:</span> <strong style="color:${res.roots_match?'var(--green)':'var(--red)'}">${res.roots_match?'✓ Sama':'✗ Berbeda'}</strong></div>
        </div>
      `;
    }
  } catch(e) {
    showAlert('alertVerify', 'Gagal terhubung ke server.', 'error');
  }
  setLoading('btnVerify', 'spinnerVerify', false);
}

async function refreshStats() {
  try {
    const res = await apiGet('blockchain/getStats');
    if (res.success) {
      document.getElementById('statPending').textContent   = res.stats.pending   || 0;
      document.getElementById('statConfirmed').textContent = res.stats.confirmed || 0;
      document.getElementById('statBlocks').textContent    = res.blocks;
      document.getElementById('statValue').textContent     = 'Rp ' + Number(res.total_value).toLocaleString('id');
    }
  } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  const savedTab = sessionStorage.getItem('activeTab');
  if (savedTab) {
    sessionStorage.removeItem('activeTab');
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + savedTab)?.classList.add('active');
    document.querySelector(`[onclick="switchTab('${savedTab}')"]`)?.classList.add('active');
  }
  refreshQueue();
  updateClock();
  setInterval(updateClock, 1000);
});

async function refreshAll() {
  const activeTab = document.querySelector('.tab.active')?.getAttribute('onclick')?.replace("switchTab('","")?.replace("')","") || 'queue';
  sessionStorage.setItem('activeTab', activeTab);
  location.reload();
}

function updateClock() {
  const now = new Date();
  const pad = n => String(n).padStart(2, '0');
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const str = `${pad(now.getDate())} ${months[now.getMonth()]} ${now.getFullYear()} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
  document.getElementById('headerClock').textContent = str;
}
</script>
</body>
</html>