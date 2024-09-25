<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <!-- <v-row>
      <v-col col="3" xs="12" sm="12" md="2" cols="12">
        <v-card style="background-color: #3366cc" dark>
          <v-card-text>
            <div class="white--text">Total</div>
            <div>{{ totalRowsCount }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row> -->

    <v-container fluid>
      <v-row class="">
        <v-col v-for="(item, index) in stats" :key="index" cols="12" md="2">
          <v-card rounded="lg" outlined class="pa-4">
            <v-row no-gutter>
              <v-col cols="2" class="pt-7">
                <v-icon size="40" color="black">{{ item.icon }}</v-icon>
              </v-col>
              <v-col class="text-center">
                <h1><AssetsTextLabel color="black" :label="item.count" /></h1>
                <AssetsTextLabel color="black" :label="item.label" />
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="4" class="text-right">
          <v-row>
            <v-col>
              <v-text-field
                class=""
                label="Search..."
                dense
                outlined
                flat
                append-icon="mdi-magnify"
                @input="searchIt"
                v-model="search"
                hide-details
              ></v-text-field>
            </v-col>
            <v-col>
              <FilterDateRange @filter-attr="filterAttr" />
            </v-col>
          </v-row>
        </v-col>
        <v-col class="text-right">
          <AssetsIcon
            icon="printer-outline"
            @click="process('reservation_report_print', endpoint)"
          />
          &nbsp;
          <AssetsIcon
            icon="download-outline"
            @click="process('reservation_report_download', endpoint)"
          />
        </v-col>
      </v-row>
    </v-container>

    <v-dialog v-model="payingDialog" persistent max-width="1000px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="payingDialog = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-card-text>
          <Paying
            :BookingData="checkData"
            @close-dialog="closeDialogs"
          ></Paying>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <!-- <v-alert class="rounded-md" dense flat>
        <v-row>
          <v-col> <AssetsTextLabel :label="`${Model} List`" /></v-col>
        </v-row>
      </v-alert> -->
      <v-data-table
        hide-default-header
        dense
        small
        :headers="headers_table"
        :items="data"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [50, 100, 500, 1000],
        }"
        class="elevation-0"
        :server-items-length="totalRowsCount"
      >
        <template v-slot:header="{ props: { headers } }">
          <AssetsTableHeader :cols="headers" />
        </template>

        <template v-slot:item.sno="{ item, index }">
          <AssetsTextLabel :label="index + 1" />
          <!-- {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + itemIndex(item))
              : ""
          }} -->
        </template>
        <template v-slot:item.res_number="item">
          <span
            class="blue--text"
            @click="goToRevView(item.item)"
            style="cursor: pointer"
          >
            <AssetsTextLabel :label="item.item.reservation_no || `---`" />
          </span>
        </template>
        <template v-slot:item.source="item">
          <AssetsTextLabel :label="item.item.source || `---`" />
        </template>
        <template v-slot:item.rooms="item">
          <span v-for="(room, index) in item.item.booked_rooms" :key="index">
            <AssetsTextLabel :label="room.room_no" />
            <AssetsTextLabel
              :label="item.item.booked_rooms.length - 1 == index ? `` : `,`"
            />
          </span>
        </template>
        <template v-slot:item.reference="item">
          <AssetsTextLabel :label="item.item.reference_no || `---`" />
        </template>
        <template v-slot:item.guest="item">
          <AssetsTextLabel :label="item.item.customer.first_name || `---`" />
        </template>
        <template v-slot:item.check_in="item">
          <AssetsTextLabel :label="convert_date_format(item.item.check_in)" />
        </template>
        <template v-slot:item.check_out="item">
          <AssetsTextLabel :label="convert_date_format(item.item.check_out)" />
        </template>
        <template v-slot:item.total="item">
          <AssetsTextLabel
            :label="$utils.currency_format(item.item.total_price)"
          />
        </template>
        <template v-slot:item.posting="item">
          <AssetsTextLabel
            :label="$utils.currency_format(item.item.total_posting_amount)"
          />
        </template>
        <template v-slot:item.paid="item">
          <AssetsTextLabel
            :label="$utils.currency_format(item.item.paid_amounts)"
          />
        </template>
        <template v-slot:item.balance="item">
          <AssetsTextLabel :label="$utils.currency_format(item.item.balance)" />
        </template>
        <template v-slot:item.res_date="item">
          <AssetsTextLabel
            :label="convert_date_format(item.item.booking_date)"
          />
        </template>
        <template v-slot:item.options="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list dense>
              <v-list-item>
                <v-list-item-title style="cursor: pointer">
                  <v-icon
                    @click="viewCustomerBilling(item)"
                    x-small
                    color="primary"
                    class="mr-2"
                  >
                    mdi-eye
                  </v-icon>
                  <AssetsTextLabel color="text-color" label="View" />
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title style="cursor: pointer">
                  <v-icon
                    v-if="
                      can('reservation_edit') ||
                      can('in_house_edit') ||
                      can('checkout_edit')
                    "
                    @click="get_payment(item)"
                    x-small
                    color="primary"
                    class="mr-2"
                  >
                    mdi-cash-multiple
                  </v-icon>
                  <AssetsTextLabel color="text-color" label="Pay" />
                </v-list-item-title>
              </v-list-item>
              <v-list-item>
                <v-list-item-title style="cursor: pointer">
                  <v-icon
                    @click="redirect_to_invoice(item.id)"
                    x-small
                    color="primary"
                    class="mr-2"
                  >
                    mdi-cash-multiple
                  </v-icon>
                  <AssetsTextLabel color="text-color" label="Invoice" />
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>
<script>
import Paying from "../../components/booking/Paying.vue";
import CustomFilter from "../filter/CustomFilter.vue";
export default {
  props: ["endpoint", "Model"],
  components: {
    Paying,
    CustomFilter,
  },
  data: () => ({
    stats: [
      { icon: "mdi-walk", number: "10", label: "WALKING" },
      { icon: "mdi-laptop", number: "04", label: "OTA" },
      { icon: "mdi-account-tie", number: "10", label: "Corporate" },
      { icon: "mdi-cloud-outline", number: "10", label: "WebSite" },
      { icon: "mdi-gift-outline", number: "01", label: "Complimentary" },
      { icon: "mdi-account-outline", number: "01", label: "Travel Agent" },
    ],
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
        width: "100px",
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
      { text: "Options", value: "options", align: "left", sortable: false },
    ],
    editedIndex: -1,
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
      if (this.from_date && this.to_date) this.getDataFromApi();
    },

    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      //this.filterType = data.type;
      this.search = data.search;
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return (
        year +
        "-" +
        (month < 10 ? "0" : "") +
        month +
        "-" +
        (day < 10 ? "0" : "") +
        day
      );
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
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

        this.$axios.get(url, options).then(({ data }) => {
          this.data = data.data;
          this.calculateStates(data.data);
          this.pagination.current = data.current_page;
          this.pagination.total = data.last_page;
          this.loading = false;
          this.totalRowsCount = data.total;
          this.currentPage = page;
          this.perPage = itemsPerPage;
        });
      }
    },

    searchIt() {
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
      }
    },
    calculateStates(customers) {
      const countResult = customers.reduce((acc, { type }) => {
        acc[type] = (acc[type] || 0) + 1;
        return acc;
      }, {});

      // Step 2: Map counts to desired output format
      this.stats = [
        {
          icon: "mdi-walk",
          count: countResult.Walking
            ? countResult.Walking.toString().padStart(2, "0")
            : "00",
          label: "WALKING",
        },
        {
          icon: "mdi-laptop",
          count: countResult.Online
            ? countResult.Online.toString().padStart(2, "0")
            : "00",
          label: "OTA",
        },
        {
          icon: "mdi-account-tie",
          count: countResult.Corporate
            ? countResult.Corporate.toString().padStart(2, "0")
            : "00",
          label: "Corporate",
        },
        {
          icon: "mdi-cloud-outline",
          count: countResult.website
            ? countResult.website.toString().padStart(2, "0")
            : "00",
          label: "WebSite",
        },
        {
          icon: "mdi-gift-outline",
          count: countResult.Complimentary
            ? countResult.Complimentary.toString().padStart(2, "0")
            : "00",
          label: "Complimentary",
        },
        {
          icon: "mdi-account-outline",
          count: countResult["Travel Agency"]
            ? countResult["Travel Agency"].toString().padStart(2, "0")
            : "00",
          label: "Travel Agent",
        },
      ];
    },
  },
};
</script>
