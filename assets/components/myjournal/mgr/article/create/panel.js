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
		// this.form = Ext.getCmp('modx-panel-resource');
		// if (MODx.config.use_editor && MODx.loadRTE) {
			// this.rteElements = Ext.get('ta');			
            // if (!this.rteLoaded) {					
                // MODx.loadRTE(this.rteElements);
                // this.rteLoaded = true;
            // } else if (this.rteLoaded) {				
                // if (MODx.unloadRTE){
                    // MODx.unloadRTE('ta');
                // }
                // this.rteLoaded = false;
            // }
        // }
		// tinyMCE.init(Tiny.config);
		// tinyMCE.execCommand('mceAddControl',false,'content');
		jQuery("#content").markItUp(mySettings);
	}
});
Ext.reg('myjournal-main-panel',MyJournal.panel);