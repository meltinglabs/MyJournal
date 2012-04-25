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
                ,waitMsg: 'Saving, please wait...'
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