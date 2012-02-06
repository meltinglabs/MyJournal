Ext.ns('MyJournal');

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
            ,id: 'modx-resource-header'
        },MODx.getPageStructure([{
			title: 'Article'
			,tbarCfg:{
				cls: 'save-btns'
			}
			,tbar:['->',{
				xtype: 'button'
				,text: 'Cancel'
				// ,handler: this.onBackToContainer
				// ,scope: this
			},'-',{
				xtype: 'button'
				,text: 'Preview'
				// ,handler: this.onPreview
				// ,scope: this
			},{
				xtype: 'button'
				,text: 'Save Changes'
				,handler: this.onSaveResource
				,scope: this
			}]
			,items:[{
				xtype: 'myjournal-panel-resource'		
				,id: 'modx-panel-resource'	
			}]            			
		}])]
    });
    MyJournal.panel.superclass.constructor.call(this,config);
	// this.on('render', this.setup, this);
};
Ext.extend(MyJournal.panel,MODx.Panel,{
	setup: function(){}
	
	,onSaveResource: function(btn, e){
		var form = Ext.getCmp('modx-panel-resource').getForm();
		if(form.isValid()){
			// console.log(form.getValues())
			form.submit({
				url: MODx.config.connectors_url+'resource/index.php'
				,method: 'POST'
				,params: { action: 'update' }
				,waitMsg: 'Saving, please wait...'
				,success: function(f, action){}
				,failure: function(form, action){
					/* Push it above the form */
					// console.log(action.result.message)
				}
				,scope: this
			});
		}
	}

	,toggleRTE: function(){
		jQuery("#content").markItUp(mySettings);
	}
});
Ext.reg('myjournal-main-panel',MyJournal.panel);