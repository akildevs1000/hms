<template>
    <div v-if="can('calendar_create')">
        <v-card flat>
            <v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>


                    <v-row>
                        <v-col md="3" cols="12"><label>Description</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Unit Cost</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Qty</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            Total
                        </v-col>
                    </v-row>


                    <v-row>
                        <v-col md="3" cols="12"><label>Hall rent</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>5000</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Per 4hrs</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Electricity (EB) Charges</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>5000</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Per 4hrs</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col md="3" cols="12"><label>Sound System with Projector</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>5000</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Per 4hrs</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Setting arrangment & Cleaning charges</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>5000</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Per 4hrs</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Food Total</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>5000</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>Per 4hrs</label>
                        </v-col>
                        <v-col md="3" cols="12">

                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                    </v-row>





                </v-form>

                <v-spacer></v-spacer>
                <v-row>
                    <v-col cols="12" class=" text-right">



                        <v-btn color="primary" fill dense @click="validate">
                            Next
                        </v-btn>
                    </v-col>
                </v-row>







            </v-card-text>
        </v-card>
    </div>
    <NoAccess v-else />
</template>
<script>

export default {

    data() {
        return {
            valid: true,

            nameRules: [
                v => !!v || 'Name is required',

            ],

            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],


            activeTab: 0,
            vertical: false,


            customer: { contact_person_name: '', company_name: '' }

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
        // reset() {
        //     this.$refs.form.reset()
        // },
        // resetValidation() {
        //     this.$refs.form.resetValidation()
        // },
    },
};
</script>

