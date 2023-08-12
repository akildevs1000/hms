<template>
    <div v-if="can('settings_Emails_access') && can('settings_Emails_view')">
        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" :color="snackbarColor" elevation="24">
                {{ snackbarResponse }}
            </v-snackbar>
        </div>
        <!-- <v-dialog v-model="newItemDialog" max-width="40%">
            <v-card>
                <v-card-title dense class=" primary  white--text background">
                    <span v-if="viewMode">View Email Info </span>
                    <span v-else-if="editedItemIndex == -1">Add Emails </span>
                    <span v-else>Edit Email Info </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="newItemDialog = false" outlined dark color="white">
                        mdi mdi-close-circle
                    </v-icon>
                </v-card-title>
                <v-card-text>
                    <v-container>

                        <v-col md="12" cols="2" class="text-right"> <v-btn dense small
                                v-if="can(`employee_document_edit_access`)" @click="addDocumentInfo"
                                class="primary mb-2 text-right">Add
                                Email +
                            </v-btn></v-col>


                        <v-row v-for="(d, index) in Document.items" :key="index">

                            <v-col md="4" cols="12">
                                <label>Email</label>
                                <v-text-field placeholder="johndoe@gmail.com" type="email" :disabled="viewMode"
                                    v-model="d.email" outlined dense small :hide-details="true"></v-text-field>
                                <span dense v-if="errors && errors.email" class="error--text">{{
                                    errors.email[0]
                                }}</span>
                            </v-col>

                            <v-col md="4" cols="12">
                                <label> Status</label>
                                <v-select :disabled="viewMode" :items="[{ id: 1, name: 'Active' }, {
                                    id: 0, name: 'In-Active'
                                }]" v-model="d.status" outlined dense small :hide-details="true" item-text="name"
                                    item-value="id" placeholder="Select status">
                                </v-select>
                                <span v-if="errors && errors.status" class="error--text">{{ errors.status[0]
                                }}</span>
                            </v-col>
                            <v-col cols="2">
                                <v-icon class="error--text mt-8" @click="removeItem(index)">mdi-delete</v-icon>
                            </v-col>
                        </v-row>

                        <v-card-actions class="mt-5" v-if="!viewMode">
                            <v-btn @click="newItemDialog = false;" dark filled color="red">Cancel</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn @click="save()" dark filled color="primary">Save</v-btn>
                        </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>


        </v-dialog> -->
        <v-dialog v-model="editItemDialog" max-width="20%">
            <v-card>
                <v-card-title dense class=" primary  white--text background">
                    <span v-if="viewMode">View Email Info </span>
                    <span v-else-if="editedItemIndex == -1">Add Emails </span>
                    <span v-else>Edit Email Info </span>
                    <v-spacer></v-spacer>
                    <v-icon @click="editItemDialog = false" outlined dark color="white">
                        mdi mdi-close-circle
                    </v-icon>
                </v-card-title>
                <v-card-text>
                    <v-container>


                        <v-row>

                            <v-col md="12" cols="12">
                                <label>Email</label>
                                <v-text-field placeholder="johndoe@gmail.com" type="email" :disabled="viewMode"
                                    v-model="editedItem.email" outlined dense small :hide-details="true"></v-text-field>
                                <span dense v-if="errors && errors.email" class="error--text">{{
                                    errors.email[0]
                                }}</span>
                            </v-col>

                            <v-col md="12" cols="12">
                                <label> Status</label>
                                <v-select :disabled="viewMode" :items="[{ id: 1, name: 'Active' }, {
                                    id: 0, name: 'In-Active'
                                }]" v-model="editedItem.status" outlined dense small :hide-details="true"
                                    item-text="name" item-value="id" placeholder="Select status">
                                </v-select>
                                <span v-if="errors && errors.status" class="error--text">{{ errors.status[0]
                                }}</span>
                            </v-col>

                        </v-row>

                        <v-card-actions class="mt-5" v-if="!viewMode">
                            <v-btn @click="editItemDialog = false;" dark filled color="red">Cancel</v-btn>
                            <v-spacer></v-spacer>
                            <v-btn @click="save()" dark filled color="primary">Save</v-btn>
                        </v-card-actions>
                    </v-container>
                </v-card-text>
            </v-card>


        </v-dialog>
        <v-card class="mb-5" elevation="0">
            <v-toolbar class="rounded-md mb-2 white--text" color="background" dense flat>
                <v-toolbar-title><span>Emails</span></v-toolbar-title>
                <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                            <v-icon color="white" class="ml-2" @click="reload()" dark>mdi
                                mdi-reload</v-icon>
                        </v-btn>
                    </template>
                    <span>Reload</span>
                </v-tooltip>
                <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="toggleFilter">
                            <v-icon white color="#FFF">mdi-filter</v-icon>
                        </v-btn>
                    </template>
                    <span>Filter</span>
                </v-tooltip>
                <v-spacer></v-spacer>
                <v-tooltip v-if="can('settings_Emails_create')" top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="AddNewEmail()">
                            <v-icon color="white" dark white>mdi-plus-circle</v-icon>
                        </v-btn>
                    </template>
                    <span>Add New Email Id</span>
                </v-tooltip>

            </v-toolbar>
            <v-row>
                <v-col cols="12">
                    <v-data-table dense :headers="headers_table" :items="data" :loading="loading" :options.sync="options"
                        :footer-props="{
                            itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
                        }" class="elevation-1" :server-items-length="totalTableRowsCount">
                        <template v-slot:header="{ props: { headers } }">
                            <tr v-if="isFilter">
                                <td v-for="  header   in      headers     " :key="header.text">
                                    <v-text-field v-if="header.filterable && !header.filterSpecial" clearable
                                        :hide-details="true" v-model="filters[header.value]" no-title outlined dense small
                                        :id="header.value" autocomplete="off" @input="applyFilters()"></v-text-field>
                                    <v-autocomplete outlined dense v-model="filters[header.value]"
                                        v-if="header.filterable && header.filterSpecial && header.key == 'status'"
                                        :items="[{ value: '', title: 'All' }, { value: '0', title: 'Active' }, { value: '1', title: 'In-Active' }]"
                                        item-value="value" item-text="title" :hide-details="true" clearable
                                        @click:clear="filters[header.key] = ''; applyFilters()"
                                        @change="applyFilters()"></v-autocomplete>

                                    <v-autocomplete v-model="filters[header.key]"
                                        v-if="header.filterable && header.filterSpecial && header.key == 'Email_type'"
                                        @change="applyFilters()" clearable
                                        @click:clear="filters[header.key] = ''; applyFilters();" outlined dense
                                        :hide-details="true" :items="EmailTypesForSelectOptions" item-text="name"
                                        item-value="id">
                                    </v-autocomplete>
                                    <v-select v-model="filters[header.key]"
                                        v-if="header.filterable && header.filterSpecial && header.key == 'floor_no'"
                                        :items="floors" outlined dense clearable
                                        @click:clear="filters[header.kye] = ''; applyFilters();" :hide-details="true"
                                        @change="applyFilters()"></v-select>

                                </td>
                            </tr>
                        </template>
                        <template v-slot:item.sno="{ item, index }">
                            {{ currentPage ? ((currentPage - 1) * perPage) +
                                (cumulativeIndex +
                                    itemIndex(item)) : '' }}
                        </template>
                        <template v-slot:item.email="{ item }">
                            {{ item.email }}</template>
                        <template v-slot:item.status="{ item }">
                            {{ item.status == '0' ? "Active" : "In-active" }}
                        </template>
                        <template v-slot:item.options="{ item }"
                            v-if="can('settings_Emails_view') || can('settings_Emails_edit') || can('settings_Emails_delete')">
                            <v-menu bottom left>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                                        <v-icon>mdi-dots-vertical</v-icon>
                                    </v-btn>
                                </template>
                                <v-list width="120" dense>
                                    <v-list-item v-if="can('settings_Emails_view')" @click="editItem(item, true)">
                                        <v-list-item-title style="cursor: pointer">
                                            <v-icon color="primary" small> mdi-eye </v-icon>
                                            View
                                        </v-list-item-title>
                                    </v-list-item>
                                    <v-list-item v-if="can('settings_Emails_edit')" @click="editItem(item, false)">
                                        <v-list-item-title style="cursor: pointer">
                                            <v-icon color="secondary" small> mdi-pencil </v-icon>
                                            Edit
                                        </v-list-item-title>
                                    </v-list-item>
                                    <v-list-item v-if="can('settings_Emails_delete')" @click="deleteItem(item)">
                                        <v-list-item-title style="cursor: pointer">
                                            <v-icon color="error" small> mdi-delete </v-icon>
                                            Delete
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>

                        </template>




                    </v-data-table>
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
            headers_table: [{ text: "#", value: "sno", align: "left", sortable: false, filterable: false, },
            { text: "Email", value: "email", align: "left", sortable: true, filterable: true, },
            { text: "Status", value: "status", align: "left", key: "status", sortable: true, filterable: true, filterSpecial: true, },
            { text: "Options", value: "options", align: "left", sortable: false },
            ],
            EmailTypesData: [],


            endpoint: 'email_notifications',

            newItemDialog: false,
            editItemDialog: false,

            //add edit item details 
            editedItem: {},
            editedItemIndex: -1,
            EmailTypesForSelectOptions: [],
            errors: {},
            snackbar: false,
            snackbarColor: "red",
            snackbarResponse: "",
            viewMode: false,

            Document: {
                items: [{ email: "", status: "" }],
            },
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

            this.getDataFromApi();

        },
        methods: {
            removeItem(index) {
                this.Document.items.splice(index, 1);
            },
            addDocumentInfo() {
                this.Document.items.push({
                    email: "",
                    status: "",
                });
            },

            can(per) {
                let u = this.$auth.user;
                return (
                    (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
                );
            },
            AddNewEmail() {
                this.Document = {
                    items: [{ email: "", status: "" }],
                };
                this.editedItem = {}
                this.editedItemIndex = -1;
                this.viewMode = false;
                //this.newItemDialog = true;
                this.editItemDialog = true;
            },
            // updateIndex(page) {

            //     this.currentPage = page;
            //     this.cumulativeIndex = (page - 1) * this.perPage;

            // },
            applyFilters() {
                this.$set(this.options, 'page', 1);
                this.getDataFromApi(this.endpoint, 1);
            },
            toggleFilter() {
                this.isFilter = !this.isFilter;

            },
            itemIndex(item) {
                return this.data.indexOf(item);
            },
            reload() {
                this.isFilter = false;
                this.filters = {};
                this.$set(this.options, 'page', 1);
                this.getDataFromApi(this.endpoint, 1);
            },
            getDataFromApi(url = this.endpoint, customPage = 0) {
                this.loading = true;
                let { sortBy, sortDesc, page, itemsPerPage } = this.options;
                let sortedBy = sortBy ? sortBy[0] : '';
                let sortedDesc = sortDesc ? sortDesc[0] : '';
                if (customPage == 1) page = 1;
                this.currentPage = page;
                this.perPage = itemsPerPage;

                let options = {
                    params: {
                        page: page,
                        sortBy: sortedBy,
                        sortDesc: sortedDesc,
                        per_page: itemsPerPage,
                        company_id: this.$auth.user.company.id,
                        ...this.filters,
                    }
                };
                this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {

                    this.loading = false;
                    this.data = data.data;
                    this.totalTableRowsCount = data.total;

                });


            },


            viewItem(item) {

                this.editItem(item, true);
            },
            editItem(item, viewMode = false) {

                this.viewMode = viewMode;
                this.editedItem = {};
                let options = { params: { company_id: this.$auth.user.company.id, } };
                this.editItemDialog = true;

                this.$axios.get(`${this.endpoint}/${item.id}`, options).then(({ data }) => {

                    this.editedItem = data;
                    this.editedItemIndex = item.id;

                });
            },
            save() {


                if (this.editedItemIndex != -1) {
                    let options = {
                        params: {
                            company_id: this.$auth.user.company.id,
                            email: this.editedItem.email,
                            status: this.editedItem.status,

                        }
                    };
                    console.log(this.$auth.user);
                    this.$axios.put(`${this.endpoint}/${this.editedItemIndex}`, options.params).then(({ data }) => {
                        if (data.status) {
                            this.getDataFromApi();
                            this.errors = {};
                            this.editedItem = {};
                            this.snackbar = true;
                            this.snackbarColor = "greeen";
                            this.snackbarResponse = data.message;

                            this.editItemDialog = false;
                        }
                        else {
                            if (data.errors) {

                                this.errors = data.errors;
                            }
                            else {

                                this.errors = {};

                                this.snackbar = true;
                                this.snackbarColor = "red";
                                this.snackbarResponse = data.message;

                            }

                        }
                    });

                }
                else {

                    // let payload = new FormData();

                    // this.Document.items.forEach((e) => {
                    //     payload.append(`items[][email]`, e.email);
                    //     payload.append(`items[][status]`, e.status);
                    // });

                    // payload.append(`company_id`, this.$auth?.user?.company?.id);

                    let options = {
                        params: {
                            company_id: this.$auth.user.company.id,
                            email: this.editedItem.email,
                            status: this.editedItem.status,
                            // list: JSON.stringify(this.Document.items)
                        }
                    };

                    this.$axios.post(`${this.endpoint}/storeSingle`, options.params).then(({ data }) => {
                        if (data.status) {
                            this.getDataFromApi();
                            this.errors = {};
                            this.editedItem = {};
                            this.snackbar = true;
                            this.snackbarColor = "greeen";
                            this.snackbarResponse = data.message;

                            // this.newItemDialog = false;
                            this.editItemDialog = false;
                        }
                        else {
                            if (data.errors) {

                                this.errors = data.errors;
                            }
                            else {

                                this.errors = {};

                                this.snackbar = true;
                                this.snackbarColor = "red";
                                this.snackbarResponse = data.message;

                            }

                        }
                    });

                }

            },
            deleteItem(item) {
                confirm(
                    "Are you sure you wish to delete?"
                ) &&
                    this.$axios
                        .delete(this.endpoint + "/" + item.id)
                        .then(({ data }) => {
                            this.getDataFromApi();
                            this.snackbar = data.status;
                            this.snackbarResponse = data.message;
                        })
                        .catch((err) => console.log(err));
            },
        },


    };
</script> 
 