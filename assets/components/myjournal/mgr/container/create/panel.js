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
            html: '<h2>Create a new Journal Container</h2>'
            ,border: false
            ,cls: 'modx-page-header'
            ,id: 'modx-page-header'
        },{
            layout: 'form'
            ,autoHeight: true
            ,defaults: { border: false }
            ,items:[{
                xtype:'myjournal-form-abstract'
                ,cls: 'main-wrapper form-with-labels'
                ,id: 'modx-panel-resource'
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: _('resource_pagetitle')
                    ,name: 'pagetitle'
                    ,id: 'modx-resource-pagetitle'
                    ,anchor: '100%'
                    ,enableKeyEvents: true                    
                    ,allowBlank: false
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
                    ,fieldLabel: _('resource_alias')
                    ,name: 'alias'
                    ,id: 'modx-resource-alias'
                    ,maxLength: 100
                    ,anchor: '100%'
                },{
                    xtype: 'label'
                    ,forId: 'modx-resource-alias'
                    ,html: _('myjournal.label_alias_desc') //We don't use the one supplied with the core - Eye FTW!
                    ,cls: 'desc-under'
                },{
                    xtype: 'textarea'
                    ,name: 'content'
                    ,id: 'content'
                    ,fieldLabel: _('resource_content')
                    ,anchor: '100%'
                    ,height: 400
                    ,grow: false
                    ,hidden: true
                    ,value: (MyJournal.record.content || MyJournal.record.ta) || ''
                },{
                    xtype: 'hidden'
                    ,name: 'type'
                    ,value: 'document'
                },{
                    xtype: 'hidden'
                    ,name: 'published'
                    ,value: 0
                },{
                    xtype: 'hidden'
                    ,name: 'isfolder'
                    ,value: 1
                },{
                    xtype: 'hidden'
                    ,name: 'context_key'
                    ,id: 'modx-resource-context-key'
                    ,value: MyJournal.record.context_key || 'web'
                },{
                    xtype: 'hidden'
                    ,name: 'create_resource_token'
                    ,id: 'modx-create-resource-token'
                    ,value: MyJournal.record.create_resource_token || ''
                },{
                    xtype: 'hidden'
                    ,name: 'parent'
                    ,value: MyJournal.record.parent || 0
                    ,id: 'modx-resource-parent-hidden'
                },{
                    xtype: 'hidden'
                    ,name: 'class_key'
                    ,value: MyJournal.record.class_key || 0
                    ,id: 'modx-resource-parent-old-hidden'
                }]
                ,buttonAlign: 'center'            
                ,buttons: [{
                     xtype: 'button'
                    ,id: 'action-btn'
                    ,text: 'Create Container'
                    ,handler: this.save
                    ,disabled: true
                    ,scope: this
                }]
                ,saveAction: 'create'
                ,onSaveSuccess: function(form, action){
                    this.disable();
                    location.href = '?a='+MODx.action['resource/update']+'&id='+action.result.object.id;
                }    
            }]
        }]
    });
    MyJournal.panel.superclass.constructor.call(this,config);
    
    this.on('afterrender', this._init, this);
};
Ext.extend(MyJournal.panel, Ext.Panel,{  
    _init: function(){
        this.form = Ext.getCmp('modx-panel-resource');
        this.button = Ext.getCmp('action-btn');
    }
    
    ,setAlias: function(field){
        if(this.button.disabled) this.button.enable();
        var value = field.getValue();
        if(value.length == 0) this.button.disable();
        this.form.setAlias(field);
    }
    
    ,save: function(){
        this.form.save();
    }
});
Ext.reg('myjournal-main-panel',MyJournal.panel);