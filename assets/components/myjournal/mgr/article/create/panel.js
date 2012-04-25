/**
 * Loads the main panel for MyJournal CRC - Create.
 * 
 * @class MyJournal.MainPanel
 * @extends MyJournal.Form.Abstract
 * @param {Object} config An object of configuration properties
 * @xtype modx-panel-myjournal
 */
MyJournal.MainPanel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'myjournal-main-panel'
        ,cls: 'container myjournal'
        ,unstyled: true
        ,defaults: { collapsible: false ,autoHeight: true }
        ,items: [{
            html: '<h2>Your title...</h2>'
            ,border: false
            ,cls: 'modx-page-header'
            ,id: 'modx-page-header'
        },MODx.getPageStructure([{
            title: 'Article'
            ,items: [{
                xtype: 'myjournal-create-article-panel'
            }]
        }])]
    });
    MyJournal.MainPanel.superclass.constructor.call(this,config);    
};
Ext.extend(MyJournal.MainPanel, Ext.Panel,{});
Ext.reg('myjournal-main-panel',MyJournal.MainPanel);