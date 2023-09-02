<template>
    <div v-if="can('calendar_create')">

        <v-row>
            <v-col md="8">
                <v-tabs v-model="activeTab" :vertical="vertical" background-color="primary" dark show-arrows>
                    <div class="py-3" style="background-color: #1259a7">
                        <span class="mx-2">Party Hall booking on 2023-09-01</span>
                    </div>
                    <v-col cols="6" class="mx-2 text-center">
                        <h2>Customer </h2>
                    </v-col>

                    <v-spacer></v-spacer>


                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-account-tie </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-party-popper </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-chef-hat </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-cash-multiple </v-icon>
                    </v-tab>
                    <v-icon dark class="pa-2">
                        mdi mdi-close-box
                    </v-icon>
                    <v-tabs-slider color="#1259a7"></v-tabs-slider>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallCustomer />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>

                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallEvent />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallFood />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallAmount />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                </v-tabs>
            </v-col>

            <v-col md="4">
                <v-tabs color="primary" :vertical="vertical" background-color="primary" dark show-arrows>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-list-box-outline </v-icon>
                    </v-tab>
                    <v-tabs-slider color="#1259a7"></v-tabs-slider>
                    <v-tab-item>
                        <v-card flat>
                            <v-divider class="px-5 py-0"></v-divider>

                            <section class="payment-section pt-0 mt-1">

                                <div class="input-group input-group-sm px-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        Total
                                    </span>
                                    <div type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        1111111
                                    </div>
                                </div>
                                <div class="input-group input-group-sm px-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        Advance Payment
                                    </span>
                                    <div type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        11111111111
                                    </div>
                                </div>
                                <div class="input-group input-group-sm px-5 mb-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <strong>Balance Amount</strong>
                                    </span>
                                    <div type="text" class="form-control red--text" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        <strong>11111111111</strong>
                                    </div>
                                </div>
                                <div class="input-group input-group-sm px-3 mb-5">
                                    <v-btn style="background-color: #4390fc; margin-right: 5px" width="50%" height="40"
                                        dark>
                                        Pay
                                    </v-btn>
                                    <v-btn style="background-color: #5fafa3" width="50%" height="40" @click="store"
                                        :loading="subLoad" dark>Book</v-btn>
                                </div>
                            </section>
                        </v-card>
                    </v-tab-item>
                </v-tabs>

            </v-col>






        </v-row>

    </div>
    <NoAccess v-else />
</template>
<script>
import HallCustomer from "../../components/partyhall/hallcustomer.vue";
import HallEvent from "../../components/partyhall/HallEvent.vue";
import HallFood from "../../components/partyhall/HallFood.vue";
import HallAmount from "../../components/partyhall/HallAmount.vue";

export default {
    components: {
        HallCustomer,
        HallEvent,
        HallFood,
        HallAmount,

    },
    data() {
        return {
            subLoad: false,
            valid: true,
            name: '',
            nameRules: [
                v => !!v || 'Name is required',
                v => (v && v.length <= 10) || 'Name must be less than 10 characters',
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
            this.$refs.form.validate()
        },
        reset() {
            this.$refs.form.reset()
        },
        resetValidation() {
            this.$refs.form.resetValidation()
        },
        store() {

        },
    },
};
</script>

