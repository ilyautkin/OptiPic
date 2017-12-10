OptiPic.window.CreateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'optipic-item-window-create';
    }
    Ext.applyIf(config, {
        title: _('optipic_item_create'),
        width: 550,
        autoHeight: true,
        url: OptiPic.config.connector_url,
        action: 'mgr/item/create',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    OptiPic.window.CreateItem.superclass.constructor.call(this, config);
};
Ext.extend(OptiPic.window.CreateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('optipic_item_name'),
            name: 'file',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textfield',
            fieldLabel: _('optipic_item_optimized'),
            name: 'optimized',
            id: config.id + '-optimized',
            anchor: '99%'
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('optipic-item-window-create', OptiPic.window.CreateItem);


OptiPic.window.UpdateItem = function (config) {
    config = config || {};
    if (!config.id) {
        config.id = 'optipic-item-window-update';
    }
    Ext.applyIf(config, {
        title: _('optipic_item_update'),
        width: 550,
        autoHeight: true,
        url: OptiPic.config.connector_url,
        action: 'mgr/item/update',
        fields: this.getFields(config),
        keys: [{
            key: Ext.EventObject.ENTER, shift: true, fn: function () {
                this.submit()
            }, scope: this
        }]
    });
    OptiPic.window.UpdateItem.superclass.constructor.call(this, config);
};
Ext.extend(OptiPic.window.UpdateItem, MODx.Window, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id',
        }, {
            xtype: 'textfield',
            fieldLabel: _('optipic_item_name'),
            name: 'file',
            id: config.id + '-name',
            anchor: '99%',
            allowBlank: false,
        }, {
            xtype: 'textfield',
            fieldLabel: _('optipic_item_optimized'),
            name: 'optimized',
            id: config.id + '-optimized',
            anchor: '99%',
        }];
    },

    loadDropZones: function () {
    }

});
Ext.reg('optipic-item-window-update', OptiPic.window.UpdateItem);