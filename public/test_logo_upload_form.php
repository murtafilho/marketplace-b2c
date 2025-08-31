<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Upload de Logo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .success { color: green; background: #e8f5e8; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: red; background: #ffeaea; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: blue; background: #e8f4fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .form-group { margin: 15px 0; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
        .current-logo { text-align: center; margin: 20px 0; }
        .current-logo img { max-width: 200px; max-height: 100px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Teste de Upload de Logo</h1>
        
        <?php
        // Verificar se é uma requisição POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo'])) {
            echo '<div class="info">📤 Tentativa de upload detectada...</div>';
            
            $file = $_FILES['logo'];
            
            // Verificar se houve erro no upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                echo '<div class="error">❌ Erro no upload: ' . $file['error'] . '</div>';
            } else {
                echo '<div class="success">✅ Arquivo recebido com sucesso!</div>';
                echo '<div class="info">';
                echo 'Nome: ' . htmlspecialchars($file['name']) . '<br>';
                echo 'Tipo: ' . htmlspecialchars($file['type']) . '<br>';
                echo 'Tamanho: ' . number_format($file['size'] / 1024, 2) . ' KB<br>';
                echo 'Arquivo temporário: ' . htmlspecialchars($file['tmp_name']) . '<br>';
                echo '</div>';
                
                // Verificar se é uma imagem
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
                if (in_array($file['type'], $allowedTypes)) {
                    echo '<div class="success">✅ Tipo de arquivo válido para logo</div>';
                    
                    // Simular salvamento (não vamos realmente salvar para não interferir)
                    echo '<div class="info">ℹ️ Em um upload real, o arquivo seria processado pelo Laravel e salvo via Spatie Media Library</div>';
                } else {
                    echo '<div class="error">❌ Tipo de arquivo não permitido. Use: JPEG, PNG, GIF, SVG ou WebP</div>';
                }
            }
        }
        ?>
        
        <div class="current-logo">
            <h3>🖼️ Logo Atual</h3>
            <?php
            // Tentar mostrar a logo atual
            $logoUrl = 'https://marketplace-b2c.test/storage/4/Design-sem-nome.png';
            echo '<img src="' . $logoUrl . '" alt="Logo atual" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'block\'">';
            echo '<div style="display:none; color: #666;">❌ Logo não encontrada ou não acessível</div>';
            ?>
            <p><small>URL: <?php echo $logoUrl; ?></small></p>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <h3>📁 Testar Upload de Nova Logo</h3>
            
            <div class="form-group">
                <label for="logo">Selecionar arquivo de logo:</label>
                <input type="file" id="logo" name="logo" accept="image/*" required>
                <small>Formatos aceitos: JPEG, PNG, GIF, SVG, WebP (máx. 2MB)</small>
            </div>
            
            <button type="submit">🚀 Testar Upload</button>
        </form>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
            <h3>🔗 Links Úteis</h3>
            <ul>
                <li><a href="/admin/layout" target="_blank">Página de Administração de Layout</a></li>
                <li><a href="/test_logo_status.php" target="_blank">Status da Logo (Diagnóstico)</a></li>
                <li><a href="/" target="_blank">Página Inicial (ver logo no header)</a></li>
            </ul>
        </div>
        
        <div style="margin-top: 20px; font-size: 12px; color: #666;">
            <strong>Nota:</strong> Este é apenas um teste de upload. Para fazer upload real da logo, use a <a href="/admin/layout">página de administração</a>.
        </div>
    </div>
</body>
</html>