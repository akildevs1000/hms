<template>
    <div v-if="can('calendar_create')">
        <v-card flat>
            <v-card-text>
                <v-row>

                    <v-col cols="12">
                        <v-form ref="form" v-model="valid" lazy-validation>

                            <v-row v-for="(d, index) in Food.items" :key="index">

                                <v-col cols="3">
                                    <label class="col-form-label">Food Name </label>
                                    <v-text-field dense outlined v-model="d.name" required
                                        :rules="nameRules"></v-text-field>

                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Price Per Item/Person </label>
                                    <v-text-field type="number" dense outlined v-model="d.price_per_item" required
                                        :rules="nameRules" @input="calculateFoodPrice()"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Qty/Pax </label>
                                    <v-text-field type="number" @input="calculateFoodPrice()" dense outlined v-model="d.qty"
                                        required :rules="nameRules"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Total </label>
                                    <v-text-field disabled dense outlined v-model="d.total" style="text-align:right"
                                        class="text-box-right-input bold"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <v-icon size="30" class="error--text mt-7"
                                        @click="removeItem(index)">mdi-delete</v-icon>
                                    <v-icon size="30" v-if="Food.items.length == (index + 1)" fill
                                        class="primary--text mt-7  " @click="addDocumentInfo()">mdi-plus-box</v-icon>


                                </v-col>
                            </v-row>
                            <v-row>


                                <v-col cols="7" class="text-right bold" style="font-weight:bold">
                                    Grand Total


                                </v-col>
                                <v-col cols="2" class="text-right bold">
                                    {{ foodGrandTotal }}
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="text-right" md="10" cols="6">
                                    <!-- <v-btn @click="addDocumentInfo" class="primary mb-2 text-right">
                                        Add +
                                    </v-btn> -->
                                </v-col>
                                <v-col class="text-right" md="2" cols="6">
                                    <v-btn @click="nextTab" class="primary mb-2 text-right">
                                        Next
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-col>






                </v-row>
            </v-card-text>
        </v-card>
    </div>
    <NoAccess v-else />
</template>
<script>

export default {
    props: ["nextTabTrigger"],
    data() {
        return {
            foodGrandTotal: 0,
            Food: {
                items: [{ name: "", price_per_item: "", qty: "", total: "" }],
            },
            document_list: [],
            date_menu: false,
            valid: true,
            name: '',
            nameRules: [
                v => v !== undefined && v !== null && v !== '' ? true : 'Required',


            ],
            email: '',
            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],
            select: null,
            items: [
                'Item 1',
                'Item 2',
                'Item 3',
                'Item 4',
            ],
            checkbox: false,


            activeTab: 0,
            vertical: false,

        };
    },
    watch: {
        nextTabTrigger() {
            let obj = { ...this.Food };
            Object.assign({}, obj)
            obj = JSON.parse(JSON.stringify(obj))

            this.$store.commit("partyHallBookingFood", obj);

        },

    },
    created() {

    },
    methods: {
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },
        validate() {
            return this.$refs.form.validate()
        },
        reset() {
            this.$refs.form.reset()
        },
        resetValidation() {
            this.$refs.form.resetValidation()
        },
        addDocumentInfo() {

            this.Food.items.unshift({
                name: "", price_per_item: "", qty: "", total: ""
            });

            console.log("this.Food.items", this.Food.items);

        },

        close_document_info() {

            this.Food.items = [];

            this.errors = [];

            // this.Document = {
            //   items: [{ title: "", file: "" }],
            // };
        },

        removeItem(index) {


            this.Food.items.splice(index, 1);
        },

        calculateFoodPrice() {

            this.foodGrandTotal = 0;
            this.Food.items.forEach(item => {
                if (parseInt(item.qty) > 0 && parseInt(item.price_per_item)) {
                    item.total = parseInt(item.qty) * parseInt(item.price_per_item);
                    this.foodGrandTotal += item.total;

                }
            });


        },

        nextTab() {


            if (this.validate()) {
                console.log(this.Food);
                let obj = this.Food.items;
                Object.assign({}, obj)
                obj = JSON.parse(JSON.stringify(obj));
                console.log(obj);
                this.$store.commit("partyHallBookingFood", obj);
                this.$emit("c-next-tab", null);

            }

        },
    },
};
</script>

