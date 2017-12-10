OptiPic.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'optipic-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('optipic') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('optipic_items'),
                layout: 'anchor',
                items: [{
                    html: _('optipic_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'optipic-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    OptiPic.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(OptiPic.panel.Home, MODx.Panel);
Ext.reg('optipic-panel-home', OptiPic.panel.Home);
