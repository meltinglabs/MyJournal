/**
 * Loads the main panel for MyJournal CRC - Create.
 * 
 * @class MyJournal.panel
 * @extends MODx.Panel
 * @param {Object} config An object of configuration properties
 * @xtype modx-panel-myjournal
 */
MyJournal.panel = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'myjournal-main-panel'
        ,cls: 'container myjournal'
        ,unstyled: true
        ,defaults: { collapsible: false ,autoHeight: true }
        ,items: [{
            html: '<h2>'+ MyJournal.record.pagetitle +'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
            ,id: 'modx-page-header'
        },MODx.getPageStructure([{
            title: 'Articles'
            ,items:[{
                xtype: 'myjournal-articles-list-grid'                
            }]
        },{
            title: 'Container Page'
            ,items: [{
                xtype: 'myjournal-panel-resource'
                ,tbarCfg:{ cls: 'main-tbar' }
                ,bodyCssClass: 'main-wrapper with-tbar form-with-labels'
            }]
        }])]
    });
    MyJournal.panel.superclass.constructor.call(this,config);
    this._init();
};
Ext.extend(MyJournal.panel,MODx.Panel,{
    _init: function(){
        me = this;
        this.actionToolbar = new Ext.Toolbar({
            renderTo: "modAB"
            ,id: 'modx-action-buttons'
            ,defaults: { scope: me }
            ,cls: 'myjournal'
            ,items: [{
                text: 'View Page'
                ,xtype: 'button'
                ,iconCls: 'icon-view'
                ,handler: this.view
            }]
        });                                
        this.actionToolbar.doLayout();
    }
    
    ,view: function(btn, e){
        window.open(MyJournal.preview_url);
        return false;
    }
});
Ext.reg('myjournal-main-panel',MyJournal.panel);