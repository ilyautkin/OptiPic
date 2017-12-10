var OptiPic = function (config) {
    config = config || {};
    OptiPic.superclass.constructor.call(this, config);
};
Ext.extend(OptiPic, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('optipic', OptiPic);

OptiPic = new OptiPic();