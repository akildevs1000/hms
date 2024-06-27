<template>
  <div v-if="can('accounts_posting_access') && can('accounts_posting_view')">
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
    <v-dialog v-model="postingDialog" persistent max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Posting</span>
        </v-toolbar>
        <v-card-text>
          <v-container>
            <table>
              <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                color="primary"></v-progress-linear>
              <tr>
                <th>Bill No</th>
                <td style="width: 300px">
                  <v-text-field dense outlined type="number" v-model="posting.bill_no"
                    :hide-details="true"></v-text-field>
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
                  <v-select v-model="room_no" :items="bookedRooms" item-text="resourceId" item-value="id" dense outlined
                    :hide-details="true" :height="1" @change="get_booked_room_details(room_no)"></v-select>
                </td>
              </tr>
              <tr>
                <th>Customer Name</th>
                <td style="width: 300px">
                  {{ customer_name || "---" }}
                </td>
              </tr>

              <tr>
                <th>Room Type</th>
                <td>
                  {{ room_type || "---" }}
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Item
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field dense outlined type="text" v-model="posting.item" :hide-details="true"></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  QTY
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field dense outlined type="number" v-model="posting.qty" :hide-details="true"></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Amount
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-text-field dense outlined type="number" v-model="posting.amount" :hide-details="true"></v-text-field>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Type
                  <span class="text-danger">*</span>
                </th>
                <td>
                  <v-select v-model="posting.tax_type" :items="[
                    { id: -1, name: 'select..' },
                    { name: 'Food' },
                    { name: 'Mess' },
                    { name: 'Bed' },
                  ]" item-text="name" item-value="id" dense outlined :hide-details="true" :height="1"
                    @change="get_amount_with_tax(posting.tax_type)"></v-select>
                </td>
              </tr>
              <tr style="background-color: white">
                <th>
                  Amount With Tax
                  <span class="text-danger">*</span>
                </th>
                <td>
                  {{ posting.amount_with_tax }}
                </td>
              </tr>
              <tr></tr>
            </table>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn class="primary" small @click="store_posting" :loading="loading">submit</v-btn>
          <v-btn class="error" small @click="postingDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-row>
      <div class="col-xl-3 my-0 py-0 col-lg-6 text-uppercase">
        <div class="card px-2" style="background-color: #800000">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Total</h6>
              <span class="data-1"> {{ $auth.user.company.currency }} {{ totalAmount || 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    </v-row>

    <v-row>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field class="" label="Search..." dense outlined flat append-icon="mdi-magnify" v-model="search"
          hide-details></v-text-field>
      </v-col>
      <v-col xs="12" sm="12" md="4" cols="12">
        <CustomFilter @filter-attr="filterAttr" :defaultFilterType="1" />
        <!-- <DateRangePicker key="postings" :disabled="false" :DPStart_date="from_date" :DPEnd_date="to_date"
          column="date_range" @selected-dates="handleDatesFilter" /> -->
      </v-col>


    </v-row>
    <v-card class="mb-5 rounded-md mt-6" elevation="0">
      <v-toolbar class="rounded-md" color="background" dense flat dark>
        <span> {{ Model }} List</span>
        <v-spacer></v-spacer>
        <!-- <v-btn
          class="primary"
          small
          @click="postingDialog = true"
          :loading="loading"
          >New</v-btn
        > -->
      </v-toolbar>
      <table>
        <tr>
          <th v-for="(item, index) in headers" :key="index" style="font-size: 13px">
            <span v-html="item.text"></span>
          </th>
        </tr>
        <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
          color="primary"></v-progress-linear>
        <tr v-for="(item, index) in data" :key="index" style="font-size: 13px">
          <td>{{ ++index }}</td>
          <td>{{ caps(item.bill_no) }}</td>
          <td>
            <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer">
              {{ (item.booking && item.booking.reservation_no) || "---" }}
            </span>
          </td>
          <td>
            {{
              caps(
                item.booking &&
                item.booking.customer &&
                item.booking.customer.full_name
              )
            }}
          </td>
          <td>{{ caps(item.booked_room && item.booked_room.room_no) }}</td>
          <td>{{ caps(item.booked_room && item.booked_room.room_type) }}</td>
          <!-- <td>{{ caps(item.booking.customer.full_name) }}</td> -->

          <td>{{ caps(item.item) }}</td>
          <td>{{ caps(item.qty) }}</td>
          <td>{{ caps(item.amount_with_tax) }}</td>
          <td>{{ caps(item.tax) }}</td>
          <td>{{ caps(item.sgst) }}</td>
          <td>{{ caps(item.cgst) }}</td>
          <td>{{ caps(item.posting_date) }}</td>
        </tr>
      </table>
    </v-card>
    <v-row>
      <v-col md="12" class="float-right">
        <div class="float-right">
          <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
            :total-visible="12"></v-pagination>
        </div>
      </v-col>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
import CustomFilter from '../components/filter/CustomFilter.vue';

export default {
  data: () => ({
    Model: "Posting",
    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
      room_no: "",
    },
    options: {},
    endpoint: "posting",
    room_type: "",
    customer_name: "",
    room_no: "",
    search: "",
    from_date: "",
    to_date: "",
    filterType: 1,
    snackbar: false,
    postingDialog: false,
    data: [],
    bookedRooms: [],
    loading: false,
    total: 0,
    headers: [
      { text: "#" },
      { text: "Bill Number" },
      { text: "Rev. No" },
      { text: "Guest" },
      { text: "Room No" },
      { text: "Room Type" },
      { text: "Item" },
      { text: "QTY" },
      { text: "Amount" },
      { text: "Tax" },
      { text: "SGST" },
      { text: "CGST" },
      { text: "Date" },
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    rooms: [],
    posting: {
      booked_room_id: "",
      booking_id: "",
      room_id: "",
      item: "",
      qty: "",
      amount: 0,
      bill_no: "",
      amount_with_tax: 0,
      tax: 0,
      sgst: 0,
      cgst: 0,
      tax_type: -1,
    },
  }),
  created() {
    // this.month = new Date().getMonth();
    // this.year = new Date().getFullYear();
    // this.from_date = this.formatDate(new Date(this.year, this.month, 1));
    // this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));
    const today = new Date();
    this.from_date = today.toISOString().slice(0, 10);
    this.to_date = today.toISOString().slice(0, 10);
    this.loading = true;
    this.room_list();
    this.getDataFromApi();
    this.booked_room_list();
  },
  computed: {
    totalAmount() {
      let sum = 0;
      this.data.map((e) => (sum += parseFloat(e.amount_with_tax)));
      return sum.toFixed(2);
    },
  },
  watch: {
    search() {
      this.getDataFromApi();
    },
  },
  methods: {
    handleDatesFilter(dates) {
      this.filterType = 5;
      this.from_date = dates[0];
      this.to_date = dates[1];
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
      return ((u && u.permissions.some(e => e == per || per == "/")) || u.is_master);
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      }
      else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    onPageChange() {
      this.getDataFromApi();
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date)
        this.getDataFromApi();
    },
    goToRevView(item) {
      this.$router.push(`/customer/details/${item.booking_id}`);
    },
    getDataFromApi(url = this.endpoint) {
      this.loading = true;
      let page = this.pagination.current;
      let options = {
        params: {
          room_id: this.pagination.room_no,
          per_page: this.pagination.per_page,
          search: this.search,
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
          filterType: this.filterType,
        },
      };
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },
    get_booked_room_details(id) {
      let room = this.bookedRooms.find((e) => e.id == id);
      this.posting.booked_room_id = room.id;
      this.posting.booking_id = room.booking_id;
      this.posting.room_id = room.room_id;
      this.room_type = room.room_type.name;
      this.customer_name = room.title;
    },
    booked_room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booked_rooms`, payload).then(({ data }) => {
        this.bookedRooms = data;
      });
    },
    get_amount_with_tax(clause) {
      let per = clause == "Food" ? 5 : 12;
      let res = this.getPercentage(this.posting.amount, per);
      let gst = parseFloat(res) / 2;
      this.posting.sgst = gst;
      this.posting.cgst = gst;
      this.posting.tax = res;
      this.posting.amount_with_tax =
        parseFloat(res) + parseFloat(this.posting.amount);
    },
    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },
    store_posting() {
      let rule = Object.keys(this.posting.item).length == 0 ||
        Object.keys(this.posting.amount).length == 0 ||
        Object.keys(this.posting.qty).length == 0 ||
        Object.keys(this.posting.bill_no).length == 0 ||
        this.posting.tax_type == -1;
      if (rule) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let per = this.posting.tax_type == "Food" ? 5 : 12;
      console.log(per);
      let payload = {
        ...this.posting,
        company_id: this.$auth.user.company.id,
        tax_type: per,
      };
      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          }
          else {
            this.getDataFromApi();
            this.loading = false;
            this.snackbar = true;
            this.response = data.message;
          }
        })
        .catch((e) => console.log(e));
    },
    room_list() {
      this.$axios.get(`room_list_menu`).then(({ data }) => {
        this.rooms = data;
        this.rooms.unshift({ id: "", room_no: "Select All" });
      });
    },
    searchIt(e) {
      // if (e.length == 0) {
      //   this.getDataFromApi(this.endpoint);
      // } else if (e.length > 1) {
      //   this.getDataFromApi(`${this.endpoint}/search/${e}`);
      // }
    },
  },
  components: { CustomFilter }
};
</script>