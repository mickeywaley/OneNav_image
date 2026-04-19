<!-- OneNav 自定义背景 + 蒙版 + 透明度 -->
<style>
/* 背景图全局样式 */
body {
    background-image: var(--bg-image) !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    background-repeat: no-repeat !important;
}
/* 蒙版 */
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
// 配置项（你可以在后台修改）
const oneNavBg = {
    bgUrl: "https://f.xx.com/websq/背景图片地址bj.jpg",      // 默认背景图
    maskEnable: true,                           // 是否开启蒙版 true/false
    maskOpacity: 0.3                            // 透明度 0~1
};

// 自动应用样式
document.documentElement.style.setProperty("--bg-image", `url(${oneNavBg.bgUrl})`);
document.documentElement.style.setProperty("--mask-opacity", oneNavBg.maskOpacity);
document.documentElement.style.setProperty("--mask-display", oneNavBg.maskEnable ? "block" : "none");
</script>
