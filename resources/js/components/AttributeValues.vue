<template>
    <div class="">
        <div class="tile">
            <h3 class="tile-title">Option Values</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Value</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="value in values">
                                <td style="width: 25%" class="text-center">{{ value.id}}</td>
                                <td style="width: 25%" class="text-center">{{ value.value}}</td>
                                <td style="width: 25%" class="text-center">{{ value.price}}</td>
                                <td style="width: 25%" class="text-center">
                                    <button class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "attribute-values",
        props: ['attributeid'],
        data() {
            return {
                values: [],
                value: '',
                price: '',
                currentId: '',
                addValue: true,
                key: 0,
            }
        },
        created: function() {
            this.loadvalues();
        },
        methods: {
            loadvalues() {
                let attributeId = this.attributeid;
                let _this = this;
                axios.post('/admin/attributes/get-values', {
                    id: attributeId
                }).then (function(response) {
                    _this.values = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
    }
</script>
