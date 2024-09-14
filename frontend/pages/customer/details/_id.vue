<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-dialog
        v-model="GRCDialog"
        persistent
        :width="900"
        class="checkin-models"
      >
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ "GRC" }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="GRCDialog = false"
              >mdi-close</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <Grc :bookingId="this.$route.params.id"> </Grc>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="imgView" max-width="80%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Preview</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="imgView = false"
              >mdi-close</v-icon
            >
          </v-toolbar>
          <v-container>
            <ImagePreview :docObj="documentObj"></ImagePreview>
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

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
              :BookingData="payData"
              @close-dialog="closeDialogs"
            ></Paying>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar :color="roomTypeColor" dark flat dense>
            <v-tabs
              v-model="activeTab"
              background-color="primary"
              align-with-title
            >
              <span class="py-3 ml-2"> On-premises Guest</span>
              <v-spacer></v-spacer>
              <v-tab
                active-class="active-link"
                v-for="item in itemsCustomer"
                :key="item"
              >
                {{ item }}
              </v-tab>
              <v-tabs-slider color="#1259a7"></v-tabs-slider>
            </v-tabs>
            <v-icon dark class="pa-0" @click="redirect"> mdi-close </v-icon>
          </v-toolbar>
          <v-tabs-items v-model="activeTab">
            <v-tab-item class="px-3 py-4">
              <v-row>
                <v-col md="2" cols="12">
                  <v-row no-gutters>
                    <v-col cols="12" class="text-center">
                      <v-avatar size="200" tile>
                        <v-img
                          :src="
                            booking?.customer?.captured_photo ||
                            '/no-profile-image.png'
                          "
                        ></v-img>
                      </v-avatar>
                    </v-col>
                    <v-col cols="3"></v-col>
                    <v-col cols="6" class="text-center">
                      <v-row>
                        <v-col>
                          <v-img
                            :src="
                              booking?.customer?.captured_photo || '/idf.png'
                            "
                            style="margin: 0 auto; width: 50px; height: 50px"
                            contain
                          ></v-img>
                        </v-col>
                        <v-col>
                          <v-img
                            :src="
                              booking?.customer?.captured_photo || '/idb.png'
                            "
                            style="margin: 0 auto; width: 50px; height: 50px"
                            contain
                          ></v-img>
                        </v-col>
                      </v-row>
                    </v-col>

                    <v-col cols="3"></v-col>
                    <!-- <v-col cols="12" class="text-center">
                      <v-btn
                        small
                        dark
                        class="primary"
                        @click="process_grc(booking.id)"
                      >
                        GRC
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                      <v-btn
                        small
                        dark
                        class="primary"
                        @click="downloadCustomerAttachments"
                      >
                        <v-icon>mdi-download-outline</v-icon>
                        <v-icon>mdi-account-tie</v-icon>
                      </v-btn>
                    </v-col> -->
                    <v-col md="12" class="pr-0 mr-0">
                      <div class="text-box-amt mt-5">
                        <table style="width: 100%">
                          <tr class="bg-white amt-border-full">
                            <td>Room:</td>
                            <!-- $auth.user.company.currency -->
                            <td class="text-right">
                              {{
                                transactionSummary &&
                                $utils.currency_format(
                                  transactionSummary.sumDebit -
                                    transactionSummary.tot_posting
                                )
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Posting :</td>
                            <td class="text-right">
                              {{
                                $utils.currency_format(
                                  transactionSummary.tot_posting
                                )
                              }}
                            </td>
                          </tr>
                          <tr
                            class="bg-white amt-border bold"
                            style="font-weight: bold"
                          >
                            <td>Total :</td>
                            <td class="text-right">
                              {{
                                $utils.currency_format(
                                  transactionSummary.sumDebit
                                )
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Paid :</td>
                            <td class="text-right">
                              -{{
                                $utils.currency_format(
                                  transactionSummary.sumCredit
                                )
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Balance :</td>
                            <td class="red--text text-right">
                              {{
                                $utils.currency_format(
                                  transactionSummary.balance
                                )
                              }}
                            </td>
                          </tr>
                        </table>
                      </div>
                    </v-col>
                  </v-row>
                </v-col>

                <v-col
                  md="10"
                  cols="12"
                  v-if="booking && booking.customer && booking.customer.id"
                >
                  <v-row class="mt-4">
                    <v-col cols="3">
                      <v-text-field
                        label="Group Name"
                        v-model="booking.group_name"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>
                    <v-col cols="3">
                      <v-text-field
                        label="Full Name"
                        v-model="booking.title"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>
                    <v-col cols="3">
                      <v-text-field
                        label="Mobile"
                        v-model="booking.customer.contact_no"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>
                    <v-col cols="3">
                      <v-text-field
                        label="Whatsapp"
                        v-model="booking.customer.whatsapp"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Reservation No"
                        v-model="booking.reservation_no"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Number of Rooms"
                        v-model="bookedRooms.length"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Booking Date"
                        v-model="booking.booking_date"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Nights"
                        v-model="booking.total_days"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Check In"
                        v-model="booking.check_in_date"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Check Out"
                        v-model="booking.check_out_date"
                        dense
                        outlined
                        hide-details
                      ></v-text-field>
                    </v-col>

                
                 

                

                    <v-col cols="3">
                      <v-text-field
                        label="Pay Type"
                        dense
                        outlined
                        hide-details
                        v-model="getRelatedPaidBy"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Reach By"
                        dense
                        outlined
                        hide-details
                        v-model="booking.customer.type"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="4">
                      <v-text-field
                        label="Source"
                        dense
                        outlined
                        hide-details
                        v-model="booking.source"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="4">
                      <v-text-field
                        label="Reference Number"
                        dense
                        outlined
                        hide-details
                        v-model="booking.reference_no"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        label="Purpose"
                        dense
                        outlined
                        hide-details
                        v-model="booking.purpose"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="Country"
                        dense
                        outlined
                        hide-details
                        v-model="booking.customer.country"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="State"
                        dense
                        outlined
                        hide-details
                        v-model="booking.customer.state"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="City"
                        dense
                        outlined
                        hide-details
                        v-model="booking.customer.city"
                      ></v-text-field>
                    </v-col>

                    <v-col cols="3">
                      <v-text-field
                        label="City"
                        dense
                        outlined
                        hide-details
                        v-model="booking.customer.zip_code"
                      ></v-text-field>
                    </v-col>

                  
                    <v-col cols="12">
                      <v-text-field
                        label="Guest Request"
                        dense
                        outlined
                        hide-details
                        v-model="booking.request"
                      ></v-text-field>
                    </v-col>
                  </v-row>

                  <v-row class="my-0 py-0">
                    <v-col
                      md="8"
                      v-if="booking && booking.booking_type == 'hall'"
                    >
                      <v-row>
                        <v-col>
                          <div class="text-box" style="float: left">
                            <h6>Event Start</h6>
                            <p>
                              {{ booking.hall_check_in_date || "---" }}
                            </p>
                          </div>
                        </v-col>
                        <v-col>
                          <div class="text-box" style="float: left">
                            <h6>Event End</h6>
                            <p>
                              {{ booking.hall_check_out_date || "---" }}
                            </p>
                          </div>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <CustomerRooms :booking="booking" :orderRooms="orderRooms" />
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <CustomerPostings
                :full_name="booking.title"
                :postings="postings"
              />
            </v-tab-item>
            <v-tab-item class="px-3 pt-5">
              <CustomerTransactions
                :transactions="transactions"
                :totalTransactionAmount="totalTransactionAmount"
              />
            </v-tab-item>
            <v-tab-item class="px-3" v-if="room_category_type == 'Hall'">
              <v-col cols="12">
                <table class="responsive-table mt-0 pt-5">
                  <thead>
                    <tr class="table-header-text">
                      <th>#</th>
                      <th>Name</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <v-progress-linear
                    v-if="loading"
                    :active="loading"
                    :indeterminate="loading"
                    absolute
                    color="primary"
                  ></v-progress-linear>

                  <tbody>
                    <tr
                      v-for="(item, index) in food"
                      :key="index"
                      style="font-size: 13px"
                      class="no-bg"
                    >
                      <td>
                        <b>{{ ++index }}</b>
                      </td>
                      <td>{{ item.name || "---" }}</td>
                      <td>{{ item.qty || "---" }}</td>

                      <td>
                        {{ item.price_per_item || "---" }}
                      </td>
                      <td>
                        {{ item.total || "---" }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </v-col>
            </v-tab-item>
            <v-tab-item class="px-3" v-if="room_category_type == 'Hall'">
              <v-card flat>
                <v-row>
                  <v-col cols="6">
                    <v-row style="background-color: #e3e3e3">
                      <v-col md="3" cols="12"
                        ><label class="text-h6">Name</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right"> Total </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>

                    <v-row>
                      <v-col md="3" cols="12"><label>Hall rent</label> </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(hallRentTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Electricity (EB) Charges</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(electricityTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>

                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Sound System </label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(audioTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label> Projector</label> </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(projecterTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Setting arrangment & Cleaning charges</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(cleaningTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Food Total</label> </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(foodTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Additional Events</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(otherCharges) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row style="font-weight: bold">
                      <v-col md="3" cols="12"><label>Total</label> </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(hallItemsTotal) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="6">
                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Adjusted Final Amount(After Discount)</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(inv_total_without_tax) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Tax Collected with Food tax</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(inv_total_tax) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>

                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"
                        ><label>Grand Total</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">
                        {{ getPriceFormat(inv_total) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right"> </v-col>
                    </v-row>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="12" class="pt-5">
                    <h4>Additional Events/Charges</h4>
                    <table class="responsive-table mt-0">
                      <thead>
                        <tr class="table-header-text">
                          <th>#</th>
                          <th>Name</th>
                          <th>Qty</th>
                          <th>Price</th>
                          <th>Total</th>
                        </tr>
                      </thead>
                      <v-progress-linear
                        v-if="loading"
                        :active="loading"
                        :indeterminate="loading"
                        absolute
                        color="primary"
                      ></v-progress-linear>

                      <tbody>
                        <tr
                          v-for="(item, index) in extra_amounts"
                          :key="index"
                          style="font-size: 13px"
                          class="no-bg"
                        >
                          <td>
                            <b>{{ ++index }}</b>
                          </td>
                          <td>{{ item.name || "---" }}</td>
                          <td>{{ item.qty || "---" }}</td>

                          <td>
                            {{ item.price_per_item || "---" }}
                          </td>
                          <td>
                            {{ item.total || "---" }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </v-col>
                </v-row>
              </v-card>
            </v-tab-item>

            <v-tab-item class="px-3 py-4">
              <v-row>
                <v-col md="12" cols="12">
                  <v-row class="mt-4">
                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>Online Payment Status</h6>
                        <p
                          style="color: green; font-weight: bold"
                          v-if="
                            booking &&
                            booking.online_payment_response &&
                            booking.online_payment_response
                              .razorpay_payment_link_status == 'paid'
                          "
                        >
                          Success
                        </p>
                        <p v-else style="color: red; font-weight: bold">
                          Failed
                        </p>
                      </div>
                    </v-col>
                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>Online Booking Reference Number</h6>
                        <p>
                          {{
                            (booking &&
                              booking.widget_confirmation_number &&
                              booking.widget_confirmation_number.split(
                                "_"
                              )[1]) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>

                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>RazorPay - Reference Id</h6>
                        <p>
                          {{
                            (booking && booking.payment_reference_id) || "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>RazorPay - Payment Id</h6>
                        <p>
                          {{
                            (booking &&
                              booking.online_payment_response &&
                              booking.online_payment_response
                                .razorpay_payment_id) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <!-- <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>RazorPay - Razorpay Order Id</h6>
                        <p>
                          (booking &&
                              booking.online_payment_response &&
                              booking.online_payment_response.razorpay_payment_id) ||
                            "---"
                        </p>
                      </div>
                    </v-col> -->
                  </v-row>
                </v-col>
              </v-row>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="7"> </v-col>
    </v-row>
  </div>
</template>
<script>
import Grc from "./../../../components/booking/GRC.vue";
import ImagePreview from "../../../components/images/ImagePreview.vue";
import Paying from "../../../components/booking/Paying.vue";
export default {
  components: {
    Paying,
    ImagePreview,
    Grc,
  },
  data: () => ({
    roomTypeColor: "grey lighten-3",

    discount: 0,
    hallRentPerHour: 0,
    projecterTotalAmount: 0,
    cleaningTotalAmount: 0,
    electricityTotalAmount: 0,
    audioTotalAmount: 0,
    tax_percentage: 0,
    room_tax_amount: 0,
    hallItemsTotal: 0,

    durationInHours: 1,
    foodTotalAmount: 0,

    hallRentTotalAmount: 0,
    otherCharges: 0,

    AmountGrandTotal: 0,
    hallTaxableTotalAmount: 0,
    inv_total_without_tax: 0,
    inv_total: 0,
    inv_total_tax: 0,
    discount: 0,

    hours: [
      { id: 9, name: "09 AM" },
      { id: 10, name: "10 AM" },
      { id: 11, name: "11 AM" },
      { id: 12, name: "12 PM" },
      { id: 13, name: "01 PM" },
      { id: 14, name: "02 PM" },
      { id: 15, name: "03 PM" },
      { id: 16, name: "04 PM" },
      { id: 17, name: "05 PM" },
      { id: 18, name: "06 PM" },
      { id: 19, name: "07 PM" },
      { id: 20, name: "08 PM" },
      { id: 21, name: "09 PM" },
      { id: 22, name: "10 PM" },
      { id: 23, name: "11 PM" },
    ],

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    documentObj: {
      fileExtension: null,
      file: null,
    },
    options: {},
    imgView: false,
    showImage: "",
    Model: "Customer",
    search: "",
    GRCDialog: false,
    snackbar: false,
    dialog: false,
    payingDialog: false,
    ids: [],
    payData: {},
    loading: false,
    response: "",
    customer: [],
    itemsCustomer: [
      "Reservation",
      "Room",
      "Postings",
      "Transaction",
      "Online Booking",
    ],
    tab1: null,
    activeTab: 0,

    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",

    tab: null,
    payments: [],
    booking: [],
    bookedRooms: [],
    orderRooms: [],
    postings: [],
    transactions: [],
    transactionSummary: [],
    errors: [],
    totalAmount: 0,
    totalPostingAmount: 0,
    totalTransactionAmount: 0,
    room_category_type: "",
    food: [],
    extra_amounts: [],
  }),

  computed: {
    getRelatedPaidBy() {
      return this.booking && this.booking.paid_by == 2
        ? "Paid By Agent"
        : "Paid By Guest";
    },
  },
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() {},

  methods: {
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    downloadCustomerAttachments() {
      let id = this.$route.params.id;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute(
        "href",
        `${process.env.BACKEND_URL}download_customer_attachments/${id}`
      );
      document.body.appendChild(element);
      element.click();
    },
    getDate(dataTime) {
      return dataTime;
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    closeDialogs() {
      this.getData();
      this.payingDialog = false;
    },

    getRelaventStatus(status) {
      switch (parseInt(status)) {
        case 1:
          return "Reservation ";
        case 2:
          return "In House ";
        case 3:
          return "C/Out ";
        case 4:
          return "C/Out ";
        case 0:
          return "C/Out ";
        case -1:
          return "Cancel Reservation ";
        default:
          return "C/Out  ";
      }
    },

    cancelPosting(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete("posting_cancel/" + item.id)
          .then(({ data }) => {
            this.snackbar = data.status;
            this.response = data.message;
            this.getData();
          })
          .catch((err) => console.log(err));
    },

    numFormat(num) {
      if (!num) return "0";

      const number = num;
      const res = number.toFixed(2);
      return res;
      const formatted = number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
      return formatted;
    },

    process_grc(id) {
      this.GRCDialog = true;
    },
    getHours(time) {
      let arry = this.hours.find((hour) => hour.id === time);
      if (arry) return arry.name;
      else return "";
    },
    preview(file) {
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      let title = upper.replace(/[^A-Z]/g, " ");
      return title.replace("PRICE", "");
    },

    calTotalAmount(payments) {
      let sum = 0;
      payments.forEach((item) => {
        sum += parseFloat(item.amount);
      });
      this.totalAmount = sum;
    },

    redirect() {
      this.$router.push("/");
    },

    get_payment() {
      this.payData = this.booking;

      // {
      //   id: this.$route.params.id,
      // };
      this.payingDialog = true;
    },

    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
        this.transactionSummary = data.transactionSummary;
        const booking = data.booking;
        this.customer = booking.customer;
        this.booking = booking;

        this.booking.online_payment_response = JSON.parse(
          booking.payment_response
        );
        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        this.orderRooms = booking.order_rooms;
        this.postings = data.postings;
        //end booking
        this.loading = false;
        this.showImage = data.booking.customer.image;
        this.calTotalAmount(this.payments);
        this.room_category_type = data.booking.room_category_type;

        if (this.room_category_type == "Hall") {
          this.roomTypeColor = "green";
          this.itemsCustomer = [
            "Reservation",
            "Room",
            "Postings",
            "Transaction",
            "Food",
            "Price List",
          ];
          if (data.booking.hall_booking)
            this.food = data.booking.hall_booking.food;
          this.extra_amounts = data.booking.hall_booking.extra_amounts;

          this.hallRentTotalAmount = data.booking.hall_booking.hall_rent_amount;
          this.electricityTotalAmount =
            data.booking.hall_booking.hall_electricity_amount;

          this.audioTotalAmount = data.booking.hall_booking.hall_audio_system;
          this.projecterTotalAmount =
            data.booking.hall_booking.hall_projector_amount;
          this.cleaningTotalAmount =
            data.booking.hall_booking.hall_cleaning_charges;
          this.foodTotalAmount = data.booking.hall_booking.food_total_amount;
          this.otherCharges =
            data.booking.hall_booking.hall_extra_amounts_total;
          this.inv_total_tax = data.booking.hall_booking.inv_total_tax;
          this.inv_total_without_tax =
            data.booking.hall_booking.inv_total_without_tax;
          this.inv_total = data.booking.hall_booking.inv_total;
          this.discount = data.booking.hall_booking.discount;

          this.hallItemsTotal =
            parseFloat(this.hallRentTotalAmount) +
            parseFloat(this.electricityTotalAmount) +
            parseFloat(this.audioTotalAmount) +
            parseFloat(this.projecterTotalAmount) +
            parseFloat(this.cleaningTotalAmount) +
            parseFloat(this.foodTotalAmount) +
            parseFloat(this.otherCharges);
        } else {
          this.roomTypeColor = "primary";

          if (data.bookingwidget_confirmation_number != "") {
            this.itemsCustomer = [
              "Reservation",
              "Room",
              "Postings",
              "Transaction",
              "Online Booking",
            ];
          } else {
            this.itemsCustomer = [
              "Reservation",
              "Room",
              "Postings",
              "Transaction",
            ];
          }
        }
      });
    },
    getPriceFormat(price) {
      return parseFloat(price).toLocaleString("en-IN", {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
      });
    },
  },
};
</script>
<!-- <style scoped src="@/assets/css/custom.css"></style> -->
<!-- 
<style scoped>
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
</style> -->
