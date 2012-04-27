Ext.ns('MyJournal','MyJournal.Form','MyJournal.Field');

/**
 * A base form panel for myjournal implementation
 * 
 * @class MyJournal.Form.Abstract
 * @extends Ext.form.FormPanel
 * @param {Object} config An object of options.
 * @xtype myjournal-form-abstract
 */
MyJournal.Form.Abstract = function(config) {
    config = config || {};        
    Ext.applyIf(config,{
        unstyled: true
        ,id: 'modx-panel-resource'
        ,defaults: { unstyled: true }
        ,labelAlign: 'top'
        ,keys: [{
            key: MODx.config.keymap_save || 's'
            ,ctrl: true
            ,scope: this
            ,fn: this.save
        }]
    });
    MyJournal.Form.Abstract.superclass.constructor.call(this,config);
    this.on('afterrender', this._init, this);
};
Ext.extend(MyJournal.Form.Abstract, Ext.form.FormPanel,{
    _init: function(){
        this.form = Ext.getCmp('modx-panel-resource').getForm();
    }     
    
    ,save: function(btn, e){        
        if(this.form.isValid()){   
            this.form.submit({
                url: MODx.config.connectors_url+'resource/index.php'
                ,method: 'POST'
                ,params: { action: this.saveAction }
                // ,waitMsg: 'Saving, please wait...'
                ,success: this.onSaveSuccess
                ,failure: this.onSaveFailure
                ,scope: this
            });           
        }        
    }
    
    ,onSaveSuccess: function(form, action){}    
    ,onSaveFailure: function(form, action){
         /* @TODO Show error message */
        var response = action.result;
        var errors = response.data;
        for(var key in errors){
            if (errors.hasOwnProperty(key)) {
                fld = errors[key];
                var formField = form.findField(fld.id);
                if(formField){ formField.markInvalid(fld.msg) }
            }
        }
    }
    
    ,setAlias: function(field){       
        var value = Ext.util.Format.stripTags( field.getValue().toLowerCase() );        
        /* Update title */
        Ext.getCmp('modx-page-header').getEl().update('<h2>'+field.getValue()+'</h2>');
        
        var target = this.form.findField('alias');
        var targetValue = target.getValue();
        /* First implementation is working, needs more work */
        if (targetValue === '') {
            var alias = value.replace(/ /g, '-');
            alias = alias.replace(/[àáâãäå]/g, 'a');            
            alias = alias.replace(/æ/g, 'ae');
            alias = alias.replace(/ç/g, 'c');
            alias = alias.replace(/[èéêë]/g, 'e');
            alias = alias.replace(/[ìíîï]/g, 'i');
            alias = alias.replace(/ñ/g, 'n');
            alias = alias.replace(/[òóôõö]/g, 'o');
            alias = alias.replace(/œ/g, 'oe');
            alias = alias.replace(/[ùúûü]/g, 'u');
            alias = alias.replace(/[ýÿ]/g, 'y');
            alias = alias.replace(/[^a-z-0-9_-]/g,'');           
            target.setValue(alias)
        }
    }
    
    ,onCheckIfRTE: function(me){
        if(me.getValue()){
            jQuery("#content").markItUp(mySettings);
        }
    }
});
Ext.reg('myjournal-form-abstract', MyJournal.Form.Abstract);


/**
 * A base field class showing a radio group
 * 
 * @class MyJournal.Field.CheckboxGroup
 * @extends Ext.form.CheckboxGroup
 * @param {Object} config An object of options.
 * @xtype myjournal-field-checkboxgroup
 */
MyJournal.Field.CheckboxGroup = function(config) {
    config = config || {};
    Ext.applyIf(config,{
         mode: 'local'        
        ,columns: 1
        ,allowBlank: config.allowBlank || true
        ,items: config.data
    });
    MyJournal.Field.CheckboxGroup.superclass.constructor.call(this,config);
};
Ext.extend(MyJournal.Field.CheckboxGroup, Ext.form.CheckboxGroup,{});
Ext.reg('myjournal-field-checkboxgroup', MyJournal.Field.CheckboxGroup);

/**
 * An autotag panel
 * 
 * @class MyJournal.Field.AutoTag
 * @extends Ext.Panel
 * @param {Object} config An object of options.
 * @xtype myjournal-field-autotag
 */
MyJournal.Field.AutoTag = function(config) {
    config = config || {};    
    Ext.applyIf(config,{
        layout: 'form'
        ,unstyled: true
        ,defaults: { anchor: '100%' }
        ,items: []
        ,cls: 'auto-tag'
    });
    MyJournal.Field.AutoTag.superclass.constructor.call(this,config);
    this.on('render', this._init, this);
};
Ext.extend(MyJournal.Field.AutoTag, Ext.Panel,{
    _init: function(p){
        var id = this.data.field_id || Ext.id();
        var tagtpl_id = 'taglist-'+ this.data.field_id || 'taglist-'+ Ext.id()
        this.add({
            xtype: 'textfield'
            ,fieldLabel: this.data.field_label
            ,name: this.data.field_name
            ,id: id
            ,value: this.data.field_value
            ,enableKeyEvents: true
            ,listeners: {
                keyup: {
                    fn: this.onKeyUp
                    ,buffer: 500
                }
                ,scope: this
            }
        });
        this.add({
            xtype : 'modx-template-panel'
            ,deferredRender: false
            ,id: tagtpl_id
            ,cls: 'tag-list-panel'
            ,startingMarkup: '<div class="taglist">{text}</div>'
            ,startingText: 'Loading...'
            ,markup: '<ul class="taglist">'
                +'<tpl for="list">'
                    +'<tpl if="checked">'
                        +'<li class="item-{idx} checked"><button type="button" class="autotag" value="{value}">{value}</button></li>'
                    +'</tpl>'
                    +'<tpl if="!checked">'
                        +'<li class="item-{idx}"><button type="button" class="autotag" value="{value}">{value}</button></li>'
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
                afterrender: this.onPanelRendered
                ,scope: this
            }
        });
        this.fieldID = id;
        this.tagtplID = tagtpl_id;
    }
    
    ,onPanelRendered: function(p){
        this.field = Ext.getCmp(this.fieldID);
        this.tagtpl = Ext.getCmp(this.tagtplID);
        
        /* Add click event listener to taglist element */
        Ext.select('#'+p.id).on('click', this.onClick, this);
        this.data.newItems = [];
        /* Add current existing tags */
        p.updateDetail(this.data);
        
        /* Add decription label if the key exist and is not empty */
        if(this.data.description != ""){
            var idx = this.items.indexOf(this.tagtpl);
            idx++;
            this.insert(idx,{
                xtype: 'label'
                ,forId: this.tagtpl.id
                ,cls: 'desc-under'
                ,html: this.data.description
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
        this.data.newItems = [];
        if(newItems.length > 0){
            this.data.newItems = newItems;
        }
        /* Update tag panel */
        this.tagtpl.updateDetail(this.data);
        
        /* Mark active tag */
        var markActivelength = markActive.length;
        for(var i = 0; i < markActivelength; i++) {
            var item = Ext.util.Format.trim(markActive[i]);
            this.inArray(item, true);
        }
    }
    
    /* Check the state of a tag */
    ,inArray: function(item, markActive) {
        var list = this.data.list;
        var length = list.length;
        for(var i = 0; i < length; i++) {                
            if(list[i].value == item){
                /* Set state if element who has been found */
                if(markActive){
                    Ext.fly(this.tagtpl.id).select('.taglist .item-'+list[i].idx).addClass('checked');     
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
                value = this.field.getValue();
            list = (value != "") ? value.split(','): [];
            if(parent.toggleClass('checked') && parent.hasClass('checked')){
                this.addValue(list, tag);                
            } else {
                this.removeValue(list, tag);
            }                    
        }
    }
    
    /* Add value from field on tag click */
    ,addValue: function(list, value){
        if(list.length == 0){
            var newValue = value;
        } else {
            list.push(' '+value);
            var newValue = list.join(',');
        }
        this.field.setValue(Ext.util.Format.trim(newValue));
    }
    
    /* Remove value from field on tag click */
    ,removeValue: function(list, value){
        var length = list.length;
        for(var i = 0; i < length; i++) {
            if(Ext.util.Format.trim(list[i]) == Ext.util.Format.trim(value)){
                list.splice([i],1);
            }                        
        }
        var newValue = list.join(',');            
        this.field.setValue(Ext.util.Format.trim(newValue));
    }
});
Ext.reg('myjournal-field-autotag', MyJournal.Field.AutoTag);