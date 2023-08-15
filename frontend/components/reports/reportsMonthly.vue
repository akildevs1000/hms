<template>
    <div v-if="can('management_revenue_report_access') && can('management_revenue_report_view')">




        <v-row>
            <v-col md="12" lg="6" cols="12">
                <!-- <v-col class="text-right mr-10" mr="10"><v-icon color="blue" class="ml-2" dark @click="printTable">
                  mdi mdi-printer</v-icon> </v-col> -->
                <v-data-table dense :headers="headers_table" :items="data_table" :loading="loading" :footer-props="{
                    itemsPerPageOptions: [1000],
                }" class="elevation-1" :hide-default-footer="true">



                    <template v-slot:item.month_name="{ item }">
                        <a @click="goToDailyReport(item)">{{ item.month }}</a>


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
                            <td class="text-right font-weight-bold">{{ grandTotal.totalManagementExpenses }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalProfit }}</td>
                            <td class="text-right font-weight-bold">{{ grandTotal.totalPercentage }}%</td>
                        </tr>
                    </template>

                </v-data-table>

            </v-col>
            <v-col md="12" lg="6" cols="12">
                <ApexCharts v-model="chart" ref="realtimeChart" height="450" :options="barChartOptionsNew"
                    :series="barSeriesNew" chart-id="bar" :key="chartKey" />
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
            // filter_from_date: '',
            // filter_to_date: '',
            data_table: [],
            grandTotal: [],
            totalRowsCount: 0,
            series: [],
            barSeriesNew: [
                {
                    name: "Income",
                    data: [],
                },
                {
                    name: "Expences",
                    data: [],
                },
                {
                    name: "Sold",
                    data: [],
                },
            ],
            barChartOptionsNew: {
                customLabel: [],
                chart: {
                    type: "bar",
                    id: 'MonthlyReport'

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

            // barSeries: [
            //   {
            //     name: "Income",
            //     data: [],
            //   },
            //   {
            //     name: "Expences",
            //     data: [],
            //   },
            // ],
            // barChartOptions: {
            //   customLabel: [],
            //   chart: {
            //     type: "bar",
            //     id: 'basic-bar'

            //   },
            //   xaxis: {
            //     categories: []
            //   },
            //   yaxis: {
            //     max: 100 // Set the maximum value of the y-axis to 100
            //   },

            //   tooltip: {
            //     enabled: true,
            //     y: {
            //       formatter: function (val, opts) {


            //         return opts.w.config.customLabel[opts.dataPointIndex]
            //       },
            //       title: {
            //         formatter: function (seriesName) {
            //           return ''
            //         }
            //       }
            //     }
            //   },
            //   plotOptions: {
            //     bar: {
            //       distributed: true,
            //       dataLabels: {
            //         position: 'top', // Set the position of the labels on top of the bars
            //         formatter: function (val) {
            //           return val + '  %'; // Customize the label format, e.g., add a percentage sign
            //         }
            //       }
            //     }
            //   },
            //   colors: ['#0C9241', '#FF0000'],
            //   legend: {
            //     show: false
            //   },
            // },
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
                    text: "Month",
                    align: "left",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "month_name",
                },
                {
                    text: "Sold",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "room_sold",
                },
                {
                    text: "Income",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "income",
                },
                {
                    text: "N_M Expenses",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "expenses",
                },
                {
                    text: "M Expenses",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "management_expenses",
                },
                {
                    text: "Profit",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "profit",
                },
                {
                    text: "%",
                    align: "right",
                    sortable: false,
                    key: "employee_id",
                    filterable: false,
                    value: "percentage",
                },
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


    },
    mounted() {
        this.getYears();
        // this.month = new Date().getMonth();
        // this.year = new Date().getFullYear();

        // this.filter_from_date = this.formatDate(new Date(this.year, 0, 1));
        // this.filter_to_date = this.formatDate(new Date(this.year, this.month + 1, 0));


        this.getDataFromApi();
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
        formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero-based
            var year = date.getFullYear();

            return year + '-' + (month < 10 ? '0' : '') + month;//+ '-' + (day < 10 ? '0' : '') + day;
        },
        printTable() {


            let htmlHeaderContent = '<center><h4>Revenue Report - Month wise -  ' + this.year + '</h4></center>';
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(htmlHeaderContent);
            printWindow.document.write(document.querySelector('.v-data-table').outerHTML);
            printWindow.document.write('<style>.text-right{text-align:right;} td,th {border-top:1px solid #DDD;border-left:1px solid #DDD} table{border-right:1px solid #DDD;border-bottom:1px solid #DDD;width:100%} body{width:95%}</style></body></html>');
            printWindow.document.close();
            printWindow.print();
        },
        onPageChange() {
            this.getDataFromApi();
        },
        goToDailyReport(item) {

            this.$store.dispatch('setData', { year: item.year_number, month: item.month_number });
            this.$router.push({ path: '/management/report/daily_revenue' });

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

                    //year: this.year,
                    filter_from_date: this.filter_from_date,
                    filter_to_date: this.filter_to_date,
                },
            };

            this.$axios.get('get_report_monthly_wise_group', options).then(({ data }) => {

                this.data_table = data.data;
                this.loading = false;
                this.totalRowsCount = data.totalRowsCount;;
                this.grandTotal = data.grandTotal;

                let counter = 0;
                this.data_table.forEach(item => {

                    this.barSeriesNew[0]["data"][counter] = parseInt(item.income.replaceAll(',', ''));
                    this.barSeriesNew[1]["data"][counter] = parseInt(item.total_expenses.replaceAll(',', ''));
                    this.barSeriesNew[2]["data"][counter] = parseInt(item.sold);
                    this.barChartOptionsNew.xaxis.categories[counter] = item.month;
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
    },
};
</script>
  