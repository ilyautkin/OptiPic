OptiPic.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'optipic-panel-home',
            renderTo: 'optipic-panel-home-div'
        }]
    });
    OptiPic.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(OptiPic.page.Home, MODx.Component);
Ext.reg('optipic-page-home', OptiPic.page.Home);