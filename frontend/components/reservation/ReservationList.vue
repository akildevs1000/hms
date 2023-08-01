<template>
  <div>
    <v-row>
      <!-- <v-col xs="12" sm="12" md="3" cols="12">
        <v-select
          class="form-control"
          @change="getDataFromApi(`reservation`)"
          v-model="pagination.per_page"
          :items="[10, 25, 50, 100]"
          placeholder="Per Page Records"
          solo
          hide-details
          flat
        ></v-select>
      </v-col> -->

      <v-col xs="12" sm="12" md="3" cols="12" class="mt-0">
        <v-select class="form-control" @change="getDataFromApi(`reservation_list_dash`)" v-model="status"
          :items="reservationStatusList" item-text="name" item-value="id" placeholder="Display List" solo hide-details
          flat dense></v-select>
      </v-col>
    </v-row>
    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> {{ Model }} List</span>

        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on"
              @click="getDataFromApi()">
              <v-icon color="white" class="ml-2" dark>mdi mdi-reload</v-icon>
            </v-btn>
          </template>
          <span>Reload</span>
        </v-tooltip>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="toggleFilter">
              <v-icon dark white>mdi-filter</v-icon>
            </v-btn>
          </template>
          <span>Filter</span>
        </v-tooltip>
      </v-toolbar>


      <v-data-table dense :headers="headers_table" :items="data" :loading="loading" :options.sync="options"
        :footer-options="{ itemsPerPageOptions: [10, 20, 50, 100, 500, 1000] }" :server-items-length="totalTableRowsCount"
        @page-change="updateIndex">

        <template v-slot:header="{ props: { headers } }">
          <tr v-if="isFilter">
            <td v-for="header in headers" :key="header.text">
              <v-text-field clearable :hide-details="true" v-if="header.filterable && !header.filterSpecial"
                v-model="filters[header.key]" :id="header.value" @input="applyFilters(header.key, $event)" outlined dense
                autocomplete="off"></v-text-field>


              <v-menu v-if="header.filterSpecial && header.value == 'check_in'" ref="from_menu_filter"
                v-model="from_menu_filter" :close-on-content-click="false" transition="scale-transition" offset-y
                min-width="auto">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field :hide-details="true" clearable @click:clear="filters[header.value] = ''; applyFilters()"
                    outlined dense v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                    placeholder="Select Date"></v-text-field>
                </template>
                <v-date-picker style="height: 350px" v-model="filters[header.value]" no-title scrollable
                  @input="applyFilters()">
                  <v-spacer></v-spacer>

                  <v-btn text color="primary"
                    @click="filters[header.value] = ''; from_menu_filter = false; applyFilters()">
                    Clear
                  </v-btn>
                </v-date-picker>
              </v-menu>
              <v-menu v-if="header.filterSpecial && header.value == 'check_out'" ref="to_menu_filter"
                v-model="to_menu_filter" :close-on-content-click="false" transition="scale-transition" offset-y
                min-width="auto">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field :hide-details="true" clearable @click:clear="filters[header.value] = ''; applyFilters()"
                    outlined dense v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                    placeholder="Select Date"></v-text-field>
                </template>
                <v-date-picker style="height: 350px" v-model="filters[header.value]" no-title scrollable
                  @input="applyFilters()">
                  <v-spacer></v-spacer>

                  <v-btn text color="primary" @click="filters[header.value] = ''; to_menu_filter = false; applyFilters()">
                    Clear
                  </v-btn>
                </v-date-picker>
              </v-menu>
              <v-menu v-if="header.filterSpecial && header.value == 'booking_date'" ref="to_menu_filter1"
                v-model="to_menu_filter1" :close-on-content-click="false" transition="scale-transition" offset-y
                min-width="auto">
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field :hide-details="true" clearable @click:clear="filters[header.value] = ''; applyFilters()"
                    outlined dense v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                    placeholder="Select Date"></v-text-field>
                </template>
                <v-date-picker style="height: 380px" v-model="filters[header.value]" no-title scrollable
                  @input="applyFilters()">
                  <v-spacer></v-spacer>

                  <v-btn text color="primary"
                    @click="filters[header.value] = ''; to_menu_filter1 = false; applyFilters()">
                    Clear
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </td>
          </tr>
        </template>


        <template v-slot:item.sno="{ item, index }"> {{
          currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : ''
        }}</template>
        <template v-slot:item.reservation_no="{ item }">
          <b>{{ item.reservation_no }}</b>
        </template>
        <template v-slot:item.customer.name="{ item }">
          {{ item && item.customer.full_name }}
        </template>
        <template v-slot:item.rooms="{ item }">
          <span v-for="(room, index) in item.booked_rooms" :key="index">
            {{ room.room_no }}
            {{ item.booked_rooms.length - 1 == index ? "" : "," }}
          </span>
        </template>
        <template v-slot:item.check_in="{ item }">
          {{ item.check_in_date }}
        </template>
        <template v-slot:item.check_out="{ item }">
          {{ item.check_out_date }}
        </template>
        <template v-slot:item.total_price="{ item }">
          {{ item.total_price ? formatAmount(item.total_price) : 0 }}
        </template>
        <template v-slot:item.source="{ item }">
          {{ item.source || "---" }}
        </template>
        <template v-slot:item.booking_date="{ item }">
          {{ convert_date_format(item.booking_date) }}
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
            <b>{{ item.reservation_no }}</b>
          </td>
          <td>{{ item && item.customer.full_name }}</td>
          <td>
            <span v-for="(room, index) in item.booked_rooms" :key="index">
              {{ room.room_no }}
              {{ item.booked_rooms.length - 1 == index ? "" : "," }}
            </span>
          </td>

          <td style="width: 150px">
            {{ item.check_in_date }}
          </td>
          <td style="width: 150px">
            {{ item.check_out_date }}
          </td>

          <td>{{ item.total_price || 0 }}</td>
          
      <td>{{ item.source || "---" }}</td>
      <td>{{ convert_date_format(item.booking_date) }}</td>
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
export default {
  data: () => ({
    cumulativeIndex: 1,
    perPage: 20,
    currentPage: 1,
    filterLoader: false,
    filters: {},
    isFilter: false,
    totalTableRowsCount: 0,
    options: {},
    from_menu_filter: false,
    to_menu_filter: false,
    to_menu_filter1: false,
    from_date_filter: false,

    Model: "Reservation",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    endpoint: "reservation_list_dash",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    reservationStatusList: [
      { id: "", name: "Summary" },
      { id: 1, name: "Arrival For Today" },
      { id: 2, name: "Occupancy" },
      { id: 3, name: "Departure For Today" },
    ],
    headers_table: [
      { text: "Rev. No", value: "reservation_no", sortable: true, filterable: true, aling: "left", key: 'reservation_no' },
      { text: "Customer", value: "customer.name", sortable: true, filterable: true, align: "left", key: "customer_name" },
      { text: "Rooms", value: "rooms", sortable: true, filterable: true, align: "left", key: "rooms" },
      { text: "Arrival  Date", value: "check_in", filterable: true, sortable: true, align: "left", width: "160px", key: "check_in", filterSpecial: true, },
      { text: "Departure  Date", value: "check_out", filterable: true, sortable: true, align: "left", width: "160px", key: "check_out", filterSpecial: true, },
      { text: "Total", value: "total_price", filterable: true, sortable: true, align: "right", key: "total_price" },
      { text: "Booking Date", value: "booking_date", filterable: true, sortable: true, align: "left", key: "booking_date", filterSpecial: true, },
      { text: "Source", value: "source", filterable: true, sortable: true, align: "left", key: "source" },

    ],
    headers: [
      { text: "&nbsp Rev. No" },
      { text: "Customer" },
      { text: "Rooms" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total" },
      // { text: "Advance" },
      // { text: "Remaining" },
      { text: "Source" },
      { text: "Booking Date" },
    ],
    editedIndex: -1,
    response: "",
    status: "",
    errors: [],
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
    }
  },
  created() {

    // this.loading = true;
    this.getDataFromApi();
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

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `http://127.0.0.1:8000/api/invoice/${id}`);
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
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    applyFilters() {
      this.from_menu_filter = false;
      this.to_menu_filter = false;
      this.to_menu_filter1 = false;
      this.getDataFromApi();
    },
    updateIndex(page) {
      this.currentPage = page;
      this.cumulativeIndex = (page - 1) * this.perPage;


    },
    itemIndex(item) {
      return this.data.indexOf(item);
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      this.currentPage = page;
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          status: this.status,
          date: new Date().toJSON().slice(0, 10),
          ...this.filters,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalTableRowsCount = data.total;
      });
    },

    searchIt() {
      if (this.search.length == 0) {
        this.getDataFromApi();
      } else if (this.search.length > 2) {
        this.getDataFromApi();
      }
    },

    formatAmount(amount) {
      return amount.toString().replace(/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/g, "$1,");
    },
  },
};
</script>

<style scoped >
.no-bg {
  background-color: white !important;
}

.guest-avatar {
  max-width: 200px !important;
  height: 200px !important;
  float: left;
  margin: 0 auto;
  border-radius: 50%;
}

.text-box {
  border: 1px solid rgb(215, 211, 211);
  padding: 10px 0px 0px 10px;
  margin: 10px 20px;
  position: relative;
  border-radius: 5px;
  width: 100%;
}

.text-box-amt {
  border: 0px solid rgb(215, 211, 211);
  padding: 0px 0px 0px 0px;
  margin: 0px 00px;
  position: relative;
  border-radius: 5px;
  width: 100%;
}

.amt-border {
  border-bottom: 1px solid;
}

.amt-border-full {
  border-bottom: 1px solid;
  border-top: 1px solid;
}

.text-box p {
  margin: 5px;
}

h6 {
  position: absolute;
  top: -12px;
  left: 20px;
  background-color: white;
  padding: 0 10px;
  color: rgb(154, 152, 152);
  margin: 0;
  font-size: 15px;
}

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
  /* border: 1px solid black !important; */
}

tr:nth-child(even) {
  background-color: white;
}

.custom-text-box {
  border-radius: 2px !important;
  border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
  border: 2px solid #5fafa3 !important;
}

select:focus {
  outline: none !important;
  border-color: #5fafa3;
  box-shadow: 0 0 0px #5fafa3;
}

.table-header-text {
  font-size: 12px;
}
</style>  

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 7px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
</style>
