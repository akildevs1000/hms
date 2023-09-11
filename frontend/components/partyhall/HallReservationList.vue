<template>
    <div>
        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
                {{ response }}
            </v-snackbar>
        </div>
        <v-row class="mt-5 mb-5">
            <v-col cols="6">
                <h3>{{ Model }}</h3>

            </v-col>
            <v-col cols="6"> </v-col>
        </v-row>
        <v-row>
            <div class="col-xl-2 my-0 py-0 col-lg-2 text-uppercase">
                <div class="card px-2" style="background-color: #3366CC;">
                    <div class="card-statistic-3">
                        <div class="card-icon card-icon-large">
                            <i class="fas fa-ddoor-open"></i>
                        </div>
                        <div class="card-content">
                            <h6 class="card-title text-capitalize">Total</h6>
                            <span class="data-1"> {{ totalRowsCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </v-row>

        <v-dialog v-model="payingDialog" persistent max-width="1000px">
            <v-card>
                <v-toolbar class="rounded-md" color="background" dense flat dark>
                    <span>Payment</span>
                    <v-spacer></v-spacer>
                    <v-icon dark class="pa-0" @click="payingDialog = false">mdi mdi-close-box</v-icon>
                </v-toolbar>
                <v-card-text>
                    <Paying :BookingData="checkData" @close-dialog="closeDialogs"></Paying>
                </v-card-text>
            </v-card>
        </v-dialog>


        <v-row>
            <v-col xs="12" sm="12" md="2" cols="12">
                <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" @input="searchIt"
                    v-model="search" hide-details></v-text-field>
            </v-col>
            <v-col xs="12" sm="12" md="2" cols="12">
                <v-select outlined v-model="type" :items="types" dense placeholder="Type" flat :hide-details="true"
                    @change="getDataFromApi(endpoint)"></v-select>
            </v-col>

            <v-col xs="12" sm="12" md="2" cols="12">
                <v-select v-model="source" :items="type == 'Online' ? sources : agentList" item-value="name"
                    item-text="name" placeholder="Sources" @change="getDataFromApi(endpoint)" dense outlined
                    :hide-details="true"></v-select>



            </v-col>
            <v-col xs="12" sm="12" md="2" cols="12">
                <v-select v-model="guest_mode" :items="['Select All', 'Arrival', 'Departure']" dense outlined
                    placeholder="Type" solo flat :hide-details="true" @change="reload()"></v-select>
            </v-col>

            <v-col md="4">
                <CustomFilter @filter-attr="filterAttr" :defaultFilterType="4" />
                <!-- <DateRangePicker key="reservationList" :disabled="false" :DPStart_date="from_date" :DPEnd_date="to_date"
                    column="date_range" @selected-dates="handleDatesFilter" /> -->
            </v-col>



        </v-row>

        <v-card class="mb-5 rounded-md mt-3" elevation="0">
            <v-toolbar class="rounded-md" color="background" dense flat dark>
                <span style="line-height:20px"> {{ Model }} List</span>
                <v-spacer></v-spacer>
                <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                            @click="process('reservation_report_print', endpoint)">
                            <v-icon class="white--text">mdi-printer-outline</v-icon>
                        </v-btn>
                    </template>
                    <span>PRINT</span>
                </v-tooltip>

                <v-tooltip top color="primary">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                            @click="process('reservation_report_download', endpoint)">
                            <v-icon class="white--text">mdi-download-outline</v-icon>
                        </v-btn>
                    </template>
                    <span> DOWNLOAD </span>
                </v-tooltip>
            </v-toolbar>

            <v-data-table dense small :headers="headers_table" :items="data" :loading="loading" :options.sync="options"
                :footer-props="{
                    itemsPerPageOptions: [50, 100, 500, 1000],
                }" class="elevation-1" :server-items-length="totalRowsCount">

                <template v-slot:item.sno="{ item, index }">
                    {{ currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : '' }}
                </template>
                <template v-slot:item.res_number="item">


                    <span class="blue--text" @click="goToRevView(item.item.booking)" style="cursor: pointer">
                        {{ item.item.booking.reservation_no || "---" }}
                    </span>
                </template>
                <template v-slot:item.event_type="item">
                    {{ item.item.event.name || "---" }}
                </template>
                <template v-slot:item.source="item">
                    {{ item.item.source_type || "---" }}
                </template>
                <!-- <template v-slot:item.rooms="item">
                    <span v-for="(room, index) in item.item.booked_rooms" :key="index">
                        {{ room.room_no }}
                        {{ item.item.booked_rooms.length - 1 == index ? "" : "," }}
                    </span>
                </template> -->
                <template v-slot:item.reference="item">
                    {{ item.item.reference_no || "---" }}
                </template>
                <template v-slot:item.guest="item">
                    {{ item && item.item.customer.first_name || "---" }}
                </template>
                <template v-slot:item.event_start_time="item">

                    {{ getHours(item.item.event_start_time) }}
                </template>
                <template v-slot:item.event_end_time="item">

                    {{ getHours(item.item.event_end_time) }}
                </template>
                <template v-slot:item.event_audio_system="item">
                    {{ item.item.event_audio_system == 1 ? "Yes" : "No" }}
                </template>
                <template v-slot:item.event_projector="item">
                    {{ item.item.event_projector == 1 ? "Yes" : "No" }}
                </template>
                <template v-slot:item.event_stage_decoration="item">
                    {{ item.item.event_stage_decoration == 1 ? "Yes" : "No" }}
                </template>
                <template v-slot:item.inv_total="item">
                    {{ item.item.inv_total }}
                </template>
                <template v-slot:item.paid="item">
                    {{ item.item.booking.paid_amounts || 0 }}
                </template>
                <template v-slot:item.due="item">
                    {{ item.item.booking.balance }}
                </template>
                <template v-slot:item.view="item">
                    <v-icon @click="viewCustomerBilling(item.item.booking)" x-small color="primary" class="mr-2">
                        mdi-eye
                    </v-icon>
                </template>
                <template v-slot:item.payment="item">
                    <v-icon v-if="can('reservation_edit') || can('in_house_edit') || can('checkout_edit')"
                        @click="get_payment(item.item.booking)" x-small color="primary" class="mr-2">
                        mdi-cash-multiple
                    </v-icon>
                </template>
                <template v-slot:item.invoice="item">
                    <v-icon @click="redirect_to_invoice(item.item.booking.id)" x-small color="primary" class="mr-2">
                        mdi-cash-multiple
                    </v-icon>
                </template>
            </v-data-table>

        </v-card>
        <!-- <v-row>
            <v-col md="12" class="float-right">
                <div class="float-right">
                    <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
                        :total-visible="12"></v-pagination>
                </div>
            </v-col>
        </v-row> -->
    </div>
</template>
<script>
import Paying from "../../components/booking/Paying.vue";
import CustomFilter from "../filter/CustomFilter.vue";
import HallPaying from "./HallPaying.vue";
export default {
    props: ["endpoint", "Model"],
    components: {
        Paying,
        CustomFilter,
        HallPaying
    },
    data: () => ({

        cumulativeIndex: 1,
        perPage: 20,
        currentPage: 1,
        filterLoader: false,
        filters: {},
        isFilter: false,
        totalRowsCount: 0,
        options: {},
        // Model: "In House Reservation",
        payingDialog: false,
        pagination: {
            current: 1,
            total: 0,
            per_page: 30,
        },
        guest_mode: "",

        from_date: "",
        from_menu: false,

        to_date: "",
        to_menu: false,

        type: "",
        source: "",
        agentList: [],
        types: ["Select All", "Online", "Travel Agency", "Walking"],
        sources: [],

        options: {},
        // endpoint: "in_house_reservation_list",
        search: "",
        snackbar: false,
        dialog: false,
        data: [],
        loading: false,
        total: 0,
        hours: [
            { id: 9, name: "09 AM" }
            , { id: 10, name: "10 AM" }
            , { id: 11, name: "11 AM" }
            , { id: 12, name: "12 PM" }
            , { id: 13, name: "01 PM" }
            , { id: 14, name: "02 PM" }
            , { id: 15, name: "03 PM" }
            , { id: 16, name: "04 PM" }
            , { id: 17, name: "05 PM" }
            , { id: 18, name: "06 PM" }
            , { id: 19, name: "07 PM" }
            , { id: 20, name: "08 PM" }
            , { id: 21, name: "09 PM" }
            , { id: 22, name: "10 PM" }
            , { id: 23, name: "11 PM" }
            // , { id: 0, name: "12 AM" }
            // , { id: 1, name: "01 AM" }
            // , { id: 2, name: "02 AM" }
            // , { id: 3, name: "03 AM" }
            // , { id: 4, name: "04 AM" }
            // , { id: 5, name: "05 AM" }
            // , { id: 6, name: "06 AM" }
            // , { id: 7, name: "07 AM" }
            // , { id: 8, name: "08 AM" }


        ],
        headers: [
            { text: "#" },
            { text: "Rev. No" },
            { text: "Room" },
            { text: "Source" },
            { text: "Reference" },
            { text: "Guest" },
            { text: "C/In" },
            { text: "C/Out" },
            { text: "Total" },
            { text: "Posting" },
            { text: "Paid" },
            { text: "Balance" },
            { text: "Rev. Date" },
            // { text: "Reservation Status" },
            { text: "View" },
            { text: "Payment" },
            { text: "Invoice" },
        ],

        headers_table: [
            {
                text: "#",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "sno",
            },
            {
                text: "Rev. No",
                align: "left",
                sortable: false,
                width: "100px",
                key: "employee_id",
                filterable: true,
                value: "res_number",
            },
            {
                text: "Room",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "room_number",
            },
            {
                text: "Event Type",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_type",
            },
            {
                text: "Pax",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_pax",
            },
            {
                text: "Event Date",
                width: "105px",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_date",
            },
            {
                text: "Start Time",
                width: "105px",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_start_time",
            },
            {
                text: "End Time",
                align: "left",
                width: "105px",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_end_time",
            },
            {
                text: "Special Setup",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_special_setup",
            },
            {
                text: "Audio System",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_audio_system",
            },
            {
                text: "Projector",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_projector",
            },
            {
                text: "Projector",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "event_stage_decoration",
            },
            {
                text: "Total",
                align: "left",
                sortable: false,
                width: "105px",
                key: "employee_id",
                filterable: true,
                value: "inv_total",
            },
            {
                text: "Paid",
                align: "left",
                sortable: false,
                width: "105px",
                key: "employee_id",
                filterable: true,
                value: "paid",
            },
            {
                text: "Due",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "due",
            },
            {
                text: "Booked on ",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "booking_date",
            },
            {
                text: "View",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "view",
            },
            {
                text: "Pay",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "payment",
            },
            {
                text: "Inv",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "invoice",
            },

        ],
        editedIndex: - 1,
        response: "",
        errors: [],
        checkData: {},
        new_payment: 0,

    }),

    computed: {},

    watch: {
        search() {
            this.getDataFromApi();
        },
        options: {
            handler() {
                this.getDataFromApi();
            },
            deep: true,
        },
    },
    created() {


        // this.loading = true;
        this.month = new Date().getMonth();
        this.year = new Date().getFullYear();
        this.from_date = this.formatDate(new Date(this.year, this.month, 1));
        this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));


        this.getDataFromApi();
        this.get_agents();
        this.get_online();
    },

    methods: {
        handleDatesFilter(dates) {

            this.from_date = dates[0];
            this.to_date = dates[1];
            if (this.from_date && this.to_date)
                this.getDataFromApi();
        },

        filterAttr(data) {
            this.from_date = data.from;
            this.to_date = data.to;
            //this.filterType = data.type;
            this.search = data.search;
            if (this.from_date && this.to_date)
                this.getDataFromApi();
        },
        formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero-based
            var year = date.getFullYear();
            return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
        },
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
            );
        },

        viewCustomerBilling(item) {
            this.$router.push(`/customer/details/${item.id}`);
        },

        commonMethod() {
            this.getDataFromApi();
        },

        goToRevView(item) {
            this.$router.push(`/customer/details/${item.id}`);
        },

        get_agents() {
            let payload = {
                params: {
                    company_id: this.$auth.user.company.id,
                },
            };
            this.$axios.get(`get_agent`, payload).then(({ data }) => {
                this.agentList = [{ id: -1, name: "Select All" }].concat(data);
            });
        },

        get_online() {
            let payload = {
                params: {
                    company_id: this.$auth.user.company.id,
                },
            };
            this.$axios.get(`get_online`, payload).then(({ data }) => {
                // this.sources = data;

                this.sources = [{ id: -1, name: "Select All" }].concat(data);
            });
        },

        redirect_to_invoice(id) {
            let url = process.env.BACKEND_URL + "invoice";
            let element = document.createElement("a");
            element.setAttribute("target", "_blank");
            element.setAttribute("href", `${url}/${id}`);
            document.body.appendChild(element);
            element.click();
        },

        convert_date_format(val) {
            const date = new Date(val);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");

            return [year, month, day].join("-");
        },
        caps(str) {
            if (str == "" || str == null) {
                return "---";
            } else {
                let res = str.toString();
                return res.replace(/\b\w/g, (c) => c.toUpperCase());
            }
        },
        onPageChange() {
            this.getDataFromApi();
        },

        getRelaventColor(status) {
            switch (parseInt(status)) {
                case 1:
                    return "booked";
                case 2:
                    return "checkedIn";
                default:
                    return "checkedOut";
            }
        },

        getRelaventStatus(status) {
            switch (parseInt(status)) {
                case 1:
                    return "booked";
                case 2:
                    return "checkedIn";
                case 3:
                    return "checkedOut";
                default:
                    return "checkedOut";
            }
        },

        get_payment(item) {
            this.checkData = item;
            this.payingDialog = true;
        },

        closeDialogs() {
            this.payingDialog = false;
        },

        process(type, model) {
            let newSource;

            if (this.type == "Walking") {
                newSource = "walking";
            } else if (this.type == "Select All") {
                newSource = "";
            } else {
                newSource = this.source;
            }

            let comId = this.$auth.user.company.id; //company id
            let from = this.from_date;
            let to = this.to_date;
            let search = this.search;
            let guest_mode = this.guest_mode;

            // http://192.168.2.210:8000/api/up_coming_reservation_list?page=1&per_page=30&company_id=2&search=&from=&to=&source=

            let url =
                process.env.BACKEND_URL +
                `${type}?company_id=${comId}&from=${from}&to=${to}&search${search}&source${newSource}&r_type=${model}&guest_mode=${guest_mode}`;
            console.log(url);
            let element = document.createElement("a");
            element.setAttribute("target", "_blank");
            element.setAttribute("href", `${url}`);
            document.body.appendChild(element);
            element.click();
        },
        toggleFilter() {
            this.isFilter = !this.isFilter;
        },
        clearFilters() {
            this.filters = {};

            this.isFilter = false;
            this.getDataFromApi();
        },
        applyFilters() {
            this.getDataFromApi();
        },
        updateIndex(page) {

            this.currentPage = page;
            this.cumulativeIndex = (page - 1) * this.perPage;


        },
        itemIndex(item) {
            return this.data.indexOf(item);
        },
        reload() {

            this.getDataFromApi(this.endpoint, 1);
        },
        getDataFromApi(url = this.endpoint, customPage = 0) {

            if (this.from_date && this.to_date) {
                // :items="type == 'Online' ? sources : agentList"

                let newSource;

                if (this.type == "Walking") {
                    newSource = "walking";
                } else if (this.type == "Select All") {
                    newSource = "";
                } else {
                    newSource = this.source;
                }
                this.loading = true;

                let { sortBy, sortDesc, page, itemsPerPage } = this.options;

                let sortedBy = sortBy ? sortBy[0] : "";
                let sortedDesc = sortDesc ? sortDesc[0] : "";
                if (customPage == 1) page = 1;
                this.currentPage = page;

                let options = {
                    params: {
                        page: page,
                        sortBy: sortedBy,
                        sortDesc: sortedDesc,
                        per_page: itemsPerPage,
                        company_id: this.$auth.user.company.id,
                        search: this.search,
                        guest_mode: this.guest_mode,
                        from: this.from_date,
                        to: this.to_date,
                        source: newSource,
                        ...this.filters,
                    },
                };

                this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
                    this.data = data.data;
                    this.pagination.current = data.current_page;
                    this.pagination.total = data.last_page;
                    this.loading = false;
                    this.totalRowsCount = data.total;
                    this.currentPage = page;
                    this.perPage = itemsPerPage;
                });

            }
        },
        getHours(time) {
            let arry = this.hours.find(hour => hour.id === time);
            if (arry) return arry.name;
            else return '';
        },
        searchIt() {
            if (this.search.length == 0) {
                this.getDataFromApi();
            } else if (this.search.length > 2) {
                this.getDataFromApi();
            }
        },
    },
};
</script>
<style scoped src="@/assets/dashtem.css"></style>
  
