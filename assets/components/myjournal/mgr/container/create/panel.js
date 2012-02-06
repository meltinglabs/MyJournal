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
            html: '<h2>'+ _('myjournal.new_container') +'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
			layout: 'form'
			,autoHeight: true
			,defaults: { border: false }
			,items:[{
				tbarCfg:{
					cls: 'save-btns'
				}
				,tbar: ['->',{
					xtype: 'button'
					,text: 'Cancel'
					// ,handler: this.onBackToContainer
					// ,scope: this
				},'-',{
					xtype: 'button'
					,text: 'Save Changes'
					,handler: this.onSaveResource
					,scope: this
				}]
			},{
				xtype: 'myjournal-panel-resource'		
				,id: 'modx-panel-resource'	
			}]            			
		}]
    });
    MyJournal.panel.superclass.constructor.call(this,config);
	// this.on('render', this.setup, this);
};
Ext.extend(MyJournal.panel,MODx.Panel,{
	setup: function(){}

	,toggleRTE: function(){
		// jQuery("#content").markItUp(mySettings);
	}
	
	,onSaveResource: function(){
		var form = Ext.getCmp('modx-panel-resource').getForm();
		if(form.isValid()){
			form.submit({
				url: MODx.config.connectors_url+'resource/index.php'
				,method: 'POST'
				,params: { action: 'create' }
				,waitMsg: 'Saving, please wait...'
				,success: function(f, action){					
					this.disable();
					location.href = '?a='+MODx.action['resource/update']+'&id='+action.result.object.id;
				}
				,failure: function(form, action){
					/* Push it above the form */
					// console.log(action.result.message)
				}
				,scope: this
			});
		}	
	}
});
Ext.reg('myjournal-main-panel',MyJournal.panel);