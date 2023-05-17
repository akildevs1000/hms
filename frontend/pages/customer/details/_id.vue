<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row>
      <v-dialog v-model="GRCDialog" persistent :width="900" class="checkin-models">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ "GRC" }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="GRCDialog = false">mdi mdi-close-box</v-icon>
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
            <v-icon dark class="pa-0" @click="imgView = false">mdi mdi-close-box</v-icon>
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
            <v-icon dark class="pa-0" @click="payingDialog = false">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-card-text>
            <Paying :BookingData="payData" @close-dialog="closeDialogs"></Paying>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-col md="12">
        <v-card class="mb-5 rounded-md mt-3" elevation="0">
          <v-toolbar color="primary" dark flat dense>
            <v-tabs v-model="activeTab" align-with-title>
              <span class="py-3 ml-2">
                {{
                  getRelaventStatus(booking && booking.booking_status)
                }}Guest</span>
              <v-spacer></v-spacer>
              <v-tab active-class="active-link" v-for="item in itemsCustomer" :key="item">
                {{ item }}
              </v-tab>
              <v-tabs-slider color="#1259a7"></v-tabs-slider>
            </v-tabs>
            <v-icon dark class="pa-0" @click="redirect">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-tabs-items v-model="activeTab">
            <v-tab-item class="px-3 py-4">
              <v-row>
                <v-col md="2" cols="12">
                  <v-row>
                    <v-col md="12">
                      <v-img class="guest-avatar" :src="showImage"> </v-img>
                    </v-col>
                    <v-col md="12">
                      <div class="d-flex justify-space-between">
                        <v-btn v-if="booking && booking.document" style="width: 50%" small dark
                          class="primary pt-4 pb-4 mt-4 mr-1 w-50 ipad-preview"
                          @click="preview(booking && booking.document)">
                          ID
                          <v-icon right dark>mdi-file</v-icon>
                        </v-btn>
                        <v-btn style="width: 50%" small dark class="primary pt-4 pb-4 mt-4 w-50"
                          @click="process_grc(booking.id)">
                          GRC
                          <v-icon right dark>mdi-file</v-icon>
                        </v-btn>
                      </div>
                    </v-col>
                    <v-col md="12" class="pr-0 mr-0">
                      <div class="text-box-amt">
                        <table>
                          <tr class="bg-white amt-border-full">
                            <td>Room:</td>
                            <td class="text-right">
                              ₹{{
                                transactionSummary &&
                                transactionSummary.sumDebit
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Posting :</td>
                            <td class="text-right">
                              ₹{{
                                transactionSummary &&
                                transactionSummary.tot_posting
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Paid :</td>
                            <td class="text-right">
                              ₹{{
                                transactionSummary &&
                                transactionSummary.sumCredit
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Balance :</td>
                            <td class="red--text text-right">
                              ₹{{ numFormat(transactionSummary.balance) }}
                            </td>
                          </tr>
                        </table>
                      </div>
                    </v-col>
                  </v-row>
                </v-col>

                <v-col md="9" cols="12">
                  <v-row class="mt-4">
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Guest Name</h6>
                        <p>
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.title) ||
                            ""
                          }}.
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.full_name) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Mobile</h6>
                        <p>
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.contact_no) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Whatsapp</h6>
                        <p>
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.whatsapp) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>
                  <v-row class="my-0 py-0">
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Reservation No</h6>
                        <p>
                          {{ (booking && booking.reservation_no) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Number of Rooms</h6>
                        <p>
                          {{ (bookedRooms && bookedRooms.length) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Booking Date</h6>
                        <p>
                          {{ (booking && booking.booking_date) || "---" }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>

                  <v-row class="my-0 py-0">
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Check In</h6>
                        <p>{{ (booking && booking.check_in_date) || "---" }}</p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Check Out</h6>
                        <p>
                          {{ (booking && booking.check_out_date) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Days</h6>
                        <p>
                          {{ (booking && booking.total_days) || "---" }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>

                  <v-row class="my-0 py-0">
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Pay Type</h6>
                        <p>
                          {{
                            (booking && booking.paid_by == 2
                              ? "Paid By Agent"
                              : "Paid By Guest") || "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Source</h6>
                        <p>
                          {{ (booking && booking.source) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4">
                      <div class="text-box" style="float: left">
                        <h6>Reference Number</h6>
                        <p>
                          {{ (booking && booking.reference_no) || "---" }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>

                  <v-row class="my-0 py-0">
                    <v-col md="6">
                      <div class="text-box" style="float: left">
                        <h6>Guest Request</h6>
                        <p>
                          {{ (booking && booking.request) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="6">
                      <div class="text-box" style="float: left">
                        <h6>Purpose</h6>
                        <p>
                          {{ (booking && booking.purpose) || "---" }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>

                  <v-row class="my-0 py-0">
                    <v-col md="6">
                      <div class="text-box" style="float: left">
                        <h6>GST</h6>
                        <p>
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.gst_number) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="6">
                      <div class="text-box" style="float: left">
                        <h6>Address</h6>
                        <p>
                          {{
                            (booking &&
                              booking.customer &&
                              booking.customer.address) ||
                            "---"
                          }}
                        </p>
                      </div>
                    </v-col>
                  </v-row>

                  <!-- <v-row>
                    <v-col>
                      <div class="text-box" style="float: left">
                        <tr class="bg-white">
                          <td>Posting Amount :</td>
                          <td>
                            {{
                              transactionSummary &&
                              transactionSummary.tot_posting
                            }}
                          </td>
                        </tr>
                        <tr class="bg-white">
                          <td>Total Amount :</td>
                          <td>
                            {{
                              transactionSummary && transactionSummary.sumDebit
                            }}
                          </td>
                        </tr>
                        <tr class="bg-white">
                          <td>Paid Amount :</td>
                          <td>
                            {{
                              transactionSummary && transactionSummary.sumCredit
                            }}
                          </td>
                        </tr>
                        <tr class="bg-white">
                          <td>Remaining Amount :</td>
                          <td class="red--text">
                            {{ numFormat(transactionSummary.balance) }}
                          </td>
                        </tr>
                      </div>
                    </v-col>
                  </v-row> -->
                </v-col>
              </v-row>

              <!-- <tr
                    class="bg-white"
                    v-if="
                      booking && booking.customer && booking.customer.gst_number
                    "
                  >
                    <td>GST :</td>
                    <td>
                      {{
                        booking &&
                        booking.customer &&
                        booking.customer.gst_number
                      }}
                    </td>
                  </tr> -->

              <!-- <v-alert border="left" colored-border color="deep-purple accent-4" elevation="1">
                <table>
                  <tr>
                    <td>Customer :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.title) ||
                        ""
                      }}.
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.full_name) ||
                        "---"
                      }}
                    </td>
                    <td>Contact No :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.contact_no) ||
                        "---"
                      }}
                    </td>
                    <td>Whatsapp :</td>
                    <td>
                      {{
                        (booking &&
                          booking.customer &&
                          booking.customer.whatsapp) ||
                        "---"
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6">
                      <hr />
                    </td>
                  </tr>
                  <tr>
                    <td>Reservation No :</td>
                    <td>{{ (booking && booking.reservation_no) || "---" }}</td>
                    <td>Number of Rooms :</td>
                    <td>{{ (bookedRooms && bookedRooms.length) || "---" }}</td>
                    <td>Booking Date :</td>
                    <td>{{ (booking && booking.booking_date) || "---" }}</td>
                  </tr>
                  <tr>
                    <td>Source :</td>
                    <td>{{ (booking && booking.source) || "---" }}</td>
                    <td>Reference Number :</td>
                    <td>{{ (booking && booking.reference_no) || "---" }}</td>
                    <td>Pay Type :</td>
                    <td>
                      {{
                        (booking && booking.paid_by == 2
                          ? "Paid By Agent"
                          : "Paid By Guest") || "---"
                      }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Check In :</td>
                    <td>{{ (booking && booking.check_in_date) || "---" }}</td>
                    <td>Check Out :</td>
                    <td>{{ (booking && booking.check_out_date) || "---" }}</td>
                    <td>Days :</td>
                    <td>{{ (booking && booking.total_days) || "---" }}</td>
                  </tr>
                  <tr>
                    <td colspan="6">
                      <hr />
                    </td>
                  </tr>

                  <tr class="bg-white">
                    <td>Posting Amount :</td>
                    <td>
                      {{ transactionSummary && transactionSummary.tot_posting }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Total Amount :</td>
                    <td>
                      {{ transactionSummary && transactionSummary.sumDebit }}
                     </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Paid Amount :</td>
                    <td>
                      {{ transactionSummary && transactionSummary.sumCredit }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Remaining Amount :</td>
                    <td class="red--text">
                      {{ numFormat(transactionSummary.balance) }}
                    </td>
                  </tr>

                  <tr class="bg-white">
                    <td colspan="6">
                      <hr />
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Customer Request :</td>
                    <td>
                      {{ (booking && booking.request) || "---" }}
                    </td>
                  </tr>
                  <tr class="bg-white">
                    <td>Purpose :</td>
                    <td>
                      {{ (booking && booking.purpose) || "---" }}
                    </td>
                  </tr>
                  <tr class="bg-white" v-if="booking && booking.document">
                    <td>Document :</td>
                    <td class="red--text">
                      <v-btn small dark class="primary pt-4 pb-4" @click="preview(booking && booking.document)">
                        Preview
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                </table>
              </v-alert> -->

              <!-- <div>
                <v-row>
                  <v-col cols="3"><b>Reservation No </b></v-col>
                  <v-col cols="3">{{ (booking && booking.id) || "---" }}</v-col>
                  <v-col cols="4"><b>Number of Rooms </b></v-col>
                  <v-col cols="2">{{
                    (bookedRooms && bookedRooms.length) || "---"
                  }}</v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Source </b></v-col>
                  <v-col cols="3">{{
                    (booking && booking.source) || "---"
                  }}</v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Check In </b></v-col>
                  <v-col cols="3">{{
                    (booking && booking.check_in_date) || "---"
                  }}</v-col>
                  <v-col cols="2"><b>Check Out </b></v-col>
                  <v-col cols="4">{{
                    (booking && booking.check_out_date) || "---"
                  }}</v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Rooms Amount </b></v-col>
                  <v-col cols="3">{{
                    (booking && booking.total_price) || "0"
                  }}</v-col>
                  <v-col cols="2"><b>Days </b></v-col>
                  <v-col cols="2">{{
                    (booking && booking.total_days) || "---"
                  }}</v-col>
                </v-row>

                <v-row>
                  <v-row>
                    <v-col cols="3"><b>Posting Amount</b></v-col>
                    <v-col cols="4">
                      <span> {{ totalPostingAmount || "0" }}</span>
                    </v-col>
                  </v-row>
                  <v-col cols="3"><b>Remaining Amount</b></v-col>
                  <v-col cols="4">
                    <span>
                      {{ (booking && booking.remaining_price) || "0" }}</span
                    >
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Grand Remaining</b></v-col>
                  <v-col cols="4">
                    <span class="red--text">
                      {{
                        (booking && booking.grand_remaining_price) || "0"
                      }}</span
                    >
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="3"><b>Customer Request</b></v-col>
                  <v-col cols="8">{{
                    (booking && booking.request) || "---"
                  }}</v-col>
                </v-row>
                <v-row v-if="booking && booking.document">
                  <v-col cols="3"><b>Document</b></v-col>
                  <v-col cols="8">
                    <v-btn
                      small
                      dark
                      class="primary pt-4 pb-4"
                      @click="preview(booking && booking.document)"
                    >
                      Preview
                      <v-icon right dark>mdi-file</v-icon>
                    </v-btn>
                  </v-col>
                </v-row>
              </div> -->
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <!--
                  this is booked rooms
                <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Description</th>
                    <th>Adults</th>
                    <th>Child</th>
                    <th>Babies</th>
                    <th>Meal Plan</th>
                    <th>Adult Food Amount</th>
                    <th>Child Food Amount</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">After Discount</th>
                    <th class="text-right">Sgst</th>
                    <th class="text-right">Cgst</th>
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in bookedRooms" :key="index">
                  <tr style="font-size: 13px">
                    <td>{{ item.room_no || "---" }}</td>
                    <td>{{ item.room_type || "---" }}</td>
                    <td>{{ item.no_of_adult || "---" }}</td>
                    <td>{{ item.no_of_child || "---" }}</td>
                    <td>{{ item.no_of_baby || "---" }}</td>
                    <td>{{ capsTitle(item.meal) || "---" }}</td>
                    <td class="text-right">
                      {{ item.tot_adult_food || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.tot_child_food || "---" }}
                    </td>
                    <td class="text-right">{{ item.price || "---" }}</td>
                    <td class="text-right">
                      {{ item.room_discount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.after_discount || "---" }}
                    </td>
                    <td class="text-right">{{ item.sgst || "---" }}</td>
                    <td class="text-right">{{ item.cgst || "---" }}</td>
                    <td class="text-right">{{ item.total || "---" }}</td>
                  </tr>
                  <tr
                    style="font-size: 13px"
                    v-for="(postingItem, postingIndex) in item.postings"
                    :key="postingIndex"
                  >
                    <td>{{ item.room_no || "---" }}</td>
                    <td>(Posting) {{ postingItem.item || "---" }}</td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right"></td>
                    <td class="text-right">
                      {{ postingItem.amount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ postingItem.amount || "---" }}
                    </td>
                    <td class="text-right">{{ postingItem.sgst || "---" }}</td>
                    <td class="text-right">{{ postingItem.cgst || "---" }}</td>
                    <td class="text-right">
                      {{ postingItem.amount_with_tax || "---" }}
                    </td>
                  </tr>
                </tbody>
              </table> -->

              <!-- <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Description</th>
                    <th>Adults</th>
                    <th>Child</th>
                    <th>Babies</th>
                    <th>Meal Plan</th>
                    <th>Adult Food Amount</th>
                    <th>Child Food Amount</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">After Discount</th>
                    <th class="text-right">Sgst</th>
                    <th class="text-right">Cgst</th>
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in bookedRooms" :key="index">
                  <tr style="font-size: 13px">
                    <td>{{ item.room_no || "---" }}</td>
                    <td>{{ item.room_type || "---" }}</td>
                    <td>{{ item.no_of_adult || "---" }}</td>
                    <td>{{ item.no_of_child || "---" }}</td>
                    <td>{{ item.no_of_baby || "---" }}</td>
                    <td>{{ capsTitle(item.meal) || "---" }}</td>
                    <td class="text-right">
                      {{ item.tot_adult_food || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.tot_child_food || "---" }}
                    </td>
                    <td class="text-right">{{ item.price || "---" }}</td>
                    <td class="text-right">
                      {{ item.room_discount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.after_discount || "---" }}
                    </td>
                    <td class="text-right">{{ item.sgst || "---" }}</td>
                    <td class="text-right">{{ item.cgst || "---" }}</td>
                    <td class="text-right">{{ item.total || "---" }}</td>
                  </tr>
                </tbody>
              </table> -->

              <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Date</th>
                    <th>Room</th>
                    <th>Description</th>
                    <th>Adults</th>
                    <th>Child</th>
                    <th>Babies</th>
                    <th>Meal Plan</th>
                    <th>Adult Food Amount</th>
                    <th>Child Food Amount</th>
                    <!-- <th class="text-right">Price</th> -->
                    <!-- <th class="text-right">Discount</th> -->
                    <th class="text-right">Price</th>
                    <!-- <th class="text-right">Sgst</th> -->
                    <!-- <th class="text-right">Cgst</th> -->
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in orderRooms" :key="index">
                  <tr style="font-size: 13px">
                    <td>{{ ++index || "---" }}</td>
                    <td>{{ item.date || "---" }}</td>
                    <td>{{ item.room_no || "---" }}</td>
                    <td>{{ item.room_type || "---" }}</td>
                    <td>{{ item.no_of_adult || "---" }}</td>
                    <td>{{ item.no_of_child || "---" }}</td>
                    <td>{{ item.no_of_baby || "---" }}</td>
                    <td>{{ capsTitle(item.meal) || "---" }}</td>
                    <td class="text-right">
                      {{ item.tot_adult_food || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.tot_child_food || "---" }}
                    </td>
                    <!-- <td class="text-right">{{ item.price || "---" }}</td>
                    <td class="text-right">
                      {{ item.room_discount || "---" }}
                    </td> -->
                    <td class="text-right">
                      {{ item.after_discount || "---" }}
                    </td>
                    <!-- <td class="text-right">{{ item.sgst || "---" }}</td> -->
                    <!-- <td class="text-right">{{ item.cgst || "---" }}</td> -->
                    <td class="text-right">{{ item.total || "---" }}</td>
                  </tr>
                </tbody>
              </table>
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Bill</th>
                    <th>Date</th>
                    <th>Room Type</th>
                    <th>Room</th>
                    <th>Item</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">QTY</th>
                    <th class="text-right">Sgst</th>
                    <th class="text-right">Cgst</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr style="font-size: 13px" v-for="(item, postingIndex) in postings" :key="postingIndex">
                    <td>{{ ++postingIndex }}</td>
                    <td>{{ item.bill_no || "---" }}</td>
                    <td>{{ item.posting_date || "---" }}</td>
                    <td>
                      {{
                        (item.room &&
                          item.room.room_type &&
                          item.room.room_type.name) ||
                        "---"
                      }}
                    </td>
                    <td>{{ (item.room && item.room.room_no) || "---" }}</td>
                    <td>{{ item.item || "---" }}</td>
                    <td class="text-right">{{ item.single_amt || "---" }}</td>
                    <td>{{ item.qty || "---" }}</td>
                    <td class="text-right">{{ item.sgst || "---" }}</td>
                    <td class="text-right">{{ item.cgst || "---" }}</td>
                    <td class="text-right">
                      {{ item.amount_with_tax || "---" }}
                    </td>
                    <td class="text-center">
                      <v-icon x-small color="accent" @click="cancelPosting(item)" class="mr-2">
                        mdi-delete
                      </v-icon>
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-tab-item>
            <v-tab-item class="px-3">
              <v-card flat>
                <v-row>
                  <v-col md="12" class="mt-2 text-right">
                    <v-btn small class="elevation-0" color="#ECF0F4" @click="get_payment()" v-if="can()">
                      Transaction
                      <v-icon right>mdi mdi-cash-sync</v-icon>
                    </v-btn>
                  </v-col>
                  <v-col cols="12">
                    <table class="responsive-table mt-0">
                      <thead>
                        <tr class="table-header-text">
                          <th>#</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>payment Mode</th>
                          <th>Reference</th>
                          <th>Description</th>
                          <th>Debit</th>
                          <th>Credit</th>
                          <th>Balance</th>
                        </tr>
                      </thead>
                      <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                        color="primary"></v-progress-linear>
                      <tbody>
                        <tr v-for="(item, index) in transactions" :key="index" style="font-size: 13px" class="no-bg">
                          <td>
                            <b>{{ ++index }}</b>
                          </td>
                          <td>{{ item.date || "---" }}</td>
                          <td>{{ item.time || "---" }}</td>
                          <td>
                            {{
                              (item &&
                                item.payment_mode &&
                                item.payment_mode.name) ||
                              "---"
                            }}
                          </td>
                          <td>{{ item.reference_number || "---" }}</td>
                          <td>{{ item.desc || "---" }}</td>
                          <td class="text-right">
                            {{ item && item.debit == 0 ? "---" : item.debit }}
                          </td>
                          <td class="text-right">
                            {{ item && item.credit == 0 ? "---" : item.credit }}
                          </td>
                          <td class="text-right">
                            {{ item.balance || "---" }}
                          </td>
                        </tr>
                      </tbody>
                      <tr style="font-size: 13px">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th class="text-right">Balance</th>
                        <td class="text-right">
                          {{ totalTransactionAmount }}
                        </td>
                      </tr>
                    </table>
                  </v-col>
                </v-row>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col md="7">

      </v-col>
    </v-row>
  </div>
</template>
<script>
import Grc from "./../../../components/booking/GRC.vue";
import ImagePreview from "../../../components/images/ImagePreview.vue";
import Paying from '../../../components/booking/Paying.vue';
export default {
  components: {
    Paying,
    ImagePreview,
    Grc,
  },
  data: () => ({
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
    itemsCustomer: ["Reservation", "Room", "Postings", "Transaction"],
    tab1: null,
    activeTab: 0,

    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",

    tab: null,
    headers: [
      {
        text: "#",
      },
      {
        text: "Type",
      },
      {
        text: "Source",
      },
      {
        text: "Rooms",
      },
      {
        text: "Booking Date",
      },
      {
        text: "Check In",
      },
      {
        text: "Check Out",
      },
      {
        text: "Total Price",
      },
    ],
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
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() { },

  methods: {
    getDate(dataTime) {
      return dataTime;
    },

    can() {
      return this.$auth.user.user_type == "company" ? true : false;

      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
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
        case -1:
          return "Cancel Reservation ";
        default:
          return " ";
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

      // let url = 'grc_report_download';
      // let element = document.createElement("a");
      // element.setAttribute("target", "_blank");
      // element.setAttribute("href", `${process.env.BACKEND_URL}${url}/${id}`);
      // document.body.appendChild(element);
      // element.click();
    },

    // preview(file) {
    //   let element = document.createElement("a");
    //   element.setAttribute("target", "_blank");
    //   element.setAttribute("href", file);
    //   document.body.appendChild(element);
    //   element.click();
    //   // document.body.removeChild(element);
    // },

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
      this.payData = this.booking

      // {
      //   id: this.$route.params.id,
      // };
      this.payingDialog = true;
    },

    getData() {
      let id = this.$route.params.id;
      console.log(id);
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
        this.transactionSummary = data.transactionSummary;
        const booking = data.booking;
        this.customer = booking.customer;
        this.booking = booking;
        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        this.orderRooms = booking.order_rooms;
        this.postings = data.postings;
        //end booking
        this.loading = false;
        this.showImage = data.booking.customer.image;
        this.calTotalAmount(this.payments);
      });
    },
  },
};
</script>
<style scoped src="@/assets/custom.css"></style>

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
</style>
