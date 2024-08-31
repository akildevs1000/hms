<template>
  <div v-if="can('calendar_create')">
    <v-dialog v-model="dialog" width="1000">
      <template v-slot:activator="{ on, attrs }">
        <div style="text-align: center">
          <v-btn
            dense
            x-small
            v-bind="attrs"
            v-on="on"
            class="text-center"
            title="Hall"
            color="#34444c"
            style="width: 37px; height: 26px"
          >
            <v-icon color="white">mdi-google-classroom</v-icon>
            <span v-if="!onlyButton"> Hall</span>
          </v-btn>
          <div v-if="onlyButton" style="font-size: 10px; text-align: center">
            Hall
          </div>
        </div>
      </template>
      <v-card>
        <v-toolbar
          small
          style="height: 40px !important"
          class="rounded-md"
          color="background"
          dense
          flat
          dark
        >
          <span>Hall Booking Information</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="close"> mdi-close </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-tabs v-model="activeTab">
            <v-container>
              <v-btn
                class="ml-4 mb-2"
                color="primary"
                small
                @click="searchDialog = true"
              >
                Search
                <v-icon right dark>mdi-magnify</v-icon>
              </v-btn>
            </v-container>
            <v-spacer></v-spacer>
            <v-tab>
              <v-icon> mdi mdi-account-tie </v-icon>
            </v-tab>
            <v-tab>
              <v-icon> mdi mdi-bed </v-icon>
            </v-tab>
            <v-tab v-if="customer.id > 0">
              <v-icon> mdi mdi-clipboard-text-clock </v-icon>
            </v-tab>
            <v-tabs-slider color="#1259a7"></v-tabs-slider>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-row>
                    <v-col md="2" cols="12" class="mt-2">
                      <v-row no-gutters class="pa-2">
                        <v-col cols="12" class="text-right">
                          <v-icon color="primary" small>mdi-eye</v-icon>
                        </v-col>
                        <v-col cols="12" class="mt-2">
                          <v-img
                            :src="
                              customer.captured_photo || '/no-profile-image.png'
                            "
                          ></v-img>
                        </v-col>
                        <v-col cols="6">
                          <v-img
                            :src="customer.captured_photo || '/idf.png'"
                            style="margin: 0 auto; width: 50px; height: 50px"
                            contain
                          ></v-img>
                        </v-col>
                        <v-col cols="6">
                          <v-img
                            :src="customer.captured_photo || '/idb.png'"
                            style="margin: 0 auto; width: 50px; height: 50px"
                            contain
                          ></v-img>
                        </v-col>
                      </v-row>
                    </v-col>
                    <v-col md="10" cols="12">
                      <v-row>
                        <v-col md="4" dense>
                          <v-autocomplete
                            label="Business Source"
                            v-model="customer.customer_type"
                            :items="business_sources"
                            dense
                            item-text="name"
                            item-value="name"
                            outlined
                            :hide-details="true"
                          ></v-autocomplete>
                        </v-col>
                        <v-col md="8">
                          <SourceType
                            :key="sourceCompKey"
                            @sourceType="handleSource"
                          />
                        </v-col>

                        <v-col md="2" cols="12" sm="12">
                          <v-select
                            v-model="customer.title"
                            :items="titleItems"
                            label="Title *"
                            dense
                            item-text="name"
                            item-value="name"
                            :hide-details="errors && !errors.title"
                            :error-messages="
                              errors && errors.title ? errors.title[0] : ''
                            "
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-text-field
                            label="First Name *"
                            dense
                            outlined
                            type="text"
                            v-model="customer.first_name"
                            :hide-details="errors && !errors.first_name"
                            :error-messages="
                              errors && errors.first_name
                                ? errors.first_name[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-text-field
                            label="Last Name"
                            dense
                            :hide-details="true"
                            outlined
                            type="text"
                            v-model="customer.last_name"
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Email *"
                            outlined
                            type="email"
                            v-model="customer.email"
                            :hide-details="errors && !errors.email"
                            :error-messages="
                              errors && errors.email ? errors.email[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Contact No *"
                            outlined
                            max="1111111111111"
                            type="number"
                            v-model="customer.contact_no"
                            :hide-details="errors && !errors.contact_no"
                            :error-messages="
                              errors && errors.contact_no
                                ? errors.contact_no[0]
                                : ''
                            "
                            @keyup="mergeContact"
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Whatsapp No"
                            outlined
                            max="1111111111111"
                            type="number"
                            v-model="customer.whatsapp"
                            :hide-details="errors && !errors.whatsapp"
                            :error-messages="
                              errors && errors.whatsapp
                                ? errors.whatsapp[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-menu
                            v-model="customer.dob_menu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                          >
                            <template v-slot:activator="{ on, attrs }">
                              <v-text-field
                                v-model="customer.dob"
                                readonly
                                label="DOB"
                                v-on="on"
                                v-bind="attrs"
                                :hide-details="true"
                                dense
                                outlined
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              no-title
                              v-model="customer.dob"
                              @input="customer.dob_menu = false"
                            ></v-date-picker>
                          </v-menu>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-select
                            v-model="customer.nationality"
                            :items="countryList"
                            label="Nationality"
                            item-text="name"
                            item-value="name"
                            :hide-details="errors && !errors.nationality"
                            :error-messages="
                              errors && errors.nationality
                                ? errors.nationality[0]
                                : ''
                            "
                            dense
                            outlined
                          ></v-select>
                        </v-col>

                        <v-col md="4">
                          <v-select
                            label="Purpose"
                            v-model="room.purpose"
                            :items="purposes"
                            dense
                            :hide-details="true"
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Car Number"
                            outlined
                            :hide-details="true"
                            type="text"
                            v-model="customer.car_no"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                  <FullAddress @location="handleFullAddress" />

                  <v-row>
                    <v-col>
                      <v-textarea
                        rows="3"
                        label="Customer Request"
                        v-model="room.request"
                        :hide-details="true"
                        outlined
                      ></v-textarea>
                    </v-col>
                    <v-col cols="12" class="text-right">
                      <v-btn small @click="nextTab" color="primary">Next</v-btn>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-tab-item>

            <v-tab-item class="pt-5">
              <v-row>
                <v-col
                  md="12"
                  cols="12"
                  class="d-flex py-0 my-0 justify-center"
                >
                  <table class="styled-table py-0 my-0" style="width: 100%">
                    <thead>
                      <tr>
                        <td><small>Date</small></td>
                        <td><small>Day</small></td>
                        <td><small>Hall</small></td>
                        <td><small>Type</small></td>
                        <td><small>Tariff</small></td>
                        <td><small>Adult</small></td>
                        <td><small>Child</small></td>
                        <td><small>Meal</small></td>
                        <td><small>Price</small></td>
                        <td><small>Extras</small></td>
                        <td><small>Total</small></td>
                        <td></td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in priceListTableView"
                        :key="index"
                      >
                        <td>
                          {{ item.date }}
                        </td>
                        <td>
                          {{ item.day }}
                        </td>
                        <td>
                          {{ item.room_type }}
                        </td>
                        <td>
                          {{ item.day_type }}
                        </td>
                        <td>
                          {{ item.room_price }}
                        </td>
                        <td>{{ item.no_of_adult }}</td>
                        <td>{{ item.no_of_child }}</td>
                        <td>{{ item.meal_name }}</td>
                        <td>
                          {{ convert_decimal(item.price) }}
                        </td>
                        <td>
                          {{
                            convert_decimal(
                              item.cleaning +
                                item.electricity +
                                item.generator +
                                item.audio +
                                item.extra_booking_hours_charges +
                                item.projector
                            )
                          }}
                        </td>
                        <td>
                          {{ convert_decimal(item.total_price) }}
                        </td>
                        <td class="text-center">
                          <HallDetails :selectedRooms="selectedRooms" />
                          <v-icon small color="red" @click="deleteItem(index)"
                            >mdi-close</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </v-col>
                <v-col md="12" style="padding-top: 0px; font-weight: bold">
                  <div
                    class="d-flex justify-space-around py-3 styled-table"
                    style="margin-top: 5px"
                  >
                    <v-col cols="10" class="text-right">
                      <div>Sub Total:</div>
                      <div>Add :</div>
                      <div>Discount :</div>
                      <v-divider color="#4390FC"></v-divider>
                      <div style="font-size: 18px; font-weight: bold">
                        Total :
                      </div>
                    </v-col>
                    <v-col cols="2" class="text-right">
                      <div>
                        {{ convert_decimal(subTotal()) }}
                      </div>

                      <div>
                        {{ convert_decimal(temp.room_extra_amount) }}
                      </div>
                      <div style="color: red">
                        -{{ convert_decimal(temp.room_discount) }}
                      </div>
                      <v-divider color="#4390FC"></v-divider>
                      <div style="font-size: 18px; font-weight: bold">
                        {{ convert_decimal(processCalculation()) }}
                      </div>
                    </v-col>
                  </div>
                  <v-divider color="#4390FC"></v-divider>
                </v-col>
                <v-col md="12" class="text-right">
                  <HallDialog label="Add Hall" @tableData="handleTableData"
                /></v-col>
                <v-col md="3" sm="12" cols="12" dense>
                  <v-select
                    label="Discount/Extra"
                    v-model="extraPayType"
                    :items="['Discount', 'ExtraAmount']"
                    dense
                    :hide-details="true"
                    outlined
                  ></v-select>
                </v-col>
                <v-col
                  md="4"
                  sm="12"
                  cols="12"
                  dense
                  v-if="extraPayType == 'Discount'"
                >
                  <v-text-field
                    label="Discount Amount"
                    dense
                    outlined
                    type="number"
                    v-model="temp.room_discount"
                    :hide-details="true"
                    @keyup="processCalculation"
                  ></v-text-field>
                </v-col>
                <v-col
                  md="4"
                  sm="12"
                  cols="12"
                  dense
                  v-if="extraPayType == 'Discount'"
                >
                  <v-text-field
                    label="Reason"
                    dense
                    outlined
                    type="text"
                    v-model="temp.discount_reason"
                    :hide-details="true"
                  ></v-text-field>
                </v-col>
                <v-col
                  md="4"
                  sm="12"
                  cols="12"
                  dense
                  v-if="extraPayType == 'ExtraAmount'"
                >
                  <v-text-field
                    label="Extra Amount"
                    dense
                    outlined
                    type="number"
                    v-model="temp.room_extra_amount"
                    @keyup="processCalculation"
                    :hide-details="true"
                  ></v-text-field>
                </v-col>
                <v-col
                  md="4"
                  sm="12"
                  cols="12"
                  dense
                  v-if="extraPayType == 'ExtraAmount'"
                >
                  <v-text-field
                    label="Reason"
                    dense
                    outlined
                    type="text"
                    v-model="temp.extra_amount_reason"
                    :hide-details="true"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row class="text-right mb-3">
                <v-col>
                  <v-btn
                    small
                    style="background-color: #4390fc; margin-right: 5px"
                    @click="advanceDialog = true"
                    dark
                  >
                    <v-icon small>mdi-wallet</v-icon> Pay
                  </v-btn>
                  <v-btn
                    small
                    style="background-color: #5fafa3"
                    @click="store"
                    :loading="subLoad"
                    dark
                  >
                    <v-icon small>mdi-floppy</v-icon>

                    Book</v-btn
                  >
                </v-col>
              </v-row>
            </v-tab-item>

            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <History :customerId="customer.id"></History>
                </v-card-text>
              </v-card>
            </v-tab-item>
          </v-tabs>
        </v-card-text>
      </v-card>
    </v-dialog>

    <!---------------------------------------------------------------->

    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="error--text">*</span>
              </label>
              <v-text-field
                dense
                outlined
                type="text"
                v-model="search.mobile"
                :hide-details="true"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="get_customer" :loading="checkLoader">
            Search
            <v-icon right dark>mdi mdi-magnify</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="advanceDialog" width="600">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="advanceDialog = false">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-row class="px-5 mt-2">
            <div class="input-group input-group-sm px-3">
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="width: 220px !important; color: black !important"
              >
                <v-autocomplete
                  v-model="room.payment_mode_id"
                  :items="[
                    { id: 1, name: 'Cash' },
                    { id: 2, name: 'Card' },
                    { id: 3, name: 'Online' },
                    { id: 4, name: 'Bank' },
                    { id: 5, name: 'UPI' },
                    { id: 6, name: 'Cheque' },
                  ]"
                  cache-items
                  item-text="name"
                  item-value="id"
                  class="ma-0 pa-0"
                  dense
                  flat
                  hide-no-data
                  hide-details
                  solo
                  elevation="0"
                  background-color="#E9ECEF"
                  style="color: black !important"
                >
                </v-autocomplete>
              </span>
              <input
                type="number"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 48px"
                @keyup="runAllFunctions"
                :disabled="room.paid_by == '2' ? true : false"
                v-model="room.advance_price"
              />
            </div>

            <div
              class="input-group input-group-sm px-3"
              v-if="room.payment_mode_id != 1"
            >
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="width: 220px !important"
              >
                Reference No
              </span>
              <input
                v-model="room.reference_number"
                type="text"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 39px"
              />
            </div>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            @click="
              (e) => {
                selectedRooms.length > 0
                  ? (advanceDialog = false)
                  : alert('oops', 'Select Hall');
              }
            "
          >
            Pay
            <!-- <v-icon right dark>mdi mdi-magnify</v-icon> -->
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
  <NoAccess v-else />
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
const today = new Date();
const tomorrow = new Date(today);
const checkoutTime = new Date(today); // Copy check-in time

tomorrow.setDate(tomorrow.getDate() + 1);
function formatTime(date) {
  let hours = date.getHours().toString().padStart(2, "0");
  let minutes = date.getMinutes().toString().padStart(2, "0");
  return `${hours}:${minutes}`;
}
checkoutTime.setHours(today.getHours() + 4);

export default {
  props: ["onlyButton"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      additional_charges: {},
      is_cleaning_charges: false,
      is_electricity_charges: false,
      is_generator_charges: false,
      is_audio_charges: false,
      is_projector_charges: false,
      dialog: false,
      foodplans: [],
      checkin_date_menu: false,
      checkout_date_menu: false,
      checkin_time_menu: false,
      checkout_time_menu: false,
      room_type_list: ``,
      documentDialog: false,
      // -------customer history---------------
      customer: "",
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,
      advanceDialog: false,
      selectRoomLoading: false,
      roomTab: null,
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
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      // ------------------

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
        "Marriage",
      ],
      selectMeal: [],
      wantNewDoc: false,
      row: null,
      calIn: {},
      calOut: {},
      searchDialog: false,
      RoomDrawer: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" },
      ],
      val: 1,
      Model: "Reservation",
      isSelectRoom: true,
      isBed: false,
      subLoad: false,
      isDiscount: false,
      isExtra: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      types: [
        "Online",
        "Walking",
        "Travel Agency",
        "Complimentary",
        "Corporate",
      ],

      search: {
        mobile: "",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],

      agentList: [],
      CorporateList: [],
      // room_extra_amount: 0,
      idCards: [],
      imgView: false,
      priceListTableView: [],

      temp: {
        total_booking_hours: 0,
        extra_hours_charges: 0,
        food_plan_price: 1,
        food_plan_id: 1,
        cleaning: 0,
        electricity: 0,
        generator: 0,
        audio: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        check_in_time: null,
        check_out_time: null,
        // meal: [],
        bed_amount: 0,
        room_extra_amount: 0,
        extra_amount_reason: "",
        room_discount: 0,
        after_discount: 0, //(price - room_discount)
        room_tax: 0,
        total_with_tax: 0, //(after_discount * room_tax)
        total: 0, //(total_with_tax * bed_amount)
        grand_total: 0, //(total * days)
        company_id: this.$auth.user.company.id,

        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      },

      defaultTemp: {
        food_plan_price: 1,
        food_plan_id: 1,
        cleaning: 0,
        electricity: 0,
        generator: 0,
        audio: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        check_in_time: null,
        check_out_time: null,
        // meal: [],
        bed_amount: 0,
        room_extra_amount: 0,
        extra_amount_reason: "",
        room_discount: 0,
        after_discount: 0, //(price - room_discount)
        room_tax: 0,
        total_with_tax: 0, //(after_discount * room_tax)
        total: 0, //(total_with_tax * bed_amount)
        grand_total: 0, //(total * days)
        company_id: this.$auth.user.company.id,

        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      },
      merge_food_in_room_price: "",
      gst_calculation: {
        recal_basePrice: 0,
        recal_gst_percentage: 0,
        recal_gst_total: 0,
        recal_final: 0,
      },
      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      isCorporate: false,
      isAgent: false,
      isDiff: false,
      sourceCompKey: 1,
      room: {
        customer_type: null,
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: null,
        source: null,
        agent_name: "",
        booking_status: 1,
        check_in: null,
        check_out: null,
        discount: 0,
        reference_number: "",
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        sub_total: 0,
        after_discount: 0,
        sales_tax: 0,
        total_price: 0,
        remaining_price: 0,
        request: "",
        company_id: this.$auth.user.company.id,
        remark: "",
        rooms: "",
        reference_no: "",
        paid_by: "",
        purpose: "Tour",
        // priceList: [],
      },
      reservation: {
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_in_time: formatTime(today), // show time like mm:ss
        check_out_time: formatTime(checkoutTime), // show time like mm:ss
        room_no: "",
        room_id: 82,
        room_type: "",
        price: 0,
        origin_price: "",
        isCalculate: true,
        priceList: [],
        total_tax: 0,
        total_price_after_discount: 0,
        total_price: 0,
        total_discount: 0,
      },
      countryList: [],
      foodPriceList: [],
      person_type_arr: [],

      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],

      customer: {
        customer_type: "",
        title: "Mr",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: null,
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        //  new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        //   .toISOString()
        //   .substr(0, 10)
      },
      id_card_type_id: 0,
      errors: [],
      tempAdult: {
        tot_ab: 0,
        tot_al: 0,
        tot_ad: 0,
      },
      tempChild: {
        tot_cb: 0,
        tot_cl: 0,
        tot_cd: 0,
      },

      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
      extraPayType: "",
      allFood: [],

      breakfast: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      lunch: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      dinner: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      documentObj: {
        fileExtension: null,
        file: null,
      },
      business_sources: [],
    };
  },
  async created() {
    this.get_reservation();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
    this.get_Corporate();
    // this.getImage();
    this.preloader = false;

    this.get_business_sources();
  },
  methods: {
    handleSource(e) {
      this.room.type = e;
    },
    close() {
      this.sourceCompKey += 1;

      this.customer = {
        customer_type: null,
        title: "Mr",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: null,
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
      };

      this.room = {
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        customer_type: "",
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: null,
        source: null,
        agent_name: "",
        booking_status: 1,
        discount: 0,
        reference_number: "",
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        sub_total: 0,
        after_discount: 0,
        sales_tax: 0,
        total_price: 0,
        remaining_price: 0,
        request: "",
        company_id: this.$auth.user.company.id,
        remark: "",
        rooms: "",
        reference_no: "",
        paid_by: "",
        purpose: "",
      };

      this.priceListTableView = [];
      this.selectedRooms = [];

      this.activeTab = 0;

      this.dialog = false;
    },
    handleTableData({ arrToMerge, payload }) {
      this.room.check_in = payload.check_in;
      this.room.check_out = payload.check_out;
      this.selectedRooms = [payload];
      this.priceListTableView = arrToMerge;
    },
    async get_business_sources() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      let { data } = await this.$axios.get("business-source-list", config);
      this.business_sources = data;
    },
    handleFullAddress(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
    },
    deleteItem(index) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms.splice(index, 1);
    },

    nextTab() {
      if (!this.customer.customer_type) {
        this.$swal("Warning", "Select Business Source", "error");
        return;
      }

      if (!this.customer.first_name) {
        this.$swal("Warning", "Customer first name is required", "error");
        return;
      }

      if (!this.customer.last_name) {
        this.$swal("Warning", "Customer last name is required", "error");
        return;
      }

      if (!this.customer.contact_no) {
        this.$swal("Warning", "Customer contact no is required", "error");
        return;
      }

      if (!this.room.type) {
        this.$swal("Warning", "Select Source Type", "error");
        return;
      }

      this.activeTab += 1;
    },

    runAllFunctions() {
      this.getDays();
      this.subTotal();
      this.processCalculation();
    },

    getDays() {
      let ci = new Date(this.temp.check_in);
      let co = new Date(this.temp.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      if (days > 0) {
        return (this.room.total_days = days);
      }
    },

    get_reservation() {
      this.temp.room_id = this.reservation.room_id;
      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type;
      this.temp.price = this.reservation.price;
      this.temp.check_in = this.reservation.check_in;
      this.temp.check_out = this.reservation.check_out;

      this.temp.check_in_time = this.reservation.check_in_time;
      this.temp.check_out_time = this.reservation.check_out_time;

      this.temp.room_tax = this.reservation.total_tax;
      this.room.check_in = this.reservation.check_in;
      this.room.check_out = this.reservation.check_out;
      this.temp.priceList = this.reservation.priceList;
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    processCalculation() {
      let discount = parseFloat(this.temp.room_discount) || 0;
      let room_extra_amount = parseFloat(this.temp.room_extra_amount) || 0;
      let sub_total = parseFloat(this.room.sub_total) || 0;

      let advance_price = parseFloat(this.room.advance_price) || 0;

      let afterExtraAmount = sub_total + room_extra_amount;
      let afterDiscount = afterExtraAmount - discount;

      this.room.remaining_price = afterDiscount - advance_price;

      return (this.room.total_price = afterDiscount);
    },

    subTotal() {
      return (this.room.sub_total = this.priceListTableView.reduce(
        (total, num) => total + num.total_price,
        0
      ));
    },

    getType(val) {
      if (val == "Online") {
        this.isOnline = true;
        this.isCorporate = false;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isCorporate = false;
        this.isOnline = false;
        this.isAgent = true;
        return;
      }
      if (val == "Corporate") {
        this.isOnline = false;
        this.isAgent = false;
        this.isCorporate = true;
        return;
      }

      if (val == "Walking") {
        this.room.source = "walking";
      }

      if (val == "Complimentary") {
        this.room.source = "complimentary";
      }

      this.isOnline = false;
      this.isAgent = false;
    },

    get_gst(item, type) {
      // agent
      // online
      // corporate

      switch (type) {
        case "agent":
          this.customer.gst_number = this.agentList.find(
            (e) => e.name == item
          ).gst;
          break;
        case "online":
          this.customer.gst_number = this.sources.find(
            (e) => e.name == item
          ).gst;
          break;
        case "corporate":
          this.customer.gst_number = this.CorporateList.find(
            (e) => e.name == item
          ).gst;
          break;
        default:
          break;
      }
    },

    get_agents() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },
    get_Corporate() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_corporate`, payload).then(({ data }) => {
        this.CorporateList = data;
      });
    },

    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
      });
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },

    mergeEntries(entries) {
      const result = [];

      entries.forEach((entry) => {
        const existingEntry = result.find(
          (e) => e.room_type === entry.room_type && e.date === entry.date
        );

        if (existingEntry) {
          existingEntry.no_of_rooms += entry.no_of_rooms;
          existingEntry.total_price += entry.total_price;
        } else {
          result.push({ ...entry });
        }
      });

      return result;
    },

    get_customer() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.search.mobile;
      if (contact_no == undefined || contact_no == "") {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer/${contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            // this.customer = {};
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            return;
          }

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };
          this.customer.id_card_type_id = parseInt(
            this.customer.id_card_type_id
          );

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },

    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    store() {
      if (!this.customer.customer_type) {
        this.$swal("Warning", "Select Business Source", "error");
        return;
      }

      if (!this.customer.first_name) {
        this.$swal("Warning", "Customer first name is required", "error");
        return;
      }

      if (!this.customer.last_name) {
        this.$swal("Warning", "Customer last name is required", "error");
        return;
      }

      if (!this.customer.contact_no) {
        this.$swal("Warning", "Customer contact no is required", "error");
        return;
      }

      if (!this.room.type) {
        this.$swal("Warning", "Select Source Type", "error");
        return;
      }

      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }

      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.$swal("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
      }

      if (this.reservation.booking_status == 2) {
        this.room.booking_status = 2;
      }

      let rooms = this.selectedRooms.map((e) => e.room_no);
      this.room.rooms = rooms.toString();
      let payload = {
        ...this.room,
        ...this.customer,
      };
      this.$axios
        .post("/booking_validate1", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.$swal(
              "No reservation created!",
              "Some fields are missing or invalid",
              "error"
            );
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.errors = [];
            this.store_booking();
          }
        })
        .catch((e) => console.log(e));
    },

    store_booking() {
      let payload = {
        ...this.room,
        customer_type: this.customer.customer_type,
        selectedRooms: this.selectedRooms,
        ...this.customer,
        user_id: this.$auth.user.id,
        booking_type: "hall",
      };

      this.subLoad = false;

      this.$axios
        .post("/hall-booking", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.selectedRooms = [];
            this.priceListTableView = [];
            this.room_type_list = {};
            this.$emit(`success`);
            this.dialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
