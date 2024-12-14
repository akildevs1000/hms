<template>
  <div class="text-center">
    <v-dialog persistent v-model="dialog" width="850">
      <AssetsIconClose left="840" @click="dialog = false" />
      <template v-slot:activator="{ on, attrs }">
        <span v-bind="attrs" v-on="on"> Modify Booking </span>
      </template>
      <v-card>
        <v-alert class="grey lighten-3 primary--text" flat dense>
          <div style="font-size: 18px">Modify Booking</div>
        </v-alert>

        <v-card-text v-if="json" style="max-height: 600px; overflow: auto">
          <v-row v-if="bookingResponse">
            <!-- Left Column -->
            <v-col cols="12" md="6">
              <v-row no-gutter>
                <v-col cols="12" class="text-center">
                  <v-avatar size="125">
                    <img
                      class="pa-2"
                      style="border: 1px solid grey"
                      :src="
                        bookingResponse?.customer?.captured_photo ||
                        'https://i.pinimg.com/474x/e4/c5/9f/e4c59fdbb41ccd0f87dc0be871d91d98.jpg'
                      "
                      alt="Profile Image"
                    />
                  </v-avatar>
                </v-col>
              </v-row>
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <!-- Form Fields -->
                    <v-text-field
                      readonly
                      v-model="bookingResponse.customer.full_name"
                      label="Full Name"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      readonly
                      v-model="bookingResponse.customer.contact_no"
                      label="Phone Number"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      readonly
                      v-model="bookingResponse.booking.source"
                      label="Source"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      readonly
                      v-model="formattedCheckinDate"
                      label="Check In"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      readonly
                      v-model="formattedCheckOutDate"
                      label="Check Out"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      label="Adult"
                      dense
                      outlined
                      v-model="bookingResponse.no_of_adult"
                      hide-details
                      readonly
                    ></v-text-field>
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      label="Child"
                      dense
                      outlined
                      v-model="bookingResponse.no_of_child"
                      hide-details
                      readonly
                    ></v-text-field>
                  </v-col>
                  <v-col cols="4">
                    <v-text-field
                      label="Extra Bed"
                      dense
                      outlined
                      v-model="bookingResponse.extra_bed_qty"
                      hide-details
                      readonly
                    ></v-text-field>
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      readonly
                      v-model="bookingResponse.room_type"
                      label="Room Type"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>

                  <v-col cols="6">
                    <v-text-field
                      readonly
                      v-model="bookingResponse.room_no"
                      label="Room Number"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      disabled
                      v-model="formattedFoodPlan"
                      label="Food Plan"
                      dense
                      hide-details
                      outlined
                    />
                  </v-col>

                  <v-col cols="12">
                    <v-row justify="center">
                      <v-col cols="4" class="d-flex justify-center">
                        <v-checkbox
                          readonly
                          label="Early C/I"
                          hide-details
                          dense
                        ></v-checkbox>
                      </v-col>
                      <v-col cols="4" class="d-flex justify-center">
                        <v-checkbox
                          readonly
                          label="Late C/O"
                          hide-details
                          dense
                        ></v-checkbox>
                      </v-col>
                    </v-row>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      readonly
                      v-model="bookingResponse.booking.request"
                      label="Guest Request"
                      dense
                      hide-details
                      outlined
                      rows="2"
                    />

                    <br />
                    <!-- Booking Info -->
                    <table class="mt-5">
                      <tr>
                        <td
                          class="text-left border-bottom"
                          style="font-size: 11px"
                        >
                          Booking Price:
                        </td>
                        <td
                          class="text-right border-bottom"
                          style="font-size: 11px"
                        >
                          {{
                            $utils.currency_format(old?.booking?.sub_total || 0)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left border-bottom"
                          style="font-size: 11px"
                        >
                          Add:
                        </td>
                        <td
                          class="text-right border-bottom"
                          style="font-size: 11px"
                        >
                          {{
                            $utils.currency_format(
                              old?.booking?.total_extra || 0
                            )
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
                            $utils.currency_format(old?.booking?.discount || 0)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left border-bottom"
                          style="font-size: 11px"
                        >
                          After Discount:
                        </td>
                        <td
                          class="text-right border-bottom"
                          style="font-size: 11px"
                        >
                          {{ $utils.currency_format(after_discount || 0) }}
                        </td>
                      </tr>
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
                            $utils.currency_format(
                              old?.booking?.total_posting_amount
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td
                          class="text-left border-bottom"
                          style="font-size: 11px"
                        >
                          Total:
                        </td>
                        <td
                          class="text-right border-bottom"
                          style="font-size: 11px"
                        >
                          {{ $utils.currency_format(total) }}
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
                          -{{
                            $utils.currency_format(json?.booking?.paid_amounts)
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
                          {{ $utils.currency_format(balance) }}
                        </td>
                      </tr>
                    </table>
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
            <v-divider vertical></v-divider>
            <!-- Right Column -->
            <v-col cols="12" md="6">
              <v-container>
                <v-row>
                  <v-col cols="12">
                    <Heading label="Edit Booking" />
                  </v-col>
                  <v-col cols="6">
                    <v-menu
                      v-model="checkin_menu"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="formattedCheckinDate"
                          persistent-hint
                          readonly
                          v-bind="attrs"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        :min="new Date().toISOString().substr(0, 10)"
                        v-model="json.check_in"
                        no-title
                        @input="
                          () => {
                            checkin_menu = false;
                            getPricesByRoomId(json.room_id);
                          }
                        "
                      ></v-date-picker>
                    </v-menu>
                  </v-col>

                  <v-col cols="6">
                    <v-menu
                      v-model="checkout_menu"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="formattedCheckOutDate"
                          persistent-hint
                          readonly
                          v-bind="attrs"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        :min="addOneDay(json.check_in)"
                        v-model="json.check_out"
                        no-title
                        @input="
                          () => {
                            checkout_menu = false;
                            getPricesByRoomId(json.room_id);
                          }
                        "
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col cols="6">
                    <v-autocomplete
                      v-model="json.room_type_id"
                      @change="getFilteredRooms"
                      label="Room Type"
                      outlined
                      dense
                      hide-details
                      item-value="id"
                      item-text="name"
                      :items="roomTypes"
                      return-object
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="6">
                    <v-autocomplete
                      @change="getPricesByRoomId(json.room_id)"
                      :items="filteredRooms"
                      item-text="room_no"
                      item-value="id"
                      dense
                      outlined
                      v-model="json.room_id"
                      :hide-details="true"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="4">
                    <v-autocomplete
                      disabled
                      label="Adult"
                      :items="[1, 2, 3]"
                      dense
                      outlined
                      v-model="json.no_of_adult"
                      hide-details
                      required
                      @change="getPricesByRoomId(json.room_id)"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="4">
                    <v-autocomplete
                      disabled
                      label="Child"
                      :items="[0, 1, 2, 3]"
                      dense
                      outlined
                      v-model="json.no_of_child"
                      hide-details
                      required
                      @change="getPricesByRoomId(json.room_id)"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="4">
                    <v-autocomplete
                      disabled
                      label="Extra Bed"
                      :items="[0, 1, 2, 3]"
                      dense
                      outlined
                      v-model="json.extra_bed_qty"
                      hide-details
                      required
                      @change="getPricesByRoomId(json.room_id)"
                    ></v-autocomplete>
                  </v-col>

                  <v-col cols="4">
                    <v-autocomplete
                      disabled
                      label="Food Plan"
                      outlined
                      dense
                      hide-details
                      item-value="id"
                      item-text="title"
                      v-model="json.food_plan_id"
                      :items="foodplans"
                      @change="getPricesByRoomId(json.room_id)"
                    ></v-autocomplete>
                  </v-col>
                  <v-col cols="8">
                    <v-row justify="center">
                      <v-col cols="6" class="d-flex justify-center">
                        <v-checkbox
                          disabled
                          label="Early Check In"
                          hide-details
                          dense
                          v-model="is_early_check_in"
                          @change="getPricesByRoomId(json.room_id)"
                        ></v-checkbox>
                      </v-col>
                      <v-col cols="6" class="d-flex justify-center">
                        <v-checkbox
                          disabled
                          label="Late Check Out"
                          hide-details
                          dense
                          v-model="is_late_check_out"
                          @change="getPricesByRoomId(json.room_id)"
                        ></v-checkbox>
                      </v-col>
                    </v-row>
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      label="Notes"
                      dense
                      hide-details
                      outlined
                      rows="2"
                    />
                  </v-col>

                  <!-- <v-col cols="12" class="my-3">
                    <Heading label="New Price" />
                  </v-col> -->
                  <v-col cols="12">
                    <table
                      v-if="bookingResponse"
                      style="width: 100%"
                      cellspacing="3"
                    >
                      <tr>
                        <td style="width: 50%">Old Total Days:</td>
                        <td style="width: 50%" class="text-right">
                          {{ old?.total_days || 0 }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">
                          <b>Old Room Price with tax: </b>
                        </td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              old?.order_rooms_sum_total_with_tax
                            )
                          }}
                          <!-- {{
                            $utils.currency_format(
                              parseFloat(
                                old?.booking?.order_rooms_sum_grand_total
                              ) -
                                parseFloat(
                                  old?.booking?.order_rooms_sum_food_plan_price
                                ) -
                                parseFloat(
                                  old?.booking?.order_rooms_sum_bed_amount
                                ) -
                                parseFloat(
                                  old?.booking?.order_rooms_sum_early_check_in
                                ) -
                                parseFloat(
                                  old?.booking?.order_rooms_sum_late_check_out
                                ) -
                                parseFloat(old?.booking?.total_extra) +
                                parseFloat(old?.booking?.discount)
                            )
                          }} -->
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">Old Food Price</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              old?.order_rooms_sum_food_plan_price || 0
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">Old Bed Amount</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              old?.order_rooms_sum_bed_amount || 0
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">Old Early Check In</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              old?.order_rooms_sum_early_check_in || 0
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">Old Late Check Out</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              old?.order_rooms_sum_late_check_out || 0
                            )
                          }}
                        </td>
                      </tr>

                      <tr>
                        <td style="width: 50%">Old Add</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              parseFloat(old.booking.total_extra) /
                                old.booking.booked_rooms.length
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%" class="red--text">
                          Old Discount
                        </td>
                        <td style="width: 50%" class="red--text text-right">
                          -{{
                            $utils.currency_format(
                              parseFloat(old.booking.discount) /
                                old.booking.booked_rooms.length
                            )
                          }}
                        </td>
                      </tr>

                      <tr>
                        <td style="width: 50%" class="border-top">Old Total</td>
                        <td style="width: 50%" class="border-top text-right">
                          {{
                            $utils.currency_format(
                              parseFloat(old.order_rooms_sum_grand_total)
                            )
                          }}
                        </td>
                      </tr>
                    </table>

                    <table
                      v-if="bookingResponse"
                      style="width: 100%"
                      cellspacing="3"
                      class="mt-2"
                    >
                      <tr>
                        <td style="width: 50%" class="border-top">
                          New Total Days:
                        </td>
                        <td style="width: 50%" class="border-top text-right">
                          {{ json?.total_days || 0 }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">
                          <b>New Room Price with tax: </b>
                        </td>
                        <td style="width: 50%" class="text-right">
                          {{ $utils.currency_format(json?.total_price || 0) }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">New Food Price</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(
                              json?.food_plan_price_for_all_days || 0
                            )
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">New Bed Amount</td>
                        <td style="width: 50%" class="text-right">
                          {{ $utils.currency_format(json?.bed_amount || 0) }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">New Early Check In</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(json?.early_check_in || 0)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">New Late Check Out</td>
                        <td style="width: 50%" class="text-right">
                          {{
                            $utils.currency_format(json?.late_check_out || 0)
                          }}
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%">New Add</td>
                        <td style="width: 50%" class="text-right">
                          {{ $utils.currency_format(json?.total_extra || 0) }}

                          <!-- {{
                            $utils.currency_format(
                              parseFloat(old.booking.total_extra) /
                                old.booking.booked_rooms.length
                            )
                          }} -->
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%" class="red--text">
                          New Discount
                        </td>
                        <td style="width: 50%" class="red--text text-right">
                          -{{ $utils.currency_format(json?.discount || 0) }}

                          <!-- -{{
                            $utils.currency_format(
                              parseFloat(old.booking.discount) /
                                old.booking.booked_rooms.length
                            )
                          }} -->
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 50%" class="border-top border-bottom">
                          <b>New Total</b>
                        </td>
                        <td
                          style="width: 50%"
                          class="border-top border-bottom text-right"
                        >
                          <b>
                            {{
                              $utils.currency_format(json?.new_total || 0)
                            }}</b
                          >
                        </td>
                      </tr>
                      <tr>
                        <td
                          style="width: 50%"
                          class="border-top border-bottom"
                        ></td>
                        <td
                          style="width: 50%"
                          class="border-top border-bottom text-right"
                        ></td>
                      </tr>
                      <!-- <tr>
                        <td style="width: 50%" class="border-top">
                          <b>Booking Balance</b>
                        </td>
                        <td style="width: 50%" class="border-top text-right">
                          <b>
                            {{ $utils.currency_format(json?.balance || 0) }}</b
                          >
                        </td>
                      </tr> -->
                      <!-- <tr>
                        <td style="width: 50%" class="red--text">
                          <b>Old Room Total</b>
                        </td>
                        <td style="width: 50%" class="text-right red--text">
                          -<b>{{
                            $utils.currency_format(
                              parseFloat(old.order_rooms_sum_grand_total) +
                                parseFloat(old.booking.total_extra) /
                                  old.booking.booked_rooms.length -
                                parseFloat(old.booking.discount) /
                                  old.booking.booked_rooms.length
                            )
                          }}</b>
                        </td>
                      </tr> -->
                      <!-- <tr>
                        <td style="width: 50%" class="red--text">
                          <b>Paid Total</b>
                        </td>
                        <td style="width: 50%" class="text-right red--text">
                          -<b>{{
                            $utils.currency_format(json?.booking?.paid_amounts)
                          }}</b>
                        </td>
                      </tr> -->
                      <tr>
                        <td style="width: 50%" class="red--text">
                          <b>Previous Booking Balance</b>
                        </td>
                        <td style="width: 50%" class="text-right">
                          <b>{{
                            $utils.currency_format(
                              old.booking.grand_remaining_price
                            )
                          }}</b>
                        </td>
                      </tr>

                      <!-- <tr>
                        <td style="width: 50%" class="border-top">
                          <b>Due Amount</b>
                        </td>
                        <td style="width: 50%" class="border-top text-right">
                          <b
                            v-if="
                              json.new_total -
                                parseFloat(json?.booking?.paid_amounts) >=
                              0
                            "
                          >
                            {{
                              $utils.currency_format(
                                parseFloat(json.new_total) -
                                  parseFloat(json?.booking?.paid_amounts)
                              )
                            }}</b
                          ><b v-else class="red--text">
                            {{
                              $utils.currency_format(
                                parseFloat(json.new_total) -
                                  parseFloat(json?.booking?.paid_amounts)
                              )
                            }}

                             
                          </b>
                        </td>
                      </tr> -->
                      <tr>
                        <td style="width: 70%" class="border-top">
                          <b>Extra Difference This Room</b>
                        </td>
                        <td style="width: 30%" class="border-top text-right">
                          <b v-if="difference >= 0">{{
                            $utils.currency_format(difference)
                          }}</b>
                          <b v-else class="red--text">{{
                            $utils.currency_format(difference)
                          }}</b>
                        </td>
                      </tr>
                      <tr>
                        <td style="width: 70%" class="border-top">
                          <b>Final Booking Total Due </b>
                        </td>
                        <td style="width: 30%" class="border-top text-right">
                          <b>{{
                            $utils.currency_format(
                              parseFloat(
                                old.booking.order_rooms_sum_grand_total
                              ) + difference
                            )
                          }}</b>

                          <!-- <div>
                            Same room old Price :
                            {{ old.order_rooms_sum_grand_total }}
                          </div>
                          <div>
                            Same room New Price :
                            {{ json.new_total }}
                          </div>

                          <div>
                            Old Booking Total :
                            {{ old.booking.order_rooms_sum_grand_total }}
                          </div> -->
                        </td>
                      </tr>
                    </table>
                  </v-col>
                  <v-col cols="12" class="text-center mt-5">
                    <AssetsButtonCancel @close="dialog = false" />
                    &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                    <AssetsButtonSubmit @click="submit" />
                  </v-col>
                </v-row>
              </v-container>
            </v-col>
          </v-row>
          <v-card-text v-else>
            <div
              class="d-flex justify-center align-center"
              style="height: 400px"
            >
              <div>Loading......</div>
            </div>
          </v-card-text>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["BookedRoomId", "BookingId"],
  data() {
    return {
      json: null,
      selectedNewRoom: null,
      oldRoomsOrderRooms: [],
      room_orders: [],

      is_early_check_in: false,
      is_late_check_out: false,

      early_check_in: 0,
      late_check_out: 0,

      foodplans: [],
      checkin_menu: false,
      checkout_menu: false,
      rooms: [],
      filteredRooms: [],
      old: {
        food_plan_price: 0,
        room_price: 0,
        booking_total_price: 0,
        room_id: 0,
        room_type_id: 0,
        room_no: 0,
        room_type: null,
      },
      payload: {
        booking_remaining_price: 0,
        booking_total_price: 0,
        room_type_id: 0,
        extra_bed_qty: 0,
        bed_amount: 0,
        check_in: "",
        check_out: "",
        total_price: 0,
        old_remaining_price: 0,
        remaining_price: 0,
        company_id: 0,
        booking_id: 0,
        room_id: 0,
        user_id: 0,
        advance_price: 0,
      },
      customer: {
        title: null,
        group: null,
        agent_name: null,
        contact_no: null,
      },
      dialog: false,
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      reference: "",
      errors: [],
      roomTypes: [],
      checkOutDialog: false,
      bookingResponse: null,
      roomPriceResponse: null,
      additional_charges: {},
    };
  },

  async created() {
    this.preloader = false;

    await this.get_food_plans();

    await this.get_additional_charges();

    await this.get_booking();

    await this.get_room_types();
  },

  mounted() {},

  computed: {
    after_discount() {
      //return parseFloat(this.old?.booking?.sub_total || 0);
      return (
        parseFloat(this.old?.booking?.sub_total || 0) -
        parseFloat(this.old?.booking?.discount || 0) +
        parseFloat(this.old?.booking?.total_extra || 0)
      );
    },
    total() {
      return (
        this.after_discount +
        +parseFloat(this.old?.booking?.total_posting_amount || 0)
      );
    },
    balance() {
      return this.total - parseFloat(this.old?.booking?.paid_amounts || 0);
    },
    difference() {
      return (
        parseFloat(this.json.new_total) -
        parseFloat(this.old.order_rooms_sum_grand_total)
      );
      // return this.total - parseFloat(this.old?.booking?.paid_amounts || 0);
    },
    formattedFoodPlan() {
      return (
        this.foodplans.find((e) => e.id == this.bookingResponse.food_plan_id)
          ?.title || "---"
      );
    },
    formattedCheckinDate() {
      if (!this.json.check_in) return "";

      const date = new Date(this.json.check_in);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 12:00`;
    },
    formattedCheckOutDate() {
      if (!this.json.check_out) return "";

      const date = new Date(this.json.check_out);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 11:00`;
    },
  },

  methods: {
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },

    get_food_charges(id) {
      let { unit_price } = this.foodplans.find((e) => e.id == id);

      return unit_price;
    },

    async get_food_plans() {
      let { data: foodplans } = await this.$axios.get(`foodplan-list`);

      this.foodplans = foodplans;
    },
    async get_rooms(room_id) {
      let { data: rooms } = await this.$axios.get(
        `get_available_rooms_for_modify`,
        {
          params: {
            company_id: this.$auth.user.company.id,
          },
        }
      );

      this.filteredRooms = rooms.filter((e) => e.id == room_id);
    },

    async getFilteredRooms(item) {
      let { data: rooms } = await this.$axios.get(
        `get_available_rooms_by_date_and_room_type`,
        {
          params: {
            company_id: this.$auth.user.company.id,
            check_in: this.json.check_in,
            check_out: this.json.check_out,
            room_type_id: item.id,
          },
        }
      );

      this.filteredRooms = rooms;
    },
    async get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },
    async getPricesByRoomId(id) {
      this.roomPriceResponse = [];
      let found = this.filteredRooms.find((e) => e.id == id);
      if (!found) return;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: found.room_type.name || "",
          room_no: found.room_no,
          checkin: this.json.check_in,
          checkout: this.json.check_out,
          BookedRoomId: this.BookedRoomId,
        },
      };
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return;
          }

          this.selectedNewRoom = data;

          this.room_orders = data.data;

          let selected_food_plan = this.getFoodCalculation(this.json);

          let total_days = data.data.length || 0;

          let new_room_price_single_day = data.total_price / total_days;

          let food_plan_price_for_all_days =
            total_days * selected_food_plan.food_plan_price;

          let bed_amount =
            (this.json.extra_bed_qty
              ? this.json.extra_bed_qty *
                (this.additional_charges.extra_bed || 0)
              : 0) * total_days;

          let early_check_in =
            (this.is_early_check_in
              ? this.additional_charges.early_check_in || 0
              : 0) * total_days;
          let late_check_out =
            (this.is_late_check_out
              ? this.additional_charges.late_check_out || 0
              : 0) * total_days;

          // let total_extra =
          //   this.json.booking.total_extra /
          //   total_days /
          //   this.json.booked_room_count;

          let total_extra =
            parseFloat(this.old.booking.total_extra) /
            this.old.booking.booked_rooms.length;

          let discount =
            parseFloat(this.old.booking.discount) /
            this.old.booking.booked_rooms.length;

          // let discount =
          //   this.json.booking.discount /
          //   total_days /
          //   this.json.booked_room_count;

          let total_price = data.total_price;

          let total =
            parseInt(total_price) +
            parseFloat(food_plan_price_for_all_days) +
            parseFloat(bed_amount) +
            parseFloat(early_check_in) +
            parseFloat(late_check_out) +
            parseFloat(total_extra) -
            parseFloat(discount);

          let old_total = this.old?.booking?.order_rooms_sum_grand_total;
          let old_room_grand_total =
            parseFloat(this.old.order_rooms_sum_grand_total) +
            parseFloat(this.old.booking.total_extra) /
              this.old.booking.booked_rooms.length -
            parseFloat(this.old.booking.discount) /
              this.old.booking.booked_rooms.length;

          let new_total = total;

          let balance = this.balance;

          let booking_total_price = this.balance - old_total + new_total;

          this.json = {
            ...this.json,
            total_days,
            ...selected_food_plan,
            room_type_id: data.room.room_type_id,
            room_type_id: data.room.room_type_id,

            room_id: data.room.id,
            room_type: found.room_type.name,

            total_price,
            food_plan_price_for_all_days,
            bed_amount,
            early_check_in,
            late_check_out,
            total,

            id: this.bookingResponse.booking.id,
            room_price: new_room_price_single_day,
            total_tax: data.total_tax,
            room_tax: data.total_tax / total_days,
            booking_total_price,

            balance,
            old_total,
            new_total,
            old_room_grand_total,
            discount,
            total_extra,
          };

          // this.payload = {

          // if (this.bookingResponse.booking.group_name) {
          //   if (
          //     this.old.booking_total_price !== this.payload.booking_total_price
          //   ) {
          //     let roomPrice = this.old.food_plan_price + this.old.room_price;
          //     let restOfPrices = this.old.booking_total_price - roomPrice;
          //     this.payload.booking_total_price = this.$utils.currency_format(
          //       this.payload.booking_total_price + restOfPrices
          //     );
          //   }
          // }
        });
    },
    getFoodCalculation({ no_of_adult, no_of_child, food_plan_id }) {
      let selectedFP = this.foodplans.find((e) => e.id == food_plan_id);

      if (!selectedFP) {
        return null;
      }

      let total_members = no_of_adult + no_of_child;

      let { title, unit_price } = selectedFP;

      let food_plan_price =
        unit_price * no_of_adult + (unit_price * no_of_child) / 2;

      return {
        meal: "------",
        meal_name: title,
        food_plan_price: food_plan_price,
        breakfast: selectedFP.breakfast ? total_members : 0,
        lunch: selectedFP.lunch ? total_members : 0,
        dinner: selectedFP.dinner ? total_members : 0,
      };
    },
    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    async get_booking() {
      let payload = {
        params: {
          id: this.BookedRoomId,
          booking_id: this.BookingId,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`/get_booking_for_modify`, payload).then(({ data }) => {
        this.bookingResponse = data;

        let { booking } = data;

        this.oldRoomsOrderRooms = booking?.order_rooms || 0;

        let total_days = data.days || 0;

        this.old = data;
        this.old.room_price = data.price;
        this.old.booking_total_price = booking.total_price;
        this.old.room_type = data?.room?.room_type?.name;
        this.old.days = total_days;
        this.old.total_days = total_days;

        this.old.total_extra = booking.total_extra;
        this.old.discount = booking.discount;

        this.early_check_in = data.early_check_in;
        this.late_check_out = data.late_check_out;

        this.is_early_check_in = this.early_check_in > 0 ? true : false;
        this.is_late_check_out = this.late_check_out > 0 ? true : false;

        this.json = {
          total_days: 0,
          total_price: 0,
          bed_amount: 0,
          early_check_in: 0,
          late_check_out: 0,
          food_plan_price: 0,
          total_extra: 0,
          discount: 0,
          booking: booking,
          booking_remaining_price: 0,
          booking_total_price: 0,

          // addtional_cols
          room_tax: 0,
          room_price: 0,

          id: data.id,
          booking_id: booking.id,
          room_id: data.room_id,
          room_no: data.room_no,
          room_type_id: data.room.room_type_id,
          room_type: data?.room?.room_type?.name,
          food_plan_id: parseInt(data.food_plan_id),
          extra_bed_qty: data.extra_bed_qty,
          no_of_adult: data.no_of_adult,
          no_of_child: data.no_of_child,
          check_in: data.checkin_date_only,
          check_out: data.checkout_date_only,

          booked_room_count: data.booked_room_count,
          booked_room_count: data.booked_room_count,
        };
        console.log(
          "🚀 ~ this.$axios.get ~ data.booked_room_count:",
          data.booked_room_count
        );

        this.get_rooms(data.room_id);
      });
    },
    addOneDay(originalDate) {
      if (!originalDate) {
        return new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      date.setDate(date.getDate() + 1);

      return date.toISOString().split("T")[0];
    },
    submit() {
      if (!this.room_orders.length) {
        this.alert("Warning!", "No changes detected", "error");
        return;
      }
      let room_id = this.json.room_id;

      let foundRoom = this.filteredRooms.find((e) => e.id == room_id);

      this.json.company_id = this.$auth.user.company_id;
      this.json.user_id = this.$auth.user.id;
      this.json.booked_room_id = this.BookedRoomId;

      let roomObject = {
        room_no: foundRoom.room_no ?? "---",
        room_type: foundRoom.room_type.name ?? "---",
        room_id: foundRoom.id ?? 0,
      };

      let { booking, ...json } = this.json;

      let payload = {
        roomObject,
        room_orders: this.room_orders,
        json: json,
        old: this.old,
      };
      console.log(payload);
      // return;

      this.loading = true;
      this.$axios
        .post(`/modify_booking`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.dialog = false;
            this.loading = false;

            this.alert("Sucess!", "Booking has been modified", "success");
            this.$emit("close-calender-room");
          }
        })
        .catch((e) => {
          this.loading = false;
          console.log(e);
        });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
