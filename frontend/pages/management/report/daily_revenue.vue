<template>
    <div v-if="can('management_revenue_report_access') && can('management_revenue_report_view')">

        <v-row>

            <v-col md="2">
                <v-select :items="years" label="Select Year" outlined dense v-model="year"
                    @change="getDataFromApi()"></v-select>
            </v-col>
            <v-col md="2">
                <v-select :items="months" lable="Select Month" outlined dense v-model="month" item-value="id"
                    item-text="name" @change="getDataFromApi()"></v-select> </v-col>
        </v-row>

        <div>
            <v-card class="mb-5" elevation="0">
                <v-toolbar class="rounded-md mb-2 white--text" color="background" dense flat>
                    <v-col cols="12">
                        <span> Revenue Report - Day wise</span>


                        <v-tooltip top color="primary">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                                    <v-icon color="white" class="ml-2" @click="getDataFromApi" dark>mdi mdi-reload</v-icon>
                                </v-btn>
                            </template>
                            <span>Reload</span>
                        </v-tooltip>
                    </v-col>
                </v-toolbar>
                <v-row>
                    <v-col cols="12">
                        <ApexCharts v-model="chart" ref="realtimeChart" :options="barChartOptions" :series="barSeries"
                            chart-id="bar" :height="400" :key="chartKey" />
                    </v-col>
                    <v-col cols="12">
                        <v-data-table dense :headers="headers_table" :items="data_table" :loading="loading" :footer-props="{
                            itemsPerPageOptions: [31],
                        }" class="elevation-1" :hide-default-footer="true">

                            <template v-slot:item.date="{ item }">
                                <a @click="goToNightAuditReport(item)">{{ item.date }}</a>
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
                                    <td class="text-center  font-weight-bold" colspan="2">TOTAL</td>
                                    <td class="text-right font-weight-bold"> {{ grandTotal.totalRooms }}</td>
                                    <td class="text-right font-weight-bold">{{ grandTotal.totalIncome }}</td>
                                    <td class="text-right font-weight-bold">{{ grandTotal.totalExpenses }}</td>
                                    <td class="text-right font-weight-bold">{{ grandTotal.totalManagementExpenses }}</td>
                                    <td class="text-right font-weight-bold">{{ grandTotal.totalProfit }}</td>
                                    <td class="text-right font-weight-bold">{{ grandTotal.totalPercentage }}%</td>
                                </tr>
                            </template>

                        </v-data-table>

                    </v-col>

                </v-row>

            </v-card>
        </div>
    </div>
    <NoAccess v-else />
</template>
  
<script>
import SourceReport from '../../../components/bookingSource/SourceReport.vue';

export default {
    components: {
        SourceReport,
    },
    data() {
        return {


            options: {},
            data_table: [],
            grandTotal: [],
            totalRowsCount: 0,
            series: [],


            barSeries: [
                {
                    name: "Percentage %",
                    data: [],
                },
            ],
            barChartOptions: {
                customLabel: [],
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
                    text: "Room Sold",
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
                    text: "Non.Mng Expenses",
                    align: "right",
                    sortable: true,
                    key: "employee_id",
                    filterable: false,
                    value: "expenses",
                },
                {
                    text: "Management Expenses",
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
        this.loading = true;

        this.getYears();
        this.month = new Date().getMonth() + 1;
        this.year = new Date().getFullYear();

        let filters = this.$store.getters.getDataToSend;
        if (filters.month) {
            this.month = parseInt(filters.month);
        }

        if (filters.year) {
            this.year = parseInt(filters.year);
        }
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
                    per_page: itemsPerPage,
                    company_id: this.$auth.user.company.id,
                    month: this.month,
                    year: this.year,

                },
            };

            this.$axios.get('get_report_daily_wise_group', options).then(({ data }) => {

                this.data_table = data.data;
                this.loading = false;
                this.totalRowsCount = 12;
                this.grandTotal = data.grandTotal;

                let counter = 0;
                this.data_table.forEach(item => {

                    this.barSeries[0]["data"][counter] = item.percentage;
                    this.barChartOptions.xaxis.categories[counter] = item.month;
                    this.barChartOptions.colors[counter] = item.color;
                    this.barChartOptions.customLabel[counter] = '<table><tr><td>Percentage</td><td> : ' + item.percentage + '%</td></tr> '
                        + "<tr><td>Rooms Sold</td><td> :  " + item.sold + '</td></tr> '
                        + "<tr><td>Income</td><td style='text-align:right;color:green'> :  " + item.income + '</td></tr> '
                        + "<tr><td>Non-Mng Expenses</td><td style='text-align:right;color:red'>   - " + item.expenses + '</td></tr> '
                        + "<tr><td>Management Expenses</td><td style='text-align:right;color:red'>  - " + item.management_expenses + '</td></tr> '
                        + "<tr><td>Proffit</td><td style='text-align:right; '>   = " + item.profit + '</td></tr> </table>'

                    counter++;

                });
                try {
                    this.$refs.realtimeChart.updateSeries([{
                        data: this.barSeries[0].data,
                    }], false, true);
                }
                catch (e) { }

                this.loading = false;

            });
        },
    },
};
</script>
  