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

                <table style="width:50%">
                    <tr>
                        <td>
                            #
                        </td>

                        <td>
                            Category
                        </td>

                        <td>
                            Price
                        </td>

                        <td>
                            Rooms
                        </td>
                    </tr>
                    <tr v-for="(items, key, index)   in  data ">
                        <td>
                            {{ ++index }}
                        </td>
                        <td>
                            {{ key }}
                        </td>
                        <td>{{ items[0].price }}</td>
                        <td> (
                            <span v-for="rooms   in  items "> {{ rooms.room_no }}, </span>)

                        </td>

                    </tr>
                </table>



            </v-col>
        </v-row>
    </div>
    <NoAccess v-else />
</template>
<script>
export default {
    layout: "widget",
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

    created() {
        this.loading = true;
        this.from_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);
        this.to_date = new Date(Date.now() - 86400000)
            .toISOString()
            .slice(0, 10);

        this.getDataFromApi();
    },

    methods: {



        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
            );
        },

        getDataFromApi(url = this.endpoint) {


            this.from_menu = false;
            this.to_menu = false;
            url = 'widget_rooms_availability';
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
                this.data = data.data;

                //this.data = Object.keys(data.data).flatMap((roomType) => data.data[roomType]);
                console.log(this.data);
                this.loading = false;
            });
        },

    },



};
</script> 