<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1>Testing</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-9 border shadow-lg" style="position:relative; height: 600px; padding:0;">


                <template v-for="item in list" :key="item.id">
                    <div :style="{width:item.width+'px',height:item.height+'px',left:item.x+'px',top:item.y+'px'}" class="canvas-item" @click="select(item)" role="button">{{ item.label }}</div>
                </template>


            </div>

            <div class="col-3">
                <div class="mb-3">
                    <label for="label" class="form-label">Label</label>
                    <input type="text" class="form-control" id="label" placeholder="Label" v-model="selected.label">
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="width" class="form-label">Width</label>
                        <input type="number" class="form-control" id="width" placeholder="width" v-model="selected.width">
                    </div>
                    <div class="col-6">
                        <label for="height" class="form-label">Height</label>
                        <input type="number" class="form-control" id="height" placeholder="height"  v-model="selected.height">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="x" class="form-label">X</label>
                        <input type="number" class="form-control" id="x" placeholder="x"  v-model="selected.x">
                    </div>
                    <div class="col-6">
                        <label for="y" class="form-label">Y</label>
                        <input type="number" class="form-control" id="y" placeholder="y"  v-model="selected.y">
                    </div>
                </div>

                <div class="mt-4">
                     <div v-if="selected.id">
                       <button class="btn btn-info w-100">Save</button>
                         <button class="btn btn-link w-100" @click="reset()">Cancel</button>
                    </div>
                    <div v-else>
                         <button class="btn btn-primary w-100" @click="addItem()">Add New</button>
                    </div>
                </div>

                <table class="table table-bordered mt-4 table-hover">
                <thead>
                <tr class="table-dark">
                    <th>Label</th>
                    <th>w</th>
                    <th>h</th>
                    <th>x</th>
                    <th>y</th>
                </tr>
                </thead>
                    <tbody>
                    <tr v-for="item in list" :key="item.id" class="bg-white" role="button" @click="select(item)">
                        <td>{{ item.label }}</td>
                        <td>{{ item.width }}</td>
                        <td>{{ item.height }}</td>
                        <td>{{ item.x }}</td>
                        <td>{{ item.y }}</td>
                    </tr>
                    </tbody>
            </table>

            </div>




        </div>


    </div>

</template>
<script>
import {CanvasItem as CanvasItemInterface} from "@/store/testing";
export default {
    name: "testing",
    mounted() {
        console.log("mounted");

        this.$store.dispatch("testing/addItem",{
            id:"1",
            label:"Testing 1",
            width: 300,
            height:100,
            x: 400,
            y: 200
        });

        this.reset();
    },
    data: () => ({
        selected: {}
    }),

    computed: {
        list() {
            const state_list = this.$store.state.testing.list;
            console.log("state list",state_list)
            return state_list;
        },

    },
    methods: {
        addItem(){
            if (this.selected.id==""){
                this.selected.id = new Date();
            }
            this.$store.dispatch("testing/addItem",this.selected)
        },
        select(item){
            console.log("selected",item)
            this.selected = item;

        },
        remove(item){
            console.log(item)

        },
        reset() {
            console.log("resetting the form")
            this.selected = {
                id: "",
                label: "new item",
                width: 200,
                height: 100,
                x: 0,
                y: 0
            };
        }
    }


};
</script>
<style lang="scss">
.canvas-item {
    border: 1px solid #000000;
    background: rgba(0,0,250,0.2);
    position:absolute;
    z-index: 999;
    &:hover {
        background: rgba(0,0,250,0.4);
    }
}
</style>