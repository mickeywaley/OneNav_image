<!-- OneNav 全局背景 + 蒙版 + 全局白色字体（含顶部搜索框） -->
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

/* 清除所有白色背景 */
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

/* 全局所有文字白色 + 描边阴影 */
html, body,
#aside, #aside *,
#main, #main *,
.header, .header *,
/* 顶部搜索框通用 */
.search-input,
input[type="text"],
input::placeholder,
/* 链接、标题、普通文本 */
a, span, p, li, h1,h2,h3,h4,h5,h6,
.site-name, .site-desc, .menu-item {
    color: #ffffff !important;
    text-shadow: 0 1px 4px rgba(0,0,0,0.6) !important;
}

/* 搜索框占位符文字变白 */
::placeholder {
    color: #eeeeee !important;
    opacity: 1 !important;
}

/* 搜索按钮、提交文字变白 */
button, .search-btn, input[type="submit"] {
    color: #fff !important;
}

/* 链接悬浮效果 */
a:hover {
    color: #f0f0f0 !important;
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
    bgUrl: "https://f.aq520.com/websq/bj.jpg",
    maskEnable: true,
    maskOpacity: 0.3
};

document.documentElement.style.setProperty("--bg-image", `url(${oneNavBg.bgUrl})`);
document.documentElement.style.setProperty("--mask-opacity", oneNavBg.maskOpacity);
document.documentElement.style.setProperty("--mask-display", oneNavBg.maskEnable ? "block" : "none");
</script>
