Ext.ns('MyJournal');

/**
 * A vertical tabpanel with custom settings
 * 
 * @class MyJournal.VerticalTabs
 * @extends MODx.VerticalTabs
 * @param {Object} config An object of options.
 * @xtype myjournal-vtabs
 */
MyJournal.VerticalTabs = function(config) {
	config = config || {};		
	Ext.applyIf(config,{
		cls: 'vertical-tabs-panel wrapped'
		,monitorResize: true
		,border: false
		,defaults: {
			bodyCssClass: 'vertical-tabs-body'
            ,autoScroll: true
            ,autoHeight: true
            ,border: false
			,layout: 'form'
		}
		,listeners:{	//Dirty fix
			tabchange: function(tb, pnl){ 				
				this.fixPanelWidth();	
			}
			,resize: function(){
				var pnl = this.getActiveTab();
				if(pnl != null){ this.fixPanelWidth(); }	
			}
			,scope: this
		}		
	});
	MyJournal.VerticalTabs.superclass.constructor.call(this,config);
};
Ext.extend(MyJournal.VerticalTabs, MODx.VerticalTabs,{
	// Prevent panel body and childern elment to overflow from the panel width
	fixPanelWidth: function(){	
		var pnl = this;
		var w = this.bwrap.getWidth();
		pnl.body.setWidth(w);
		pnl.doLayout();
	}
});
Ext.reg('myjournal-vtabs', MyJournal.VerticalTabs);

/**
 * A radio panel
 * 
 * @class MyJournal.RadioPanel
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype myjournal-radios
 */
MyJournal.RadioPanel = function(config) {
	config = config || {};	
	Ext.applyIf(config,{
		layout: 'form'
		,defaultType: 'radiogroup'
		,unstyled: true
		,items: []
		,cls: 'radios-and-checkboxes'
	});
	MyJournal.RadioPanel.superclass.constructor.call(this,config);
	this.on('afterrender', this.init, this);
};
Ext.extend(MyJournal.RadioPanel, Ext.Panel,{
	init: function(){	
		var items = [];
		Ext.iterate(this.tv.items, function(label, value){
			var item = { boxLabel: label, inputValue: value };
			if(this.tv.value == value){ item.checked = true }
			items.push(item);
		}, this);	
		var fieldLabel = this.tv.caption;
		if(this.tv.description != ""){
			fieldLabel += '<span class="desc-under">'+ this.tv.description +'</span>';
			var cls = 'with-desc';
		}
		var allowBlank = this.tv.input_properties.allowBlank === "true" ? true : false;
		this.add({
            fieldLabel: fieldLabel
			,cls: cls || ''
			,columns: 1
			,allowBlank: allowBlank
			,defaults: { name: 'tv'+this.tv.id, hideLabel: true }
            ,items: [ items ]
		});
	}
});
Ext.reg('myjournal-radios', MyJournal.RadioPanel);

/**
 * A radio panel
 * 
 * @class MyJournal.CheckboxPanel
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype myjournal-radios
 */
MyJournal.CheckboxPanel = function(config) {
	config = config || {};	
	Ext.applyIf(config,{
		layout: 'form'
		,defaultType: 'checkboxgroup'
		,unstyled: true
		,items: []
		,cls: 'radios-and-checkboxes'
	});
	MyJournal.CheckboxPanel.superclass.constructor.call(this,config);
	this.on('afterrender', this.init, this);
};
Ext.extend(MyJournal.CheckboxPanel, Ext.Panel,{
	init: function(){	
		var items = [];
		Ext.iterate(this.tv.items, function(label, value){
			var item = { boxLabel: label, inputValue: value };
			if(this.inArray(value, this.tv.value)){ item.checked = true }
			items.push(item);			
		}, this);
		var fieldLabel = this.tv.caption;
		if(this.tv.description != ""){
			fieldLabel += '<span class="desc-under">'+ this.tv.description +'</span>';
			var cls = 'with-desc';
		}
		var allowBlank = this.tv.input_properties.allowBlank === "true" ? true : false;
		this.add({
            fieldLabel: fieldLabel
			,cls: cls || ''
			,columns: 1
			,allowBlank: allowBlank
			,defaults: { name: 'tv'+this.tv.id+'[]', hideLabel: true }
            ,items: [ items ]
		});
	}
	
	//Uses to set the "checked" state of the chekboxes
	,inArray: function(needle, haystack) {
		var length = haystack.length;
		for(var i = 0; i < length; i++) {
			if(haystack[i] == needle) return true;
		}
		return false;
	}
});
Ext.reg('myjournal-checkboxes', MyJournal.CheckboxPanel);