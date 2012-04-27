/**
 * Loads the main panel for MyJournal CRC - Create.
 * 
 * @class MyJournal.UpdateArticlePanel
 * @extends MyJournal.Form.Abstract
 * @param {Object} config An object of configuration properties
 * @xtype myjournal-update-article-panel
 */
MyJournal.UpdateArticlePanel = function(config) {
    config = config || {};
    Ext.applyIf(config,{        
        layout: 'form'
        ,autoHeight: true
        ,defaults: { border: false }
        ,items:[{
            layout: 'column'
            ,defaults: { unstyled: true, border: false }
            ,items:[{
                layout: 'form'
                ,id: 'myjournal-container-main-column'
                ,columnWidth: 1
                ,defaults: { unstyled: true }
                ,items:[]
            },{
                layout: 'form'
                ,id: 'myjournal-container-sidebar'
                ,width: 250
                ,cls: 'aside'
                ,defaults: { labelAlign: 'top', unstyled: true, layout: 'form' }
                ,items: []
            }]
            ,tbar:['->',{
                xtype: 'button'
                ,text: 'Cancel'
                // ,handler: this.onBackToContainer
                // ,scope: this
            },'-',{
                xtype: 'button'
                ,text: 'Preview'
                ,iconCls: 'icon-view'
                ,handler: this.view
                ,scope: this
            },{
                xtype: 'button'
                ,text: 'Test'
                ,handler: this.test
                ,scope: this
            },{
                text: 'Save'
                ,cls: 'green'
                ,handler: this.save
                ,scope: this
            }]
            ,tbarCfg:{ cls: 'main-tbar' }
            ,bodyCssClass: 'main-wrapper with-tbar form-with-labels'
        }]
        ,saveAction: 'update'
    });
    MyJournal.UpdateArticlePanel.superclass.constructor.call(this,config);    
    this.on('afterrender', this._init, this);
    this._setup();
};
Ext.extend(MyJournal.UpdateArticlePanel, MyJournal.Form.Abstract,{  
    _init: function(){
        this.form = Ext.getCmp('modx-panel-resource').getForm();
    }
    
    ,test: function(){
        Ext.Ajax.request({
            url : MyJournal.connector_url
            ,params : {
                action: 'taxonomy/test'
            }
            ,method: 'GET'
            ,scope: this
            ,success: function ( result, request ) { 
                var data = Ext.util.JSON.decode( result.responseText );
                console.log(data)
            }
            ,failure: function ( result, request) { 
                Ext.MessageBox.alert(_('bigbrother.alert_failed'), result.responseText); 
            } 
        });
    }
    
    ,_addTagsField: function(){
        var data = MyJournal.tags;
        
        Ext.getCmp('myjournal-container-sidebar').add({
            xtype: 'panel'        
            ,cls: 'aside-block-wrapper'            
            ,defaults: { anchor: '100%' }
            ,items:[{
                xtype: 'myjournal-field-autotag'     
                ,id: 'tagpanel'
                ,data: data       
            }]
        });
    }
    
    ,view: function(btn, e){
        window.open(MyJournal.preview_url);
        return false;
    }
    
    ,_setup: function(){
        this._addHiddenFields();
        this._addResourceFields();
        this._addSidebarDocumentFields();
        this._addSidebarSettingsFields();
        this._addTagsField();
        this.doLayout();
        
        if(MyJournal.record.resourceGroups.length > 0){
            this.asideDarker = Ext.getCmp('aside-darker'),
            this.asideDarker.add({
                 xtype: 'myjournal-field-checkboxgroup'
                ,fieldLabel: 'Access & Privacy'
                ,data: MyJournal.record.resourceGroups
            });           
        }
        this.doLayout();
    }
    
    ,onSaveSuccess: function(form, action){
        this.enable();
    }
       
    ,_addResourceFields: function(){
        Ext.getCmp('myjournal-container-main-column').add({
            xtype: 'panel'    
            ,id: 'modx-main-resource-field'
            ,layout: 'form'    
            ,labelAlign: 'top'
            ,defaults: { anchor: '100%' }
            ,items:[{
                xtype: 'textfield'
                ,fieldLabel: _('resource_pagetitle')
                ,name: 'pagetitle'
                ,id: 'modx-resource-pagetitle'
                ,anchor: '100%'
                ,value: MyJournal.record.pagetitle
                ,allowBlank: false
                ,enableKeyEvents: true
                ,listeners: {
                    keyup: this.setAlias
                    ,scope: this
                    ,buffer: 1000
                }
            },{
                xtype: 'label'
                ,forId: 'modx-resource-pagetitle'
                ,html: _('resource_pagetitle_help')
                ,cls: 'desc-under'
            },{
                xtype: 'textfield'
                ,fieldLabel: _('resource_longtitle')
                ,name: 'longtitle'
                ,id: 'modx-resource-longtitle'
                ,value: MyJournal.record.longtitle || ''
                ,anchor: '100%'
                // ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-longtitle'
                ,html: _('resource_longtitle_help')
                ,cls: 'desc-under'
                // ,hidden: true
            },{            
                xtype: 'textarea'
                ,fieldLabel: _('resource_description')
                ,name: 'description'
                ,id: 'modx-resource-description'
                ,maxLength: 255
                ,anchor: '100%'
                ,value: MyJournal.record.description || ''
                ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-description'
                ,html: _('resource_description_help') 
                ,cls: 'desc-under'
                ,hidden: true
            },{
                 xtype: 'textarea'
                ,fieldLabel: _('resource_summary')
                ,name: 'introtext'
                ,id: 'modx-resource-introtext'
                ,grow: true
                ,anchor: '100%'
                ,value: MyJournal.record.introtext || ''
                ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-introtext'
                ,html: _('resource_summary_help') 
                ,cls: 'desc-under'
                ,hidden: true
            },{
                xtype: 'textarea'
                ,name: 'content'
                ,id: 'content'
                ,fieldLabel: _('resource_content')
                ,anchor: '100%'
                ,height: 400
                ,grow: false
                ,value: (MyJournal.record.content || MyJournal.record.ta) || ''
            }]
        });
    }
    
    ,_addSidebarDocumentFields: function(){
        Ext.getCmp('myjournal-container-sidebar').add({
            xtype: 'panel'        
            ,cls: 'aside-block-wrapper'
            ,defaults: { anchor: '100%' }
            ,items:[{
                xtype: 'textfield'
                ,fieldLabel: _('resource_menutitle')
                ,name: 'menutitle'
                ,id: 'modx-resource-menutitle'
                ,maxLength: 255
                ,anchor: '100%'
                ,value: ''
                ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-menutitle'
                ,html: _('resource_menutitle_help')
                ,cls: 'desc-under'
                ,hidden: true
            },{
                fieldLabel: 'Article Status'
                ,name: 'published'
                ,id: 'modx-resource-published'                
                ,xtype: 'combo'
                ,store: new Ext.data.ArrayStore({
                    fields: ['d', 'v']
                    ,data : [[0, 'Not Published']
                        ,[1, 'Published']
                    ]
                })
                ,displayField:'v'
                ,valueField:'d'
                ,hiddenName : 'published'
                ,typeAhead: true
                ,mode: 'local'
                ,forceSelection: true
                ,triggerAction: 'all'
                ,selectOnFocus:true
                ,value: MyJournal.record.published
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('resource_hide_from_menus')
                ,description: '<b>[[*hidemenu]]</b><br />'+_('resource_hide_from_menus_help')
                ,hideLabel: true
                ,name: 'hidemenu'
                ,id: 'modx-resource-hidemenu'
                ,inputValue: 1
                ,checked: true
                ,hidden: true
            },{
                xtype: 'textfield'
                ,fieldLabel: _('resource_alias')
                ,name: 'alias'
                ,id: 'modx-resource-alias'
                ,maxLength: 100
                ,anchor: '100%'
                ,value: MyJournal.record.alias || ''
                // ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-alias'
                ,html: _('myjournal.label_alias_desc') //We don't use the one supplied with the core - Eye FTW!
                ,cls: 'desc-under'
                // ,hidden: true
            },{
                xtype: 'modx-combo-template'
                ,fieldLabel: _('resource_template')
                ,description: '<b>[[*template]]</b><br />'+_('resource_template_help')
                ,name: 'template'
                ,id: 'modx-resource-template'
                ,anchor: '100%'
                ,editable: false
                ,baseParams: {
                    action: 'getList'
                    ,combo: '1'
                }
                ,value: MyJournal.record.template || 0
            }]
        });
    }
    ,_addSidebarSettingsFields: function(){
        
        Ext.getCmp('myjournal-container-sidebar').add({
            xtype: 'panel'    
            ,cls: 'aside-block-wrapper darker'
            ,id: 'aside-darker'
            ,defaults: { anchor: '100%' }
            ,items:[{
                xtype: 'modx-field-parent-change'
                ,fieldLabel: _('resource_parent')
                ,name: 'parent-cmb'
                ,id: 'modx-resource-parent'
                ,value: MyJournal.record.parent || 0
                ,hidden: true
            },{
                xtype: 'label'
                ,forId: 'modx-resource-parent'
                ,html: _('resource_parent_help')
                ,cls: 'desc-under'
                ,hidden: true
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('resource_cacheable')
                ,description: '<b>[[*cacheable]]</b><br />'+_('resource_cacheable_help')
                ,hideLabel: true
                ,name: 'cacheable'
                ,id: 'modx-resource-cacheable'
                // ,hidden: true
                ,inputValue: 1
                ,checked: parseInt(MyJournal.record.cacheable)
            },{
                 xtype: 'xcheckbox'
                ,boxLabel: _('resource_searchable')
                ,description: '<b>[[*searchable]]</b><br />'+_('resource_searchable_help')
                ,hideLabel: true
                // ,hidden: true
                ,name: 'searchable'
                ,id: 'modx-resource-searchable'
                ,inputValue: 1
                ,checked: parseInt(MyJournal.record.searchable) || true
            },{
                 xtype: 'xcheckbox'
                ,boxLabel: _('resource_uri_override')
                ,description: _('resource_uri_override_help')
                ,hideLabel: true
                ,hidden: true
                ,name: 'uri_override'
                ,value: 1
                ,checked: parseInt(MyJournal.record.uri_override) ? true : false
                ,id: 'modx-resource-uri-override'
            },{
                 xtype: 'xcheckbox'
                ,boxLabel: _('resource_syncsite')
                ,description: _('resource_syncsite_help')
                ,hideLabel: true
                ,hidden: true
                ,name: 'syncsite'
                ,id: 'modx-resource-syncsite'
                ,inputValue: 1
                ,checked: parseInt(MyJournal.record.syncsite) || true
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('resource_richtext')
                ,description: _('resource_richtext_help')
                ,hideLabel: true
                ,hidden: true
                ,name: 'richtext'
                ,id: 'modx-resource-richtext'
                ,inputValue: 1
                ,checked: isNaN(parseFloat(MyJournal.record.richtext)) ? MyJournal.record.richtext : parseInt(MyJournal.record.richtext)
                ,listeners:{
                    afterrender: this.onCheckIfRTE
                    ,scope: this
                }
            }]
        });
    }
    
    ,_addHiddenFields: function(){
        Ext.getCmp('myjournal-container-main-column').add({
             xtype: 'hidden'
            ,fieldLabel: _('id')
            ,hideLabel: true
            ,description: '<b>[[*id]]</b><br />'
            ,name: 'id'
            ,id: 'modx-resource-id'
            ,anchor: '100%'
            ,value: MyJournal.resource || MyJournal.record.id
            ,submitValue: true
        },{
            xtype: 'hidden'
            ,name: 'type'
            ,value: 'document'
        },{
            xtype: 'hidden'
            ,name: 'context_key'
            ,id: 'modx-resource-context-key'
            ,value: MyJournal.record.context_key || 'web'
        },{
            xtype: 'hidden'
            ,name: 'content'
            ,id: 'hiddenContent'
            ,value: (MyJournal.record.content || MyJournal.record.ta) || ''
        },{
            xtype: 'hidden'
            ,name: 'create-resource-token'
            ,id: 'modx-create-resource-token'
            ,value: MyJournal.record.create_resource_token || ''
        },{
            xtype: 'hidden'
            ,name: 'parent'
            ,value: MyJournal.record.parent || 0
            ,id: 'modx-resource-parent-hidden'
        },{
            xtype: 'hidden'
            ,name: 'parent-original'
            ,value: MyJournal.record.parent || 0
            ,id: 'modx-resource-parent-old-hidden'
        },{
            xtype: 'hidden'
            ,name: 'class_key'
            ,value: MyJournal.record.class_key        
        });
    }
    
    ,onCheckIfRTE: function(me){
        if(me.getValue()){
            jQuery("#content").markItUp(mySettings);
        }
    }
});
Ext.reg('myjournal-update-article-panel',MyJournal.UpdateArticlePanel);