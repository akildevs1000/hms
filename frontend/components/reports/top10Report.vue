<template>
    <div v-if="can('management_top_10_customers_access') && can('management_top_10_customers_view')">


        <v-dialog v-model="viewCustomerDialog" max-width="60%">
            <v-card>
                <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
                    <span>Customer History</span>
                    <v-spacer></v-spacer>
                    <v-icon dark class="pa-0" @click="viewCustomerDialog = false">mdi-close</v-icon>
                </v-toolbar>
                <v-container class="mt-0 pt-0">
                    <CustomerIndex :customer_id="customer_id" :edit_mode="false"
                        @close-dialog="viewCustomerDialog = false" />
                </v-container>
                <v-card-actions> </v-card-actions>
            </v-card>
        </v-dialog>
        <div>
            <v-row>
                <v-col cols="8">
                    <v-toolbar class="rounded-2" color="background" dense flat dark>
                        <span> Customer Wise Report </span>
                        <v-spacer></v-spacer>
                        <v-tooltip top color="primary">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                                    @click="process('revenue_customer_wise_report_print', endpoint)">
                                    <v-icon class="white--text">mdi-printer-outline</v-icon>
                                </v-btn>
                            </template>
                            <span>PRINT</span>
                        </v-tooltip>

                        <v-tooltip top color="primary">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                                    @click="process('revenue_customer_wise_report_download', endpoint)">
                                    <v-icon class="white--text">mdi-download-outline</v-icon>
                                </v-btn>
                            </template>
                            <span> DOWNLOAD </span>
                        </v-tooltip>
                    </v-toolbar>
                    <v-data-table dense :headers="headers_table" :items="data_table" :loading="loading" class="elevation-1"
                        :disable-pagination="true" :hide-default-footer="true">

                        <template v-slot:item.sno="{ item, index }">

                            {{ currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : '' }}
                        </template>

                        <template v-slot:item.name="{ item }">
                            <a @click="getToCheckoutPage(item)"> {{ item.title }}</a> </template>

                        <template v-slot:item.phone_number="{ item }">
                            {{ item.customer.contact_no }}
                        </template>
                        <template v-slot:item.no_of_visits="{ item }">
                            {{ item.number_of_visits }}
                        </template>
                        <template v-slot:item.no_of_rooms="{ item }">
                            {{ getRoomsCount(item.rooms) }}
                        </template>
                        <template v-slot:item.revenue="{ item }">
                            {{ getPriceFormat(item.customer_total_price) }}
                        </template>

                        <template v-slot:item.percentage="{ item }">
                            {{ getPercentage(item.customer_total_price) }} %
                        </template>
                        <template slot="body.append">
                            <tr>
                                <td class="text-right  font-weight-bold" colspan="3">TOTAL</td>
                                <td class="text-right font-weight-bold">{{ total_visits }}</td>
                                <td class="text-right font-weight-bold">{{ total_rooms }}</td>
                                <td class="text-right font-weight-bold">{{ getPriceFormat(total_price) }}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </template>

                    </v-data-table>

                </v-col>
                <v-col cols="4">

                    <ApexCharts :options="chartOptions" ref="realtimeChart" :series="series" :height="400"
                        chart-id="pieChart" :key="chartKey" />
                </v-col>
            </v-row>


        </div>
    </div>
    <NoAccess v-else />
</template>
  
<script>

export default {
    props: ['filter_from_date', 'filter_to_date'],

    data() {
        return {
            cumulativeIndex: 1,
            perPage: 20,
            currentPage: 1,

            viewCustomerDialog: false,
            customer_id: '',
            // menu_from_filter: '',
            // filter_from_date: '',

            // menu_to_filter: '',
            // filter_to_date: '',
            total_rooms: 0,
            total_visits: 0,
            total_price: 0,
            data_table: [],
            grandTotal: [],
            totalRowsCount: 0,
            series: [],
            colors: [],

            series: [],

            chartOptions: {
                chart: {
                    width: 380,
                    type: "pie",
                },
                labels: [],// ["Sold", "Unsold"],
                colors: [],// ["#228B22", "#D71921"], // set custom colors
                customLabel: [],
                // plotOptions: {
                //   pie: {
                //     dataLabels: {
                //       offset: -5,
                //     },
                //   },
                // },
                legend: {
                    show: false,
                },
                dataLabels: {
                    formatter(val, opts) {
                        const name = opts.w.globals.labels[opts.seriesIndex];
                        return [name, val.toFixed(1) + "%"];
                    },
                },

                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function (val, opts) {
                            return opts.config.customLabel[opts.seriesIndex]
                        },
                        title: {
                            formatter: function (seriesName) {
                                return ''
                            }
                        }
                    }
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200,
                            },
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                ],
            },
            // -------------------end pie chart ----------------
            barChartOptions: {

                chart: {
                    type: "bar",
                    id: 'basic-bar'

                },
                xaxis: {
                    categories: []
                },
                yaxis: {
                    max: 100 // Set the maximum value of the y-axis to 100
                },

                plotOptions: {
                    bar: {
                        distributed: true,
                        dataLabels: {
                            position: 'top', // Set the position of the labels on top of the bars
                            formatter: function (val) {
                                return val + '111 %'; // Customize the label format, e.g., add a percentage sign
                            }
                        }
                    }
                },
                colors: [],
                legend: {
                    show: false
                },
            },
            series: [{
                name: 'series-1',
                data: []//[30, 40, 45, 50, 49, 60, 70, 91]

            }],

            Model: "Report",
            endpoint: "get_occupancy_rate_by_month",
            from_date: "",
            from_menu: false,
            loading: false,
            to_date: "",
            to_menu: false,

            vertical: false,
            activeTab: 0,
            chart: {},
            chartKey: 0,
            year: "",
            years: "",
            month: "",
            months: [
                { id: '', name: "All Months" },
                { id: 1, name: "January" },
                { id: 2, name: "February" },
                { id: 3, name: "March" },
                { id: 4, name: "April" },
                { id: 5, name: "May" },
                { id: 6, name: "June" },
                { id: 7, name: "July" },
                { id: 8, name: "August" },
                { id: 9, name: "September" },
                { id: 10, name: "October" },
                { id: 11, name: "November" },
                { id: 12, name: "December" },
            ],

            options: {},
            data: [],
            headers: [
                {
                    text: "#",
                },
                {
                    text: "Date",
                },
                {
                    text: "Sold (%)",
                },
                {
                    text: "Unsold (%)",
                },
            ],
            headers_table: [
                {
                    text: "#",
                    align: "left",
                    sortable: false,
                    key: "sno",
                    filterable: true,
                    value: "sno",
                },
                {
                    text: "Name",
                    align: "left",
                    sortable: false,
                    filterable: false,
                    value: "name",
                },
                {
                    text: "Phone Number",
                    align: "right",
                    sortable: false,

                    filterable: false,
                    value: "phone_number",
                },
                {
                    text: "Visits",
                    align: "right",
                    sortable: false,

                    filterable: false,
                    value: "no_of_visits",
                },
                {
                    text: "Rooms",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "no_of_rooms",
                },
                {
                    text: "Booking Amount",
                    align: "right",
                    sortable: false,

                    filterable: false,
                    value: "revenue",
                },
                // {
                //     text: "%",
                //     align: "right",
                //     sortable: false,

                //     filterable: false,
                //     value: "percentage",
                // },

            ],

        };
    },
    watch: {

        filter_from_date() {

            this.getDataFromApi();

        },
        filter_to_date() {
            this.getDataFromApi();

        }
    },
    created() {
        this.loading = true;

        // this.getYears();
        // //this.month = new Date().getMonth() + 1;
        // //this.year = new Date().getFullYear();

        // this.month = new Date().getMonth();
        // this.year = new Date().getFullYear();

        // this.filter_from_date = this.formatDate(new Date(this.year, 0, 1));
        // this.filter_to_date = this.formatDate(new Date(this.year, this.month + 1, 0));

        this.getDataFromApi();
    },
    mounted() {
        // this.getDataFromApi();

    },
    // watch: {

    //   options: {
    //     handler() {
    //       this.getMonthlyWiseData();
    //     },
    //     deep: true,
    //   },
    // },
    methods: {
        updateIndex(page) {

            this.currentPage = page;
            this.cumulativeIndex = (page - 1) * this.perPage;


        },
        itemIndex(item) {
            return this.data_table.indexOf(item);
        },
        formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero-based
            var year = date.getFullYear();

            return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
        },
        getToCheckoutPage(item) {
            // this.$store.dispatch('setData', { customer_name: item.first_name });
            // this.$router.push('reservation/check_out');
            this.customer_id = item.customer_id;
            this.viewCustomerDialog = true;
        },
        getPriceFormat(amount) {

            amount = parseFloat(amount);
            return amount.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        },
        getPercentage(customer_total_price) {
            if (this.total_price > 0) return Math.round((customer_total_price / this.total_price) * 100);
            else return 0;
        },
        getRoomsCount(rooms) {
            return rooms.split(",").length;
        },
        getColorCode(index) {
            return this.colors[index].color;
        },
        onPageChange() {
            this.getDataFromApi();
        },

        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
            );
        },

        commonMethod() {
            this.getDataFromApi();
        },

        getDaysInMonth(month = 2, year = new Date().getFullYear()) {

        },

        forceChartRerender() {
            this.chartKey += 1;
        },
        getYears() {
            const year = new Date().getFullYear();
            this.years = Array.from({ length: 10 }, (_, i) => year - i);

        },
        getDataFromApi(url = this.endpoint) {


            let { sortBy, sortDesc, page, itemsPerPage } = this.options;

            let sortedBy = sortBy ? sortBy[0] : "";
            let sortedDesc = sortDesc ? sortDesc[0] : "";


            this.loading = true;
            let options = {
                params: {
                    page: page,
                    sortBy: sortedBy,
                    sortDesc: sortedDesc,
                    //per_page: itemsPerPage,
                    company_id: this.$auth.user.company.id,
                    month: this.month,
                    year: this.year,
                    filter_from_date: this.filter_from_date,
                    filter_to_date: this.filter_to_date,

                },
            };



            this.$axios.get('get_report_top_ten_customers', options).then(({ data }) => {

                this.data_table = data.data;
                // this.total_price = data.total_price;
                this.colors = data.colors;
                this.loading = false;

                this.grandTotal = data.grandTotal;

                this.series.splice(0, this.series.length);


                this.total_rooms = 0;
                this.total_visits = 0;
                let counter = 0;
                this.data_table.forEach(item => {


                    let rooms = this.getRoomsCount(item.rooms);
                    if (counter <= 9) {
                        // this.series.push(Math.round((item.customer_total_price / data.total_price) * 100));
                        this.series.push(Math.round((item.customer_total_price)));
                        this.chartOptions.labels[counter] = item.title;
                        this.chartOptions.customLabel[counter] = item.title + "<br/>Total Amount: " + this.getPriceFormat(item.customer_total_price) + "<br/>No.of Visits: " + item.number_of_visits + "<br/>No.of Rooms: " + rooms;

                        this.chartOptions.colors[counter] = data.colors[counter].color;

                    }

                    this.total_rooms += rooms;
                    this.total_visits += item.number_of_visits;
                    this.total_price += Math.round((item.customer_total_price));
                    counter++;



                });

                this.loading = false;

            });
        },
        process(type, model) {

            let url =
                process.env.BACKEND_URL +
                `${type}?company_id=${this.$auth.user.company.id}&filter_from_date=${this.filter_from_date}&filter_to_date=${this.filter_to_date}`;
            console.log(url);
            let element = document.createElement("a");
            element.setAttribute("target", "_blank");
            element.setAttribute("href", `${url}`);
            document.body.appendChild(element);
            element.click();
        },
    },
};
</script>
  