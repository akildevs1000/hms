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
          <v-toolbar :color="roomTypeColor" dark flat dense>
            <v-tabs v-model="activeTab" align-with-title>
              <span class="py-3 ml-2">
                {{ room_category_type }} {{
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
                    <v-col cols="12" class="text-center">
                       <v-avatar size="200">
                        <v-img
                            :src="booking?.customer?.captured_photo || '/no-profile-image.jpg'"
                          ></v-img>
                       </v-avatar>
                      </v-col>
                      <v-col cols="12" class="text-center">
                        <v-btn small dark class="primary"
                          @click="process_grc(booking.id)">
                          GRC
                          <v-icon right dark>mdi-file</v-icon>
                        </v-btn>
                        <v-btn small dark class="primary"
                        @click="downloadCustomerAttachments">
                        <v-icon>mdi-download-outline</v-icon>
                        <v-icon>mdi-account-tie</v-icon>
                        </v-btn>
                      </v-col>
                    <v-col md="12" class="pr-0 mr-0">
                      <div class="text-box-amt">
                        <table>
                          <tr class="bg-white amt-border-full">
                            <td>Room:</td>
                            <td class="text-right">

                              {{ $auth.user.company.currency }}{{
                                transactionSummary &&
                                numFormat(transactionSummary.sumDebit - transactionSummary.tot_posting)
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Posting :</td>
                            <td class="text-right">
                              {{ $auth.user.company.currency }}{{
                                transactionSummary &&
                                transactionSummary.tot_posting
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border bold" style="font-weight:bold">
                            <td>Total :</td>
                            <td class="text-right">
                              {{ $auth.user.company.currency }}{{
                                transactionSummary &&
                                transactionSummary.sumDebit
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border  ">
                            <td>Paid :</td>
                            <td class="text-right">
                              - {{ $auth.user.company.currency }}{{
                                transactionSummary &&
                                transactionSummary.sumCredit
                              }}
                            </td>
                          </tr>
                          <tr class="bg-white amt-border">
                            <td>Balance :</td>
                            <td class="red--text text-right">
                              {{ $auth.user.company.currency }}{{ numFormat(transactionSummary.balance) }}
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
                    <v-col md="4" v-if="booking.room_category_type == 'Hall'">
                      <div class="text-box" style="float: left">
                        <h6>Event Date</h6>
                        <p>{{ (booking &&
                          booking.hall_booking.event_date) ||
                          "---" }}</p>

                      </div>
                    </v-col>
                    <v-col md="4" v-if="booking.room_category_type == 'Hall'">
                      <div class="text-box" style="float: left">
                        <h6>Time</h6>
                        <p>

                        <p>{{ (booking &&
                          getHours(booking.hall_booking.event_start_time)) + ' - ' +
                          getHours(booking.hall_booking.event_end_time) || "---" }}</p>


                        </p>
                      </div>
                    </v-col>
                    <v-col md="4" v-if="booking.room_category_type != 'Hall'">
                      <div class="text-box" style="float: left">
                        <h6>Check In</h6>
                        <p>{{ (booking && booking.check_in_date) || "---" }}</p>
                      </div>
                    </v-col>
                    <v-col md="4" v-if="booking.room_category_type != 'Hall'">
                      <div class="text-box" style="float: left">
                        <h6>Check Out</h6>
                        <p>

                        <p>{{ (booking && booking.check_out_date) || "---" }}</p>

                        </p>
                      </div>
                    </v-col>
                    <v-col md="4" v-if="booking.room_category_type == 'Hall'">
                      <div class="text-box" style="float: left">
                        <h6>Pax</h6>
                        <p>
                          {{ (booking && booking.hall_booking.event_pax) || "---" }}
                        </p>
                      </div>
                    </v-col>
                    <v-col md="4" v-if="booking.room_category_type != 'Hall'">
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
                </v-col>
              </v-row>
            </v-tab-item>
            <v-tab-item class="px-3 py-4">
              <table class="responsive-table">
                <thead>
                  <tr class="table-header-text">
                    <th>No</th>
                    <th>Date</th>
                    <th>Room</th>
                    <th>Description</th>
                    <th>Adults</th>
                    <th>Child</th>
                    <th>Meal</th>
                    <th>Extra Bed</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                  </tr>
                </thead>
                <tbody v-for="(item, index) in orderRooms" :key="index">
                  <tr style="font-size: 13px">
                    <td>{{ ++index || "---" }}</td>
                    <td>{{ item.date || "---" }}</td>
                    <td>{{ item.room_no || "---" }}</td>
                    <td>{{ item.room_type || "---" }}</td>
                    <td>{{ item.no_of_adult }}</td>
                    <td>{{ item.no_of_child}}</td>
                    <td>{{item?.foodplan?.title + " ("+item?.foodplan?.unit_price+") " || "---"}}</td>
                    <td class="text-right">
                      {{ item.bed_amount || "---" }}
                    </td>
                    <td class="text-right">
                      {{ item.price || "---" }}
                    </td>
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
                      <v-icon v-if="can('accounts_posting_delete')"> x-small color="accent" @click="cancelPosting(item)"
                        class="mr-2">
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
                    <v-btn small class="elevation-0" color="#ECF0F4" @click="get_payment()">
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
                          <th>Staff</th>
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
                          <td>{{ item.user?.name || "---" }} {{ item.user?.last_name }}</td>
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
            <v-tab-item class="px-3" v-if="room_category_type == 'Hall'">
              <v-col cols="12">

                <table class="responsive-table mt-0  pt-5">
                  <thead>
                    <tr class="table-header-text">
                      <th>#</th>
                      <th>Name</th>
                      <th>Qty</th>
                      <th>Price</th>
                      <th>Total</th>

                    </tr>
                  </thead>
                  <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                    color="primary"></v-progress-linear>


                  <tbody>
                    <tr v-for="(item, index) in food" :key="index" style="font-size: 13px" class="no-bg">
                      <td>
                        <b>{{ ++index }}</b>
                      </td>
                      <td>{{ item.name || "---" }}</td>
                      <td>{{ item.qty || "---" }}</td>

                      <td>
                        {{

                          item.price_per_item || "---"
                        }}
                      </td>
                      <td>
                        {{

                          item.total || "---"
                        }}
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
                    <v-row style="background-color: #e3e3e3;">
                      <v-col md="3" cols="12"><label class="text-h6">Name</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        Total
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>


                    <v-row>
                      <v-col md="3" cols="12"><label>Hall rent</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(hallRentTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Electricity (EB) Charges</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(electricityTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>

                    <v-row>
                      <v-col md="3" cols="12"><label>Sound System </label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(audioTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label> Projector</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(projecterTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Setting arrangment & Cleaning charges</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(cleaningTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Food Total</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(foodTotalAmount) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Additional Events</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(otherCharges) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row style="font-weight:bold">
                      <v-col md="3" cols="12"><label>Total</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(hallItemsTotal) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="6">

                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Adjusted Final Amount(After Discount)</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(inv_total_without_tax) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Tax Collected with Food tax</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(inv_total_tax) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
                    </v-row>

                    <v-row>
                      <v-col cols="12">
                        <hr />
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12"><label>Grand Total</label>
                      </v-col>

                      <v-col md="2" cols="12" class="text-right">

                        {{ getPriceFormat(inv_total) }}
                      </v-col>
                      <v-col md="1" cols="12" class="text-right">


                      </v-col>
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
                      <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
                        color="primary"></v-progress-linear>


                      <tbody>
                        <tr v-for="(item, index) in extra_amounts" :key="index" style="font-size: 13px" class="no-bg">
                          <td>
                            <b>{{ ++index }}</b>
                          </td>
                          <td>{{ item.name || "---" }}</td>
                          <td>{{ item.qty || "---" }}</td>

                          <td>
                            {{

                              item.price_per_item || "---"
                            }}
                          </td>
                          <td>
                            {{

                              item.total || "---"
                            }}
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
                        <p style="color:green;;font-weight:bold" v-if="booking &&
                              booking.online_payment_response &&
                              booking.online_payment_response.razorpay_payment_link_status=='paid'">
                           Success
                        </p>
                        <p v-else style="color:red;font-weight:bold">
Failed
                        </p>
                      </div>
                    </v-col>
                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>Online Booking Reference Number</h6>
                        <p>
                          {{
                             (booking && booking.widget_confirmation_number && 
                              booking.widget_confirmation_number.split('_')[1]) || '---'
                          }} 
                        </p>
                      </div>
                    </v-col>
                    
                    
                   
                    <v-col md="3">
                      <div class="text-box" style="float: left">
                        <h6>RazorPay - Reference Id</h6>
                        <p>
                          {{
                            (booking &&
                              booking.payment_reference_id) ||
                            "---"
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
                              booking.online_payment_response.razorpay_payment_id) ||
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

    roomTypeColor: "primary",


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
      { id: 9, name: "09 AM" }
      , { id: 10, name: "10 AM" }
      , { id: 11, name: "11 AM" }
      , { id: 12, name: "12 PM" }
      , { id: 13, name: "01 PM" }
      , { id: 14, name: "02 PM" }
      , { id: 15, name: "03 PM" }
      , { id: 16, name: "04 PM" }
      , { id: 17, name: "05 PM" }
      , { id: 18, name: "06 PM" }
      , { id: 19, name: "07 PM" }
      , { id: 20, name: "08 PM" }
      , { id: 21, name: "09 PM" }
      , { id: 22, name: "10 PM" }
      , { id: 23, name: "11 PM" }
      // , { id: 0, name: "12 AM" }
      // , { id: 1, name: "01 AM" }
      // , { id: 2, name: "02 AM" }
      // , { id: 3, name: "03 AM" }
      // , { id: 4, name: "04 AM" }
      // , { id: 5, name: "05 AM" }
      // , { id: 6, name: "06 AM" }
      // , { id: 7, name: "07 AM" }
      // , { id: 8, name: "08 AM" }


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
    itemsCustomer: ["Reservation", "Room", "Postings", "Transaction","Online Booking"],
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
    room_category_type: '',
    food: [],
    extra_amounts: [],
  }),

  computed: {},
  created() {
    this.loading = true;
    this.getData();
  },
  mounted() { },

  methods: {
    downloadCustomerAttachments(){
      let id = this.$route.params.id;      
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${process.env.BACKEND_URL}download_customer_attachments/${id}`);
      document.body.appendChild(element);
      element.click();
    },
    getDate(dataTime) {
      return dataTime;
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
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
    getHours(time) {
      let arry = this.hours.find(hour => hour.id === time);
      if (arry) return arry.name;
      else return '';
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
      this.$router.push("/hotel/calendar1");
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
      this.$axios.get(`booking_customer/${id}`).then(({ data }) => {
        //assign booking
        this.totalPostingAmount = data.totalPostingAmount;
        this.totalTransactionAmount = data.totalTransactionAmount;
        this.transactions = data.transaction;
        this.transactionSummary = data.transactionSummary;
        const booking = data.booking;
        this.customer = booking.customer;
        this.booking = booking;

        this.booking.online_payment_response =JSON.parse(booking.payment_response);
        this.payments = booking.payments;
        this.bookedRooms = booking.booked_rooms;
        this.orderRooms = booking.order_rooms;
        this.postings = data.postings;
        //end booking
        this.loading = false;
        this.showImage = data.booking.customer.image;
        this.calTotalAmount(this.payments);
        this.room_category_type = data.booking.room_category_type;

        if (this.room_category_type == 'Hall') {
          this.roomTypeColor = "green";
          this.itemsCustomer = ["Reservation", "Room", "Postings", "Transaction", "Food", "Price List"];
          if (data.booking.hall_booking)
            this.food = data.booking.hall_booking.food;
          this.extra_amounts = data.booking.hall_booking.extra_amounts;

          this.hallRentTotalAmount = data.booking.hall_booking.hall_rent_amount;
          this.electricityTotalAmount = data.booking.hall_booking.hall_electricity_amount;

          this.audioTotalAmount = data.booking.hall_booking.hall_audio_system;
          this.projecterTotalAmount = data.booking.hall_booking.hall_projector_amount;
          this.cleaningTotalAmount = data.booking.hall_booking.hall_cleaning_charges;
          this.foodTotalAmount = data.booking.hall_booking.food_total_amount;
          this.otherCharges = data.booking.hall_booking.hall_extra_amounts_total;
          this.inv_total_tax = data.booking.hall_booking.inv_total_tax;
          this.inv_total_without_tax = data.booking.hall_booking.inv_total_without_tax;
          this.inv_total = data.booking.hall_booking.inv_total;
          this.discount = data.booking.hall_booking.discount;


          this.hallItemsTotal = parseFloat(this.hallRentTotalAmount)
            + parseFloat(this.electricityTotalAmount)
            + parseFloat(this.audioTotalAmount)
            + parseFloat(this.projecterTotalAmount)
            + parseFloat(this.cleaningTotalAmount)
            + parseFloat(this.foodTotalAmount)
            + parseFloat(this.otherCharges)
            ;

        }
        else {
          this.roomTypeColor = "primary";

          if( data.bookingwidget_confirmation_number!='')
          {
            this.itemsCustomer = ["Reservation", "Room", "Postings", "Transaction","Online Booking"];
          }
          else

          {
            this.itemsCustomer = ["Reservation", "Room", "Postings", "Transaction" ];
          }
         
        }



      });
    },
    getPriceFormat(price) {

      return parseFloat(price).toLocaleString('en-IN', {
        maximumFractionDigits: 2,
        minimumFractionDigits: 2,
      });
    },
  },
};
</script>
<style scoped src="@/assets/css/custom.css"></style>

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
