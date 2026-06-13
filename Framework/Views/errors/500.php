<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Volt ⚡ <?= $code ?></title>
    <style>
        body { background: #0d0d0d; color: #00ffcc; font-family: 'Courier New', monospace; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { text-align: center; border: 1px solid #00ffcc; padding: 40px; box-shadow: 0 0 20px rgba(0, 255, 204, 0.2); }
        h1 { font-size: 5rem; margin: 0; text-shadow: 2px 2px #ff00ff; }
        p { text-transform: uppercase; letter-spacing: 2px; }
    </style>
</head>
<body>
<div class="container">
    <h1>500</h1>
    <p><?= function_exists('__') ? __('error_page.title_500') : "Internal Server Error" ?></p>
    <small><?= function_exists('__') ? __('error_page.message_500') : "An internal server error occurred." ?></small>
</div>
</body>
</html>