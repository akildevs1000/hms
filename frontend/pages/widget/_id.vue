<template>
    <div>
        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
                {{ response }}
            </v-snackbar>
        </div>
        <v-row>

            <v-col md="4" sm="12" xs="12" cols="12">
                <div class="fixed">
                    <div class="fixed-content">
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
                                    <v-date-picker v-model="from_date" @input="from_menu = false"></v-date-picker>
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
                                    <v-date-picker :min="addOneDay()" v-model="to_date"
                                        @input="to_menu = false"></v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>






                        <v-row>
                            <v-col md="4" class="text-right"> <span>Rooms</span></v-col>
                            <v-col md="8">

                                <!-- <v-select label="Guests" dense small outlined append-icon=" mdi-account" variant="outlined"
                            :items="rooms"></v-select> -->
                                <v-menu v-model="guest_menu" :close-on-content-click="false" :nudge-right="40"
                                    transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="search.no_of_room" readonly v-bind="attrs" v-on="on" dense
                                            class="custom-text-box shadow-none" solo flat label="To" :hide-details="true"
                                            append-icon="mdi-bed" variant="outlined"></v-text-field>
                                    </template>


                                    <div class="wrapper" @input="guest_menu = false" style="width:200px">
                                        <span class="minus" @mouseup=" 
                                            search.no_of_room == 1 ? 1 : --search.no_of_room"
                                            @click="search.no_of_room < 1 || search.no_of_room">-</span>
                                        <span class="num">{{ search.no_of_room }}</span>
                                        <span class="plus" @mouseup=" search.no_of_room < 10 ? ++search.no_of_room : 10"
                                            @click=" search.no_of_room > 9 || search.no_of_room">+</span>
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
                                        <v-text-field v-model="search.no_of_adult" readonly v-bind="attrs" v-on="on" dense
                                            class="custom-text-box shadow-none" solo flat label="To" :hide-details="true"
                                            append-icon="mdi-account" variant="outlined"></v-text-field>
                                    </template>


                                    <div class="wrapper" @input="adult_menu = false" style="width:200px">
                                        <span class="minus" @mouseup=" 
                                            search.no_of_adult == 1 ? 1 : --search.no_of_adult"
                                            @click="search.no_of_adult < 1 || search.no_of_adult">-</span>
                                        <span class="num">{{ search.no_of_adult }}</span>
                                        <span class="plus" @mouseup=" search.no_of_adult < 2 ? ++search.no_of_adult : 2"
                                            @click=" search.no_of_adult > 2 || search.no_of_adult">+</span>
                                    </div>
                                </v-menu>

                            </v-col>

                        </v-row>
                        <v-row class="text-center">
                            <v-col cols="12"><v-btn size="100px" @click="getDataFromApi()" :loading="loading"
                                    color="primary" dence small>{{ btn_message
                                    }}</v-btn></v-col>

                        </v-row>
                    </div>
                </div>
            </v-col>


            <v-col md="8" xs="12" sm="12">
                <v-row :key="key" v-for="( item, key )   in   data  " height="350px"
                    v-if="search.no_of_room <= item.length">

                    <v-col md="6" cols="12"><img :src="item[0].room_type.pic || '/noimage.png'"
                            class="rounded-shaped rounded-xl " width="100%" height="400px" elevation="12" />
                    </v-col>
                    <v-col md="6" cols="12">
                        <h2> {{ key }}</h2>
                        <div class="mt-3">
                            <span> <v-icon>mdi-shower-head</v-icon> Shower</span>&NonBreakingSpace;
                            <span> <v-icon>mdi-account-supervisor</v-icon> 2 Guests</span>&NonBreakingSpace;
                            <span> <v-icon>mdi-bed</v-icon> King Bed</span>&NonBreakingSpace;
                            <span> <v-icon>mdi-bathtub</v-icon> Bathroom</span>

                        </div>
                        <div class="mt-3">
                            <i> {{ item[0].room_type.short_description }}</i><br />
                        </div>
                        <div class="mt-3">
                            {{ item[0].room_type.description }}
                        </div>
                        <div style="color:green" class="mt-2">
                            <h2>{{ item[0].price }}/-</h2>
                        </div>
                        <div>
                            <h3>Available Rooms : {{ item.length }}</h3>
                        </div>

                        <div class="mt-5 text-right mr-3">
                            <v-btn @click="booknow(item)" class="primary" fill dark>Book Now</v-btn>
                        </div>

                    </v-col>
                    <v-col cols="12"><v-divider class="p-0 m-0" dense></v-divider></v-col>

                </v-row>

            </v-col>


        </v-row>
        <!-- <v-row>
            <v-col md="12">
               
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

                            
                        </td>

                    </tr>
                </table>



            </v-col>
        </v-row> -->
    </div>
</template>
<script>
export default {
    layout: "widget",
    auth: false,
    data: () => ({
        company_id: 0,
        btn_message: "Check Availability",
        adult_menu: false,
        search: {
            no_of_adult: 2,
            no_of_room: 1,
        },

        guest_menu: false,
        dialogNewRole: false,
        options: {},
        Model: "Role",
        endpoint: "role",

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


        today_date: '',
        from_date: "",
        from_menu: false,

        to_date: "",
        to_menu: false,

        rooms: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],

        reservation: {
            check_in: null,
            check_out: null,
            room_no: "",
            room_id: "",
            room_type: "",
            price: "",
            origin_price: "",
            room_id: "",
            isCalculate: false,
        },
    }),
    mounted() {
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://securegw-stage.paytm.in/merchantpgpui/checkoutjs/merchants/HYDERS48297255078432.js');
        recaptchaScript.setAttribute('crossorigin', 'anonymous');


        document.head.appendChild(recaptchaScript)
    },
    computed: {

    },
    watch: {
        from_date() {
            this.to_date = this.addOneDay();//this.from_date;

        },
        to_date() {

            this.getDataFromApi();
        },

    },
    created() {

        this.$store.commit("widget_data", "");
        this.company_id = this.$route.params.id;
        this.loading = true;
        let date = new Date();
        this.today_date = this.GetTodayDate(date);
        this.from_date = this.today_date;
        this.to_date = this.addOneDay();;

        this.getDataFromApi();

        //console.log('widget_data', this.$store.state.widget_data);

    },

    methods: {
        makePaymentPayTm() {
            console.log("paytm payment function called");
            var that = this;
            function onScriptLoad() {
                var config = {
                    root: "",
                    flow: "DEFAULT",
                    data: {
                        orderId: that.paytm.orderId,
                        token: that.paytm.token,
                        tokenType: "TXN_TOKEN",
                        amount: that.paytm.amount,
                    },
                    handler: {
                        notifyMerchant: function (eventName, data) {
                            console.log("notifyMerchant handler function called");
                            console.log("eventName => ", eventName);
                            console.log("data => ", data);
                        },
                    },
                };

                if (window.Paytm && window.Paytm.CheckoutJS) {
                    window.Paytm.CheckoutJS.onLoad(function excecuteAfterCompleteLoad() {
                        // initialze configuration using init method
                        window.Paytm.CheckoutJS.init(config)
                            .then(function onSuccess() {
                                // after successfully updating configuration, invoke JS Checkout
                                window.Paytm.CheckoutJS.invoke();
                            })
                            .catch(function onError(error) {
                                console.log("error => ", error);
                            });
                    });
                }
            }
            onScriptLoad();
        },
        booknow(item) {
            this.reservation.company_id = this.company_id;
            this.reservation.isCalculate = true;

            this.reservation.room_type = item[0].room_type.name;
            this.reservation.room_no = item[0].room_no;
            this.reservation.check_in = this.from_date;

            this.reservation.check_out = this.to_date;

            let payload = {
                params: {
                    company_id: this.company_id,
                    roomType: item[0].room_type.name,
                    room_no: item[0].room_no,
                    checkin: this.from_date,
                    checkout: this.to_date,
                },
            };
            //this.$store.commit("booking_payload", payload);
            this.$axios
                .get(`get_data_by_select_with_tax`, payload)
                .then(({ data }) => {
                    if (!data.status) {

                        this.alert("Failure!", data.data, "error");
                        return false;
                    }

                    this.reservation.room_id = data.room.id;
                    this.reservation.price = data.total_price;
                    this.reservation.priceList = data.data;
                    this.reservation.total_tax = data.total_tax;

                    this.reservation.total_price_after_discount = data.total_price_after_discount;
                    this.reservation.total_price = data.total_price;
                    this.reservation.total_discount = data.total_discount;

                    let commitObj = {
                        widget_booking_id: new Date().getTime() / 1000,
                        ...this.reservation,

                    };
                    //console.log('reservation1', commitObj);
                    this.$store.commit("widget_data", commitObj);
                    //this.$store.commit("reservation", commitObj);
                    this.$router.push(`/widget/checkoutpage`);
                });
        },
        GetTodayDate(date) {

            let dd = (date.getDate() < 10 ? '0' : '') + date.getDate();
            let mm = ((date.getMonth() + 1) < 10 ? '0' : '') + (date.getMonth() + 1);
            let yy = date.getFullYear();
            var formattedDate = yy + '-' + mm + '-' + dd;


            return formattedDate;
        },

        addOneDay() {


            var modifiedDate = new Date(this.from_date);
            modifiedDate.setDate(modifiedDate.getDate() + 1); // Add 1 day


            return this.GetTodayDate(modifiedDate);
        },

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
                    company_id: this.company_id,
                    from_date: this.from_date,
                    to_date: this.to_date,
                },
            };

            this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
                this.data = data.data;

                this.loading = false;

            });
        },

    },



};
</script> 
