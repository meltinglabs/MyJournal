Ext.ns('MyJournal');

/**
 * The panel container for Cliche TV thumbnail
 * @class MyJournal.Browser
 * @extend Ext.Panel
 * @xtype clichethumbnail
 */
MyJournal.Browser = function(config) {
    config = config || {};
    Ext.applyIf(config,{
		layout: 'form'
		,items:[{
			// xtype: 'hidden'
			// ,name: 'tv'+config.tv.id
			// ,id: 'tv'+config.tv.id
		// },{
			xtype : 'modx-template-panel'
			,id: 'clichethumbnail-pw-'+config.id
			,startingMarkup: '<div class="cropped">{text}</div>'
			,startingText: 'default text'
			,markup: '<div class="cropped">'
				+'<img src="{thumbnail}?t={timestamp}" alt="preview alt text" class="thumb_pw"/>'
			+'</div>'
			,listeners:{
				afterrender: this.init
				,scope: this
			}
		}]	
		,win: false
		,tbar : [{
			xtype: 'button'
			,text: 'Seelect a file...'
            ,id: 'manage-thumb-btn-'+config.id
			,handler: this.browse
			,scope: this
		// },{
            // xtype: 'button'
            // ,text: _('clichethumbnail.btn_remove_thumbnail')
            // ,id: 'remove-thumb-btn-'+config.id
            // ,handler: this.onRemoveThumbnail
            // ,hidden: true
            // ,scope: this
        }]
	});
	MyJournal.Browser.superclass.constructor.call(this,config);
}
Ext.extend(MyJournal.Browser, Ext.Panel,{
	init: function(){	
		// if(typeof(this.tv.value) == "string")
			// this.tv.value = Ext.util.JSON.decode(this.tv.value);
		// if(typeof(this.tv.value) == "object" &&  typeof(this.tv.value.thumbnail) !== "undefined"){
            // Ext.getCmp('remove-thumb-btn-'+ this.tv.id).show();
            // Ext.getCmp('manage-thumb-btn-'+ this.tv.id).setText(_('clichethumbnail.btn_replace_thumbnail'));
			// Ext.getCmp('clichethumbnail-pw-'+this.tv.id).updateDetail(this.tv.value);
		// }
	} // eo function init
	
	,browse: function(btn,e){
		if(!this.win){
			this.win = new MyJournal.BrowserWindow({
				id: 'browser-window-'+ this.id
				,uid: this.id
				,listeners:{
					show: function(w){
						//Fix for webkit browsers - @TODO set a maximum height for tiny screen
						w.setHeight('auto');
					}
				}
			});
		}
		this.win.show(btn.id);
		var pos = this.win.getPosition(true);
		this.win.setPosition(pos[0], 35);
	} // eo function showThumbnailManager
	
	,isEmpty: function (obj) {
		for(var prop in obj) {
			if(obj.hasOwnProperty(prop))
			return false;
		}
		return true;
    } // eo function isEmpty
	
	,onRemoveThumbnail: function(btn, e){
		// Ext.getCmp('manage-thumb-btn-'+ this.tv.id).setText(_('clichethumbnail.btn_browse'));
		// this.reset();
		// btn.hide();
		// var tv = Ext.select('#tv'+this.tv.id);
		// tv.elements[0].value = Ext.encode({});
		// if(typeof Ext.getCmp('modx-panel-resource').markDirty == "function"){
			// Ext.getCmp('modx-panel-resource').markDirty();
		// }  
	} // eo function onRemoveThumbnail
	
	,onUpdateThumbnailPreview: function(image){
		// Ext.getCmp('clichethumbnail-pw-'+this.tv.id).updateDetail(image);
		// var btn = Ext.getCmp('remove-thumb-btn-'+ this.tv.id);
		// if(btn.hidden){
			// btn.show();
			// Ext.getCmp('manage-thumb-btn-'+ this.tv.id).setText(_('clichethumbnail.btn_replace_thumbnail'));
		// }
	} // eo function onUpdateThumbnailPreview
});	
Ext.reg('myjournal-browser',MyJournal.Browser);