<template>
    <div>


        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
                {{ response }}
            </v-snackbar>
        </div>
        <v-row>
            <v-row>
                <v-col md="3">
                    <div>
                        <v-row>
                            <v-col md="4" class="text-right"> <span>Check in</span></v-col>
                            <v-col md="8">
                                <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40"
                                    transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense
                                            :hide-details="true" class="custom-text-box shadow-none" solo flat label="From"
                                            append-icon=" mdi-calendar-arrow-left" variant="outlined"></v-text-field>

                                    </template>
                                    <v-date-picker no-title v-model="from_date" @input="from_menu = false"
                                        @change="getDataFromApi"></v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col md="4" class="text-right"> <span>Check Out</span></v-col>
                            <v-col md="8">
                                <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40"
                                    transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense
                                            class="custom-text-box shadow-none" solo flat label="To" :hide-details="true"
                                            append-icon=" mdi-calendar-arrow-right" variant="outlined"></v-text-field>
                                    </template>
                                    <v-date-picker v-model="to_date" @input="to_menu = false" @change="getDataFromApi"
                                        no-title></v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>





                        <v-row>
                            <v-row>
                                <v-col md="4" class="text-right"> <span>Rooms</span></v-col>
                                <v-col md="8">

                                    <!-- <v-select label="Guests" dense small outlined append-icon=" mdi-account" variant="outlined"
                            :items="rooms"></v-select> -->
                                    <v-menu v-model="guest_menu" :close-on-content-click="false" :nudge-right="40"
                                        transition="scale-transition" offset-y min-width="auto">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="temp.no_of_room" readonly v-bind="attrs" v-on="on" dense
                                                class="custom-text-box shadow-none" solo flat label="To"
                                                :hide-details="true" append-icon="mdi-bed"
                                                variant="outlined"></v-text-field>
                                        </template>


                                        <div class="wrapper" @input="guest_menu = false" style="width:200px">
                                            <span class="minus" @mouseup=" 
                                                temp.no_of_room == 1 ? 1 : --temp.no_of_room"
                                                @click="temp.no_of_room < 1 || temp.no_of_room">-</span>
                                            <span class="num">{{ temp.no_of_room }}</span>
                                            <span class="plus" @mouseup=" temp.no_of_room < 10 ? ++temp.no_of_room : 10"
                                                @click=" temp.no_of_room > 9 || temp.no_of_room">+</span>
                                        </div>
                                    </v-menu>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col md="4" class="text-right"> <span>Adult</span></v-col>
                                <v-col md="8">
                                    <!-- <v-select label="Total Rooms" :items="rooms" dense small outlined append-icon="mdi-bed"
                            variant="outlined"></v-select> -->
                                    <v-menu v-model="adult_menu" :close-on-content-click="false" :nudge-right="40"
                                        transition="scale-transition" offset-y min-width="auto">
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field v-model="temp.no_of_adult" readonly v-bind="attrs" v-on="on" dense
                                                class="custom-text-box shadow-none" solo flat label="To"
                                                :hide-details="true" append-icon="mdi-account"
                                                variant="outlined"></v-text-field>
                                        </template>


                                        <div class="wrapper" @input="adult_menu = false" style="width:200px">
                                            <span class="minus" @mouseup=" 
                                                temp.no_of_adult == 1 ? 1 : --temp.no_of_adult"
                                                @click="temp.no_of_adult < 1 || temp.no_of_adult">-</span>
                                            <span class="num">{{ temp.no_of_adult }}</span>
                                            <span class="plus" @mouseup=" temp.no_of_adult < 10 ? ++temp.no_of_adult : 10"
                                                @click=" temp.no_of_adult > 9 || temp.no_of_adult">+</span>
                                        </div>
                                    </v-menu>

                                </v-col>
                            </v-row>
                        </v-row>
                    </div>
                </v-col>


                <v-col md="9">
                    <v-row v-for="( items, key, index )   in   data  " height="350px">
                        <v-col md="4" cols="12"><img :src="getImagePath(key)" class="rounded-shaped rounded-xl "
                                elevation="12" width="100%" height="400px" />
                        </v-col>
                        <v-col md="8" cols="12">
                            <h2> {{ key }}</h2>
                            <div class="mt-3">
                                <span> <v-icon>mdi-shower-head</v-icon> Shower</span>&NonBreakingSpace;
                                <span> <v-icon>mdi-account-supervisor</v-icon> 2 Guests</span>&NonBreakingSpace;
                                <span> <v-icon>mdi-bed</v-icon> King Bed</span>&NonBreakingSpace;
                                <span> <v-icon>mdi-bathtub</v-icon> Bathroom</span>

                            </div>
                            <div class="mt-3">
                                <i>Private Pool / Ocean View / Single Level</i><br />
                            </div>
                            <div class="mt-3">
                                Stunning beachfront location with 60 square meters / 646 square feet of interior space,
                                located
                                on
                                the
                                west side of the resort in a private tropical garden with a plunge pool and a private
                                outdoor
                                shower. <br />
                                Stunning beachfront location with 60 square meters / 646 square feet of interior space,
                                located
                                on
                                the
                                west side of the resort in a private tropical garden with a plunge pool and a private
                                outdoor
                                shower. <br />
                                Stunning beachfront location with 60 square meters / 646 square feet of interior space,
                                located
                                on
                                the
                                west side of the resort in a private tropical garden with a plunge pool and a private
                                outdoor
                                shower.
                            </div>
                            <div style="color:green" class="mt-2">
                                <h2>{{ items[0].price }}/-</h2>
                            </div>
                            <div>
                                <h3>Available Rooms : {{ items.length }}</h3>
                            </div>

                            <div class="mt-5">
                                <v-btn @click="booknow()" class="primary" fill dark>Book Now</v-btn>
                            </div>

                        </v-col>
                    </v-row>

                </v-col>

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
                        <tr v-for="( items, key, index )   in   data  ">
                            <td>
                                {{ ++index }}
                            </td>
                            <td>
                                {{ key }}
                            </td>
                            <td>{{ items[0].price }}</td>
                            <td> (
                                <span v-for=" rooms    in   items  "> {{ rooms.room_no }}, </span>)

                                <!-- {{ items[0].rooms.length() }} -->
                            </td>

                        </tr>
                    </table>



                </v-col>
            </v-row>

    </div>
</template>
<script>
export default {
    layout: "widget",
    data: () => ({
        adult_menu: false,
        temp: {
            no_of_adult: 1,
            no_of_room: 1,
        },

        guest_menu: false,
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

        rooms: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
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


        getImagePath(name) {
            return process.env.BACKEND_URL.replace('/api', '') + "storage/rooms/" + name + ".jpg";
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

                this.loading = false;
            });
        },

    },



};
</script> 