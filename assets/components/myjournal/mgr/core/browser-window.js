/**
 * @class MyJournal.BrowserWindow
 * @extends Ext.Window
 * @param {Object} config An object of configuration parameters
 * @xtype clichethumbnail-manager
 */
MyJournal.BrowserWindow = function(config) {
    config = config || {};	
    Ext.applyIf(config,{ 
		title: _('modx_browser')+' ('+(MODx.ctx ? MODx.ctx : 'web')+')'
		,width: '90%'
		,minWidth: 500
		,height: 500
		,autoScroll: true
		,cls: 'myjournal-window-browser'
		,layout: 'column'
		,closeAction: 'hide'
		,items: []
		,buttons: [{
			id: 'cancel-btn-' + config.uid
            ,text: _('cancel')
            ,handler: this.hide
            ,scope: this
        }]
        ,keys: {
            key: 27
            ,handler: this.hide
            ,scope: this
        }
	});
    MyJournal.BrowserWindow.superclass.constructor.call(this,config);
	this._loadView();
	this._init();
};
Ext.extend(MyJournal.BrowserWindow,Ext.Window,{
	_init: function(){
		this.add({
			xtype: 'modx-tree-directory'
			,width: 265
			,cls: 'browser-tree'
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
			,tbar: [{
				icon: MODx.config.manager_url+'templates/default/images/restyle/icons/folder.png'
				,cls: 'x-btn-icon'
				,tooltip: {text: _('file_folder_create')}
				,handler: this.createDirectory
				,scope: this
				,hidden: MODx.perm.directory_create ? false : true
			},{
				icon: MODx.config.manager_url+'templates/default/images/restyle/icons/page_white.png'
				,cls: 'x-btn-icon'
				,tooltip: {text: _('file_create')}
				,handler: this.createFile
				,scope: this
				,hidden: MODx.perm.file_create ? false : true
			},{
				icon: MODx.config.manager_url+'templates/default/images/restyle/icons/file_upload.png'
				,cls: 'x-btn-icon'
				,tooltip: {text: _('upload_files')}
				,handler: this.uploadFiles
				,scope: this
				,hidden: MODx.perm.file_upload ? false : true
			},'-',{
				icon: MODx.config.manager_url+'templates/default/images/restyle/icons/file_manager.png'
				,cls: 'x-btn-icon'
				,tooltip: {text: _('modx_browser')}
				,handler: this.loadFileManager
				,scope: this
				,hidden: MODx.perm.file_manager && !MODx.browserOpen ? false : true
			}]
			,listeners: {
				afterUpload: this.view.run
				,beforerender: function(me){
					me.tbarCfg.cls = "browser-tree-tbar";
				}
				,changeSource: function(s) {
					this.source = s;
					this.view.source = s;
					this.view.baseParams.source = s;
					this.view.run();
				}
				,click: function(node){
					this.load(node.id);
				}
				,scope:this
			}
			
			/* Will be moved back into the core ? */
			,addSourceToolbar: function() {
				var t = Ext.get(this.config.id+'-tbar');
				if (!t) { return; }
				var ae = Ext.get(this.config.id+'-sourcebar');
				if (ae) { return; }
				var fbd = t.createChild({tag: 'div' ,cls: 'modx-formpanel' ,autoHeight: true, id: this.config.id+'-sourcebar'});
				var tb = new Ext.Toolbar({
					applyTo: fbd
					,autoHeight: true
				});
				var cb = MODx.load({
					xtype: 'modx-combo-source'
					,ctCls: 'modx-leftbar-second-tb'
					,value: MODx.config.default_media_source
					,width: 248
					,hideLabel: true
					,listeners: {
						'select':{fn:this.changeSource,scope:this}
					}
				});
				tb.add(cb);
				tb.doLayout();
				this.searchBar = tb;
			}
		},{
			columnWidth: 1
			,autoScroll: true
			,layout: 'column'
			,cls: 'no-margin browser-view-and-details'
			,unstyled: true
			,items:[{
				items: this.view
				,cls: 'browser-main-view'
				,columnWidth: 1
				,border: false
				,tbar: this.getToolbar()
				,tbarCfg: {
					cls: 'boxed-toolbar'
				}
			},{
				xtype: 'modx-template-panel'
				,id: 'browser-detail-'+this.uid
				,cls: 'aside-details no-margin'
				,width: 220
				,startingText: 'Aside text'
				,markup: this._detailTpl()
				,init: function(){
					this.defaultMarkup = new Ext.XTemplate(this.startingMarkup, { compiled: true });
					this.reset();		
					this.tpl = new Ext.XTemplate(this.markup,{ 
						compiled: true 
						,isEmpty: function (v) {
							return (v == '' || v == null || v == undefined || v === 0);
						}
						,hasThumb: function(v){
							return (v != "/buttons/manager/templates/default/images/restyle/nopreview.jpg");
						}
					});
				}
			}]		
		});
	}
	
	,_loadView: function(){
		this.ident = 'browser-ident-'+this.uid;
		this.view = MODx.load({
			xtype: 'myjournal-browser-view'
			,id: 'myjournal-browser-view-'+this.uid			
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
	
	,getToolbar: function() {
        return [
            _('filter')+':'
        ,{
            xtype: 'textfield'
            ,id: this.ident+'filter'
            ,selectOnFocus: true
            ,width: 150
            ,listeners: {
                render: function(){
                    Ext.getCmp(this.ident+'filter').getEl().on('keyup', function(){
                        this.filter();
                    }, this, { buffer:500 });
                }
				,scope:this
            }
        }, ' ', '-', _('sort_by')+':'
        , {
            id: this.ident+'sortSelect'
            ,xtype: 'combo'
            ,typeAhead: true
            ,triggerAction: 'all'
            ,width: 150
            ,editable: false
            ,mode: 'local'
            ,displayField: 'desc'
            ,valueField: 'name'
            ,lazyInit: false
            ,value: 'name'
            ,store: new Ext.data.SimpleStore({
                fields: ['name', 'desc'],
                data : [['name',_('name')],['size',_('file_size')],['lastmod',_('last_modified')]]
            })
            ,listeners: {
                select: this.sortImages
				,scope:this
            }
        }];
    }
	
	,filter : function(){
        var filter = Ext.getCmp(this.ident+'filter');
        this.view.store.filter('name', filter.getValue(),true);
        this.view.select(0);
    }
	
	,sortImages : function(){
        var v = Ext.getCmp(this.ident+'sortSelect').getValue();
        this.view.store.sort(v, v == 'name' ? 'asc' : 'desc');
        this.view.select(0);
    }
	
	,_detailTpl: function(){
		return '<div class="details">'
			+'<tpl for=".">'
				+'<div class="selected">'
					+'<tpl if="this.hasThumb(thumb) == true">'
						+'<a href="{thumb}" title="Album {name} preview" alt="Alt for current image" class="lightbox" />'
							+'<img src="{thumb}" alt="{name}" />'
						+'</a>'
					+'</tpl>'
					+'<h5>{name}</h5>'
					// +'<tpl if="!downloaded">'
						+'<button class="inline-button green">Select This File</button>'
					// +'</tpl>'
				+'</div>'
				+'<div class="description infos">'
					+'<h4>Informations</h4>'
					+'<ul>'
						+'<tpl if="this.isEmpty(sizeString) == false">'
							+'<li>'
								+'<span class="infoname">'+_('file_size')+':</span>'
								+'<span class="infovalue">{sizeString}</span>'
							+'</li>'
						+'</tpl>'
						+'<tpl if="this.isEmpty(dateString) == false">'
							+'<li>'
								+'<span class="infoname">'+_('last_modified')+':</span>'
								+'<span class="infovalue">{dateString}</span>'
							+'</li>'
						+'</tpl>'
					+'</ul>'
				+'</div>'
			+'</tpl>'
		+'</div>';
	}
	
	,load: function(dir){
		dir = dir || (Ext.isEmpty(this.openTo) ? '' : this.openTo);
        this.view.run({
            dir: dir
            ,source: this.source
            ,allowedFileTypes: this.allowedFileTypes || ''
            ,wctx: this.wctx || 'web'
        });
	}
});
Ext.reg("clichethumbnail-manager", MyJournal.BrowserWindow);

/**
 * The view panel base class for a single album
 *
 * @class MyJournal.BrowserView
 * @extends MODx.DataView
 * @param {Object} config An object of options.
 * @xtype myjournal-browser-view
 */
MyJournal.BrowserView = function(config) {
    config = config || {};
    this._initTemplates();
    Ext.applyIf(config,{
		url: MODx.config.connectors_url+'browser/directory.php'
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
		,emptyText : '<div class="empty-msg"><h4>'+_('file_err_filter')+'</h4></div>'
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
            ,source: this.source || MODx.config.default_media_source
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
                ,source: this.source
                ,wctx: this.wctx || 'web'
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
    }

	,formatData: function(data) {
        var formatSize = function(size){
            if(size < 1024) {
                return size + " bytes";
            } else {
                return (Math.round(((size*10) / 1024))/10) + " KB";
            }
        };
        data.shortName = Ext.util.Format.ellipsis(data.name,15);
        data.sizeString = data.size != 0 ? formatSize(data.size) : 0;
        data.dateString = !Ext.isEmpty(data.lastmod) ? new Date(data.lastmod).format("m/d/Y g:i a") : 0;
        this.lookup[data.name] = data;
        return data;
    }

    ,_initTemplates: function() {
		this.templates.thumb = new Ext.XTemplate('<tpl for=".">'
			+'<div class="thumb-wrapper" id="{name}">'
				+'<div class="thumb">'
					+'<tpl if="this.hasThumb(thumb) == true">'
						+'<img src="{thumb}" title="{name}" alt="{name}" />'
					+'</tpl>'
					+'<tpl if="this.hasThumb(thumb) == false">'
						+'<span class="no-preview">No preview available</span>'
					+'</tpl>'
					+'<span class="img-loading-mask">&nbsp;</span>'
				+'</div>'
				+'<span class="name">{shortName}</span>'
			+'</div>'
		+'</tpl>'
		+'<div class="clear"></div>', {
			compiled: true
			,hasThumb: function(v){
				return (v != "/buttons/manager/templates/default/images/restyle/nopreview.jpg");
			}
		});
    }
});
Ext.reg('myjournal-browser-view',MyJournal.BrowserView);