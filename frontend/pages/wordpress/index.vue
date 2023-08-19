<template>
    <div v-if="can('settings_roles_access') && can('settings_roles_view')">


        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
                {{ response }}
            </v-snackbar>
        </div>
        <v-row>

            <v-col md="2">
                <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
                    offset-y min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
                            class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
                    </template>
                    <v-date-picker no-title v-model="from_date" @input="from_menu = false"
                        @change="getDataFromApi"></v-date-picker>
                </v-menu>
            </v-col>
            <v-col md="2">
                <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
                    offset-y min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense
                            class="custom-text-box shadow-none" solo flat label="To" :hide-details="true"></v-text-field>
                    </template>
                    <v-date-picker v-model="to_date" @input="to_menu = false" @change="getDataFromApi"
                        no-title></v-date-picker>
                </v-menu>
            </v-col>


        </v-row>

        <v-row>
            <v-col md="12">
                <!-- {{ data }} -->


                <!-- <div v-for="items in data">{{ items }}

                    {{ item[0] }}

                </div> -->
                <v-data-table v-model="ids" item-key="id" :headers="headers" :items="data" :loading="loading"
                    :options.sync="options" :footer-props="{
                        itemsPerPageOptions: [50, 100, 500, 1000],
                    }" class="elevation-1">
                    <template v-slot:top>

                        <v-card class="mb-5 rounded-md mt-3" elevation="0">
                            <v-toolbar class="rounded-md" style="border-radius: 5px 5px 0px 0px" color="background" dense
                                flat dark>
                                <span> Dashboard / Roles List</span>
                                <v-tooltip top color="primary">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                                            @click="getDataFromApi()">
                                            <v-icon color="white" class="ml-2" dark>mdi mdi-reload</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Reload</span>
                                </v-tooltip>
                                <v-spacer></v-spacer>
                                <v-toolbar-items>

                                    <v-col>
                                        <v-tooltip top color="primary" v-if="can('settings_roles_create')">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs"
                                                    v-on="on">
                                                    <v-icon color="white" class="ml-2" @click="dispalyNewDialog()" dark>mdi
                                                        mdi-plus-circle</v-icon>
                                                </v-btn>
                                            </template>
                                            <span>Add New Role</span>
                                        </v-tooltip>
                                    </v-col>
                                </v-toolbar-items>
                            </v-toolbar>
                        </v-card>

                    </template>

                </v-data-table>

            </v-col>
        </v-row>
    </div>
    <NoAccess v-else />
</template>
<script>
export default {
    data: () => ({
        dialogNewRole: false,
        options: {},
        Model: "Role",
        endpoint: "role",
        search: "",
        snackbar: false,
        dialog: false,
        ids: [],
        loading: false,
        total: 0,
        headers: [
            {
                label: "Room Type",
                field: "room_type.name",
            },
            {
                label: "Room Number",
                field: "room_no",
            },
            {
                label: "Status",
                field: "status",
            },
            {
                label: "Price",
                field: "price",
            },
        ],
        editedIndex: -1,
        editedItem: { name: "", description: "" },
        defaultItem: { name: "", description: "" },
        response: "",
        data: [],
        errors: [],


        permission_ids: [],
        permissions: [],
        formTitle: 'New',
        selectall1: '',
        selectall2: '',
        selectall3: '',
        selectall4: '',
        selectall5: '',
        editPermissionId: '',


        from_date: "",
        from_menu: false,

        to_date: "",
        to_menu: false,
    }),

    computed: {

    },

    watch: {
        editedIndex(val) {
            this.formTitle = val === -1 ? "New" : "Edit";

        },
        dialog(val) {
            val || this.close();
            this.errors = [];
            this.search = "";
        },
        options: {
            handler() {
                this.getDataFromApi();
            },
            deep: true,
        },
    },
    created() {
        this.loading = true;
        this.from_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
        this.to_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);

        //permissions
        this.getPermissions();
    },

    methods: {

        dispalyNewDialog() {
            this.errors = [];
            this.editedItem = { name: "", description: "" };
            this.editedIndex = -1;
            this.formTitle = "New";
            this.dialogNewRole = true;
            this.permission_ids = [];
        },

        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
            );
        },

        getDataFromApi(url = this.endpoint) {


            this.from_menu = false;
            this.to_menu = false;
            url = 'wordpress_rooms_availability';
            this.loading = true;

            const { page, itemsPerPage } = this.options;

            let options = {
                params: {
                    per_page: itemsPerPage,
                    company_id: this.$auth.user.company.id,
                    from_date: this.from_date,
                    to_date: this.to_date,
                },
            };

            this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
                //  this.data = data.data;

                this.data = Object.keys(data.data).flatMap((roomType) => data.data[roomType]);

                this.loading = false;
            });
        },
        searchIt(e) {
            if (e.length == 0) {
                this.getDataFromApi();
            } else if (e.length > 2) {
                this.getDataFromApi(`${this.endpoint}/search/${e}`);
            }
        },

        editItem(item) {


            this.errors = [];
            this.editedIndex = this.data.indexOf(item);
            this.editedItem = Object.assign({}, item);
            //this.dialog = true;
            this.dialogNewRole = true;

            this.selectall1 = false;
            this.selectall2 = false;
            this.selectall3 = false;
            this.selectall4 = false;
            this.selectall5 = false;
            this.loading = true;

            const { page, itemsPerPage } = this.options;

            let options = {
                params: {
                    per_page: itemsPerPage,
                    company_id: this.$auth.user.company.id
                }
            };

            this.$axios.get('assign-permission/role-id/' + item.id, options).then(({ data }) => {
                //this.data = data;
                this.loading = false;
                if (data[0]) {
                    this.permission_ids = data[0].permission_ids;
                    this.editPermissionId = data[0].id;

                    //alert(this.editPermissionId);
                }

                // else
                //   this.$router.push("/assign_permission/create");

            });
        },

        delteteSelectedRecords() {
            let just_ids = this.ids.map((e) => e.id);
            confirm(
                "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
            ) &&
                this.$axios
                    .post(`${this.endpoint}/delete/selected`, {
                        ids: just_ids,
                    })
                    .then(({ data }) => {
                        if (!data.status) {
                            this.errors = data.errors;
                        } else {
                            this.getDataFromApi();
                            this.snackbar = data.status;
                            this.ids = [];
                            this.response = "Selected records has been deleted";
                        }
                    })
                    .catch((err) => console.log(err));
        },

        deleteItem(item) {
            confirm(
                "Are you sure you wish to delete , to mitigate any inconvenience in future."
            ) &&
                this.$axios
                    .delete(this.endpoint + "/" + item.id)
                    .then(({ data }) => {
                        if (!data.status) {
                            this.errors = data.errors;
                        } else {

                            this.deletePermission();



                            this.getDataFromApi();
                            this.snackbar = data.status;
                            this.response = data.message;
                        }
                    })
                    .catch((err) => console.log(err));
        },

        close() {
            this.dialog = false;
            this.dialogNewRole = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300);
        },

        save() {
            let payload = {
                name: this.editedItem.name.toLowerCase(),
                description: this.editedItem.description.toLowerCase(),

                company_id: this.$auth.user.company.id,
            };
            if (this.editedIndex > -1) {
                this.$axios
                    .put(this.endpoint + "/" + this.editedItem.id, payload)
                    .then(({ data }) => {
                        if (!data.status) {
                            this.errors = data.errors;
                        } else {

                            if (this.editPermissionId == '') {
                                this.savePermisions(this.editedItem.id);
                            }
                            else {
                                this.updatePermission(this.editedItem.id);
                            }


                            this.getDataFromApi();
                            // const index = this.data.findIndex(
                            //   (item) => item.id == this.editedItem.id
                            // );
                            // this.data.splice(index, 1, {
                            //   id: this.editedItem.id,
                            //   name: this.editedItem.name,
                            // });
                            this.snackbar = data.status;
                            this.response = data.message;
                            this.dialogNewRole = false;
                            this.close();
                        }
                    })
                    .catch((err) => console.log(err));
            } else {
                this.$axios
                    .post(this.endpoint, payload)
                    .then(({ data }) => {
                        if (!data.status) {
                            this.errors = data.errors;
                        } else {
                            this.savePermisions(data.record.id);
                            this.getDataFromApi();
                            this.snackbar = data.status;
                            this.response = data.message;
                            this.close();
                            this.errors = [];
                            this.search = "";
                        }
                    })
                    .catch((res) => console.log(res));
            }
        },
        //permissions
        deletePermission(id) {
            this.$axios
                .delete(this.endpoint + "/" + id)
                .then(({ data }) => {

                    this.snackbar = data.status;
                    this.response = data.message;
                })
                .catch(err => console.log(err));
        },
        updatePermission(role_id) {
            //alert(this.editPermissionId);
            console.log(this.editPermissionId);
            let payload = {
                role_id: role_id,
                permission_ids: this.permission_ids
            };
            this.$axios
                .put("assign-permission/" + this.editPermissionId, payload)
                .then(({ data }) => {
                    if (!data.status) {
                        this.errors = data.errors;
                        return;
                    }
                    this.response = "Permissions has been assigned";
                    this.snackbar = true;
                    //setTimeout(() => this.$router.push("/assign_permission"), 2000);
                });
        },

        getPermissions(url = "dropDownList") {
            this.$axios
                .get(url)
                .then(({ data }) => {
                    this.permissions = data.data;
                })
                .catch(err => console.log(err));
        },
        capsTitle(val) {
            if (val == 'gst') { val = val.toUpperCase(); return val; }
            let res = val;
            let r = res.replace(/[^a-z]/g, " ");
            let title = r.replace(/\b\w/g, c => c.toUpperCase());
            return title;
        },
        setAllIds(filter, e) {
            let isSelected = e;

            // this.permission_ids = this.just_ids
            //   ? this.permissions.map(e => e.id)
            //   : [];

            // Function to filter IDs containing the text "edit"
            let data = this.permissions;
            const filteredIds = [];

            // Loop through each module in the data
            for (const module in data) {
                if (data.hasOwnProperty(module)) {
                    // Filter the items in the module where the "name" contains "edit"
                    const editItems = data[module].filter(item => item.name.includes(filter));

                    // Extract and store the IDs from the filtered items
                    const editItemIds = editItems.map(item => item.id);

                    if (isSelected)
                        this.permission_ids.push(...editItemIds);
                    else {

                        const indexToDelete = this.permission_ids.findIndex(item => item === editItemIds[0]);

                        if (indexToDelete !== -1) {
                            this.permission_ids.splice(indexToDelete, 1);

                        }
                    }
                }
            }
            const uniqueSet = new Set(this.permission_ids);
            this.permission_ids = Array.from(uniqueSet);


        },

        savePermisions(role_id) {
            this.errors = [];
            let payload = {
                role_id: role_id,//this.role_id,
                permission_ids: this.permission_ids,
                company_id: this.$auth.user.company.id
            };

            this.$axios.post("assign-permission", payload).then(({ data }) => {
                if (!data.status) {
                    this.errors = data.errors;
                    return;
                }
                this.msg = "Permissions has been assigned";
                this.snackbar = true;
                //setTimeout(() => this.$router.push("/assign_permission"), 1000);
            });
        }
    },



};
</script> 