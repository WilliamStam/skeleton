<template>
    <div class="layout-profiler">
        <div>profiler: {{ profiler }}</div>
        <button type="button" class="btn btn-primary" @click="modal.show()">
            profiler
        </button>
        <div class="modal fade" ref="systemProfilerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg ">
                <div class="modal-content bg-transparent border-0 shadow-none">
                    <div class="modal-body p-0 bg-transparent">


                        <template v-for="(profiler,index) in profiler_list" :key="index">
                            <div class="profiler-page bg-white p-1 mb-3 shadow">

                                <table class="table table-sm profiler-table m-0">
                                    <thead>
                                    <tr class="table-dark">
                                        <th colspan="2">
                                            {{ profiler.method }} {{ profiler.url }}
                                        </th>
                                        <th>
                                            Component
                                        </th>
                                        <th class="text-end">
                                            Time (ms)
                                        </th>
                                        <th class="text-end">
                                            Memory (b)
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="item in profiler.items" :key="item.key">
                                        <tr
                                            @click="toggleDataRow(item.key)"
                                            :class="item.data ? 'record': ''"
                                        >
                                            <td style="width: 1rem" class="text-center">
                                                <div v-if="item.data">
                                                    <fa icon="minus-square" v-if="selectedRecord==item.key"></fa>
                                                    <fa icon="plus-square" v-else></fa>
                                                </div>

                                            </td>
                                            <td>
                                                <div class="profiler-bar" :style="{ width:  formatNumber(item.time.percent,3) + '%', left:  formatNumber(item.time.offset,3) + '%' }"></div>
                                                {{ item.label }}
                                            </td>
                                            <td>
                                                {{ item.component }}
                                            </td>
                                            <td class="time">
                                                {{ formatNumber(item.time.total, 2) }}
                                            </td>
                                            <td class="memory">
                                                {{ formatNumber(item.memory.total, 0) }}
                                            </td>
                                        </tr>
                                        <tr v-if="item.data" :data-id="item.key" :class="selectedRecord == item.key?'':'hide'">
                                            <td colspan="5" style="word-wrap: break-word;min-width: 100%;max-width: 160px;">

                                                <div class="overflow-auto mx-1 my-2 shadow-lg" style="border:1px solid #cccccc;">
                                                    <pre style="max-width: 100%; " class="p-3  m-0">{{
                                                            item.data
                                                        }}</pre>
                                                </div>


                                            </td>
                                        </tr>
                                    </template>

                                    </tbody>
                                    <tfoot>
                                    <tr class="table-dark text-end text-bold">
                                        <td colspan="3" class="text-start">
                                            Request {{ profiler.total.request }}ms
                                        </td>
                                        <td>
                                            {{ formatNumber(profiler.total.time, 2) }}
                                        </td>
                                        <td>
                                            {{ formatNumber(profiler.total.memory, 0) }}
                                        </td>
                                    </tr>
                                    </tfoot>


                                </table>


                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>


    </div>

</template>
<style lang="scss">
.profiler-table {
    font-size: xx-small;

    tr {
        position: relative;
        z-index: 1;

        .profiler-bar {
            opacity: 1;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            background: #f1f1f1;
            z-index: -1;
        }

        td {
            //border-right: 1px solid $table-border-color;
        }

        .time {
            text-align: right;
        }

        .memory {
            text-align: right;
        }

        &.record {
            &:hover {
                cursor: pointer;

                td {
                    background: $primary;
                    color: $white;
                }
            }

        }

        &.hide {
            display: none;
        }
    }
}
</style>
<script >
import {
    Modal,
} from "bootstrap";

export default {
    name: "PageProfiler",
    data: () => ({
        modal: null,
        selectedRecord: undefined
    }),
    mounted() {
        this.modal = new Modal(this.$refs.systemProfilerModal);
        console.log("profiler mounted");
    },
    computed: {
        profiler_list() {
            console.log("PROFILER LIST",this.$store.state.profiler.list)
            return this.$store.state.profiler.list;
        }
    },
    methods: {

        formatNumber(value, decimals = 2) {
            let val = (value / 1);
            if (decimals) {
                val = val.toFixed(decimals);
            }
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        },
        toggleDataRow(id) {
            this.selectedRecord = this.selectedRecord == id ? undefined : id;
        }
    }
};
</script>