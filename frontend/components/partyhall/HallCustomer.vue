<template>
    <div v-if="can('calendar_create')">
        <v-card flat>
            <v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>


                        <v-col md="4" cols="12">
                            <v-text-field dense label="Contact Person Name" outlined type="text"
                                v-model="customer.contact_person_name" required :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Company Name" outlined type="text" v-model="customer.company_name"
                                required :rules="nameRules"></v-text-field>
                        </v-col>

                        <v-col md="4" cols="12">
                            <v-text-field dense label="Address" outlined type="text" v-model="customer.address" required
                                :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="City" outlined type="text" v-model="customer.city" required
                                :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Phone Number" outlined type="text" v-model="customer.phone_number"
                                required :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Whatsapp Number" outlined type="text"
                                v-model="customer.whatsapp_number" required :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Email" outlined type="text" v-model="customer.email" required
                                :rules="emailRules"></v-text-field>
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

