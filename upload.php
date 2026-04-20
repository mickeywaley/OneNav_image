<?php
// ======================================
// 配置区
// ======================================
define('DEFAULT_PASS', 'admin123');
define('PASS_FILE', __DIR__ . '/.admin_pass.txt');
define('DEFAULT_BG_URL', 'https://f.aq520.com/websq/bj.jpg');
define('UPLOAD_DIR', __DIR__);
define('TARGET_NAME', 'bj.jpg');
define('MAX_SIZE_KB', 300);
define('MANUAL_COMPRESS_URL', 'http://www.zuohaotu.com/image-compress.aspx');
define('GITHUB_URL', 'https://github.com/mickeywaley/OneNav_image/');

session_start();

// 初始化密码（加密存储）
if (!file_exists(PASS_FILE)) {
    file_put_contents(PASS_FILE, password_hash(DEFAULT_PASS, PASSWORD_DEFAULT));
}
$ADMIN_HASH = trim(file_get_contents(PASS_FILE));

$msg = '';
$error = '';

// 登录验证（加密对比）
if (isset($_POST['login'])) {
    $input = trim($_POST['password']);
    if (password_verify($input, $ADMIN_HASH)) {
        $_SESSION['admin'] = true;
        $msg = '登录成功';
    } else {
        $error = '密码错误';
    }
}

// 修改密码（加密存储）
if (isset($_SESSION['admin']) && isset($_POST['change_pass'])) {
    $old = trim($_POST['old_pass']);
    $new1 = trim($_POST['new_pass1']);
    $new2 = trim($_POST['new_pass2']);
    
    if (!password_verify($old, $ADMIN_HASH)) {
        $error = '原密码不正确';
    } elseif ($new1 !== $new2) {
        $error = '两次新密码不一致';
    } elseif (strlen($new1) < 4) {
        $error = '密码长度不能小于4位';
    } else {
        file_put_contents(PASS_FILE, password_hash($new1, PASSWORD_DEFAULT));
        $msg = '密码修改成功，已加密存储';
    }
}

// 退出
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: upload.php');
    exit;
}

// 自动压缩图片到300KB
function autoCompressImage($sourcePath, $maxSizeKb = 300, $stepQuality = 15)
{
    if (!file_exists($sourcePath)) return false;
    $maxSize = $maxSizeKb * 1024;
    $fileSize = filesize($sourcePath);
    if ($fileSize <= $maxSize) return true;

    $info = getimagesize($sourcePath);
    if (!$info) return false;
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg': $img = imagecreatefromjpeg($sourcePath); break;
        case 'image/png':  $img = imagecreatefrompng($sourcePath);  break;
        case 'image/webp': $img = imagecreatefromwebp($sourcePath); break;
        case 'image/gif':  $img = imagecreatefromgif($sourcePath);  break;
        default: return false;
    }
    if (!$img) return false;

    $quality = 85;
    $tempFile = sys_get_temp_dir() . '/compress_' . uniqid() . '.jpg';
    do {
        imagejpeg($img, $tempFile, $quality);
        if (filesize($tempFile) <= $maxSize) break;
        $quality -= $stepQuality;
    } while ($quality > 10);

    $ok = copy($tempFile, $sourcePath);
    @unlink($tempFile);
    imagedestroy($img);
    return $ok;
}

// 获取图片信息：宽、高、大小KB
function getImageInfo($path)
{
    $w = $h = 0;
    $size = filesize($path);
    $kb = round($size / 1024, 1);
    $info = getimagesize($path);
    if ($info) {
        $w = $info[0];
        $h = $info[1];
    }
    return [$w, $h, $kb];
}

// 上传（修复刷新重复提交）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['admin']) && !empty($_FILES['image']['name'])) {
    $file = $_FILES['image'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allow)) {
        $error = '只允许 jpg、png、gif、webp';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $error = '上传失败，错误码：' . $file['error'];
    } else {
        $filename = 'bg_' . time() . rand(100,999) . '.' . $ext;
        $dest = UPLOAD_DIR . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            autoCompressImage($dest, MAX_SIZE_KB);
            clearstatcache(true, $dest);
            $finalKb = round(filesize($dest) / 1024, 1);
            if ($finalKb > MAX_SIZE_KB) {
                $msg = "上传成功，但仍超过300KB，建议去 <a href='".MANUAL_COMPRESS_URL."' target='_blank'>做好图</a> 手动压缩后再上传";
            } else {
                $msg = "上传并压缩成功：{$filename}（{$finalKb}KB）";
            }
            header('Location: upload.php');
            exit;
        } else {
            $error = '文件移动失败，请检查目录权限';
        }
    }
}

// 设置为背景
if (isset($_SESSION['admin']) && isset($_GET['set_default'])) {
    $source = trim($_GET['set_default']);
    $sourcePath = UPLOAD_DIR . '/' . $source;
    $targetPath = UPLOAD_DIR . '/' . TARGET_NAME;

    if (!file_exists($sourcePath)) {
        $error = '文件不存在';
    } else {
        if (file_exists($targetPath)) {
            $backup = 'bg_old_' . time() . rand(100,999) . '.jpg';
            rename($targetPath, UPLOAD_DIR . '/' . $backup);
        }
        if (rename($sourcePath, $targetPath)) {
            $msg = '已设为默认背景：bj.jpg';
        } else {
            $error = '设置失败';
        }
    }
}

// 删除
if (isset($_SESSION['admin']) && isset($_GET['delete'])) {
    $file = trim($_GET['delete']);
    $path = UPLOAD_DIR . '/' . $file;
    if (file_exists($path) && $file !== TARGET_NAME) {
        unlink($path);
        $msg = '已删除：' . $file;
    } else {
        $error = '无法删除或文件不存在';
    }
}

// 读取图片列表
$images = [];
$dh = opendir(UPLOAD_DIR);
while (($f = readdir($dh)) !== false) {
    $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
        $images[] = $f;
    }
}
closedir($dh);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>OneNav 背景图管理</title>
    <style>
        :root {
            --bg: #f5f5f5;
            --card: #fff;
            --text: #333;
            --gray: #666;
            --border: #ddd;
            --primary: #007bff;
            --danger: #dc3545;
            --success: #28a745;
        }
        .dark {
            --bg: #1a1a1a;
            --card: #2a2a2a;
            --text: #eee;
            --gray: #aaa;
            --border: #444;
            --primary: #409eff;
            --danger: #f56c6c;
            --success: #67c23a;
        }

        *{box-sizing:border-box;margin:0;padding:0;font-family:system-ui}
        body{background:var(--bg);color:var(--text);max-width:1000px;margin:20px auto;padding:20px}
        .card{background:var(--card);padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
        h1,h2,h3{margin-bottom:15px}
        .msg{color:var(--success);background:rgba(103,194,58,0.1);padding:10px;border-radius:5px;margin:10px 0}
        .err{color:var(--danger);background:rgba(245,108,108,0.1);padding:10px;border-radius:5px;margin:10px 0}
        .gray{color:var(--gray);font-size:14px}
        
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
        .menu{position:relative}
        .menu-btn{background:var(--primary);color:#fff;border:none;padding:8px 12px;border-radius:5px;cursor:pointer}
        .menu-dropdown{
            position:absolute;top:100%;right:0;background:var(--card);border:1px solid var(--border);
            border-radius:6px;width:200px;padding:10px;display:none;margin-top:5px;z-index:99
        }
        .menu-dropdown.show{display:block}
        .menu-dropdown input{width:100%;padding:8px;margin:6px 0;border:1px solid var(--border);border-radius:5px;background:var(--bg);color:var(--text)}
        .menu-dropdown button{width:100%;margin-top:6px}

        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:15px;margin-top:20px}
        .item{border:1px solid var(--border);border-radius:8px;overflow:hidden;position:relative}
        .item img{width:100%;height:120px;object-fit:cover;cursor:pointer}
        .info{padding:5px;font-size:12px;color:var(--gray)}
        .btns{display:flex;gap:5px;padding:5px}
        .btns button{width:100%;padding:5px;font-size:12px;background:var(--primary);color:#fff;border:none;border-radius:4px;cursor:pointer}
        button.danger{background:var(--danger)}
        .current{position:absolute;top:5px;right:5px;background:var(--success);color:#fff;font-size:12px;padding:2px 6px;border-radius:4px}

        .login{max-width:360px;margin:0 auto}
        input{padding:10px;border:1px solid var(--border);border-radius:5px;background:var(--bg);color:var(--text)}
        button{cursor:pointer}

        .preview-modal{
            display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;
        }
        .preview-modal.show{display:flex}
        .preview-modal img{max-width:100%;max-height:100%;object-fit:contain}

        .footer{text-align:center;color:var(--gray);font-size:12px;margin-top:40px}
        .footer a{color:var(--primary);text-decoration:none}
        .footer a:hover{text-decoration:underline}
    </style>
</head>
<body class="<?= isset($_COOKIE['dark']) ? 'dark' : '' ?>">

<div class="preview-modal" id="previewModal" onclick="closePreview()">
    <img src="" id="previewImg">
</div>

<div class="card">
    <div class="header">
        <h1>📷 OneNav 背景图管理</h1>
        <?php if (isset($_SESSION['admin'])): ?>
        <div class="menu">
            <button class="menu-btn" id="menuBtn">菜单 ⚙️</button>
            <div class="menu-dropdown" id="menuDrop">
                <button class="danger" onclick="location.href='?logout=1'">退出登录</button>
                <hr style="margin:10px 0;border-color:var(--border)">
                <form method="post">
                    <input type="password" name="old_pass" placeholder="原密码" required>
                    <input type="password" name="new_pass1" placeholder="新密码" required>
                    <input type="password" name="new_pass2" placeholder="确认新密码" required>
                    <button type="submit" name="change_pass">修改密码</button>
                </form>
                <hr style="margin:10px 0;border-color:var(--border)">
                <button onclick="toggleDark()">切换深色/浅色</button>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['admin'])): ?>
    <span>管理员已登录</span>
    <?php endif; ?>
</div>

<?php if ($msg): ?>
<div class="msg"><?= $msg ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="err"><?= $error ?></div>
<?php endif; ?>

<?php if (!isset($_SESSION['admin'])): ?>
<div class="card login">
    <h2>管理员登录</h2>
    <p class="gray" style="margin-bottom:10px">默认密码：admin123</p>
    <form method="post">
        <input type="password" name="password" placeholder="请输入密码" style="width:100%" required>
        <button type="submit" name="login" style="width:100%;margin-top:10px;padding:10px">登录</button>
    </form>
</div>
<?php else: ?>

<div class="card">
    <h3>⬆️ 上传图片（自动压缩≤300KB）</h3>
    <form method="post" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" style="padding:10px 16px">上传</button>
    </form>
</div>

<div class="card">
    <h3>🖼️ 当前默认背景 bj.jpg</h3>
    <?php if (file_exists(TARGET_NAME)): ?>
        <?php [$w,$h,$kb] = getImageInfo(TARGET_NAME); ?>
        <img src="<?= TARGET_NAME ?>?t=<?=time()?>" 
             style="max-width:280px;border-radius:8px;cursor:pointer"
             onclick="openPreview('<?= TARGET_NAME ?>?t=<?=time()?>')">
        <p class="gray"><?= $w ?>×<?= $h ?> · <?= $kb ?>KB</p>
    <?php else: ?>
        <img src="<?= DEFAULT_BG_URL ?>" style="max-width:280px;border-radius:8px">
        <p class="gray">使用默认远程图</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>📂 所有背景图片（点击预览）</h3>
    <?php if (empty($images)): ?>
        <p>暂无图片</p>
    <?php else: ?>
    <div class="grid">
        <?php foreach ($images as $img): ?>
        <?php [$w,$h,$kb] = getImageInfo($img); ?>
        <div class="item">
            <?php if ($img === TARGET_NAME): ?>
            <span class="current">默认</span>
            <?php endif; ?>
            
            <img src="<?= $img ?>?t=<?=time()?>" onclick="openPreview('<?= $img ?>?t=<?=time()?>')">

            <div class="info">
                <?= $w ?>×<?= $h ?><br>
                <?= $kb ?>KB<br>
                <?= basename($img) ?>
            </div>
            <div class="btns">
                <?php if ($img !== TARGET_NAME): ?>
                <a href="?set_default=<?=urlencode($img)?>" onclick="return confirm('确定设为背景？')">
                    <button>设为背景</button>
                </a>
                <?php endif; ?>
                <a href="?delete=<?=urlencode($img)?>" onclick="return confirm('确定删除？')">
                    <button class="danger">删除</button>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<div class="footer">
    <a href="<?= GITHUB_URL ?>" target="_blank">GitHub 开源地址</a>
</div>

<script>
document.getElementById('menuBtn')?.addEventListener('click',()=>{
    document.getElementById('menuDrop').classList.toggle('show')
})

function toggleDark(){
    document.body.classList.toggle('dark')
    document.cookie = 'dark='+(document.body.classList.contains('dark')?1:0)+';path=/;max-age=31536000'
}

function openPreview(src) {
    document.getElementById('previewImg').src = src;
    document.getElementById('previewModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closePreview() {
    document.getElementById('previewModal').classList.remove('show');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePreview();
});
</script>
</body>
</html>
