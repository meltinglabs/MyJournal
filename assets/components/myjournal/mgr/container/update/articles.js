/**
 * Grid definition for Articles list
 * 
 * @class MyJournal.ArticlesListGrid
 * @extends MODx.grid.Grid
 * @param {Object} config An object of options.
 * @xtype myjournal-articles-list-grid
 */
MyJournal.ArticlesListGrid = function(config) {
    config = config || {};    
    this._init();
    Ext.applyIf(config,{
        store: this.store
        ,cls: 'main-wrapper'
        ,columns: [{
            header: 'Date'
            ,dataIndex: 'createdon'
            ,id: 'date'
            ,width: 60
            ,sortable: true
            ,renderer: { fn:this.dateColumnRenderer, scope:this }            
        },{
            header: 'Title'
            ,dataIndex: 'pagetitle'
            ,id:'main'
            ,width: 200
            ,sortable: true
            ,renderer: { fn:this.mainColumnRenderer, scope:this }
        },{
            header: 'Author'
            ,dataIndex: 'createdby_username'
            ,id:'author'
            ,width: 150
            ,sortable: true
            // ,renderer: {fn:this._renderAuthor,scope:this}
        }]
        ,stripeRows : true
        ,trackMouseOver : false
        ,enableHdMenu: false
        ,selModel : new Ext.grid.RowSelectionModel({ singleSelect : true })
        ,border: false
        ,autoHeight: true
        ,tbar:[{
            xtype: 'button'
            ,text: 'Create New Article'
            ,scope: this
            ,handler: this.createArticle
            ,iconCls: 'icon-add'
        }]
        ,bbar: new Ext.PagingToolbar({
            pageSize: 10
            ,store: this.store
            ,displayInfo: true
            ,displayMsg: '{0} - {1} of {2}'
            ,emptyMsg: this.emtpyMsg || 'No data'       
        })
        ,viewConfig: {
            scrollOffset: 0
            ,forceFit: true
            ,emptyText: this.emptyText || '<h4>No data to display</h4>'
            ,enableRowBody:true
        }
    });
    MyJournal.ArticlesListGrid.superclass.constructor.call(this,config);
    this.on('click', this.onClick, this);
    // this.on('afterrender', this.getStore().load(), this);
};
Ext.extend(MyJournal.ArticlesListGrid,Ext.grid.GridPanel,{
    _init: function(){
        this.store = new Ext.data.Store({
            url: MyJournal.connector_url
            ,baseParams: { 
                action: 'articles/getList'
                ,'parent': MyJournal.resource_id
            }
            ,reader: new Ext.data.JsonReader({
                totalProperty: 'total'
                ,root: 'results'
                ,idProperty: 'id'
                ,messageProperty: 'message'
            },Ext.data.Record.create([
                'id'
                ,'pagetitle'
                ,'published'
                ,'publishedon'
                ,'publishedon_date'
                ,'publishedon_time'
                ,'uri'
                ,'uri_override'
                ,'createdby'
                ,{ name: 'createdon', type: 'date', dateFormat: 'Y-m-d H:i:s' }
                // ,'createdon'
                ,'createdby_username'
                ,'preview_url'
            ]))            
            ,listeners:{
                load: function(){ Ext.getCmp('modx-content').doLayout(); }
                ,exception : function(proxy, type, action, options, res, arg) {
                    console.log('error');
                }
                ,scope: this
            }
            ,autoLoad: true
            ,remoteSort: true
        });
        this.mainColumnTpl = new Ext.XTemplate('<tpl for=".">'
            +'<h3 class="main-column{state:defaultValue("")}">'
                +'<a href="index.php?a='+MODx.action['resource/update']+'&id={id}">{name}</a>'
            +'</h3>'
            +'<tpl if="actions !== null">'
                +'<ul class="actions">'
                    +'<tpl for="actions">'
                        +'<li><button type="button" class="controlBtn {className}">{text}</button></li>'
                    +'</tpl>'
                +'</ul>'
            +'</tpl>'
        +'</tpl>', {
            compiled: true
        });    
        this.dateColumnTpl = new Ext.XTemplate('<tpl for=".">'
            +'<div class="day_month">{day} {month} {year}<span class="hour">{hour}</span></div>', {
            compiled: true
        });    
    }
        
    ,mainColumnRenderer:function (value, metaData, record, rowIndex, colIndex, store){
        var rec = record.data;
        var state = (rec.published) ? ' published' : ' not-published';
        var values = { name: value, state: state, id: rec.id, actions: null };

        var h = [];
        h.push({ className:'edit', text: 'Edit' });
        h.push({ 
            className: (rec.published) ? 'unpublish': 'publish orange'
            ,text: (rec.published) ? 'Unpublish': 'Publish'
        });
        h.push({ className:'delete', text: 'Remove' });
        h.push({ className:'view', text: 'View' });

        values.actions = h;        
        return this.mainColumnTpl.apply(values);
    }
        
    ,dateColumnRenderer:function (value, metaData, record, rowIndex, colIndex, store){
        var values = {};
        values.day = value.format('d')
        values.month = value.format('M')
        values.year = value.format('Y')
        values.hour = value.format('h:i')    
        return this.dateColumnTpl.apply(values);
    }
    
    ,onClick: function(e){
        var t = e.getTarget();
        var elm = t.className.split(' ')[0];
        if(elm == 'controlBtn'){
            var act = t.className.split(' ')[1];
            var record = this.getSelectionModel().getSelected();
            switch (act) {
                case 'edit':
                    location.href = 'index.php?a='+MODx.action['resource/update']+'&id='+record.data.id;
                    break;
                case 'view':
                    window.open(record.data.preview_url);
                    break;
                default:
                    break;
            }
        }
    }
    
    ,createArticle: function(btn,e) {
        location.href = 'index.php?a='+MODx.action['resource/create']+'&class_key=MyArticle&parent='+MODx.request.id;
    }
});
Ext.reg('myjournal-articles-list-grid',MyJournal.ArticlesListGrid);