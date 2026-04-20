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

// 密码存储
if (!file_exists(PASS_FILE)) {
    file_put_contents(PASS_FILE, DEFAULT_PASS);
}
$ADMIN_PASS = trim(file_get_contents(PASS_FILE));

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

$msg = '';
$error = '';

// 登录
if (isset($_POST['login'])) {
    if (trim($_POST['password']) === $ADMIN_PASS) {
        $_SESSION['admin'] = true;
        $msg = '登录成功';
    } else {
        $error = '密码错误';
    }
}

// 修改密码
if (isset($_SESSION['admin']) && isset($_POST['change_pass'])) {
    $old = trim($_POST['old_pass']);
    $new1 = trim($_POST['new_pass1']);
    $new2 = trim($_POST['new_pass2']);
    
    if ($old !== $ADMIN_PASS) {
        $error = '原密码不正确';
    } elseif ($new1 !== $new2) {
        $error = '两次新密码不一致';
    } elseif (strlen($new1) < 4) {
        $error = '密码长度不能小于4位';
    } else {
        file_put_contents(PASS_FILE, $new1);
        $msg = '密码修改成功，请牢记新密码';
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

// 上传
if (isset($_SESSION['admin']) && isset($_FILES['image'])) {
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
<meta charset="UTF-8">
<title>背景图管理</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:system-ui}
body{max-width:1000px;margin:20px auto;padding:20px;background:#f5f5f5;padding-bottom:120px}
.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 5px #0000000a}
h1,h2,h3{margin-bottom:15px;color:#333}
.msg{color:green;background:#ecffec;padding:10px;border-radius:5px;margin:10px 0}
.err{color:red;background:#ffeaea;padding:10px;border-radius:5px;margin:10px 0}
.gray{color:#666;font-size:14px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:15px;margin-top:20px}
.item{border:1px solid #ddd;border-radius:8px;overflow:hidden;position:relative}
.item img{width:100%;height:120px;object-fit:cover;cursor:pointer}
.info{padding:5px;font-size:12px;color:#666}
.btns{display:flex;gap:5px;padding:5px}
.btns button{width:100%;padding:5px;font-size:12px;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer}
button.danger{background:#dc3545}
.login{max-width:360px;margin:0 auto}
input{padding:10px;border:1px solid #ddd;border-radius:5px}
button{cursor:pointer}
.current{position:absolute;top:5px;right:5px;background:#28a745;color:white;font-size:12px;padding:2px 6px;border-radius:4px}

/* 图片预览弹窗 */
.preview-modal{
    display:none;position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;padding:20px;
}
.preview-modal.show{display:flex}
.preview-modal img{max-width:100%;max-height:100%;object-fit:contain}

/* 底部修改密码 一行样式 */
.bottom-bar{
    position:fixed;bottom:30px;left:0;right:0;background:#fff;border-top:1px solid #ddd;padding:10px 20px;
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;z-index:999
}
.bottom-bar input{
    flex:1;min-width:120px
}
.bottom-bar button{
    background:#007bff;color:white;border:none;padding:10px 16px;border-radius:5px;white-space:nowrap
}

/* GitHub 地址 */
.github-footer{
    position:fixed;bottom:0;left:0;right:0;background:#fff;padding:8px 20px;text-align:center;
    border-top:1px solid #eee;font-size:12px;color:#666;z-index:998
}
.github-footer a{
    color:#007bff;text-decoration:none
}
.github-footer a:hover{
    text-decoration:underline
}
</style>

<body>

<!-- 预览弹窗 -->
<div class="preview-modal" id="previewModal" onclick="closePreview()">
    <img src="" id="previewImg">
</div>

<div class="card">
    <h1>📷 导航站背景图管理</h1>
    <?php if (isset($_SESSION['admin'])): ?>
        <div style="display:flex;justify-content:space-between;align-items:center">
            <span>管理员已登录</span>
            <a href="?logout=1" onclick="return confirm('确定退出？')">
                <button class="danger">退出登录</button>
            </a>
        </div>
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
    <h3>📂 所有背景图片（点击图片预览）</h3>
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

<!-- 底部一行修改密码 -->
<form method="post" class="bottom-bar">
    <input type="password" name="old_pass" placeholder="原密码" required>
    <input type="password" name="new_pass1" placeholder="新密码" required>
    <input type="password" name="new_pass2" placeholder="确认新密码" required>
    <button type="submit" name="change_pass">修改密码</button>
</form>

<?php endif; ?>

<!-- GitHub 开源地址 -->
<div class="github-footer">
    开源项目：<a href="<?= GITHUB_URL ?>" target="_blank"><?= GITHUB_URL ?></a>
</div>

<script>
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
