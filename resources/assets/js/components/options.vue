<template>
    <div class="row">
        <div class="col-md-12">
            <label>Options</label>
        </div>
        </br>
        <div class="col-md-3">
            <input type="text" v-model="pagepath" placeholder="Enter CSS Selector Value" name="" value="">
        </div>
        <div class="col-md-3">
            <v-select :value.sync="pagerule" label="value"  :options="[{label:'text',value :'Text'},{label:'image',value :'Image'}]"></v-select>
        </div>
        <div class="col-md-3">
            <input type="text" v-model="pagevalue" placeholder="Enter Value" name="" value="">
        </div>
        <div class="col-md-3">
            <button type="button" name="button">-</button>
            <button type="button" name="button">+</button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
              pagepath: 'Page Path',
              pagerule: 'Text',
              pagevalue: 1,
            }
        },
        mounted() {
            console.log('Rules mounted.')
        }
    }
</script>

<style lang="less" scoped>

</style>
