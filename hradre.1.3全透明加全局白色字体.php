<!-- OneNav 全局背景 + 蒙版 + 全局白色字体（左侧菜单已修复） -->
<style>
/* 全局页面背景 */
html, body {
    background-image: var(--bg-image) !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    background-repeat: no-repeat !important;
    background-color: transparent !important;
}

/* 强制清除所有默认白色背景（核心修复） */
#aside,
.main-wrap,
#main,
.container,
.wrapper,
.content,
.box,
.card,
.panel,
body * {
    background: transparent !important;
    background-color: transparent !important;
    border-color: transparent !important;
    box-shadow: none !important;
}

/* ========== 全局白色字体（终极版，左侧菜单必生效） ========== */
html, body,
/* 左侧菜单（全覆盖） */
#aside,
#aside *,
#aside a,
#aside li,
#aside span,
#aside .menu,
#aside .nav,
#aside .active,
#aside .current,
/* 右侧内容 */
#main *, .card *, a, .site-name, .site-desc,
/* 通用文字 */
h1, h2, h3, h4, h5, h6, p, span {
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5) !important;
}

/* 链接悬浮效果 */
a:hover {
    color: #f5f5f5 !important;
}

/* 全屏黑色蒙版 */
body::before {
    content: "";
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #000;
    opacity: var(--mask-opacity, 0);
    z-index: -1;
    transition: opacity 0.3s;
    display: var(--mask-display);
}
</style>

<script>
// 配置项
const oneNavBg = {
    bgUrl: "https://f.aq520.com/websq/bj.jpg",      // 背景图
    maskEnable: true,                           // 开启蒙版
    maskOpacity: 0.3                            // 透明度
};

// 应用样式
document.documentElement.style.setProperty("--bg-image", `url(${oneNavBg.bgUrl})`);
document.documentElement.style.setProperty("--mask-opacity", oneNavBg.maskOpacity);
document.documentElement.style.setProperty("--mask-display", oneNavBg.maskEnable ? "block" : "none");
</script>
