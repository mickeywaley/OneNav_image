<style>
/* 优先级拉满！永远不被覆盖 */
html {
    background: url("https://f.aq520.com/websq/bj.jpg") !important;
    background-size: cover !important;
    background-position: center !important;
    background-attachment: fixed !important;
    background-color: #000 !important;
}

/* 清空所有可能的白色容器 */
body,
#app,
#app *,
.ant-layout,
.ant-layout *,
.ant-layout-content,
.ant-layout-sider,
.ant-layout-header
{
    background: transparent !important;
    background-color: transparent !important;
    background-image: none !important;
}

/* 隐藏加载白屏 */
#app-loading {
    display: none !important;
}

/* 所有文字改成白色，并加阴影提高可读性 */
* {
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.7) !important;
}

/* 去掉所有白色边框和线条 */
* {
    border-color: transparent !important;
    outline: none !important;
    box-shadow: none !important;
}
</style>
