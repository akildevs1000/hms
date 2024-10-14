<template>
  <span>
    <v-dialog v-model="payingDialog" persistent max-width="700">
      <AssetsIconClose left="690" @click="payingDialog = false" />
      <v-card>
        <v-alert class="rounded-md" color="grey lighten-3" dense flat>
          <span>Payment</span>
        </v-alert>
        <v-card-text>
          <Paying
            :BookingData="checkData"
            @close-dialog="closeDialogs"
          ></Paying>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-container fluid>
      <v-data-table
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
        <template v-slot:item.sno="{ item, index }">
          <AssetsTextLabel
            :label="
              currentPage
                ? (currentPage - 1) * perPage +
                  (cumulativeIndex + itemIndex(item))
                : ''
            "
          />
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
    </v-container>
  </span>
</template>
<script>
import Paying from "../../components/booking/Paying.vue";
import CustomFilter from "../filter/CustomFilter.vue";
export default {
  props: ["endpoint", "Model", "filter"],
  components: {
    Paying,
    CustomFilter,
  },
  data: () => ({
    stats: [],
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
    filter: {
      deep: true, // Deep watch for object changes
      handler(data) {
        this.from_date = data.from;
        this.to_date = data.to;
        this.search = data.search;
        this.getDataFromApi();
      },
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
          value: countResult.Walking
            ? countResult.Walking.toString().padStart(2, "0")
            : "00",
          label: "WALKING",
          col: 7,
          color: "green", // For activity (Walking)
        },
        {
          icon: "mdi-laptop",
          value: countResult.Online
            ? countResult.Online.toString().padStart(2, "0")
            : "00",
          label: "OTA",
          col: 7,
          color: "blue", // For online/technology (OTA)
        },
        {
          icon: "mdi-account-tie",
          value: countResult.Corporate
            ? countResult.Corporate.toString().padStart(2, "0")
            : "00",
          label: "Corporate",
          col: 7,
          color: "orange", // For business (Corporate)
        },
        {
          icon: "mdi-cloud-outline",
          value: countResult.website
            ? countResult.website.toString().padStart(2, "0")
            : "00",
          label: "WebSite",
          col: 7,
          color: "purple", // For cloud/web (Website)
        },
        {
          icon: "mdi-gift-outline",
          value: countResult.Complimentary
            ? countResult.Complimentary.toString().padStart(2, "0")
            : "00",
          label: "Complimentary",
          col: 7,
          color: "pink", // For gifts (Complimentary)
        },
        {
          icon: "mdi-account-outline",
          value: countResult["Travel Agency"]
            ? countResult["Travel Agency"].toString().padStart(2, "0")
            : "00",
          label: "Travel Agent",
          col: 7,
          color: "teal", // For service/people (Travel Agent)
        },
      ];

      this.$emit("response", this.stats);
    },
  },
};
</script>
