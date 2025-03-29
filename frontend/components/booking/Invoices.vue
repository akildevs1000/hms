<template>
  <span>
    <BookingSingle
      :noLabel="true"
      v-if="BookingId"
      ref="BookingSingleComp"
      :key="BookingId"
      :BookingId="BookingId"
    />
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
      <v-row>
        <v-col v-if="!shortView" cols="12">
          <v-data-table
            dense
            small
            :headers="headers_table"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-0"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:item.guest="{ item }">
              <AssetsTextLabel :label="item.customer.first_name || `---`" />
            </template>
            <template v-slot:item.id="{ item }">
              <div
                @click="
                  () => {
                    shortView = true;
                    selectedItem = item;
                  }
                "
              >
                <AssetsTextLabel
                  style="cursor: pointer"
                  color="blue"
                  :label="item.id"
                />
              </div>
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
                :label="$utils.currency_format(item.paid_amounts)"
              />
            </template>
            <template v-slot:item.status="{ item }">
              <span :class="item.balance > 0 ? 'red--text' : 'success--text'">{{
                item.balance > 0 ? "Upaid" : "Paid"
              }}</span>
            </template>
            <template v-slot:item.balance="{ item }">
              <AssetsTextLabel
                :color="item.balance > 0 ? 'red' : ''"
                :label="$utils.currency_format(item.balance)"
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
                  <v-list-item @click="viewCustomerBilling(item.id)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon x-small color="primary" class="mr-2">
                        mdi-eye
                      </v-icon>
                      <AssetsTextLabel color="text-color" label="View" />
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="get_payment(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="
                          can('reservation_edit') ||
                          can('in_house_edit') ||
                          can('checkout_edit')
                        "
                        x-small
                        color="primary"
                        class="mr-2"
                      >
                        mdi-cash-multiple
                      </v-icon>
                      <AssetsTextLabel color="text-color" label="Pay" />
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="redirect_to_invoice(item.id)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon x-small color="primary" class="mr-2">
                        mdi-cash-multiple
                      </v-icon>
                      <AssetsTextLabel color="text-color" label="Invoice" />
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
          </v-data-table>
        </v-col>
        <v-col v-else cols="12">
          <v-row>
            <v-col cols="3" style="max-width: 350px">
              <style>
                table {
                  font-family: arial, sans-serif;
                  border-collapse: collapse;
                  width: 100%;
                }

                td,
                th {
                  border: 1px solid #dddddd;
                  text-align: left;
                  padding: 8px;
                }
              </style>
              <table style="width: 100%">
                <tbody>
                  <tr
                    v-for="item in data"
                    :key="item.id"
                    @click="selectedItem = item"
                  >
                    <td style="width: 50%">
                      <v-row>
                        <v-col>
                          <div class="body-1" style="color: #5e5e5e">
                            <b>
                              {{ item?.customer?.first_name || "---" }}
                              {{ item?.customer?.last_name || "---" }}
                            </b>
                          </div>
                          <div>
                            {{ item?.id || "---" }} -
                            {{ item?.formatted_invoice_date || "---" }}
                          </div>
                          <div>
                            <span
                              :class="
                                item.balance > 0 ? 'red--text' : 'success--text'
                              "
                              >{{ item?.balance > 0 ? "Unpaid" : "Paid" }}</span
                            >
                          </div>
                        </v-col>
                        <v-col class="text-right">
                          <div style="color: #5e5e5e" class="body-1">
                            <b>{{ $utils.currency_format(item.balance) }}</b>
                          </div>
                        </v-col>
                      </v-row>
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-col>
            <v-col>
              <v-toolbar class="grey lighten-3" flat dense>
                <v-row>
                  <v-col>
                    <v-menu bottom right>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn outlined small v-bind="attrs" v-on="on">
                          Print/PDF <v-icon>mdi-chevron-down</v-icon>
                        </v-btn>
                      </template>

                      <v-list width="140" dense>
                        <v-list-item
                          @click="redirect_to_invoice(selectedItem.id, 'print')"
                        >
                          <v-list-item-title style="cursor: pointer"
                            >Print</v-list-item-title
                          >
                        </v-list-item>
                        <v-list-item
                          @click="redirect_to_invoice(selectedItem.id, 'pdf')"
                        >
                          <v-list-item-title style="cursor: pointer"
                            >PDF</v-list-item-title
                          >
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </v-col>
                  <v-col>
                    <div class="text-right">
                      <v-icon color="primary" @click="shortView = false"
                        >mdi-close</v-icon
                      >
                    </div>
                  </v-col>
                </v-row>
              </v-toolbar>
              <v-container>
                <v-card class="pa-5 mx-auto" max-width="800">
                  <!-- Header -->
                  <v-row class="mt-4">
                    <v-col>
                      <v-avatar tile size="100">
                        <img
                          src="https://hms-backend.test/upload/1743250338.jpeg"
                          alt=""
                        />
                      </v-avatar>
                    </v-col>
                    <v-col class="text-right">
                      <strong>Demo</strong>
                      <div>demo@gmail.com</div>
                      <div>64480E7A9AC15</div>
                    </v-col>
                  </v-row>
                  <v-divider class="my-4"></v-divider>
                  <v-row>
                    <v-col></v-col>
                    <v-col cols="4" class="text-center">
                      <div class="text-h5">Tax Invoice</div>
                    </v-col>
                    <v-col cols="4" class="text-right">
                      <strong>Invoice Number - 00001435</strong>
                    </v-col>
                  </v-row>

                  <v-divider class="my-4"></v-divider>

                  <!-- Guest Info -->
                  <v-row
                    style="
                      background: #f5f6fa;
                      margin: 0;
                      border-radius: 5px;
                      border: 1px solid #dfdfdf;
                    "
                  >
                    <v-col>
                      <strong>Guest Info:</strong> ---<br />
                      <strong>GST:</strong> ---<br />
                      <strong>Ariff Mohamed</strong><br />
                      7708004000
                    </v-col>
                    <v-col>
                      <strong>Check In:</strong> <br />
                      <span class="font-weight-bold">27 Mar 2025 00:00</span
                      ><br />
                      <strong>Check Out:</strong> <br />
                      <span class="font-weight-bold">28 Mar 2025 00:00</span>
                    </v-col>
                    <v-col>
                      <strong>Reservation No:</strong> 393<br />
                      <strong>Date:</strong> 27 Mar 2025
                    </v-col>
                    <v-col>
                      <strong>Nights:</strong> 1<br />
                      <strong>Rooms:</strong> 1<br />
                      <strong>Room Type:</strong>
                      <span class="font-weight-bold">Queen</span>
                    </v-col>
                  </v-row>

                  <v-divider class="my-4"></v-divider>

                  <!-- Invoice Table -->
                  <v-simple-table dense>
                    <thead>
                      <tr>
                        <th class="text-left">Date</th>
                        <th class="text-left">Room No</th>
                        <th class="text-left">Unit</th>
                        <th class="text-left">Price</th>
                        <th class="text-left">SGST</th>
                        <th class="text-left">CGST</th>
                        <th class="text-left">Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>27 Mar 2025</td>
                        <td>104 (Queen)</td>
                        <td>1 (pax)</td>
                        <td>3,200.00</td>
                        <td>192.00</td>
                        <td>192.00</td>
                        <td>3,584.00</td>
                      </tr>
                      <tr>
                        <td>27 Mar 2025</td>
                        <td>Tea (104)</td>
                        <td>2</td>
                        <td>100.00</td>
                        <td>2.50</td>
                        <td>2.50</td>
                        <td>105.00</td>
                      </tr>
                    </tbody>
                  </v-simple-table>

                  <v-divider class="my-4"></v-divider>

                  <!-- Summary -->
                  <v-row class="text-right">
                    <v-col cols="6" offset="6">
                      <strong>Total:</strong> 3,300.00<br />
                      <strong>SGST:</strong> 194.50<br />
                      <strong>CGST:</strong> 194.50<br />
                      <strong class="text-h6">Grand Total: 3,689.00</strong>
                    </v-col>
                  </v-row>
                </v-card>
              </v-container>
              <v-container
                style="
                  background: white !important;
                  min-height: 100vh;
                  display: flex;
                  justify-content: center;
                  padding: 50px;
                "
              >
                <v-card
                  elevation="5"
                  style="
                    width: 100%;
                    max-width: 820px; /* Adjust width as needed */
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                  "
                >
                  <iframe
                    :src="pdfUrl + '#toolbar=0'"
                    width="100%"
                    height="800"
                    style="border: none; background: white"
                  ></iframe>
                </v-card>
              </v-container>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </span>
</template>
<script>
import Paying from "../../components/booking/Paying.vue";
export default {
  props: ["endpoint", "Model", "filter"],
  components: {
    Paying,
  },
  data: () => ({
    shortView: false,
    selectedItem: null,
    BookingId: 0,
    stats: [],
    cumulativeIndex: 1,
    totalRowsCount: 0,
    options: {},
    // Model: "In House Reservation",
    payingDialog: false,

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

    headers_table: [
      {
        text: "Date",
        value: "formatted_invoice_date",
      },
      {
        text: "Invoice #",
        value: "id",
      },
      {
        text: "Guest",
        value: "guest",
      },
      {
        text: "Status",
        value: "status",
      },
      {
        text: "Total",
        align: "center",
        value: "total",
      },
      {
        text: "Posting",
        align: "center",
        value: "posting",
      },
      {
        text: "Paid",
        align: "center",
        value: "paid",
      },
      {
        text: "Balance",
        align: "center",
        value: "balance",
      },

      { text: "Options", value: "options", align: "center", sortable: false },
    ],
    editedIndex: -1,
    response: "",
    errors: [],
    checkData: {},
    new_payment: 0,
  }),

  computed: {
    pdfUrl() {
      if (!this.selectedItem) return null;
      let { id } = this.selectedItem;
      return `https://backend.myhotel2cloud.com/api/invoice/${id}`;
    },
  },
  watch: {
    filter: {
      deep: true, // Deep watch for object changes
      handler() {
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
  },

  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    viewCustomerBilling(id) {
      this.BookingId = id;

      this.$nextTick(() => {
        const bookingSingleComp = this.$refs["BookingSingleComp"];
        if (bookingSingleComp) {
          bookingSingleComp.ViewBookingDialog = true;
        } else {
          console.warn("BookingSingleComp ref is undefined");
        }
      });
    },

    redirect_to_invoice(id, model = "print") {
      let url = "https://backend.myhotel2cloud.com/api/invoice";
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${url}/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    get_payment(item) {
      this.checkData = item;
      this.payingDialog = true;
    },

    closeDialogs() {
      this.payingDialog = false;
      this.getDataFromApi();
    },
    async getDataFromApi() {
      this.loading = true;

      try {
        const { sortBy = [], sortDesc = [], page, itemsPerPage } = this.options;

        const options = {
          params: {
            page,
            sortBy: sortBy.length ? sortBy[0] : "",
            sortDesc: sortDesc.length ? sortDesc[0] : "",
            per_page: itemsPerPage,
            company_id: this.$auth?.user?.company?.id,
            ...this.filter,
          },
        };

        const { data } = await this.$axios.get(this.endpoint, options);
        this.data = data.data;
        this.totalRowsCount = data.total;
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
