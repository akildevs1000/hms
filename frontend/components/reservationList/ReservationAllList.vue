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
                <div>Dashboard / {{ Model }}</div>
            </v-col>
            <v-col cols="6"> </v-col>
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
                <v-select class="custom-text-box shadow-none" v-model="type" :items="types" dense placeholder="Type" solo
                    flat :hide-details="true" @change="getDataFromApi(endpoint)"></v-select>
            </v-col>

            <v-col xs="12" sm="12" md="2" cols="12">
                <v-select class="custom-text-box shadow-none" v-model="source"
                    :items="type == 'Online' ? sources : agentList" dense item-value="name" item-text="name"
                    placeholder="Sources" solo flat :hide-details="true" @change="getDataFromApi(endpoint)"></v-select>
            </v-col>

            <v-col xs="12" sm="12" md="2" cols="12">
                <v-select class="custom-text-box shadow-none" v-model="guest_mode"
                    :items="['Select All', 'Arrival', 'Departure']" dense placeholder="Type" solo flat :hide-details="true"
                    @change="getDataFromApi(endpoint)"></v-select>
            </v-col>

            <v-col md="2">
                <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
                    offset-y min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
                            class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
                    </template>
                    <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
                </v-menu>
            </v-col>
            <v-col md="2">
                <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
                    offset-y min-width="auto">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="to_date" readonly v-bind="attrs" v-on="on" dense
                            class="custom-text-box shadow-none" solo flat label="To" :hide-details="true"></v-text-field>
                    </template>
                    <v-date-picker v-model="to_date" @input="to_menu = false" @change="commonMethod"></v-date-picker>
                </v-menu>
            </v-col>
        </v-row>

        <v-card class="mb-5 rounded-md mt-3" elevation="0">
            <v-toolbar class="rounded-md" color="background" dense flat dark>
                <span> {{ Model }} List</span>
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
                    itemsPerPageOptions: [20, 50, 100, 500, 1000],
                }" class="elevation-1" :server-items-length="totalRowsCount" @page-change="updateIndex">

                <template v-slot:item.sno="{ item, index }">
                    {{ currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : '' }}
                </template>
                <template v-slot:item.res_number="item">


                    <span class="blue--text" @click="goToRevView(item.item)" style="cursor: pointer">
                        {{ item.item.reservation_no || "---" }}
                    </span>
                </template>
                <template v-slot:item.source="item">
                    {{ item.item.source || "---" }}
                </template>
                <template v-slot:item.rooms="item">
                    <span v-for="(room, index) in item.item.booked_rooms" :key="index">
                        {{ room.room_no }}
                        {{ item.item.booked_rooms.length - 1 == index ? "" : "," }}
                    </span>
                </template>
                <template v-slot:item.reference="item">
                    {{ item.item.reference_no || "---" }}
                </template>
                <template v-slot:item.guest="item">
                    {{ item && item.item.customer.first_name || "---" }}
                </template>
                <template v-slot:item.check_in="item">
                    {{ convert_date_format(item.item.check_in) }}
                </template>
                <template v-slot:item.check_out="item">
                    {{ convert_date_format(item.item.check_out) }}
                </template>
                <template v-slot:item.total="item">
                    {{ item.item.total_price }}
                </template>
                <template v-slot:item.posting="item">
                    {{ item.item.total_posting_amount || 0 }}
                </template>
                <template v-slot:item.paid="item">
                    {{ item.item.paid_amounts || 0 }}
                </template>
                <template v-slot:item.balance="item">
                    {{ item.item.balance || 0 }}
                </template>
                <template v-slot:item.res_date="item">
                    {{ item.item.booking_date }}
                </template>
                <template v-slot:item.view="item">
                    <v-icon @click="viewCustomerBilling(item.item)" x-small color="primary" class="mr-2">
                        mdi-eye
                    </v-icon>
                </template>
                <template v-slot:item.payment="item">
                    <v-icon @click="get_payment(item.item)" x-small color="primary" class="mr-2">
                        mdi-cash-multiple
                    </v-icon>
                </template>
                <template v-slot:item.invoice="item">
                    <v-icon @click="redirect_to_invoice(item.item.id)" x-small color="primary" class="mr-2">
                        mdi-cash-multiple
                    </v-icon>
                </template>
            </v-data-table>
            <!-- <table>
                <tr>
                    <th style="font-size: 13px" v-for="(item, index) in headers" :key="index">
                        <span v-html="item.text"></span>
                    </th>
                </tr>
                <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>
                <tr style="font-size: 13px" v-for="(item, index) in data" :key="index">
                    <td class="ps-3">
                        <b>{{ ++index }}</b>
                    </td>
                    <td class="ps-3">
                        <b>
                            <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer">
                                {{ item.reservation_no || "---" }}
                            </span>
                        </b>
                    </td>
                    <td style="width: 10%;">
                        <span v-for="(room, index) in item.booked_rooms" :key="index">
                            {{ room.room_no }}
                            {{ item.booked_rooms.length - 1 == index ? "" : "," }}
                        </span>
                    </td>
                    <td>{{ item.source }}</td>
                    <td>{{ item.reference_no || "---" }}</td>
                    <td>{{ item && item.customer.first_name || "---" }}</td>
                    <td style="width: 120px">{{ convert_date_format(item.check_in) }}</td>
                    <td style="width: 120px">
                        {{ convert_date_format(item.check_out) }}
                    </td>
                    <td>{{ item.total_price }}</td>
                    <td>{{ item.total_posting_amount || 0 }}</td>
                    <td>{{ item.paid_amounts || 0 }}</td>
                    <td>{{ item.balance || 0 }}</td>

                    <td>{{ item.booking_date }}</td>

                    <td>
                        <v-icon @click="viewCustomerBilling(item)" x-small color="primary" class="mr-2">
                            mdi-eye
                        </v-icon>
                    </td>
                    <td>
                        <v-icon @click="get_payment(item)" x-small color="primary" class="mr-2">
                            mdi-cash-multiple
                        </v-icon>
                    </td>
                    <td>
                        <v-icon @click="redirect_to_invoice(item.id)" x-small color="primary" class="mr-2">
                            mdi-cash-multiple
                        </v-icon>
                    </td>
                </tr>
            </table> -->
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
export default {
    props: ["endpoint", "Model"],
    components: {
        Paying,
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
        headers: [
            { text: "#" },
            { text: "Rev. No" },
            { text: "Rooms" },
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
                key: "employee_id",
                filterable: true,
                value: "res_number",
            },
            {
                text: "Rooms",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "rooms",
            },
            {
                text: "Source",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "source",
            },
            {
                text: "Reference",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "reference",
            },
            {
                text: "Guest",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "guest",
            },
            {
                text: "C/In",
                width: "105px",
                align: "left",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "check_in",
            },
            {
                text: "C/Out",
                align: "left",
                width: "105px",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "check_out",
            },
            {
                text: "Total",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "total",
            },
            {
                text: "Posting",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "posting",
            },
            {
                text: "Paid",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "paid",
            },
            {
                text: "Balance",
                align: "right",
                sortable: false,
                key: "employee_id",
                filterable: true,
                value: "balance",
            },
            {
                text: "Rev. Date",
                align: "left",
                sortable: false,
                width: "105px",
                key: "employee_id",
                filterable: true,
                value: "res_date",
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
        this.getDataFromApi();
        this.get_agents();
        this.get_online();
    },

    methods: {
        can(permission) {
            let user = this.$auth;
            return;
            return (
                (user && user.permissions.some((e) => e.permission == permission)) ||
                user.master
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
            alert(this.cumulativeIndex);
            this.currentPage = page;
            this.cumulativeIndex = (page - 1) * this.perPage;


        },
        itemIndex(item) {
            return this.data.indexOf(item);
        },
        getDataFromApi(url = this.endpoint) {
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
  
