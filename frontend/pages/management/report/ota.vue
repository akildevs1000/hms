<template>
  <v-card class="mt-2">
    <v-container fluid>
      <div class="pa-3">
        <div style="display: flex; justify-content: right">
          <v-autocomplete
            @change="getDataBySource"
            :items="[{ id: null, name: `Select All` }, ...sources]"
            item-text="name"
            item-value="name"
            label="Source"
            dense
            outlined
            hide-details
            style="margin-top: 1px; max-width: 200px"
          ></v-autocomplete>
          &nbsp;
          <v-autocomplete
            @change="getDataByStatus"
            :items="[
              { id: null, name: `Select All` },
              { id: `Pending`, name: `Pending` },
              {
                id: `Received`,
                name: `Received`,
              },
            ]"
            item-text="name"
            item-value="name"
            label="Status"
            dense
            outlined
            flat
            hide-details
            style="margin-top: 1px; max-width: 200px"
          ></v-autocomplete>
          <!-- <v-text-field
            label="Search..."
            dense
            outlined
            flat
            append-icon="mdi-magnify"
            @input="searchIt"
            v-model="search"
            hide-details
            style="max-width: 200px"
          ></v-text-field> -->
          &nbsp;
          <FilterDateRange :defaultDates="true" @filter-attr="filterAttr" />
        </div>
      </div>
      <div>
        <v-data-table
          style="min-height: 370px; max-height: 370px; overflow-y: auto"
          dense
          small
          :headers="headers"
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
          <template v-slot:item.res_number="{ item }">
            <span
              class="blue--text"
              @click="goToRevView(item)"
              style="cursor: pointer"
            >
              <AssetsTextLabel :label="item.reservation_no || `---`" />
            </span>
          </template>
          <template v-slot:item.source="{ item }">
            <AssetsTextLabel :label="item.source || `---`" />
          </template>
          <template v-slot:item.rooms="{ item }">
            <span v-for="(room, index) in item.booked_rooms" :key="index">
              <AssetsTextLabel :label="room.room_no" />
              <AssetsTextLabel
                :label="item.booked_rooms.length - 1 == index ? `` : `,`"
              />
            </span>
          </template>
          <template v-slot:item.reference="{ item }">
            <AssetsTextLabel :label="item.reference_no || `---`" />
          </template>
          <template v-slot:item.guest="{ item }">
            <AssetsTextLabel :label="item.customer.first_name || `---`" />
          </template>
          <template v-slot:item.check_in="{ item }">
            <AssetsTextLabel :label="convert_date_format(item.check_in)" />
          </template>
          <template v-slot:item.check_out="{ item }">
            <AssetsTextLabel :label="convert_date_format(item.check_out)" />
          </template>
          <template v-slot:item.total="{ item }">
            <AssetsTextLabel
              :label="$utils.currency_format(item.total_price)"
            />
          </template>
          <template v-slot:item.posting="{ item }">
            <AssetsTextLabel
              :label="$utils.currency_format(item.total_posting_amount)"
            />
          </template>
          <template v-slot:item.paid="{ item }">
            <AssetsTextLabel
              :label="item.balance > 0 ? `Pending` : `Received`"
            />
          </template>
          <template v-slot:item.received_date="{ item }">
            <AssetsTextLabel :label="`---`" />
          </template>
          <template v-slot:item.res_date="{ item }">
            <AssetsTextLabel :label="convert_date_format(item.booking_date)" />
          </template>
        </v-data-table>
      </div>
    </v-container>
  </v-card>
</template>
<script>
export default {
  data: () => ({
    endpoint: "ota-report",
    search: null,
    filter: null,
    stats: [],
    cumulativeIndex: 1,
    perPage: 20,
    currentPage: 1,
    filterLoader: false,
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
      {
        text: "Source",
        align: "left",
        sortable: false,
        filterable: true,
        value: "source",
      },
      {
        text: "Rev. No",
        align: "left",
        sortable: false,
        filterable: true,
        value: "res_number",
      },
      {
        text: "Booking Id",
        align: "left",
        sortable: false,
        filterable: true,
        value: "id",
      },
      {
        text: "Guest",
        align: "left",
        sortable: false,
        filterable: true,
        value: "guest",
      },
      {
        text: "C/In",
        align: "left",
        sortable: false,
        filterable: true,
        value: "check_in",
      },
      {
        text: "C/Out",
        align: "left",
        sortable: false,
        filterable: true,
        value: "check_out",
      },
      {
        text: "Amount",
        align: "right",
        sortable: false,
        filterable: true,
        value: "total",
      },
      {
        text: "Paid",
        align: "right",
        sortable: false,
        filterable: true,
        value: "paid",
      },
      {
        text: "Received Date",
        align: "right",
        sortable: false,
        filterable: true,
        value: "received_date",
      },
      {
        text: "Reference No",
        align: "right",
        sortable: false,
        filterable: true,
        value: "reference_no",
      },
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    checkData: {},
    new_payment: 0,
  }),

  computed: {},

  watch: {
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
    this.getSources();
  },

  methods: {
    getDataBySource(e) {
      console.log("🚀 ~ getDataBySource ~ e:", e);

      this.filter = {
        ...this.filter,
        source: e,
      };
      this.getDataFromApi();
    },

    getDataByStatus(e) {
      this.filter = {
        ...this.filter,
        payment_status: e,
      };
      this.getDataFromApi();
    },

    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filter = {
        from: data.from,
        to: data.to,
      };
      this.getDataFromApi();
    },
    searchIt() {
      if (this.search.length == 0) {
        this.filter = {
          ...this.filter,
          search: this.search,
        };
      } else if (this.search.length > 2) {
        this.filter = {
          ...this.filter,
          search: this.search,
        };
      }
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    viewCustomerBilling(item) {
      // this.$router.push(`/customer/details/${item.id}`);
    },

    commonMethod() {
      this.getDataFromApi();
    },

    goToRevView(item) {
      // this.$router.push(`/customer/details/${item.id}`);
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

    async getSources() {
      let config = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      let { data } = await this.$axios.get(`source-list`, config);

      this.sources = data;
    },

    redirect_to_invoice(id) {
      let url = "https://backend.myhotel2cloud.com/api/invoice";
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
        "https://backend.myhotel2cloud.com/api/" +
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
            ...this.filter,
          },
        };

        console.log(this.filter);

        this.$axios.get(url, options).then(({ data }) => {
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
  },
};
</script>
