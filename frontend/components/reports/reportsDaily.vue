<template>
    <div v-if="can('management_revenue_report_access') && can('management_revenue_report_view')">

        <v-row>
            <div id="chart">

            </div>
            <v-col cols="12">
                <ApexCharts type="bar" height="350" ref="realtimeChart" :options="barChartOptionsNew" :series="barSeriesNew"
                    :key="chartKey"></ApexCharts>
                <!-- <ApexCharts v-model="chart" ref="realtimeChart" :options="barChartOptions" :series="barSeries"
                            chart-id="bar" :height="400" :key="chartKey" /> -->
            </v-col>

            <v-col cols="12">

                <v-toolbar class="rounded-2" color="background" dense flat dark>
                    <span> Month wise Report </span>
                    <v-spacer></v-spacer>
                    <v-tooltip top color="primary">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn class="ma-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
                                @click="process('revenue_daily_report_print', endpoint)">
                                <v-icon class="white--text">mdi-printer-outline</v-icon>
                            </v-btn>
                        </template>
                        <span>PRINT</span>
                    </v-tooltip>

                    <v-tooltip top color="primary">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
                                @click="process('revenue_daily_report_download', endpoint)">
                                <v-icon class="white--text">mdi-download-outline</v-icon>
                            </v-btn>
                        </template>
                        <span> DOWNLOAD </span>
                    </v-tooltip>
                </v-toolbar>

                <v-data-table dense :headers="headers_table" ref="dataTable" :items="data_table" :loading="loading"
                    :footer-props="{
                        itemsPerPageOptions: [1000],
                    }" class="elevation-1" :hide-default-footer="true">

                    <template v-slot:item.date="{ item }">
                        <!-- <a @click="goToNightAuditReport(item)">{{ item.date }}</a> -->
                        {{ changeDateFormat(item.date) }}
                    </template>


                    <template v-slot:item.room_sold="{ item }">
                        {{ item.sold }}
                    </template>
                    <template v-slot:item.income="{ item }">
                        {{ item.income }}
                    </template>
                    <template v-slot:item.expenses="{ item }">
                        {{ item.expenses }}
                    </template>
                    <template v-slot:item.management_expenses="{ item }">
                        {{ item.management_expenses }}
                    </template>
                    <template v-slot:item.profit="{ item }">
                        {{ item.profit }}
                    </template>
                    <template v-slot:item.percentage="{ item }">
                        {{ item.percentage }} %
                    </template>
                    <template slot="body.append">
                        <tr>
                            <td class="text-center  font-weight-bold">TOTAL</td>
                            <td class="text-right font-weight-bold"> {{ grandTotal.totalRooms }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalIncome }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalExpenses }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalManagementExpenses }}
                            </td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalProfit }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalPercentage }}%</td>
                        </tr>
                    </template>

                </v-data-table>

            </v-col>

        </v-row>


    </div>
    <NoAccess v-else />
</template>
  
<script>

export default {
    props: ['filter_from_date', 'filter_to_date'],
    data() {
        return {
            // menu_from_filter: '',
            // filter_from_date: '',

            // menu_to_filter: '',
            // filter_to_date: '',

            barSeriesNew: [
                {
                    name: "Income",
                    data: [],
                },
                {
                    name: "Expences",
                    data: [],
                }, {
                    name: "Sold",
                    data: [],
                },
            ],
            barChartOptionsNew: {
                title: {
                    text: 'Daily Wise Report',
                },
                customLabel: [],
                chart: {
                    type: "bar",
                    id: 'DailyReport'

                },
                colors: ['#0C9241', '#FF0000', '#0815cb'],

                plotOptions: {
                    bar: {
                        horizontal: false,
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: false,

                    style: {
                        fontSize: '12px',
                        colors: ['#fff']
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: []
                },
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: function (val, opts) {


                            return opts.w.config.customLabel[opts.dataPointIndex]
                        },
                        title: {
                            formatter: function (seriesName) {
                                return ''
                            }
                        }
                    }
                },
            },
            options: {},
            data_table: [],
            grandTotal: [],
            totalRowsCount: 0,
            series: [],
            barSeries: [],

            // barSeries: [
            //     {
            //         name: "Percentage 1 %",
            //         data: [],
            //     },
            //     {
            //         name: "Percentage 2 %",
            //         data: [],
            //     },
            // ],
            // barChartOptions: {
            //     customLabel: [],
            //     chart: {
            //         type: "bar",
            //         id: 'basic-bar'

            //     },
            //     xaxis: {
            //         categories: []
            //     },
            //     yaxis: {
            //         max: 100 // Set the maximum value of the y-axis to 100
            //     },
            //     tooltip: {
            //         enabled: true,
            //         y: {
            //             formatter: function (val, opts) {


            //                 return opts.w.config.customLabel[opts.dataPointIndex]
            //             },
            //             title: {
            //                 formatter: function (seriesName) {
            //                     return ''
            //                 }
            //             }
            //         }
            //     },
            //     plotOptions: {
            //         bar: {
            //             distributed: true,
            //             dataLabels: {
            //                 position: 'top', // Set the position of the labels on top of the bars
            //                 formatter: function (val) {
            //                     return val + '111 %'; // Customize the label format, e.g., add a percentage sign
            //                 }
            //             }
            //         }
            //     },
            //     colors: [],
            //     legend: {
            //         show: false
            //     },
            // },
            // series: [{
            //     name: 'series-1',
            //     data: []//[30, 40, 45, 50, 49, 60, 70, 91]

            // }],

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
            // headers: [
            //     {
            //         text: "#",
            //     },
            //     {
            //         text: "Date",
            //     },
            //     {
            //         text: "Sold (%)",
            //     },
            //     {
            //         text: "Unsold (%)",
            //     },
            // ],
            headers_table: [
                {
                    text: "Date",
                    align: "left",
                    sortable: true,
                    key: "date",
                    filterable: false,
                    value: "date",
                },


                {
                    text: "Sold",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "room_sold",
                },
                {
                    text: "Income",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "income",
                },
                {
                    text: "N_M Expenses",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "expenses",
                },
                {
                    text: "M Expenses",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "management_expenses",
                },
                {
                    text: "Profit",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "profit",
                },
                {
                    text: "%",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "percentage",
                },
            ],

        };
    },
     
    created() {
        this.month = new Date().getMonth();
        this.year = new Date().getFullYear();

        this.loading = true;

    },
    mounted() {
        this.getDataFromApi();

    },
    computed: {
        // Use a computed property to calculate and return the filtered items

    },
    watch: {

        filter_from_date() {

            this.getDataFromApi();

        },
        filter_to_date() {
            this.getDataFromApi();

        }
    },
    methods: {
        formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero-based
            var year = date.getFullYear();

            return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
        },
        printTable() {

            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write('<center><h4>Revenue Report - Day wise ' + this.months[this.month].name + ' - ' + this.year + '</h4></center>');
            printWindow.document.write(document.querySelector('.v-data-table').outerHTML);
            printWindow.document.write('<style>.text-right{text-align:right;} td,th {border-top:1px solid #DDD;border-left:1px solid #DDD} table{border-right:1px solid #DDD;border-bottom:1px solid #DDD;width:100%} body{width:95%}</style></body></html>');
            printWindow.document.close();
            printWindow.print();
        },
        goToNightAuditReport(item) {
            this.$store.dispatch('setData', { date: item.date });
            this.$router.push({ path: '/management/report/audit' });

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

            this.barSeriesNew = [
                {
                    name: "Income",
                    data: [],
                },
                {
                    name: "Expences",
                    data: [],
                },
            ];
        },
        getYears() {
            const year = new Date().getFullYear();
            this.years = Array.from({ length: 10 }, (_, i) => year - i);

        },
        changeDateFormat(date) {

            var months = [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ];

            var dateParts = date.split("-");
            var year = dateParts[0].slice(2); // Extract last two digits of the year
            var month = months[parseInt(dateParts[1], 10) - 1]; // Month is 0-indexed
            var day = dateParts[2];

            return day + " " + month + " " + year;

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
                    per_page: itemsPerPage,
                    company_id: this.$auth.user.company.id,
                    // month: this.month,
                    // year: this.year,
                    filter_from_date: this.filter_from_date,
                    filter_to_date: this.filter_to_date,

                },
            };
            // this.forceChartRerender();
            this.$axios.get('get_report_daily_wise_group', options).then(({ data }) => {

                this.data_table = data.data;
                this.loading = false;
                // this.totalRowsCount = 12;
                this.grandTotal = data.grandTotal;

                let counter = 0;
                //this.forceChartRerender();
                this.barSeriesNew[0]["data"] = [];
                this.barSeriesNew[1]["data"] = [];
                this.data_table.forEach(item => {

                    this.barSeriesNew[0]["data"][counter] = parseInt(item.income.replaceAll(',', ''));
                    this.barSeriesNew[1]["data"][counter] = parseInt(item.total_expenses.replaceAll(',', ''));
                    this.barSeriesNew[2]["data"][counter] = parseInt(item.sold);
                    this.barChartOptionsNew.xaxis.categories[counter] = item.month + '-' + item.day;
                    // this.barChartOptionsNew.colors[counter] = item.color;
                    this.barChartOptionsNew.customLabel[counter] = "<table>"
                        + "<tr><td>Income</td><td style='text-align:right;color:green'> :  " + item.income + '</td></tr> '
                        + "<tr><td>Non-Mng Expenses</td><td style='text-align:right;color:red'>   - " + item.expenses + '</td></tr> '
                        + "<tr><td>Management Expenses</td><td style='text-align:right;color:red'>  - " + item.management_expenses + '</td></tr> '
                        + "<tr><td>Proffit</td><td style='text-align:right; '>   = " + item.profit + '</td></tr>'
                        + "<tr><td>Rooms Sold</td><td> :  " + item.sold + "</td></tr> "
                        + "</table > "

                    counter++;

                });
                try {
                    this.$refs.realtimeChart.updateSeries([{
                        data: this.barSeriesNew[0].data,
                    }, {
                        data: this.barSeriesNew[1].data,
                    }, {
                        data: this.barSeriesNew[2].data,
                    }], false, true);
                }
                catch (e) { }

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
  