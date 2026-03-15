<?php
// FixedCoin Wallet UI for Umbrel

$rpc_host = '127.0.0.1';
$rpc_port = 24761;
$rpc_user = 'umbrel';
$rpc_password = 'changeme';

function rpc_call($method, $params = array()) {
    global $rpc_host, $rpc_port, $rpc_user, $rpc_password;
    
    $url = "http://{$rpc_host}:{$rpc_port}/";
    
    $payload = json_encode(array(
        'jsonrpc' => '1.0',
        'method' => $method,
        'params' => $params,
        'id' => 1
    ));
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "{$rpc_user}:{$rpc_password}");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response === false) {
        return null;
    }
    
    $data = json_decode($response, true);
    return isset($data['result']) ? $data['result'] : null;
}

// Get node info
$blockchainInfo = rpc_call('getblockchaininfo');
$walletInfo = rpc_call('getwalletinfo');
$networkInfo = rpc_call('getnetworkinfo');

// Get new address if needed
if (!$walletInfo || isset($walletInfo['unlocked_until']) || isset($walletInfo['warning'])) {
    // Wallet might be locked or not loaded
}

$error = null;
if (!$blockchainInfo) {
    $error = "Cannot connect to FixedCoin node. Make sure the node is running.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'newaddress') {
        $newAddress = rpc_call('getnewaddress');
    } elseif ($_POST['action'] === 'encrypt') {
        if (!empty($_POST['password'])) {
            $result = rpc_call('encryptwallet', array($_POST['password']));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixedCoin Node - Umbrel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        header {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle {
            color: #888;
            font-size: 1.1rem;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .card h2 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #4CAF50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .stat {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stat:last-child {
            border-bottom: none;
        }
        .stat-label {
            color: #888;
        }
        .stat-value {
            font-weight: 600;
            color: #fff;
        }
        .address {
            background: rgba(76, 175, 80, 0.1);
            padding: 16px;
            border-radius: 8px;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .btn {
            background: linear-gradient(90deg, #4CAF50, #45a049);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(76, 175, 80, 0.4);
        }
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-online {
            background: #4CAF50;
            box-shadow: 0 0 10px #4CAF50;
        }
        .status-offline {
            background: #f44336;
            box-shadow: 0 0 10px #f44336;
        }
        .error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid #f44336;
            color: #f44336;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
        }
        .progress-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            border-radius: 4px;
            transition: width 0.5s;
        }
        .sync-status {
            font-size: 0.85rem;
            color: #888;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="icon.png" alt="FixedCoin" class="logo">
            <h1>FixedCoin</h1>
            <p class="subtitle">Your FixedCoin Full Node on Umbrel</p>
        </header>

        <?php if ($error): ?>
            <div class="error">
                <h2>⚠️ Node Not Connected</h2>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php else: ?>
        
        <div class="grid">
            <div class="card">
                <h2>📊 Node Status</h2>
                <div class="stat">
                    <span class="stat-label">Status</span>
                    <span class="stat-value">
                        <span class="status-dot status-online"></span>Online
                    </span>
                </div>
                <div class="stat">
                    <span class="stat-label">Current Block</span>
                    <span class="stat-value"><?php echo number_format($blockchainInfo['blocks'] ?? 0); ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Headers</span>
                    <span class="stat-value"><?php echo number_format($blockchainInfo['headers'] ?? 0); ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Difficulty</span>
                    <span class="stat-value"><?php echo number_format($blockchainInfo['difficulty'] ?? 0, 2); ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Network</span>
                    <span class="stat-value"><?php echo $networkInfo['subversion'] ?? 'Unknown'; ?></span>
                </div>
                <div class="sync-status">
                    <?php 
                    $syncProgress = 0;
                    if ($blockchainInfo && $blockchainInfo['headers'] > 0) {
                        $syncProgress = ($blockchainInfo['blocks'] / $blockchainInfo['headers']) * 100;
                    }
                    ?>
                    Sync Progress: <?php echo number_format($syncProgress, 1); ?>%
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $syncProgress; ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>💰 Wallet</h2>
                <?php if ($walletInfo): ?>
                <div class="stat">
                    <span class="stat-label">Balance</span>
                    <span class="stat-value"><?php echo $walletInfo['balance'] ?? '0.00'; ?> FIX</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Unconfirmed</span>
                    <span class="stat-value"><?php echo $walletInfo['unconfirmed_balance'] ?? '0.00'; ?> FIX</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Immature</span>
                    <span class="stat-value"><?php echo $walletInfo['immature_balance'] ?? '0.00'; ?> FIX</span>
                </div>
                <div class="stat">
                    <span class="stat-label">Transactions</span>
                    <span class="stat-value"><?php echo $walletInfo['txcount'] ?? 0; ?></span>
                </div>
                <?php else: ?>
                <p style="color: #888;">Wallet not loaded</p>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="newaddress">
                    <button type="submit" class="btn">Get New Address</button>
                </form>
                
                <?php if (isset($newAddress)): ?>
                <div class="address">
                    <strong>New Address:</strong><br>
                    <?php echo htmlspecialchars($newAddress); ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>🔗 Connection Info</h2>
                <div class="stat">
                    <span class="stat-label">Connections</span>
                    <span class="stat-value"><?php echo $networkInfo['connections'] ?? 0; ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Local Services</span>
                    <span class="stat-value"><?php echo $networkInfo['localservices'] ?? 'Unknown'; ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Protocol Version</span>
                    <span class="stat-value"><?php echo $networkInfo['protocolversion'] ?? 'Unknown'; ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Time Offset</span>
                    <span class="stat-value"><?php echo $networkInfo['timeoffset'] ?? 0; ?> seconds</span>
                </div>
            </div>
        </div>

        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 40px; color: #666;">
            <p>FixedCoin Node v1.0.0 | Powered by Umbrel</p>
        </div>
    </div>
</body>
</html>
