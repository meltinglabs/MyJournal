Ext.ns('MyJournal');
/**
 * Loads the resource container panel for MyJournal CRC.
 * 
 * @class MyJournal.ContainerPanelResource
 * @extends MODx.Panel
 * @param {Object} config An object of configuration properties
 * @xtype myjournal-panel-resource
 */
MyJournal.ContainerPanelResource = function(config) {
	config = config || {};
    Ext.applyIf(config,{
		cls: 'main-wrapper form-with-labels'
		,unstyled: true
		,defaults: { unstyled: true }
		,layout: 'column'
		,items:[{
			layout: 'form'
			,id: 'myjournal-container-main-column'
			,columnWidth: 1
			,defaults: { unstyled: true }
			,items:[]
			,listeners:{
				add:this.onPanelAdded
				,scope: this
			}
		},{
			layout: 'form'
			,id: 'myjournal-container-sidebar'
			,width: 250
			,cls: 'aside'
			,defaults: { labelAlign: 'top', unstyled: true, layout: 'form' }
			,items: []
		}]
	});
    MyJournal.ContainerPanelResource.superclass.constructor.call(this,config);
	this._setup();
	this.on('render', this._init, this);
};
Ext.extend(MyJournal.ContainerPanelResource, Ext.form.FormPanel,{
	//Add fields before render event otherwise they are not loaded in Basic Form "fields"
	_setup: function(){
		this._addHiddenFields();		
		this._addResourceFields();						
		this._addSidebarDocumentFields();
		this._addSidebarSettingsFields();
		// this._addPrivacyField();
	}
	
	//Move the TV panel after render
	,_init: function(){
		this._addTVs();
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
					keyup: this.onKeyUp
					,scope: this
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
	
	,_addTVs: function() {
        Ext.getCmp('myjournal-container-main-column').add({
            xtype: 'modx-panel-resource-tv'
            ,collapsed: false
			,cls: 'tvs-wrapper'
            ,resource: ''
			,defaults: { unstyled: true }
            ,class_key: 'MyJournalContainer'
            ,template: 1
            ,unstyled: true
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
				,value: MyJournal.record.menutitle || ''
				,hidden: true
			},{
				xtype: 'label'
				,forId: 'modx-resource-menutitle'
				,html: _('resource_menutitle_help')
				,cls: 'desc-under'
				,hidden: true
			},{
				xtype: 'xcheckbox'
				,boxLabel: _('resource_hide_from_menus')
				,description: '<b>[[*hidemenu]]</b><br />'+_('resource_hide_from_menus_help')
				,hideLabel: true
				,name: 'hidemenu'
				,id: 'modx-resource-hidemenu'
				,inputValue: 1
				,checked: parseInt(MyJournal.record.hidemenu) || false
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
			}]
		});
	}
	,_addSidebarSettingsFields: function(){
		Ext.getCmp('myjournal-container-sidebar').add({
			xtype: 'panel'	
			,cls: 'aside-block-wrapper darker'
			,defaults: { anchor: '100%' }
			,hidden: true
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
				,hidden: true
				,inputValue: 1
				,checked: parseInt(MyJournal.record.cacheable)
			},{
				 xtype: 'xcheckbox'
				,boxLabel: _('resource_searchable')
				,description: '<b>[[*searchable]]</b><br />'+_('resource_searchable_help')
				,hideLabel: true
				,hidden: true
				,name: 'searchable'
				,id: 'modx-resource-searchable'
				,inputValue: 1
				,checked: parseInt(MyJournal.record.searchable)
			},{
				 xtype: 'xcheckbox'
				,boxLabel: _('resource_uri_override')
				,description: _('resource_uri_override_help')
				,hideLabel: true
				// ,hidden: true
				,name: 'uri_override'
				,value: 1
				,checked: parseInt(MyJournal.record.uri_override) ? true : false
				,id: 'modx-resource-uri-override'
			},{
				 xtype: 'xcheckbox'
				,boxLabel: _('resource_syncsite')
				,description: _('resource_syncsite_help')
				,hideLabel: true
				// ,hidden: true
				,name: 'syncsite'
				,id: 'modx-resource-syncsite'
				,inputValue: 1
				,checked: parseInt(MyJournal.record.syncsite) || true
			},{
				xtype: 'xcheckbox'
				,boxLabel: _('resource_richtext')
				,description: _('resource_richtext_help')
				,hideLabel: true
				// ,hidden: true
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
	
	,_addPrivacyField: function(){
		Ext.getCmp('myjournal-container-sidebar').add({
			xtype: 'panel'	
			,cls: 'aside-block-wrapper darker'
			,defaults: { anchor: '100%' }
			,items:[{
				html: 'Privacy & Visibility'
				,unstyled: true
				,cls: 'box-title'
			},{
				boxLabel: 'Resource Group 1'
				,name: 'Container'
				,xtype:'checkbox'
				,hideLabel: true
			},{
				boxLabel: 'Resource Group 2'
				,name: 'fav-animal-cat'
				,xtype:'checkbox'
				,hideLabel: true
			},{
				checked: true
				,boxLabel: 'Resource Group 3'
				,name: 'fav-animal-monkey'
				,xtype:'checkbox'
				,hideLabel: true
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
				,name: 'reloaded'
				,value: !Ext.isEmpty(MODx.request.reload) ? 1 : 0
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
			});
	}
	
	,onPanelAdded: function(p, cmp){
		if(cmp.id == "modx-panel-resource-tv" && cmp.rendered){
			Ext.getCmp('myjournal-main-panel').setup();
		}
	}
	
	,onCheckIfRTE: function(me){
		if(me.getValue()){
			Ext.getCmp('myjournal-main-panel').toggleRTE();
		}
	}
	
	,onKeyUp: function(f,e) {
		var title = Ext.util.Format.stripTags(f.getValue());
		Ext.getCmp('modx-resource-header').getEl().update('<h2>'+title+'</h2>');
	}
});
Ext.reg('myjournal-panel-resource',MyJournal.ContainerPanelResource);