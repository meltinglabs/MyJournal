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
        ,listeners:{    //Dirty fix
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
 * An URL special field
 * @class MyJournal.url
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype myjournal-combo
 */
MyJournal.url = function(config) {
    config = config || {};    
    Ext.applyIf(config,{
        layout: 'form'
        ,unstyled: true        
        ,items: [{
            xtype: 'compositefield'
            ,fieldLabel: 'Phone'
            ,msgTarget: 'under'
            ,anchor: '100%'
            ,deferredRender: false
            ,items: [{
                width: 90
                ,xtype: 'combo'
                ,mode: 'local'
                ,triggerAction: 'all'
                ,forceSelection: true
                ,editable: false
                ,fieldLabel: 'Title'
                ,name: 'tv'+config.tv.id+'_prefix'
                ,id: 'tv'+config.tv.id+'_prefix'
                ,displayField: 'name'
                ,valueField: 'value'
                ,store: new Ext.data.Store({
                    data: config.tv
                    ,storeId: 'tv-store-'+config.tv.id
                    ,reader: new Ext.data.JsonReader({
                        idProperty: 'value'
                        ,root: 'prefixes'
                        ,fields: ['name', 'value']
                    })
                })
            },{    
                xtype: 'textfield'
                ,name: 'tv'+config.tv.id
                ,id: 'tv'+config.tv.id
                ,value: config.tv.value || ''
                ,flex : 1
            }]
        }]        
    });
    MyJournal.url.superclass.constructor.call(this,config);
    this.on('afterrender', this.init, this);
};
Ext.extend(MyJournal.url, Ext.Panel,{
    init: function(){
        var cmb = Ext.getCmp('tv'+this.tv.id+'_prefix');
        cmb.setValue(this.tv.prefix);
        if(this.tv.description != ""){
            var idx = this.ownerCt.items.indexOf(this); idx++;
            this.ownerCt.insert(idx,{
                xtype: 'label'
                ,forId: this.tv.id
                ,cls: 'desc-under'
                ,html: this.tv.description
            });
        }
    }
});
Ext.reg('myjournal-url', MyJournal.url);

/**
 * A combo panel
 * 
 * @class MyJournal.Combo
 * @extends Ext.form.ComboBox
 * @param {Object} config An object of options.
 * @xtype myjournal-combo
 */
MyJournal.Combo = function(config) {
    config = config || {};    
    Ext.applyIf(config,{
        mode: 'local'
        ,displayField: 'text'
        ,valueField: 'value'
        ,typeAhead: true
        ,forceSelection: true
        ,triggerAction: 'all'
        ,selectOnFocus: true
        ,store: new Ext.data.Store({
            data: config.tv
            ,storeId: 'tv-store-'+config.tv.id
            ,reader: new Ext.data.JsonReader({
                idProperty: 'value'
                ,root: 'opts'
                ,fields: ['text', 'value', 'selected']
            })
        })
    });
    MyJournal.Combo.superclass.constructor.call(this,config);
    this.on('afterrender', this.init, this);
};
Ext.extend(MyJournal.Combo, Ext.form.ComboBox,{
    init: function(){}
});
Ext.reg('myjournal-combo', MyJournal.Combo);

/**
 * A SuperBoxSelect panel
 * 
 * @class MyJournal.SuperBoxSelect
 * @extends Ext.ux.form.SuperBoxSelect
 * @param {Object} config An object of options.
 * @xtype myjournal-superboxselect
 */
MyJournal.SuperBoxSelect = function(config) {
    config = config || {};    
    Ext.applyIf(config,{
        fieldLabel : config.tv.caption
        ,name: 'tv'+config.tv.id+'[]'
        ,id: 'tv'+config.tv.id
        ,mode: 'local'
        ,displayField: 'text'
        ,valueField: 'value'
        ,extraItemCls: 'x-tag'
        // ,hiddenName: 'tv'+config.tv.id
        ,typeAhead: true
        ,forceSelection: true
        ,triggerAction: 'all'
        ,selectOnFocus: true
        ,resizable: true
        // ,allowAddNewData: true
        // ,renderFieldBtns :false
        ,store: new Ext.data.Store({
            data: config.tv
            ,storeId: 'myTvStore'+config.tv.id
            ,reader: new Ext.data.JsonReader({
                idProperty: 'value'
                ,root: 'opts'
                ,fields: ['text', 'value', 'selected']
            })
        })
    });
    MyJournal.SuperBoxSelect.superclass.constructor.call(this,config);
    this.on('afterrender', this.init, this);
    this.on('newitem', this.onAddItem, this);
};
Ext.extend(MyJournal.SuperBoxSelect, Ext.ux.form.SuperBoxSelect,{
    init: function(p){
        /* Add description label below if necessary */
        if(this.tv.description != ""){
            var idx = this.ownerCt.items.indexOf(this); idx++;
            this.ownerCt.insert(idx,{
                xtype: 'label'
                ,forId: this.id
                ,cls: 'desc-under'
                ,html: this.tv.description
            });
        }
        var selected = this.store.query('selected',true);
        if(selected.keys.length > 1){
            var value = selected.keys.join(',');
            this.setValue(value);
        }        
    }
    ,onAddItem: function(bs,v,f){
        this.addNewItem({"value": v,"text": v});
    } 
});
Ext.reg('myjournal-superboxselect', MyJournal.SuperBoxSelect);

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

/**
 * A radio panel
 * 
 * @class MyJournal.AutoTagPanel
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype myjournal-radios
 */
MyJournal.AutoTagPanel = function(config) {
    config = config || {};    
    Ext.applyIf(config,{
        layout: 'form'
        ,unstyled: true
        ,defaults: { anchor: '100%' }
        ,items: [{
            xtype: 'textfield'
            ,fieldLabel: config.tv.caption
            ,name: 'tv'+config.tv.id
            ,id: 'tv'+config.tv.id || config.tv.default_text || ''
            ,value: config.tv.value
            ,enableKeyEvents: true
            ,listeners: {
                keyup: {
                    fn: this.onKeyUp
                    ,buffer: 500
                }
                ,scope: this
            }
        },{
            xtype : 'modx-template-panel'
            ,deferredRender: false
            ,id: 'tv-taglist-'+config.tv.id
            ,cls: 'tag-list-panel'
            ,startingMarkup: '<div class="taglist">{text}</div>'
            ,startingText: 'Loading...'
            ,markup: '<ul class="taglist">'
                +'<tpl for="list">'
                    +'<tpl if="checked">'
                        +'<li class="{class} checked"><button type="button" class="autotag" value="{value}">{value}</button></li>'
                    +'</tpl>'
                    +'<tpl if="!checked">'
                        +'<li class="{class}"><button type="button" class="autotag" value="{value}">{value}</button></li>'
                    +'</tpl>'
                +'</tpl>'
                +'<tpl for="newItems">'        
                    +'<li class="new-item checked"><button type="button" class="autotag" value="{value}">{value}</button></li>'
                +'</tpl>'
            +'</ul>'
            ,updateDetail: function(data) {        
                this.body.hide();
                this.tpl.overwrite(this.body, data);
                this.body.show();
            }
            ,listeners:{
                afterrender: this.init
                ,scope: this
            }
        }]
        ,cls: 'auto-tag'
    });
    MyJournal.AutoTagPanel.superclass.constructor.call(this,config);
};
Ext.extend(MyJournal.AutoTagPanel, Ext.Panel,{
    init: function(p){    
        /* Add click event listener to taglist element */
        Ext.select('#tv-taglist-'+this.tv.id).on('click', this.onClick, this);
        this.tvField = Ext.getCmp('tv'+this.tv.id);
        this.tv.newItems = [];
        /* Add current existing tags */
        p.updateDetail(this.tv);
        
        /* Add decription label if the key exist and is not empty */
        if(this.tv.description != ""){
            var idx = this.items.indexOf(this.tvField);
            idx++;
            this.insert(idx,{
                xtype: 'label'
                ,forId: this.tvField.id
                ,cls: 'desc-under'
                ,html: this.tv.description
            });
        }
    }
    
    ,onKeyUp: function(f,e) {    
        var stringList = Ext.util.Format.stripTags(f.getValue()),
            list = stringList.split(','),
            length = list.length,
            markActive = [],
            newItems = [];
        
        for(var i = 0; i < length; i++) {
            var item = Ext.util.Format.trim(list[i]);
            if(!this.inArray(item, false)){
                var newItem = {};
                newItem.value = item;
                newItems.push(newItem);
            } else {
                markActive.push(item);
            }
        }
        /* Add new tag items in separate variable */
        this.tv.newItems = [];
        if(newItems.length > 0){
            this.tv.newItems = newItems;
        }
        /* Update tag panel */
        Ext.getCmp('tv-taglist-'+this.tv.id).updateDetail(this.tv);
        
        /* Mark active tag */
        var markActivelength = markActive.length;
        for(var i = 0; i < markActivelength; i++) {
            var item = Ext.util.Format.trim(markActive[i]);
            this.inArray(item, true);
        }
    }
    
    /* Check the state of a tag */
    ,inArray: function(item, markActive) {
        var list = this.tv.list;
        var length = list.length;
        for(var i = 0; i < length; i++) {    
            console.log(list[i].value, item);
            if(list[i].value == item){
                /* Set state if element who has been found */
                var tag = Ext.select('#tv-taglist-'+this.tv.id+' .taglist .item-'+[i]);
                if(markActive && !tag.hasClass('checked')){
                    tag.toggleClass('checked');
                }                
                return true;
            }                        
        }
        return false;
    }
    
    /* Handle tag click */
    ,onClick: function(e, t){
        var elm = t.className;
        if(elm == 'autotag'){
            var el = new Ext.Element(e.getTarget()),
                parent = el.parent(),
                tag = t.value,
                value = this.tvField.getValue();
            list = (value != "") ? value.split(','): [];
            if(parent.toggleClass('checked') && parent.hasClass('checked')){
                this.addValue(list, tag);                
            } else {
                this.removeValue(list, tag);
            }                    
        }
    }
    
    /* Add value from tag click */
    ,addValue: function(list, value){
        if(list.length == 0){
            var newValue = value;
        } else {
            list.push(' '+value);
            var newValue = list.join(',');
        }
        this.tvField.setValue(Ext.util.Format.trim(newValue));
    }
    
    /* Remove value from tag click */
    ,removeValue: function(list, value){
        var length = list.length;
        for(var i = 0; i < length; i++) {
            if(Ext.util.Format.trim(list[i]) == Ext.util.Format.trim(value)){
                list.splice([i],1);
            }                        
        }
        var newValue = list.join(',');            
        this.tvField.setValue(Ext.util.Format.trim(newValue));
    }
});
Ext.reg('myjournal-autotag', MyJournal.AutoTagPanel);