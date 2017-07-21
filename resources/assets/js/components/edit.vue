<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2>Custom Rules</h2></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="experiment_id">Experiment ID</label>
                            <input type="text" id="experiment_id" placeholder="Experiment name" v-model="exp_name" name="exp_name" value="">
                        </div>
                    </div>
                    <div class="row rules">
                        <div class="col-md-12">
                            <label>Rules</label>
                        </div>
                        <div class="col-md-3">
                            <v-select v-model="custom_rules.variable" label="value" :options="rule_variable_type" ></v-select>
                        </div>
                        <div class="col-md-3">
                            <v-select v-model="custom_rules.operator" label="value"  :options="rule_operator_type"></v-select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" v-model="custom_rules.value" placeholder="Enter value">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Options</label>
                        </div>
                    </div>

                    <ul>
                        <li v-for="(option, index) in custom_options" class="row option_item">
                            <div class="col-md-3">
                                <input type="text" v-model="option.css_selector" placeholder="Enter CSS Selector Value"  value="" required>
                            </div>
                            <div class="col-md-3">
                                <v-select v-model="option.type" label="value"  :options="option_type"></v-select>
                            </div>
                            <div class="col-md-3">
                                <div v-if="option.type.label === 'image'">
                                    <vue-core-image-upload
                                      :class="['btn', 'btn-primary']"
                                      @imageuploaded="imageuploaded"
                                      :max-file-size="5242880"
                                      url="/rule/fileupload"
                                      :data="{id:index}"
                                      >
                                    </vue-core-image-upload>
                                    <span v-if="option.value"><img v-bind:src="'../../images/'+option.value" width="50" height="50"></span>
                                </div>
                                <input v-if="option.type.label === 'text'" type="text" v-model="option.value" placeholder="Enter Value"  value="">
                            </div>
                            <div class="col-md-3">
                                <button v-on:click="removeOption(index)" type="button">-</button>
                            </div>
                        </li>
                    </ul>

                    <div class="row">
                        <div class="col-md-9">
                            <button v-on:click="addOption" class="pull-right" type="button" >New Options</button>
                        </div>
                    </div>
                    <div class="" style="margin-top:20px">
                        <!-- Indicates a successful or positive action -->
                        <input type="hidden" v-model="param" name="custom_options" value="">
                        <button v-on:click="storeRules" type="submit" class="btn btn-primary">Save</button>
                        <a href="/rule"><button type="button" class="btn btn-default">Back</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueCoreImageUpload from 'vue-core-image-upload'
    import vSelect from "vue-select"

    export default {
        props: ['experimentData'],
        components:{
            'vue-core-image-upload': VueCoreImageUpload,
            'v-select': vSelect
        },
        data() {
            return {
                exp_id: null,
                exp_name : '',
                rule_variable_type :[{label:'pagepath',value :'Page Path'},{label:'domain',value :'Domain Url'}],
                rule_operator_type :[{label:'contain',value :'Contains'},{label:'equalto',value :'Equals to'},{label:'not_contain',value :'Not Contains'},{label:'not_equalto',value :'Not Equals to'}],
                option_type :[{label:'text',value :'Text'},{label:'image',value :'Image'}],
                custom_rules : {},
                custom_options: [],
                param: '',
                src: ''
            }
        },
        mounted() {
            this.getVariables()
        },
        methods: {
            addOption: function(event){
                this.custom_options.push({
                    css_selector: '',
                    type: this.option_type[0],
                    value: '',
                    org_filename: ''
                })
            },
            removeOption: function(id){
                if(confirm("Do you really want to remove this option?"))
                {
                    this.custom_options.splice(id,1)
                }
            },
            getVariables: function(){
                let exp_arr = JSON.parse(this.experimentData);
                let that  = this;
                this.exp_id = exp_arr.id;
                this.exp_name = exp_arr.name;

                this.custom_rule_indexes = {}
                this.custom_rule_indexes['variable'] =  this.rule_variable_type.findIndex(x => x.label== exp_arr.rules.variable);
                this.custom_rule_indexes['operator'] =  this.rule_operator_type.findIndex(x => x.label== exp_arr.rules.operator);

                this.custom_rules = {
                    variable: this.rule_variable_type[this.custom_rule_indexes['variable']],
                    operator: this.rule_operator_type[this.custom_rule_indexes['operator']],
                    value: exp_arr.rules.value
                }


                exp_arr.options.map(function(item){
                    that.custom_options.push({
                        css_selector    : item.css_selector,
                        type            : that.option_type[that.option_type.findIndex(x => x.label== item.type)],
                        value           : item.value,
                        org_filename    : item.org_filename
                    })
                })
            },
            imageuploaded: function(res) {
              if (res.errors == false) {
                  let data = res.data;
                  this.custom_options[data.id].value = data.filename
                  this.custom_options[data.id].org_filename = data.org_filename
              }
            },
            storeRules: function(e){
                let rule_param = {
                    variable    : this.custom_rules.variable['label'],
                    operator    : this.custom_rules.operator['label'],
                    value       : this.custom_rules.value
                }

                let option_param = this.custom_options.map((item)=>{
                    return {
                        css_selector    : item.css_selector,
                        type            : item.type['label'],
                        value           : item.value,
                        org_filename    : item.org_filename
                    }
                })

                this.param = JSON.stringify({
                    exp_id: this.exp_id,
                    rules: JSON.stringify(rule_param),
                    options: JSON.stringify(option_param)
                })
            }
        }
    }
</script>

<style lang="less" scoped>
ul{
    li{
        list-style: none;
        margin-bottom: 10px
    }
    .option_item{
        input[type='file']{
            display: none;
        }
    }
    margin:0px;
    padding:0px;
}
</style>
