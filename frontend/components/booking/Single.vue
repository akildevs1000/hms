<template>
  <v-dialog v-model="ViewBookingDialog" :max-width="isGroup ? 1100 : 1000">
    <AssetsIconClose
      :left="isGroup ? 1090 : 990"
      @click="ViewBookingDialog = false"
    />
    <template v-if="!noLabel" v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> View Billing </span>
    </template>
    <ViewBox
      ref="ViewBox"
      :id="$route.params.id"
      :customer="booking.customer"
    />
    <div class="grey lighten-3 pa-2" style="overflow: hidden">
      <v-row>
        <v-col cols="3" v-if="isGroup">
          <v-card
            :style="`border: 3px solid white; min-height: ${
              isGroup ? 470 : 390
            }px`"
          >
            <v-card-text>
              <v-row no-gutter v-if="roomData && booking && booking.id">
                <v-col cols="12" class="pa-0 ma-0">
                  <v-row no-gutter>
                    <v-col cols="12">
                      <div
                        class="grey lighten-3 py-2 px-1"
                        style="
                          border-radius: 5px;
                          color: #6f6f68;
                          font-size: 14px;
                        "
                      >
                        Group Booking
                      </div>
                      <div class="mx-2 mt-1">
                        <table cellspacing="0" style="width: 100%">
                          <tr>
                            <td colspan="2" class="blue--text">
                              <v-row>
                                <v-col>
                                  <span> Payer </span>
                                  <!-- <v-avatar size="25">
                                    <v-img
                                      @click="
                                        $refs[`ViewBox`][`viewBoxDialog`] = true
                                      "
                                      class="zoom-on-hover"
                                      style="z-index: 1; width: 100%"
                                      :src="
                                        booking?.customer?.captured_photo ||
                                        'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                                      "
                                    />
                                  </v-avatar> -->
                                </v-col>
                                <v-col cols="6" class="text-right">
                                  <v-btn
                                    v-if="!customerScreen"
                                    @click="showRelatedScreen"
                                    text
                                    small
                                    class="grey lighten-3"
                                    ><v-icon color="primary"
                                      >mdi-account-tie</v-icon
                                    >
                                    Payer</v-btn
                                  >
                                  <v-btn
                                    v-else
                                    @click="showRelatedScreen"
                                    text
                                    small
                                    class="grey lighten-3"
                                    ><v-icon color="primary"
                                      >mdi-account</v-icon
                                    >
                                    Guest</v-btn
                                  >
                                </v-col>
                              </v-row>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <b>{{ booking.title || "---" }} </b>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              Tel : {{ booking.customer?.contact_no || "---" }}
                            </td>
                          </tr>
                          <tr>
                            <td>
                              Email : {{ booking?.customer?.email || "---" }}
                            </td>
                          </tr>
                          <tr>
                            <td>
                              Address :
                              {{ $utils.full_address(booking?.customer) }}
                            </td>
                          </tr>
                        </table>
                        <table class="my-2" style="width: 100%">
                          <tr>
                            <td class="blue--text">
                              <span> Booking Details </span>
                            </td>
                            <td class="red--text text-right">
                              <span>
                                Reservation # {{ booking.reservation_no }}
                              </span>
                            </td>
                          </tr>
                        </table>
                        <table style="width: 100%">
                          <!-- <tr>
                            <td width="50%" class="border-bottom">
                              Booking Price
                            </td>
                            <td width="50%" class="border-bottom">
                              <span class="blue--text">{{
                                $utils.currency_format(booking.total_price)
                              }}</span>
                            </td>
                          </tr> -->

                          <tr>
                            <td width="50%" class="border-bottom">Room</td>
                            <td width="50%" class="border-bottom">
                              {{ bookedRooms.length }}
                            </td>
                          </tr>
                          <tr>
                            <td width="50%" class="border-bottom">
                              Booking Date
                            </td>
                            <td width="50%" class="border-bottom">
                              {{ booking.booking_date }}
                            </td>
                          </tr>
                          <tr>
                            <td width="50%" class="border-bottom">Check In</td>
                            <td width="50%" class="border-bottom">
                              {{ booking.check_in }} 12:00 PM
                            </td>
                          </tr>
                          <tr>
                            <td width="50%" class="border-bottom">Check Out</td>
                            <td width="50%" class="border-bottom">
                              {{ booking.check_out }} 11:00 AM
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Extra Bed</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData?.extra_bed_qty ? "yes" : "no" }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">Food</td>
                            <td width="50%" class="border-bottom">
                              {{ roomData.food_plan || "No Food" }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">
                              Early Check In
                            </td>
                            <td width="50%" class="border-bottom">
                              {{ roomData?.early_check_in ? "yes" : "no" }}
                            </td>
                          </tr>

                          <tr>
                            <td width="50%" class="border-bottom">
                              Late Check Out
                            </td>
                            <td width="50%" class="border-bottom">
                              {{ roomData?.late_check_out ? "yes" : "no" }}
                            </td>
                          </tr>
                        </table>
                        <div class="mt-3">Notes : {{ booking.request }}</div>
                      </div>
                    </v-col>
                  </v-row>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col>
          <v-card
            :style="`border: 3px solid white; min-height: ${
              isGroup ? 470 : 390
            }px`"
          >
            <v-card-text>
              <v-row no-gutter v-if="booking && booking.id">
                <v-col cols="12" class="pa-0 ma-0">
                  <v-toolbar :color="`grey lighten-3`" flat dense>
                    <v-tabs
                      v-model="activeTab"
                      background-color="grey lighten-3"
                      dense
                      rounded
                    >
                      <div class="pa-3">
                        <span> On-premises Guest</span>
                      </div>

                      <v-spacer></v-spacer>
                      <v-tab v-for="item in itemsCustomer" :key="item">
                        {{ item }}
                      </v-tab>
                    </v-tabs>
                  </v-toolbar>
                  <v-tabs-items
                    v-if="customer && customer.id"
                    v-model="activeTab"
                  >
                    <v-tab-item class="px-3 py-4">
                      <v-row v-if="customerScreen">
                        <v-col md="2" cols="12">
                          <ViewBox
                            ref="ViewBox"
                            :id="$route.params.id"
                            :customer="booking.customer"
                          />
                          <div>
                            <v-img
                              @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
                              class="zoom-on-hover"
                              style="z-index: 1; width: 100%"
                              :src="
                                booking?.customer?.captured_photo ||
                                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                              "
                            />
                          </div>

                          <v-row>
                            <v-col cols="6">
                              <div style="height: 25px" class="py-1">
                                <v-img
                                  @click="
                                    $refs[`ViewBox`][`viewBoxDialog`] = true
                                  "
                                  class="zoom-on-hover"
                                  style="width: 100%"
                                  :src="
                                    booking?.customer?.id_frontend_side ||
                                    '/idf.png'
                                  "
                                />
                              </div>
                            </v-col>
                            <v-col cols="6">
                              <div style="height: 25px" class="py-1">
                                <v-img
                                  @click="
                                    $refs[`ViewBox`][`viewBoxDialog`] = true
                                  "
                                  class="zoom-on-hover"
                                  style="width: 100%"
                                  :src="
                                    booking?.customer?.id_backend_side ||
                                    '/idb.png'
                                  "
                                />
                              </div>
                            </v-col>
                            <v-col>
                              <table
                                style="width: 100%; border-collapse: collapse"
                              >
                                <tr>
                                  <td
                                    class="text-left"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    Room:
                                  </td>
                                  <td
                                    class="text-right"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    {{
                                      transactionSummary &&
                                      $utils.currency_format(
                                        transactionSummary.sumDebit -
                                          transactionSummary.tot_posting
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    Posting:
                                  </td>
                                  <td
                                    class="text-right"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    {{
                                      transactionSummary &&
                                      $utils.currency_format(
                                        transactionSummary.tot_posting
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    Total:
                                  </td>
                                  <td
                                    class="text-right"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    {{
                                      transactionSummary &&
                                      $utils.currency_format(
                                        transactionSummary.sumDebit
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    Paid:
                                  </td>
                                  <td
                                    class="text-right red--text"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    -{{
                                      transactionSummary &&
                                      $utils.currency_format(
                                        transactionSummary.sumCredit
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    Balance:
                                  </td>
                                  <td
                                    class="text-right primary--text"
                                    style="
                                      font-size: 11px;
                                      border-bottom: 1px solid #ccc;
                                    "
                                  >
                                    {{
                                      transactionSummary &&
                                      $utils.currency_format(
                                        transactionSummary.balance
                                      )
                                    }}
                                  </td>
                                </tr>
                              </table>
                            </v-col>
                          </v-row>
                        </v-col>
                        <v-col>
                          <v-row>
                            <v-col cols="4">
                              <v-text-field
                                label="Full Name"
                                v-model="full_name"
                                readonly
                                dense
                                outlined
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                label="Mobile"
                                v-model="customer.contact_no"
                                readonly
                                dense
                                outlined
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                label="Whatsapp"
                                v-model="customer.whatsapp"
                                readonly
                                dense
                                outlined
                                hide-details
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Purpose"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.purpose"
                              ></v-text-field>
                            </v-col>
                            <v-col
                              cols="9"
                              v-if="booking && booking.booking_type == 'room'"
                            >
                              <v-row>
                                <v-col>
                                  <v-text-field
                                    label="Check In"
                                    readonly
                                    :value="booking.check_in_date"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                                <v-col>
                                  <v-text-field
                                    label="Check Out"
                                    readonly
                                    :value="booking.check_out_date"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                                <v-col>
                                  <v-text-field
                                    label="Nights"
                                    readonly
                                    :value="booking.total_days"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                              </v-row>
                            </v-col>

                            <v-col cols="9" v-else>
                              <v-row>
                                <v-col cols="4">
                                  <v-text-field
                                    label="Event Start"
                                    readonly
                                    v-model="booking.hall_check_in_date"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                                <v-col cols="4">
                                  <v-text-field
                                    label="Event End"
                                    readonly
                                    v-model="booking.hall_check_out_date"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                                <v-col cols="4">
                                  <v-text-field
                                    label="Total Hours"
                                    readonly
                                    v-model="bookedRooms[0].total_booking_hours"
                                    dense
                                    outlined
                                    hide-details
                                  ></v-text-field>
                                </v-col>
                              </v-row>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Pay Type"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="getRelatedPaidBy"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Reach By"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.customer.customer_type"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Source"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.source"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Reference Number"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.reference_no"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="Country"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.customer.country"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="State"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.customer.state"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="City"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.customer.city"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="3">
                              <v-text-field
                                label="City"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.customer.zip_code"
                              ></v-text-field>
                            </v-col>

                            <v-col cols="12">
                              <v-text-field
                                label="Guest Request"
                                dense
                                outlined
                                hide-details
                                readonly
                                v-model="booking.request"
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>

                      <v-row v-else>
                        <v-col md="2" cols="12">
                          <ViewBox
                            ref="ViewBox"
                            :id="$route.params.id"
                            :customer="booking.customer"
                          />
                          <div>
                            <v-img
                              @click="$refs[`ViewBox`][`viewBoxDialog`] = true"
                              class="zoom-on-hover"
                              style="z-index: 1; width: 100%"
                              :src="
                                booking?.customer?.captured_photo ||
                                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                              "
                            />
                          </div>

                          <v-row>
                            <v-col cols="6">
                              <div style="height: 25px" class="py-1">
                                <v-img
                                  @click="
                                    $refs[`ViewBox`][`viewBoxDialog`] = true
                                  "
                                  class="zoom-on-hover"
                                  style="width: 100%"
                                  :src="
                                    booking?.customer?.id_frontend_side ||
                                    '/idf.png'
                                  "
                                />
                              </div>
                            </v-col>
                            <v-col cols="6">
                              <div style="height: 25px" class="py-1">
                                <v-img
                                  @click="
                                    $refs[`ViewBox`][`viewBoxDialog`] = true
                                  "
                                  class="zoom-on-hover"
                                  style="width: 100%"
                                  :src="
                                    booking?.customer?.id_backend_side ||
                                    '/idb.png'
                                  "
                                />
                              </div>
                            </v-col>
                            <v-col>
                              <table
                                style="width: 100%; border-collapse: collapse"
                              >
                                <tr>
                                  <td
                                    class="text-left border-bottom"
                                    style="font-size: 11px"
                                  >
                                    Posting:
                                  </td>
                                  <td
                                    class="text-right border-bottom"
                                    style="font-size: 11px"
                                  >
                                    {{
                                      $utils.currency_format(guestPostingAmount)
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left border-bottom"
                                    style="font-size: 11px"
                                  >
                                    Discount:
                                  </td>
                                  <td
                                    class="text-right border-bottom red--text"
                                    style="font-size: 11px"
                                  >
                                    -
                                    {{
                                      $utils.currency_format(
                                        posting_payment?.discount || 0
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left border-bottom"
                                    style="font-size: 11px"
                                  >
                                    Paid:
                                  </td>
                                  <td
                                    class="text-right border-bottom red--text"
                                    style="font-size: 11px"
                                  >
                                    -
                                    {{
                                      $utils.currency_format(
                                        posting_payment?.paid || 0
                                      )
                                    }}
                                  </td>
                                </tr>
                                <tr>
                                  <td
                                    class="text-left border-bottom"
                                    style="font-size: 11px"
                                  >
                                    Balance:
                                  </td>
                                  <td
                                    class="text-right border-bottom primary--text"
                                    style="font-size: 11px"
                                  >
                                    {{
                                      $utils.currency_format(setInitialBalance)
                                    }}
                                  </td>
                                </tr>
                              </table>
                            </v-col>
                          </v-row>
                        </v-col>

                        <v-col v-if="customer && customer.id">
                          <v-row>
                            <v-col cols="2">
                              <v-text-field
                                label="Title"
                                dense
                                outlined
                                type="text"
                                v-model="sub_customer.title"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                label="First Name"
                                dense
                                outlined
                                type="text"
                                v-model="sub_customer.first_name"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                label="Last Name"
                                dense
                                hide-details
                                outlined
                                type="text"
                                v-model="sub_customer.last_name"
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                dense
                                label="Email"
                                outlined
                                type="email"
                                v-model="sub_customer.email"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                dense
                                label="Contact No"
                                outlined
                                v-model="sub_customer.contact_no"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                dense
                                label="Whatsapp No"
                                outlined
                                v-model="sub_customer.whatsapp"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                dense
                                label="Date of Birth"
                                outlined
                                v-model="sub_customer.dob"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                dense
                                label="City"
                                outlined
                                v-model="sub_customer.city"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                dense
                                label="state"
                                outlined
                                v-model="sub_customer.state"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                dense
                                label="Country"
                                outlined
                                v-model="sub_customer.country"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="3">
                              <v-text-field
                                dense
                                label="Zip Code"
                                outlined
                                v-model="sub_customer.zip_code"
                                hide-details
                              ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                              <v-text-field
                                dense
                                label="Guest Request"
                                outlined
                                v-model="booking.request"
                                hide-details
                              ></v-text-field>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-tab-item>
                    <v-tab-item class="px-3 py-4">
                      <CustomerRooms
                        :booking="booking"
                        :orderRooms="orderRooms"
                        :room_no="
                          !customerScreen ? roomData && roomData.room_no : 0
                        "
                      />
                    </v-tab-item>
                    <v-tab-item class="px-3 py-4">
                      <CustomerPostings
                        :otherPayload="{
                          booking_id: roomData && roomData.booking_id,
                          room_id: roomData && roomData.room_id,
                          sub_customer_id: sub_customer && sub_customer.id,
                        }"
                        :old_balance="setInitialBalance"
                        :full_name="booking.title"
                        :postings="postings"
                        :room_no="
                          !customerScreen ? roomData && roomData.room_no : 0
                        "
                        @response="ViewBookingDialog = false"
                      />
                    </v-tab-item>
                    <v-tab-item class="px-3 pt-5">
                      <CustomerTransactions
                        :bookedRooms="bookedRooms"
                        :transactions="transactions"
                        :totalTransactionAmount="totalTransactionAmount"
                      />
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
                                    (booking && booking.payment_reference_id) ||
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
                                      booking.online_payment_response
                                        .razorpay_payment_id) ||
                                    "---"
                                  }}
                                </p>
                              </div>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-tab-item>
                  </v-tabs-items>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </v-dialog>
</template>
<script>
export default {
  props: ["BookingId", "noLabel", "roomData"],
  data: () => ({
    payment: {},
    customerScreen: true,
    ViewBookingDialog: false,
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
    snackbar: false,
    dialog: false,
    ids: [],
    payData: {},
    loading: false,
    response: "",
    customer: [],
    itemsCustomer: [],
    tab1: null,
    activeTab: 0,

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
    sub_customers: [],
  }),

  computed: {
    setInitialBalance() {
      if (!this.posting_payment) {
        return 0;
      }

      if (this.posting_payment.paid == 0 && this.posting_payment.balance == 0) {
        return this.guestPostingAmount;
      }
      return this.posting_payment.balance;
    },
    sub_customer() {
      if (this.roomData && this.roomData.sub_customer_room_history) {
        return this.roomData.sub_customer_room_history.sub_customer;
      }
    },
    posting_payment() {
      if (this.roomData && this.roomData.posting_payment) {
        return this.roomData.posting_payment;
      }
    },
    full_name() {
      return this.$utils.full_name(this.customer);
    },
    isGroup() {
      return this.booking && this.booking.group_name == "yes";
    },
    guestPostingAmount() {
      const room_no = this.roomData?.room_no || 0;
      return this.postings.reduce((acc, cur) => {
        return (
          acc + (cur?.room?.room_no === room_no ? cur.amount_with_tax || 0 : 0)
        );
      }, 0);
    },
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
    async processPostingPayment() {
      console.log("🚀 ~ processPostingPayment ~ this.payment:", this.payment);

      try {
        await this.$axios.post(`posting_payment`, this.payment);
        alert("Payment Process");
      } catch (error) {
        console.log(error);
      }
    },
    showRelatedScreen() {
      this.customerScreen = !this.customerScreen;

      if (this.customerScreen) {
        this.itemsCustomer = [
          "Reservation",
          this.booking.booking_type == "hall" ? "Hall" : "Room",
          "Postings",
          "Transaction",
          "Online Booking",
        ];
      } else {
        this.itemsCustomer = ["Reservation", "Room", "Postings"];
      }
    },
    closeDialogs() {
      this.getData();
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

    getData() {
      let id = this.BookingId;
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

        this.itemsCustomer = [
          "Reservation",
          booking.booking_type == "hall" ? "Hall" : "Room",
          "Postings",
          "Transaction",
          "Online Booking",
        ];
      });
    },
  },
};
</script>
