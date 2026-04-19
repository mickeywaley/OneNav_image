<style>
/* 全局背景图 */
html, body {
    background-image: url(https://f.xxx.com/修改背景图地址bj.jpg) !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    background-repeat: no-repeat !important;
    background-color: transparent !important;
}

/* 全局蒙版层 *默认rgba(0,0,0, 0.25 )*数字越大越黑，越小越透明*/
body::after {
    content: '' !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(0, 0, 0, 0.25) !important;
    z-index: -9999 !important;
    pointer-events: none !important;
}

/* ========== 左侧菜单 强制透明毛玻璃 ========== *默认rgba(0,0,0, 0.55 )数字越大越黑，越小越透明*/
#sidebar,
.el-aside,
.el-menu,
.el-menu-item,
.sidebar-container,
.menu-wrapper {
    background: rgba(0, 0, 0, 0.55) !important;
    background-color: rgba(0, 0, 0, 0.55) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: none !important;
}

/* 左侧文字强制白色 */
#sidebar a,
.el-menu-item,
.el-submenu__title {
    color: #ffffff !important;
    background: transparent !important;
}
#sidebar a:hover,
.el-menu-item:hover {
    background: rgba(255, 255, 255, 0.1) !important;
}

/* ========== 右侧内容区域 强制透明 ========== *默认rgba(0,0,0, 0.40 )*数字越大越黑，越小越透明*/
#main,
.el-main,
.container,
.content-wrap {
    background: rgba(0, 0, 0, 0.40) !important;
    background-color: rgba(0, 0, 0, 0.40) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    border-radius: 8px !important;
}

/* ========== 导航链接卡片 彻底透明 ========== */
.link-item,
.el-card,
.card,
.link,
.item-box {
    background: rgba(255, 255, 255, 0.08) !important;
    background-color: rgba(255, 255, 255, 0.08) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: none !important;
}
.link-item:hover {
    background: rgba(255, 255, 255, 0.18) !important;
}

/* ========== 分类标题透明 ========== */
.category h4,
.cate-title,
.title {
    background: rgba(0, 0, 0, 0.25) !important;
    color: #fff !important;
}

/* 强制清除所有白色 */
* {
    background-color: transparent !important;
    box-shadow: none !important;
    border-color: transparent !important;
}
</style>
