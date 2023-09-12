<template>
    <div v-if="can('setting_access') && can('setting_access')">
        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" :color="snackbarColor" elevation="24">
                {{ snackbarResponse }}
            </v-snackbar>
        </div>
        <v-dialog v-model="newItemDialog" max-width="20%">
            <v-card>
                <v-card-title dense class=" primary  white--text background">

                    <span>Edit Details </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="newItemDialog = false" outlined dark color="white">
                        mdi mdi-close-circle
                    </v-icon>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>

                            <v-col md="12" cols="12">
                                <label>Whatsapp Instanace ID</label>

                                <v-text-field v-model="whatsapp_instance_id" outlined dense small :hide-details="true"
                                    placeholder="Whatsapp Instanace ID"></v-text-field>
                                <span dense v-if="errors && errors.whatsapp_instance_id" class="error--text">{{
                                    errors.whatsapp_instance_id[0]
                                }}</span>
                            </v-col>



                        </v-row>

                        <v-card-actions class="mt-5" v-if="!viewMode">
                            <v-btn dark filled color="red" @click="newItemDialog = false">Cancel</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn @click="save()" dark filled color="primary">Save</v-btn>
                        </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>


        </v-dialog>
        <v-card class="mb-5" elevation="0">
            <v-row>

                <v-col md="4" cols="12" class="pl-5">
                    <label>Whatsapp Instance Id</label>


                </v-col>

                <v-col md="4" cols="12">
                    <label>: {{ company_payload.whatsapp_instance_id }}</label>
                </v-col>
                <v-col md="4" cols="12">
                    <span @click="editItem()" style="cursor: pointer;">
                        <v-icon color="secondary" small> mdi-pencil </v-icon>
                        Edit

                    </span>

                </v-col>
            </v-row>

        </v-card>
    </div>
    <NoAccess v-else />
</template>
<script>
export default
    {
        data: () => ({
            whatsapp_instance_id: '',
            company_payload: {
                whatsapp_instance_id: "",
            },
            //datatable varables
            page: 1,
            perPage: 0,
            currentPage: 1,
            cumulativeIndex: 1,
            totalTableRowsCount: 0,
            options: {},
            filters: {},
            isFilter: false,
            data: [],
            loading: false,

            roomTypesData: [],


            endpoint: 'tax_slabs',

            newItemDialog: false,

            //add edit item details 
            editedItem: {},
            editedItemIndex: -1,
            roomTypesForSelectOptions: [],
            errors: {},
            snackbar: false,
            snackbarColor: "red",
            snackbarResponse: "",
            viewMode: false,

        }),
        watch: {

            options: {
                handler() {
                    this.getDataFromApi();
                },
                deep: true,
            }


        },
        created() {
            this.company_payload.whatsapp_instance_id = this.$auth.user.company.whatsapp_instance_id;
            //this.getDataFromApi();


        },
        methods: {
            can(per) {
                let u = this.$auth.user;
                return (
                    (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
                );
            },

            editItem() {
                this.whatsapp_instance_id = this.$auth.user.company.whatsapp_instance_id;;
                this.errors = {};
                this.newItemDialog = true;

            },
            save() {

                let payload = new FormData();

                payload.append("whatsapp_instance_id", this.whatsapp_instance_id);

                let company_id = this.$auth?.user?.company?.id
                this.$axios
                    .post(`/company/${company_id}/update_settings`, payload)
                    .then(({ data }) => {
                        this.loading = false;

                        if (!data.status) {

                            this.errors = data.errors;
                        } else {

                            this.company_payload.whatsapp_instance_id = this.whatsapp_instance_id;
                            this.snackbarColor = "primary";
                            this.snackbar = true;
                            this.snackbarResponse = " updated successfully";
                            this.newItemDialog = false;
                        }
                    })
                    .catch((e) => console.log(e));



            },

        },


    };
</script> 
 