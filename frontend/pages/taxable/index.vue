<template>
  <div v-if="can(`accounts_gst_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-1">

      <v-col cols="3">

        <div class="col-md-12 col-lg-12 col-xlg-12 py-0">

          <v-row class="box text-center" style="background-color: rgb(50, 161, 92); border-radius: 7px">
            <v-col md="12" class="p-0 m-0">
              <h1 class="font-light text-white">

                <h4 class="text-white pb-0 mb-0   text-left dash-font-size text-center  "
                  style="color:white;font-size: 30px;">
                  Invoice(exl) : {{ getPriceFormat(inv_total_without_tax_collected) }}
                </h4>
              </h1>
            </v-col>

          </v-row>

        </div>

      </v-col>
      <v-col cols="3">

        <div class="col-md-11 col-lg-11 col-xlg-11 py-0">

          <v-row class="box text-center" style="background-color: rgb(255, 0, 0); border-radius: 7px">
            <v-col md="12" class="p-0 m-0">
              <h1 class="font-light text-white">

                <h4 class="text-white pb-0 mb-0   text-left dash-font-size text-center  "
                  style="color:white;font-size: 30px;">
                  SGST : {{ getPriceFormat(inv_total_tax_collected / 2) }}
                </h4>
              </h1>
            </v-col>

          </v-row>

        </div>

      </v-col>
      <v-col cols="3">

        <div class="col-md-11 col-lg-11 col-xlg-11 py-0">

          <v-row class="box text-center" style="background-color: rgb(255, 0, 0); border-radius: 7px">
            <v-col md="12" class="p-0 m-0">
              <h1 class="font-light text-white">

                <h4 class="text-white pb-0 mb-0   text-left dash-font-size text-center  "
                  style="color:white;font-size: 30px;">
                  CGST : {{ getPriceFormat(inv_total_tax_collected / 2) }}
                </h4>
              </h1>
            </v-col>

          </v-row>

        </div>

      </v-col>
      <v-col cols="3">


        <v-row class="box text-center" style="background-color: rgb(2, 173, 164); border-radius: 7px">
          <v-col md="12" class="p-0 m-0">
            <h1 class="font-light text-white">

              <h4 class="text-white pb-0 mb-0   text-left dash-font-size text-center  "
                style="color:white;font-size: 30px;">
                Total(inc): {{ getPriceFormat(parseFloat(inv_total_without_tax_collected) +
                  parseFloat(inv_total_tax_collected)) }}
              </h4>
            </h1>
          </v-col>

        </v-row>


      </v-col>
    </v-row>


    <v-row>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-text-field placeholder="Search..." @input="searchIt" v-model="search" hide-details label="Search..." dense
          outlined flat append-icon="mdi-magnify"></v-text-field>
      </v-col>
      <v-col xs="12" sm="12" md="2" cols="12">
        <v-select class="custom-text-box shadow-none" v-model="guest_mode" :items="['Select All', 'Arrival', 'Departure']"
          dense outlined placeholder="Type" solo flat :hide-details="true" @change="getDataFromApi()"></v-select>
      </v-col>

      <v-col md="3">
        <v-menu v-model="from_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition"
          offset-y min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field outlined v-model="from_date" readonly v-bind="attrs" v-on="on" dense :hide-details="true"
              class="custom-text-box shadow-none" solo flat label="From"></v-text-field>
          </template>
          <v-date-picker v-model="from_date" @input="from_menu = false" @change="commonMethod"></v-date-picker>
        </v-menu>
      </v-col>
      <v-col md="3">
        <v-menu v-model="to_menu" :close-on-content-click="false" :nudge-right="40" transition="scale-transition" offset-y
          min-width="auto">
          <template v-slot:activator="{ on, attrs }">
            <v-text-field outlined v-model="to_date" readonly v-bind="attrs" v-on="on" dense
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
              @click="process('gst_invoice_report_print')">
              <v-icon class="">mdi-printer-outline</v-icon>
            </v-btn>
          </template>
          <span>PRINT</span>
        </v-tooltip>

        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="process('gst_invoice_report_download')">
              <v-icon class="">mdi-download-outline</v-icon>
            </v-btn>
          </template>
          <span> DOWNLOAD </span>
        </v-tooltip>
        <v-tooltip top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on"
              @click="process('gst_invoice_report_csv_download')">
              <v-icon class="">mdi-file-excel-box-outline</v-icon>
            </v-btn>
          </template>
          <span> DOWNLOAD </span>
        </v-tooltip>


      </v-toolbar>
      <table>
        <tr>
          <th style="font-size: 13px" v-for="(item, index) in headers" :key="index">
            <span v-html="item.text"></span>
          </th>
        </tr>
        <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
          color="primary"></v-progress-linear>
        <tr style="font-size: 13px" v-for="(item, index) in data" :key="index">
          <td class="ps-3">
            <b>{{ item.show_taxable_invoice_number || "---" }}</b>
          </td>
          <td class="ps-3">
            <b>

              <span class="blue--text" @click="goToRevView(item)" style="cursor: pointer;">
                {{ item.reservation_no || "---" }}
              </span>

            </b>
          </td>
          <td>
            {{
              (item &&
                item.booking &&
                item.booking.customer &&
                item.booking.customer.gst_number) ||
              "---"
            }}
          </td>
          <td>{{ (item && item.booking && item.booking.source) || "---" }}</td>
          <td>
            {{
              (item &&
                item.booking &&
                item.booking.customer &&
                item.booking.customer.full_name) ||
              "---"
            }}
          </td>

          <td style="width: 120px">
            {{
              convert_date_format(item && item.booking && item.booking.check_in)
            }}
          </td>
          <td style="width: 120px">
            {{
              convert_date_format(
                item && item.booking && item.booking.check_out
              )
            }}
          </td>
          <td style=" text-align:right">
            {{ (item && item.booking && item.booking.total_price) || "0" }}
          </td>
          <td style=" text-align:right">
            {{
              (item && item.booking && item.booking.total_posting_amount) || 0
            }}
          </td>
          <td style=" text-align:right">
            {{ (item && item.booking && item.booking.paid_amounts) || 0 }}
          </td>
          <td style=" text-align:right">{{ (item && item.booking && item.booking.balance) || 0 }}</td>
          <td style="color:red;text-align:right">

            {{ (item && item.booking && item.booking.inv_total_tax_collected) || 0 }}



          </td>
          <td>

            {{ (item && item.booking && item.booking.booking_date) || "---" }}
          </td>
          <td>
            <v-icon @click="
              redirect_to_invoice(
                item && item.booking && item.booking.id,
                item.show_taxable_invoice_number
              )
              " x-small color="primary" class="mr-2">
              mdi-cash-multiple
            </v-icon>
          </td>
        </tr>
        <tr style="font-weight:bold">
          <td colspan="7" style="text-align: right;">
            Inv Total(inc.gst)
          </td>
          <td>
            {{ getPriceFormat(parseFloat(inv_total_without_tax_collected) + parseFloat(inv_total_tax_collected)) }}
          </td>
          <td>

          </td>
          <td colspan="2" style="text-align: right;">


            GST Total:
          </td>
          <td>
            {{ getPriceFormat(inv_total_tax_collected) }}
          </td>
          <td>

          </td>
          <td>

          </td>
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
</template>
<script>
export default {
  data: () => ({

    inv_total_without_tax_collected: 0,
    inv_total_tax_collected: 0,

    Model: "GST Bill",
    checkOutDialog: false,
    pagination: {
      current: 1,
      total: 0,
      per_page: 30,
    },

    from_date: "",
    from_menu: false,

    to_date: "",
    to_menu: false,

    options: {},
    endpoint: "taxable_invoice",
    search: "",
    snackbar: false,
    dialog: false,
    data: [],
    loading: false,
    total: 0,
    headers: [
      { text: "&nbsp Invoice No" },
      { text: "&nbsp Resr. No" },
      { text: "&nbsp GST" },
      { text: "Source" },
      { text: "Customer" },
      // { text: "Rooms" },
      { text: "Arrival  Date" },
      { text: "Departure  Date" },
      { text: "Total(inv)" },
      { text: "Posting" },
      { text: "Paid Amount" },
      { text: "Balance" },
      { text: "GST Amt" },
      { text: "Booking Date" },
      { text: "Invoice" },
      // { text: "Reservation Status" }
      // { text: "View" },
      // { text: "Payment" },
      // { text: "Invoice" }
    ],
    editedIndex: -1,
    response: "",
    guest_mode: "",
    errors: [],
  }),

  computed: {},

  watch: {
    search() {
      this.getDataFromApi();
    },
  },
  created() {
    // this.loading = true;

    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();
    this.from_date = this.formatDate(new Date(this.year, this.month, 1));
    this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));


    this.getDataFromApi();
  },

  methods: {
    getPriceFormat(price) {
      return price.toLocaleString("en-IN", { maximumFractionDigits: 2 });
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    convert_date_format(val) {
      if (!val) return "---";
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

    commonMethod() {
      this.getDataFromApi();
    },

    goToRevView(item) {
      this.$router.push(`/customer/details/${item.booking_id}`);
    },

    redirect_to_invoice(id, inv) {
      let url = process.env.BACKEND_URL + "invoice";
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}/${id}/${inv}`);
      document.body.appendChild(element);
      element.click();
    },

    getDataFromApi(url = this.endpoint) {



      this.loading = true;
      let page = this.pagination.current;
      if (page == 1) {
        this.inv_total_tax_collected = 0;
        this.inv_total_without_tax_collected = 0;
      }
      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company.id,
          search: this.search,
          from: this.from_date,
          to: this.to_date,
          guest_mode: this.guest_mode,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;

        if (data.current_page == 1) {
          this.$axios.get('get_invoice_grand_total', options).then(({ data }) => {


            this.inv_total_tax_collected = data.inv_total_tax_collected;
            this.inv_total_without_tax_collected = data.inv_total_without_tax_collected;




          });
        }


      });




    },

    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let guest_mode = this.guest_mode;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}&guest_mode=${guest_mode}`;
      console.log(url);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}`);
      document.body.appendChild(element);
      element.click();
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


