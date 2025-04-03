<template>
  <span>
    <BookingSingle
      :noLabel="true"
      v-if="BookingId"
      ref="BookingSingleComp"
      :key="BookingId"
      :BookingId="BookingId"
    />
    <v-dialog v-model="payingDialog" persistent max-width="800">
      <AssetsIconClose left="790" @click="payingDialog = false" />
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
    <v-dialog v-model="mailDialog" persistent max-width="500">
      <AssetsIconClose left="490" @click="mailDialog = false" />
      <v-card>
        <v-alert class="rounded-md" color="grey lighten-3" dense flat>
          <span>Send Mail</span>
        </v-alert>
        <v-card-text>
          <v-col cols="12" class="text-center">
            <v-text-field
              dense
              outlined
              label="Email"
              v-model="email"
            ></v-text-field>
          </v-col>
          <v-col cols="12" class="text-center">
            <AssetsButton
              :options="{
                label: `Cancel`,
                color: `red`,
              }"
              @click="mailDialog = false"
            />
            &nbsp;
            <AssetsButton
              :options="{
                label: `Submit`,
                color: `green`,
              }"
              @click="captureAndSendPDF"
            />
          </v-col>
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
              <div style="border: 1px solid #dfdfdfdf">
                <v-simple-table dense>
                  <tbody>
                    <!-- apply background onlyfor selected tr -->
                    <tr
                      v-for="item in data"
                      :key="item.id"
                      @click="selectedItem = item"
                      :style="{
                        backgroundColor:
                          selectedItem.id === item.id ? '#dfdfdf' : '',
                      }"
                    >
                      <td class="py-2">
                        <div class="caption font-color" style="cursor: pointer">
                          <b>
                            {{ item?.customer?.first_name || "---" }}
                            {{ item?.customer?.last_name || "---" }}
                          </b>
                        </div>
                        <div class="body-2" style="cursor: pointer">
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
                      </td>
                      <td class="text-right">
                        <div class="body-1 font-color" style="cursor: pointer">
                          <b>{{ $utils.currency_format(item.balance) }}</b>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </v-simple-table>
              </div>
            </v-col>
            <v-col>
              <style scoped>
                .hover-bold:hover {
                  font-weight: bold;
                }
                .hover-bold:hover .ml-1 {
                  color: black !important;
                }
              </style>
              <v-toolbar class="grey lighten-3" flat dense>
                <v-row>
                  <v-col>
                    <span
                      class="hover-bold"
                      text
                      style="
                        background: none;
                        border: none !important;
                        font-size: 13px;
                        cursor: pointer;
                      "
                      @click="
                        () => {
                          mailDialog = true;
                          email = selectedItem?.customer?.email ?? '';
                        }
                      "
                      small
                    >
                      Send Mail <v-icon small class="ml-1">mdi-email</v-icon>
                    </span>
                    <span
                      class="hover-bold ml-5"
                      text
                      style="
                        background: none;
                        border: none !important;
                        font-size: 13px;
                        cursor: pointer;
                      "
                      small
                      @click="viewCustomerBilling(selectedItem.id)"
                    >
                      View
                      <v-icon small class="ml-1">mdi-eye</v-icon>
                    </span>
                    <v-menu bottom right>
                      <template v-slot:activator="{ on, attrs }">
                        <span
                          class="hover-bold ml-5"
                          text
                          style="
                            background: none;
                            border: none !important;
                            font-size: 13px;
                            cursor: pointer;
                          "
                          small
                          v-bind="attrs"
                          v-on="on"
                        >
                          Print/PDF
                          <v-progress-circular
                            class="ml-1"
                            v-if="invoiceLoader"
                            size="15"
                            width="2"
                            indeterminate
                          ></v-progress-circular>
                          <v-icon v-else>mdi-chevron-down</v-icon>
                        </span>
                      </template>

                      <v-list width="140" dense>
                        <v-list-item @click="captureAndViewPDF">
                          <v-list-item-title style="cursor: pointer"
                            >Print
                          </v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="captureAndDownloadPDF">
                          <v-list-item-title style="cursor: pointer"
                            >PDF
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>

                    <span
                      text
                      @click="get_payment(selectedItem)"
                      style="
                        background: none;
                        border: none !important;
                        font-size: 13px;
                        cursor: pointer;
                      "
                      small
                      class="hover-bold ml-5"
                    >
                      Record Payment
                      <v-icon small class="ml-1" style="margin-top: -4px"
                        >mdi-cash-multiple</v-icon
                      >
                    </span>
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
                <v-card id="capture" class="pa-5 mx-auto" max-width="800">
                  <v-row class="mt-4">
                    <v-col>
                      <v-avatar tile size="100">
                        <img :src="this.logo" alt="" />
                      </v-avatar>
                    </v-col>
                    <v-col class="text-right">
                      <strong>{{ $auth?.user?.company?.name }}</strong>
                      <div>{{ $auth?.user?.company?.location }}</div>
                      <div>{{ $auth?.user?.email }}</div>
                    </v-col>
                  </v-row>
                  <v-divider class="my-4"></v-divider>
                  <v-row>
                    <v-col></v-col>
                    <v-col cols="4" class="text-center">
                      <div class="text-h5">Tax Invoice</div>
                    </v-col>
                    <v-col cols="4" class="text-right">
                      <strong>Invoice Number - {{ selectedItem.id }}</strong>
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
                    <v-col cols="3">
                      Guest Info: <br />
                      <span v-if="selectedItem?.source">
                        {{ selectedItem?.source ?? "" }}
                        <br />
                        GST: {{ selectedItem?.customer?.gst_number ?? "---" }}
                        <br />
                      </span>
                      {{ selectedItem?.customer?.full_name }} <br />
                      {{ selectedItem?.customer?.contact_no }} <br />
                      {{ selectedItem?.customer?.address }}
                    </v-col>
                    <v-col cols="3">
                      Check In: <br />
                      <strong>
                        {{ formatDate(selectedItem?.check_in) ?? "" }} <br />
                        00:00</strong
                      >
                    </v-col>
                    <v-col cols="3">
                      Check Out: <br />
                      <strong
                        >{{ formatDate(selectedItem?.check_out) ?? "" }} <br />
                        00:00</strong
                      >
                    </v-col>
                    <v-col cols="3">
                      Reservation No: <br />
                      <strong>{{ selectedItem?.reservation_no ?? "" }}</strong>

                      <br />
                      <strong
                        >Date:
                        {{
                          formatDate(selectedItem?.booking_date) ?? ""
                        }}</strong
                      >
                    </v-col>

                    <v-col cols="3"> </v-col>
                    <v-col cols="3">
                      Nights: <br />
                      <strong>{{
                        selectedItem?.total_days == 0
                          ? 1
                          : selectedItem?.total_days
                      }}</strong>
                    </v-col>
                    <v-col cols="3">
                      Rooms: <br />
                      <strong>{{
                        selectedItem?.order_rooms?.length ?? 0
                      }}</strong>
                    </v-col>
                    <v-col cols="3">
                      Room Type: <br />
                      <strong>{{
                        selectedItem?.order_rooms.length
                          ? selectedItem?.order_rooms
                              ?.map((e) => e.room_type)
                              .join(",")
                          : ""
                      }}</strong>
                    </v-col>
                  </v-row>
                  <style scoped>
                    .simple-table {
                      border-collapse: collapse;
                      width: 100%;
                      font-size: 16px;
                      text-align: left;
                    }
                    .simple-table td {
                      border: none;
                    }
                    .simple-table tfoot td {
                      font-weight: bold;
                    }
                  </style>

                  <!-- Invoice Table -->
                  <div
                    style="border: 1px solid #dfdfdf; border-radius: 5px"
                    class="mt-5"
                  >
                    <v-simple-table class="simple-table">
                      <tbody>
                        <tr>
                          <td><b>Date</b></td>
                          <td><b>Room No</b></td>
                          <td><b>Unit</b></td>
                          <td class="text-right"><b>Price</b></td>
                          <td class="text-right"><b>SGST</b></td>
                          <td class="text-right"><b>CGST</b></td>
                          <td class="text-right"><b>Total</b></td>
                        </tr>
                        <tr
                          v-for="(room, index) in selectedItem?.order_rooms"
                          :key="index"
                          class="inv-tr-txt"
                        >
                          <td>{{ formatDate(room.date) }}</td>
                          <td>{{ room.room_no }} ({{ room.room_type }})</td>
                          <td>
                            {{
                              parseInt(room.no_of_adult) +
                              parseInt(room.no_of_child)
                            }}(pax)
                          </td>
                          <td class="text-right">
                            {{ room.inv_room_listing_price }}
                          </td>
                          <td class="text-right">
                            {{ room.inv_room_sgst }}
                          </td>
                          <td class="text-right">
                            {{ room.inv_room_cgst }}
                          </td>
                          <td class="text-right">
                            {{
                              $utils.currency_format(
                                parseFloat(room.inv_room_listing_price) +
                                  parseFloat(room.inv_room_sgst) +
                                  parseFloat(room.inv_room_cgst)
                              )
                            }}
                          </td>
                        </tr>

                        <tr
                          v-for="(posting, index) in selectedItem?.postings"
                          :key="index"
                          class="inv-tr-txt"
                        >
                          <td>{{ formatDate(posting.posting_date) }}</td>
                          <td>
                            {{ posting.item }} ({{ posting?.room?.room_no }})
                          </td>
                          <td>
                            {{ posting.qty }}
                          </td>
                          <td class="text-right">
                            {{ $utils.currency_format(posting?.amount) }}
                          </td>

                          <td class="text-right">
                            {{ $utils.currency_format(posting?.sgst) }} <br />
                          </td>
                          <td class="text-right">
                            {{ $utils.currency_format(posting?.cgst) }} <br />
                          </td>
                          <td class="text-right">
                            {{
                              $utils.currency_format(posting?.amount_with_tax)
                            }}
                          </td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="3"></td>
                          <td class="text-right">
                            {{ $utils.currency_format(subtotal_price) }}
                          </td>
                          <td class="text-right">
                            {{ $utils.currency_format(subtotal_sgst) }}
                          </td>
                          <td class="text-right">
                            {{ $utils.currency_format(subtotal_cgst) }}
                          </td>
                          <td class="text-right">
                            {{ $utils.currency_format(subtotal_total) }}
                          </td>
                        </tr>
                      </tfoot>
                    </v-simple-table>
                  </div>

                  <!-- Summary -->
                  <v-row class="mt-5">
                    <v-col cols="7">
                      <div style="font-size: 15px; color: #818181">
                        Total Collected:
                        {{
                          $utils.currency_format(subtotal_sgst + subtotal_cgst)
                        }}
                      </div>
                      <div style="font-size: 15px; color: #818181">
                        SGST: {{ $utils.currency_format(subtotal_sgst) }}
                      </div>
                      <div style="font-size: 15px; color: #818181">
                        CGST {{ $utils.currency_format(subtotal_cgst) }}
                      </div>
                    </v-col>
                    <v-col cols="5">
                      <v-row no-gutters>
                        <v-col cols="12" style="padding: 5px 6px">
                          <v-row>
                            <v-col cols="6">
                              <strong>Total</strong>
                            </v-col>
                            <v-col cols="6" class="text-right">
                              <strong>{{
                                $utils.currency_format(
                                  selectedItem?.total_with_posting ?? 0
                                )
                              }}</strong>
                            </v-col>
                          </v-row>
                        </v-col>

                        <v-col cols="12" style="padding: 5px 6px">
                          <v-row>
                            <v-col
                              style="font-size: 15px; color: #818181"
                              cols="6"
                            >
                              Paid
                            </v-col>
                            <v-col
                              style="font-size: 15px; color: #818181"
                              cols="6"
                              class="text-right"
                            >
                              {{
                                $utils.currency_format(
                                  selectedItem?.paid_amounts ?? 0
                                )
                              }}
                            </v-col>
                          </v-row>
                        </v-col>

                        <v-col
                          cols="12"
                          style="
                            background: #f5f6fa;
                            padding: 5px 6px;
                            border-radius: 5px;
                          "
                        >
                          <v-row>
                            <v-col cols="6">
                              <strong>Balance</strong>
                            </v-col>
                            <v-col cols="6" class="text-right">
                              <strong>
                                {{
                                  $utils.currency_format(
                                    (selectedItem?.total_with_posting ?? 0) -
                                      (selectedItem?.paid_amounts ?? 0)
                                  )
                                }}</strong
                              >
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>

                  <v-row>
                    <v-col
                      style="font-size: 15px; color: #818181"
                      class="text-right"
                    >
                      Amount: {{ selectedItem?.total_with_posting_in_words }}
                    </v-col>
                  </v-row>
                  <v-row>
                    <v-col class="text-center">
                      <strong
                        >Thank you for choosing us. We look forward to welcoming
                        you back soon. 🙂</strong
                      >
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row>
                    <v-col
                      style="font-size: 15px; color: #818181"
                      class="text-center"
                    >
                      This Is System Generated Invoice And Does Not Require
                      Signature.
                    </v-col>
                  </v-row>
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
import html2canvas from "html2canvas";
import jsPDF from "jspdf";
export default {
  props: ["endpoint", "Model", "filter"],
  components: {
    Paying,
  },
  data: () => ({
    email: "",
    mailDialog: false,
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
    logo: null,
    invoiceLoader: false,
  }),
  computed: {
    pdfUrl() {
      if (!this.selectedItem) return null;
      let { id } = this.selectedItem;
      return `https://backend.myhotel2cloud.com/api/invoice/${id}`;
    },
    subtotal_price() {
      return this.calculateSubtotal("inv_room_listing_price", "amount");
    },
    subtotal_sgst() {
      return this.calculateSubtotal("inv_room_sgst", "sgst");
    },
    subtotal_cgst() {
      return this.calculateSubtotal("inv_room_cgst", "cgst");
    },
    subtotal_total() {
      return this.subtotal_price + this.subtotal_sgst + this.subtotal_cgst;
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
  mounted() {
    this.convertImageToBase64(this.$auth?.user?.company?.logo || "");
  },
  created() {
    // this.loading = true;
    this.getDataFromApi();
  },

  methods: {
    calculateSubtotal(roomKey, postKey) {
      return (
        (this.selectedItem?.order_rooms || []).reduce(
          (sum, room) => sum + parseFloat(room[roomKey] || 0),
          0
        ) +
        (this.selectedItem?.postings || []).reduce(
          (sum, post) => sum + parseFloat(post[postKey] || 0),
          0
        )
      );

      // $subtotal_price += $post->amount;
      // $subtotal_sgst += $post->cgst;
      // $subtotal_cgst += $post->sgst;
      // $subtotal_total += $post->amount_with_tax;
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      });
    },
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
      // url = "https://hms-backend.test/api/invoice";
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
    captureAndDownloadPDF() {
      this.invoiceLoader = true;
      // Select the element to capture
      const captureElement = document.getElementById("capture");

      // Use html2canvas to take a screenshot of the element
      html2canvas(captureElement, {
        scale: 2, // Increase the scale for better resolution
        useCORS: true, // If you have images or fonts from different origins, allow cross-origin requests
        logging: false, // Disable logging for cleaner console output
      }).then((canvas) => {
        // Convert the screenshot canvas into an image
        const imgData = canvas.toDataURL("image/png");

        // Create a new PDF instance with portrait orientation
        const pdf = new jsPDF("p", "mm", "a4"); // 'p' for portrait, 'mm' for millimeters, 'a4' for A4 size

        // A4 page dimensions in mm (portrait)
        const imgWidth = 210; // Width of A4 paper in mm (portrait)
        const imgHeight = (canvas.height * imgWidth) / canvas.width; // Maintain aspect ratio

        // Add the captured image to the PDF
        pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);

        // Save the generated PDF
        pdf.save("invoice.pdf"); // Save the PDF

        this.invoiceLoader = false;
      });
    },
    captureAndViewPDF() {
      this.invoiceLoader = true;

      const captureElement = document.getElementById("capture");

      html2canvas(captureElement, {
        scale: 2,
        useCORS: true,
        logging: false,
      }).then((canvas) => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("p", "mm", "a4");

        const imgWidth = 210;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);

        // Open the PDF in a new tab instead of downloading
        const pdfBlob = pdf.output("blob");
        const pdfUrl = URL.createObjectURL(pdfBlob);

        this.invoiceLoader = false;

        window.open(pdfUrl, "_blank");
      });
    },
    captureAndSendPDF() {
      // Select the element to capture
      const captureElement = document.getElementById("capture");

      // Use html2canvas to take a screenshot of the element
      html2canvas(captureElement, {
        scale: 2, // Increase the scale for better resolution
        useCORS: true, // Allow cross-origin requests for images or fonts
        logging: false, // Disable logging
      }).then((canvas) => {
        // Convert the screenshot canvas into an image (PNG format)
        const imgData = canvas.toDataURL("image/png");

        // Create FormData object to send the image data to the server
        const formData = new FormData();
        formData.append("image", imgData); // Sending image data as a string
        formData.append("email", this.email); // Sending image data as a string

        // Send the image to the Laravel backend using axios
        this.$axios
          .post("/upload-image", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((response) => {
            console.log("Image uploaded successfully", response);
          })
          .catch((error) => {
            console.error("Error uploading image", error);
          });
      });
    },
    // Function to convert image URL to Base64
    async convertImageToBase64(imageUrl) {
      let { data } = await this.$axios.get(`get-encoded-logo?url=${imageUrl}`);
      this.logo = data;
    },
  },
};
</script>
