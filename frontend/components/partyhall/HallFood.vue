<template>
    <div v-if="can('calendar_create')">
        <v-card flat>
            <v-card-text>
                <v-row>

                    <v-col cols="12">
                        <v-form ref="form" v-model="valid" lazy-validation>
                            <v-col class="text-right" md="12" cols="12">
                                <v-btn @click="addDocumentInfo" class="primary mb-2 text-right">
                                    Add +
                                </v-btn>
                            </v-col>
                            <v-row v-for="(d, index) in Document.items" :key="index">
                                <v-col cols="3">
                                    <label class="col-form-label">Food Name </label>
                                    <v-text-field dense outlined v-model="d.name"></v-text-field>

                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Price Per Item/Person </label>
                                    <v-text-field dense outlined v-model="d.price_per_item"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Qty/Pax </label>
                                    <v-text-field dense outlined v-model="d.qty"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <label class="col-form-label">Total </label>
                                    <v-text-field dense outlined v-model="d.total"></v-text-field>


                                </v-col>
                                <v-col cols="2">
                                    <v-icon class="error--text mt-7" @click="removeItem(index)">mdi-delete</v-icon>
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

    data() {
        return {
            Document: {
                items: [{ name: "", price_per_item: "", qty: "", total: "" }],
            },
            document_list: [],
            date_menu: false,
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
        addDocumentInfo() {

            this.Document.items.unshift({
                items: [{ name: "", price_per_item: "", qty: "", total: "" }],
            });



        },

        close_document_info() {
            this.document_list = [];
            this.Document.items = [];
            this.documents = false;
            this.errors = [];

            // this.Document = {
            //   items: [{ title: "", file: "" }],
            // };
        },

        removeItem(index) {
            this.Document.items.splice(index, 1);
        },
    },
};
</script>

