/**
 * @class MyJournal.BrowserWindow
 * @extends Ext.Window
 * @param {Object} config An object of configuration parameters
 * @xtype clichethumbnail-manager
 */
MyJournal.BrowserWindow = function(config) {
    config = config || {};	
    Ext.applyIf(config,{ 
		width: 1000
		,cls: 'myjournal-window-browser'
		,layout: 'form'
		,data: null
		,autoHeight: true
		,defaults: {
			authoHeight: true
		}
		,closeAction: 'hide'
		,items: [{
			layout: 'column'
			,id: 'browser-container-'+ config.uid
			,items:[]
		}]
	});
    MyJournal.BrowserWindow.superclass.constructor.call(this,config);
	this._loadView();
	this._init();
};
Ext.extend(MyJournal.BrowserWindow,Ext.Window,{
	_init: function(){
		Ext.getCmp('browser-container-'+ this.uid).add({
			xtype: 'modx-tree-directory'
			,width: 250
			,onUpload: function() { this.view.run(); }
			,scope: this
			,source: this.source || MODx.config.default_media_source
			,hideFiles: this.hideFiles || false
			,openTo: this.openTo || ''
			,ident: this.ident
			,rootId: '/'
			,rootName: _('files')
			,rootVisible: true
			,id: this.ident+'-tree'
			,listeners: {
				afterUpload: this.view.run
				,changeSource: function(s) {
					this.source = s;
					this.view.source = s;
					this.view.baseParams.source = s;
					this.view.run();
				}
				,scope:this
			}
		},{
			items: this.view
			,border: false
			,bbar: new Ext.PagingToolbar({
				pageSize: 12
				,store: this.view.store
				,displayInfo: true
				,autoLoad: true
			})
			,columnWidth: 1
		},{
			xtype: 'modx-template-panel'
			,id: 'browser-detail-'+this.uid
			,cls: 'aside-details'
			,width: 230
			,startingText: _('cliche.album_empty_col_msg')
			,markup: this._detailTpl()
		});
	}
	
	,_loadView: function(){
		this.ident = 'browser-ident-'+this.uid;
		this.view = MODx.load({
			xtype: 'myjournal-browser-view'
			id: 'myjournal-browser-view-'+this.uid
			,columnWidth: 1
			,onSelect: this.onSelect
			,source: this.source || MODx.config.default_media_source
			,allowedFileTypes: this.allowedFileTypes || ''
			,wctx: this.wctx || 'web'
			,openTo: this.openTo || ''
			,ident: this.ident
			,id: this.ident+'-view'
			,uid: this.uid
		});
	}
	
	,_detailTpl: function(){
		return '<div class="details">'
			+'<tpl for=".">'
				+'<div class="selected">'
					+'<a href="{image}" title="Album {name} preview" alt="'+_('cliche.album_item_cover_alt_msg')+'" class="lightbox" />'
						+'<img src="{image}" alt="{name}" />'
					+'</a>'
					+'<h5>{name}</h5>'
					+'<ul class="splitbuttons">'
						+'<li class="inline-button edit"><button ext:qtip="'+_('cliche.btn_edit_image')+'" ext:trackMouse=true ext:anchorToTarget=false" onclick="Ext.getCmp(\'cliche-album-default\').editImage(\'{id}\'); return false;">'+_('cliche.btn_edit_image')+'</button></li>'
						+'<tpl if="!is_cover">'								
							+'<li class="inline-button set-as-cover"><button ext:qtip="'+_('cliche.btn_set_as_album_cover')+'" ext:trackMouse=true ext:anchorToTarget=false" onclick="Ext.getCmp(\'cliche-album-default\').setAsCover(\'{id}\'); return false;">'+_('cliche.btn_set_as_album_cover')+'</button></li>'
						+'</tpl>'
						+'<li class="inline-button delete"><button ext:qtip="'+_('cliche.btn_delete_image')+'" ext:trackMouse=true ext:anchorToTarget=false" onclick="Ext.getCmp(\'cliche-album-default\').deleteImage(\'{id}\'); return false;">'+_('cliche.btn_delete_image')+'</button></li>'
					+'</ul>'
				+'</div>'
				+'<div class="description">'
					+'<h4>'+_('cliche.album_item_desc_title')+'</h4>'
					+'{description:defaultValue("'+_('cliche.no_desc')+'")}'						
				+'</div>'
				+'<div class="infos">'
					+'<h4>'+_('cliche.album_item_informations_title')+'</h4>'
					+'<ul>'
						+'<li>'
							+'<span class="infoname">'+_('cliche.album_item_id')+':</span>'
							+'<span class="infovalue">#{id}</span>'
						+'</li>'
						+'<li>'
							+'<span class="infoname">'+_('cliche.album_item_created_by')+':</span>'
							+'<span class="infovalue">{createdby}</span>'
						+'</li>'
						+'<li>'
							+'<span class="infoname">'+_('cliche.album_item_created_on')+':</span>'
							+'<span class="infovalue">{createdon}</span>'
						+'</li>'
					+'</ul>'
				+'</div>'
			+'</tpl>'
		+'</div>';
	}
});
Ext.reg("clichethumbnail-manager", MyJournal.BrowserWindow);

/**
 * The view panel base class for a single album
 *
 * @class MyJournal.BrowserView
 * @extends MODx.DataView
 * @param {Object} config An object of options.
 * @xtype cliche-album-view
 */
MyJournal.BrowserView = function(config) {
    config = config || {};
    this._initTemplates();
    Ext.applyIf(config,{
        url: MODx.ClicheConnectorUrl
        ,fields: [
            'name','cls','url','relativeUrl','fullRelativeUrl','image','image_width','image_height','thumb','thumb_width','thumb_height','pathname','ext','disabled'
            ,{name:'size', type: 'float'}
            ,{name:'lastmod', type:'date', dateFormat:'timestamp'}
            ,'menu'
        ]
        ,baseParams: { 
            action: 'getFiles'
            ,prependPath: config.prependPath || null
            ,prependUrl: config.prependUrl || null
            ,source: config.source || 1
            ,allowedFileTypes: config.allowedFileTypes || ''
            ,wctx: config.wctx || 'web'
            ,dir: config.openTo || ''
        }
        ,tpl: this.templates.thumb
        ,prepareData: this.formatData.createDelegate(this)
		,overClass:'x-view-over'
		,selectedClass:'selected'
		,itemSelector: 'div.thumb-wrapper'
		,loadingText : '<div class="empty-msg"><h4>Loading</h4></div>'
		,emptyText : '<div class="empty-msg"><h4>Nothing to see there...</h4></div>'
    });
    MyJournal.BrowserView.superclass.constructor.call(this,config);
    this.on('selectionchange',this.showDetails,this,{buffer: 100});
    this.on('dblclick',this.onSelect || Ext.emptyFn,this,{buffer: 100});
};
Ext.extend(MyJournal.BrowserView,MODx.DataView,{
    templates: {}	
	,run: function(p) {
        p = p || {};
        if (p.dir) { this.dir = p.dir; }
        Ext.applyIf(p,{
            action: 'getFiles'
            ,dir: this.dir
            ,source: this.config.source || MODx.config.default_media_source
        });
        this.store.load({
            params: p
            ,callback: function(rec, options, success){
				setTimeout(function(){
					Ext.getCmp('modx-content').doLayout();
				}, 500);
			}
            ,scope: this
        });
    }
	
	,removeFile: function(item,e) {
        var node = this.cm.activeNode;
        var data = this.lookup[node.id];
        var d = '';
        if (typeof(this.dir) != 'object') { d = this.dir; }
        MODx.msg.confirm({
            text: _('file_remove_confirm')
            ,url: MODx.config.connectors_url+'browser/file.php'
            ,params: {
                action: 'remove'
                ,file: d+'/'+node.id
                ,source: this.config.source
                ,wctx: this.config.wctx || 'web'
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.run({ ctx: MODx.ctx });
                },scope:this}
            }
        });
    }

    ,showDetails : function(){
        var selNode = this.getSelectedNodes();
        if(selNode && selNode.length > 0){
            selNode = selNode[0];
            var data = this.lookup[selNode.id];
            if (data) { Ext.getCmp('browser-detail-'+this.uid).updateDetail(data); }
        }
		// else{
            // Ext.getCmp(this.config.ident+'-ok-btn').disable();
            // detailEl.update('');
        // }
    }

	,formatData: function(data) {
        var formatSize = function(size){
            if(size < 1024) {
                return size + " bytes";
            } else {
                return (Math.round(((size*10) / 1024))/10) + " KB";
            }
        };
        data.shortName = Ext.util.Format.ellipsis(data.name,18);
        data.sizeString = data.size != 0 ? formatSize(data.size) : 0;
        data.dateString = !Ext.isEmpty(data.lastmod) ? new Date(data.lastmod).format("m/d/Y g:i a") : 0;
        this.lookup[data.name] = data;
        return data;
    }

    ,_initTemplates: function() {
		this.templates.thumb = new Ext.XTemplate('<tpl for=".">'
			+'<div class="thumb-wrapper">'
				+'<div class="thumb">'
					+'<tpl if="thumb">'
						+'<img src="{thumb}" title="{name}" alt="{name}" />'
					+'</tpl>'
					+'<tpl if="!thumb">'
						+'<span class="no-preview error"><strong>Error</strong>Image not found</span>'
					+'</tpl>'
					+'<span class="img-loading-mask">&nbsp;</span>'
				+'</div>'
				 ,'<tpl if="this.isEmpty(sizeString) == false">'
                    ,'<b>'+_('file_size')+':</b>'
                    ,'<span>{sizeString}</span>'
                ,'</tpl>'
                ,'<tpl if="this.isEmpty(dateString) == false">'
                    ,'<b>'+_('last_modified')+':</b>'
                    ,'<span>{dateString}</span></div>'
                ,'</tpl>'
			+'</div>'
		+'</tpl>'
		+'<div class="clear"></div>', {
			compiled: true
		});
    }
});
Ext.reg('cliche-album-view',MyJournal.BrowserView);