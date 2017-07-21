<template>
    <div class="row rules">
        <div class="col-md-12">
            <label>Rules</label>
        </div>
        <div class="col-md-3">
            <v-select :value.sync="variable" label="value" :options="[{label:'pagepath',value :'Page Path'},{label:'domain',value :'Domain Url'}]" name='test'></v-select>
        </div>
        <div class="col-md-3">
            <v-select :value.sync="operator" label="value"  :options="[{label:'equalto',value :'Contains'},{label:'equalto',value :'Equals to'}]"></v-select>
        </div>
        <div class="col-md-3">
            <input type="text" v-model="value" placeholder="Experiment IDs" name="test1" value="">
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
              variable: 'Page Path',
              operator: 'Contains',
              value: '/'
            }
        },
        mounted() {
            console.log('Rules mounted.')
        }
    }
</script>

<style lang="less" scoped>

</style>
