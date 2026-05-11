<!-- OneNav 全局背景 + 蒙版 + 全局白色字体 -->
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

/* ========== 新增：全局白色字体（核心） ========== */
html, body,
/* 左侧菜单文字 */
#aside a, #aside .menu-item, #aside .title,
/* 右侧内容文字 */
#main a, .card a, .link-item, .site-name, .site-desc,
/* 标题、普通文本 */
h1, h2, h3, h4, h5, h6, p, span, li,
/* 按钮、交互文字 */
.btn, button, .nav, .pagination {
    color: #ffffff !important; /* 纯白色 */
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important; /* 文字阴影，更清晰 */
}

/* 链接 hover 效果（可选，更美观） */
a:hover {
    color: #f0f0f0 !important;
    text-decoration: none !important;
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
