<template>
  <div v-if="can(`accounts_gst_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <!-- <v-row>
      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-ddoor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Invoice</h6>
              <span class="data-1">
                {{ getPriceFormat(inv_total_without_tax_collected) }}

              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">GST</h6>
              <span class="data-1"> {{ getPriceFormat(inv_total_tax_collected) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 my-0 py-0 col-lg-4 col-md-4 text-uppercase" v-if="can('management_income_view')">
        <div class="card px-2" style="background-color: #ce008e">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-dosor-open"></i>
            </div>
            <div class="card-content">
              <h6 class="card-title text-capitalize">Total</h6>
              <span class="data-1"> {{ getPriceFormat(parseFloat(inv_total_without_tax_collected) +
                parseFloat(inv_total_tax_collected)) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </v-row> -->

    <v-card class="mb-5 rounded-md mt-3" elevation="0">
      <v-container>
        <v-row>
          <v-col md="1">
           <div class="body-1">GST Bills</div>
          </v-col>
          <v-col md="2">
            <v-text-field
              placeholder="Search..."
              @input="searchIt"
              v-model="search"
              hide-details
              label="Search..."
              dense
              outlined
              flat
              append-icon="mdi-magnify"
            ></v-text-field>
          </v-col>
          <v-col md="2">
            <v-select
              v-model="guest_mode"
              :items="['Select All', 'Arrival', 'Departure']"
              dense
              outlined
              placeholder="Type"
              solo
              flat
              :hide-details="true"
              @change="getDataFromApi()"
            ></v-select>
          </v-col>
          <v-col md="2">
            <FilterDateRange @filter-attr="filterAttr" />
          </v-col>
          <v-col class="text-right">
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  class="ma-0"
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('gst_invoice_report_print')"
                >
                  <v-icon class="">mdi-printer-outline</v-icon>
                </v-btn>
              </template>
              <span>PRINT</span>
            </v-tooltip>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('gst_invoice_report_download')"
                >
                  <v-icon class="">mdi-download-outline</v-icon>
                </v-btn>
              </template>
              <span> DOWNLOAD </span>
            </v-tooltip>
            <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  x-small
                  :ripple="false"
                  text
                  v-bind="attrs"
                  v-on="on"
                  @click="process('gst_invoice_report_csv_download')"
                >
                  <v-icon class="">mdi-file-outline</v-icon>
                </v-btn>
              </template>
              <span> EXPORT </span>
            </v-tooltip>
          </v-col>
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="data"
              :loading="loading"
              class="elevation-0"
              dense
            >
              <template v-slot:progress>
                <v-progress-linear
                  indeterminate
                  color="primary"
                ></v-progress-linear>
              </template>

              <template v-slot:item.show_taxable_invoice_number="{ item }">
                <b>{{ item.show_taxable_invoice_number || "---" }}</b>
              </template>

              <!-- <template v-slot:item.reservation_no="{ item }">
                <b>
                  <span
                    class="blue--text"
                    @click="goToRevView(item)"
                    style="cursor: pointer"
                  >
                    {{ item.reservation_no || "---" }}
                  </span>
                </b>
              </template> -->

              <template v-slot:item.gst_number="{ item }">
                {{ item?.booking?.customer?.gst_number || "---" }}
              </template>

              <template v-slot:item.source="{ item }">
                {{ item?.booking?.source || "---" }}
              </template>

              <template v-slot:item.full_name="{ item }">
                {{ item?.booking?.customer?.full_name || "---" }}
              </template>

              <template v-slot:item.check_in="{ item }">
                {{ convert_date_format(item?.booking?.check_in) }}
              </template>

              <template v-slot:item.check_out="{ item }">
                {{ convert_date_format(item?.booking?.check_out) }}
              </template>

              <template v-slot:item.total_price="{ item }">
                <div style="text-align: right">
                  {{ item?.booking?.total_price || "0" }}
                </div>
              </template>

              <template v-slot:item.total_posting_amount="{ item }">
                <div style="text-align: right">
                  {{ item?.booking?.total_posting_amount || 0 }}
                </div>
              </template>

              <template v-slot:item.paid_amounts="{ item }">
                <div style="text-align: right">
                  {{ item?.booking?.paid_amounts || 0 }}
                </div>
              </template>

              <template v-slot:item.balance="{ item }">
                <div style="text-align: right">
                  {{ item?.booking?.balance || 0 }}
                </div>
              </template>

              <template v-slot:item.inv_total_tax_collected="{ item }">
                <div style="color: red; text-align: right">
                  {{ item?.booking?.inv_total_tax_collected || 0 }}
                </div>
              </template>

              <template v-slot:item.booking_date="{ item }">
                {{ item?.booking?.booking_date || "---" }}
              </template>

              <template v-slot:item.actions="{ item }">
                <v-icon
                  @click="
                    redirect_to_invoice(
                      item?.booking?.id,
                      item.show_taxable_invoice_number
                    )
                  "
                  x-small
                  color="primary"
                  class="mr-2"
                >
                  mdi-cash-multiple
                </v-icon>
              </template>

              <template v-slot:body.append>
                <tr style="font-weight: bold">
                  <td colspan="7" style="text-align: right">
                    Inv Total(inc.gst)
                  </td>
                  <td style="text-align: right">
                    {{
                      getPriceFormat(
                        parseFloat(inv_total_without_tax_collected) +
                          parseFloat(inv_total_tax_collected)
                      )
                    }}
                  </td>
                  <td></td>
                  <td colspan="2" style="text-align: right">GST Total:</td>
                  <td style="text-align: right">
                    {{ getPriceFormat(inv_total_tax_collected) }}
                  </td>
                  <td></td>
                  <td></td>
                </tr>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
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
    dateTimePickerheader: { key: "", type: "date_range_picker" },
    headers: [
      { text: "Invoice No", value: "show_taxable_invoice_number" },
      { text: "Resr. No", value: "reservation_no" },
      { text: "GST", value: "gst_number" },
      { text: "Source", value: "source" },
      { text: "Customer", value: "full_name" },
      { text: "Arrival Date", value: "check_in" },
      { text: "Departure Date", value: "check_out" },
      { text: "Total(inv)", value: "total_price", align: "end" },
      { text: "Posting", value: "total_posting_amount", align: "end" },
      { text: "Paid Amount", value: "paid_amounts", align: "end" },
      { text: "Balance", value: "balance", align: "end" },
      { text: "GST Amt", value: "inv_total_tax_collected", align: "end" },
      { text: "Booking Date", value: "booking_date" },
      { text: "Invoice", value: "actions" },
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
    handleDatesFilter(dates) {
      this.from_date = dates[0];
      this.to_date = dates[1];
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      //this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    getPriceFormat(price) {
      return parseFloat(price).toLocaleString("en-IN", {
        maximumFractionDigits: 2,
      });
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
      if (this.from_date && this.to_date) {
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
            this.$axios
              .get("get_invoice_grand_total", options)
              .then(({ data }) => {
                this.inv_total_tax_collected = data.inv_total_tax_collected;
                this.inv_total_without_tax_collected =
                  data.inv_total_without_tax_collected;
              });
          }
        });
      } else {
        return false;
      }
    },
    process(type) {
      let comId = this.$auth.user.company.id; //company id
      let from = this.from_date;
      let to = this.to_date;
      let guest_mode = this.guest_mode;
      let search = this.search;
      let url =
        process.env.BACKEND_URL +
        `${type}?company_id=${comId}&from=${from}&to=${to}&guest_mode=${guest_mode}&search=${search}`;
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
