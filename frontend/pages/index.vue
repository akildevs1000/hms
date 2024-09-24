<template>
  <div v-if="isPageLoad">
    <!-- <link
        href="matrix/dist/css/style.min.css"
        rel="stylesheet"
        v-if="isIndex"
      /> -->
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top
        absolute
        color="secondary"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <!-- dialogs -->
    <div>
      <v-dialog
        v-model="GRCDialog"
        persistent
        :width="900"
        class="checkin-models"
      >
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="GRCDialog = false"
              >mdi-close</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <Grc :bookingId="checkData.id"> </Grc>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="ArrivalReportDialog" persistent max-width="900px">
        <v-card>
          <v-alert color="grey lighten-3" dense flat>
            <v-row no-gutter>
              <v-col>
                <small>Arrival</small>
              </v-col>
              <v-col class="text-right">
                <v-btn
                  text
                  small
                  :color="isActiveTab == 1 ? 'primary' : ''"
                  @click="isActiveTab = 1"
                  ><small>Pending</small></v-btn
                >
                <v-btn
                  text
                  small
                  :color="isActiveTab == 2 ? 'primary' : ''"
                  @click="isActiveTab = 2"
                  ><small>Arrived</small></v-btn
                >

                <AssetsButtonClose @close="ArrivalReportDialog = false" />
              </v-col>
            </v-row>
          </v-alert>
          <v-container class="pt-0 mt-0">
            <ExpectCheckInReport
              v-if="isActiveTab == 1"
              :data="reservedWithoutAdvance"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
            <CheckInRoomsReport
              v-if="isActiveTab == 2"
              :data="[...Occupied, ...expectCheckOut]"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
          </v-container>
        </v-card>
      </v-dialog>

      <v-dialog v-model="CheckOutReportDialog" persistent max-width="900px">
        <v-card>
          <v-alert color="grey lighten-3" dense flat>
            <v-row no-gutter>
              <v-col>
                <small>Checkout</small>
              </v-col>
              <v-col class="text-right">
                <v-btn
                  text
                  small
                  :color="isActiveTab == 1 ? 'primary' : ''"
                  @click="isActiveTab = 1"
                  ><small>Pending</small></v-btn
                >
                <v-btn
                  text
                  small
                  :color="isActiveTab == 2 ? 'primary' : ''"
                  @click="isActiveTab = 2"
                  ><small>Checked Out</small></v-btn
                >
                <AssetsButtonClose @close="CheckOutReportDialog = false" />
              </v-col>
            </v-row>
          </v-alert>
          <v-container class="pt-0 mt-0">
            <ExpectCheckOutReport
              v-if="isActiveTab == 1"
              :data="[...Occupied, ...expectCheckOut]"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
            <CheckOutRoomsReport
              v-if="isActiveTab == 2"
              :data="dirtyRoomsList"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
          </v-container>
        </v-card>
      </v-dialog>

      <v-dialog v-model="InHouseDialog" persistent max-width="900px">
        <v-card>
          <v-alert color="grey lighten-3" dense flat>
            <v-row no-gutter>
              <v-col>
                <small>In House</small>
              </v-col>
              <v-col class="text-right">
                <AssetsButtonClose @close="InHouseDialog = false" />
              </v-col>
            </v-row>
          </v-alert>
          <v-container class="pt-0 mt-0">
            <InHouseReport
              :data="[...Occupied, ...expectCheckOut]"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
          </v-container>
        </v-card>
      </v-dialog>

      <v-dialog v-model="FoodDialog" persistent max-width="900px">
        <v-card>
          <v-alert color="grey lighten-3" dense flat>
            <v-row no-gutter>
              <v-col>
                <small>Food Order</small>
              </v-col>
              <v-col class="text-right">
                <AssetsButtonClose @close="FoodDialog = false" />
              </v-col>
            </v-row>
          </v-alert>
          <v-container class="pt-0 mt-0">
            <FoodOrderReport
              :data="[...Occupied, ...expectCheckOut]"
              @close-dialog="closeDialogs"
              :key="keyTabAll"
            />
          </v-container>
        </v-card>
      </v-dialog>
    </div>
    <!--end dialogs -->
    <v-row>
      <v-col cols="3" class="pr-0" style="max-width: 20%">
        <v-card class="elevation-2" style="height: 180px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Room Status</v-col
              >

              <v-col cols="4" class="text-right align-right"
                ><img
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="keyTabAll"
              name="margin"
              size="100%"
              :total="'100'"
              :colors="[`#139c4a`, `#71de36`, `#ffc000`, `#dc3545`]"
              :labels="[
                {
                  color: `#139c4a`,
                  text: `Available`,
                  value: availableRooms.length,
                },
                {
                  color: `#71de36`,
                  text: `Reserved`,
                  value: reservedWithoutAdvance.length,
                },
                {
                  color: `#ffc000`,
                  text: `CheckIn`,
                  value: Occupied.length + expectCheckOut.length,
                },
                {
                  color: `#dc3545`,
                  text: `Dirty`,
                  value: dirtyRoomsList.length,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="3" class="pr-0" style="max-width: 20%">
        <v-card class="elevation-2" style="height: 180px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Arrival</v-col
              >

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="ArrivalReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              :key="keyTabAll"
              name="reservedWithoutAdvance"
              :total="reservedWithoutAdvance.length + Occupied.length"
              size="100%"
              :colors="[`#ffc000`, `#71de36`]"
              :labels="[
                {
                  color: `#ffc000`,
                  text: `Arrived`,
                  value: Occupied.length + expectCheckOut.length,
                },
                {
                  color: `#71de36`,
                  text: `Pending`,
                  value: `${reservedWithoutAdvance.length}`,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3" class="pr-0" style="max-width: 20%">
        <v-card class="elevation-2" style="height: 180px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Checkout</v-col
              >

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="CheckOutReportDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              name="dirtyRoomsList"
              :total="dirtyRoomsList.length + expectCheckOut.length"
              size="100%"
              :colors="[`#dc3545`, `#ffc000`]"
              :labels="[
                {
                  color: `#dc3545`,
                  text: `CheckOut`,
                  value: `${dirtyRoomsList.length}`,
                },
                {
                  color: `#ffc000`,
                  text: `Pending`,
                  value: `${Occupied.length + expectCheckOut.length}`,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3" class="pr-0" style="max-width: 20%">
        <v-card class="elevation-2" style="height: 180px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >In-House</v-col
              >

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="InHouseDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />
            <Donut
              name="members"
              :total="members.total"
              size="100%"
              :colors="colors"
              :labels="[
                {
                  color: `blue`,
                  text: `Adult`,
                  value: members.adult,
                },
                {
                  color: `green`,
                  text: `Children`,
                  value: members.child,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3" class="pr-0" style="max-width: 20%">
        <v-card class="elevation-2" style="height: 180px">
          <v-card-text>
            <v-row style="margin-top: -12px"
              ><v-col cols="8" style="color: black; font-size: 12px"
                >Food Order</v-col
              >

              <v-col cols="4" class="text-right align-right"
                ><img
                  @click="FoodDialog = true"
                  src="/dashboard-arrow.png"
                  style="width: 18px; padding-top: 5px"
              /></v-col>
            </v-row>
            <v-divider color="#DDD" style="margin-bottom: 10px" />

            <Donut
              v-if="foodOrdersCount"
              name="foodOrdersCount"
              size="100%"
              :colors="colors"
              :total="foodOrdersCount.total"
              :labels="[
                {
                  color: `blue`,
                  text: `Breakfast`,
                  value: foodOrdersCount.breakfast,
                },
                {
                  color: `green`,
                  text: `Lunch`,
                  value: foodOrdersCount.lunch,
                },
                {
                  color: `orange`,
                  text: `Dinner`,
                  value: foodOrdersCount.dinner,
                },
              ]"
            />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" style="padding-top: 0px">
        <v-card class="py-2">
          <v-container fluid>
            <v-row>
              <v-col></v-col>
              <v-col cols="1" class="mt-0" style="max-width: 60px">
                <BookingHall
                  @success="handleSuccess(`Hall has been Booked`)"
                  :onlyButton="true"
                />
              </v-col>
              <v-col cols="1" class="mt-0" style="max-width: 60px">
                <BookingIndividual
                  @success="handleSuccess(`Room has been Booked`)"
                  :onlyButton="true"
                />
              </v-col>
              <v-col cols="1" class="mt-0" style="max-width: 60px">
                <BookingGroup
                  @success="handleSuccess(`Room(s) has been Booked`)"
                  :onlyButton="true"
                />
              </v-col>
              <v-col cols="1" class="mt-0" style="max-width: 60px">
                <BookingQuickCheckIn
                  :key="BookingQuickCheckInCompKey"
                  @success="handleNewSuccess"
                />
              </v-col>
              <v-col cols="1" class="mt-0" style="max-width: 60px">
                <BookingQuickCheckOut
                  :key="keyTabAll"
                  @success="handleSuccess(`Room(s) has been Checked Out`)"
                />
              </v-col>
              <v-col cols="3">
                <v-row>
                  <v-col cols="6">
                    <v-text-field
                      small
                      class="global-search-textbox"
                      clearable
                      dense
                      label="Search..."
                      outlined
                      v-model="searchQuery"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6">
                    <v-menu
                      v-model="menu2"
                      :close-on-content-click="false"
                      :nudge-right="-120"
                      transition="scale-transition"
                      offset-y
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          outlined
                          label="Filter Date"
                          class="global-search-date"
                          v-model="filterDate"
                          readonly
                          v-bind="attrs"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="filterDate"
                        @input="menu2 = false"
                        no-title
                      ></v-date-picker>
                    </v-menu>
                    <!-- <v-select
                      class="global-search-select"
                      dense
                      :items="[
                        { id: ``, name: `Select All` },
                        { id: `expected_arrival`, name: `Arrival` },
                        { id: `expected_checkout`, name: `Checkout` },
                        { id: `blocked`, name: `Blocked` },
                        // { id: `expected_arrival`, name: `Sold` },
                        { id: `available`, name: `Available` },
                        // { id: `compliment`, name: `Compliment` },
                        { id: `dirty`, name: `Dirty` },
                      ]"
                      item-text="name"
                      item-value="id"
                      label="Select  Item"
                      outlined
                      v-model="filterQuery"
                      @change="getRoomsByFilter"
                    ></v-select> -->
                  </v-col>
                </v-row>
              </v-col>
            </v-row>

            <v-row
              style="margin-top: -30px; padding-top: 0px; min-height: 600px"
            >
              <v-col
                cols="12"
                style="margin-top: 0px; padding-top: 0px; height: auto"
              >
                <!-- <pre>{{Occupied}}</pre> -->
                <v-tabs hide-slider right v-model="tab" color="#0d652d">
                  <v-tab style="font-weight: bold">All</v-tab>
                  <v-tab style="font-weight: bold">Occupied</v-tab>
                  <v-tab style="font-weight: bold">Arrival</v-tab>
                  <v-tab style="font-weight: bold">Checkedout </v-tab>
                  <v-tab style="font-weight: bold">Blocked</v-tab>
                  <!-- <v-tab>Sold </v-tab> -->
                  <v-tab style="font-weight: bold">Available</v-tab>
                  <!-- <v-tab>Compliment</v-tab> -->
                  <v-tab-item>
                    <v-card color="basil" style="height: auto">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="All"
                          :searchQuery="searchQuery"
                          :tabFilter="'All'"
                          :key="keyTabAll"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList
                      ></v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card color="basil" style="height: auto">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="occupied"
                          :searchQuery="searchQuery"
                          :tabFilter="'occupied'"
                          :key="keyTabOccupied"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList>
                      </v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card color="basil">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="expected_arrival"
                          :searchQuery="searchQuery"
                          :tabFilter="'expected_arrival'"
                          :key="keyTabexpected_arrival"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList
                      ></v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card color="basil">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="checkedout"
                          :searchQuery="searchQuery"
                          :tabFilter="'checkedout'"
                          :key="keyTabdirty"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList>
                      </v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card color="basil">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="blocked"
                          :searchQuery="searchQuery"
                          :tabFilter="'blocked'"
                          :key="keyTabblocked"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList
                      ></v-card-text>
                    </v-card>
                  </v-tab-item>
                  <v-tab-item>
                    <v-card color="basil">
                      <v-card-text>
                        <DashboardRoomsList
                          ref="RoomComp"
                          name="available"
                          :searchQuery="searchQuery"
                          :tabFilter="'available'"
                          :key="keyTabavailable"
                          :data="rooms"
                          :calenderColorCodes="calenderColorCodes"
                          @call_room_list="refreshRoomList"
                        ></DashboardRoomsList
                      ></v-card-text>
                    </v-card>
                  </v-tab-item>
                </v-tabs>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </div>
  <Preloader v-else />
</template>
<script>
import Posting from "../components/booking/Posting";
import PayAdvance from "../components/booking/PayAdvance";
import CheckIn from "../components/booking/CheckIn.vue";
import CheckOut from "../components/booking/CheckOut.vue";
import NewCheckIn from "../components/booking/NewCheckIn.vue";
import ReservationList from "../components/reservation/ReservationList.vue";
import Available from "../components/svg/Available.vue";
import Dirty from "../components/svg/Dirty.vue";
import Booked from "../components/svg/Booked.vue";
import CheckOutSvg from "../components/svg/CheckOutSvg.vue";
import PaidBookedSvg from "../components/svg/PaidBookedSvg.vue";
import ExpectCheckInSvg from "../components/svg/ExpectCheckInSvg.vue";
import ExpectCheckOutSvg from "../components/svg/ExpectCheckOutSvg.vue";
import CheckInSvg from "../components/svg/CheckInSvg.vue";
// import FoodOrderRooms from "../components/food/FoodOrderRooms.vue";

import FoodOrderReport from "../components/summary_reports/FoodOrderReport.vue";
import InHouseReport from "../components/summary_reports/InHouseReport.vue";
import ExpectCheckInReport from "../components/summary_reports/ExpectCheckInReport.vue";
import ExpectCheckOutReport from "../components/summary_reports/ExpectCheckOutReport.vue";
import AvailableRoomsReport from "../components/summary_reports/AvailableRoomsReport.vue";
import CheckInRoomsReport from "../components/summary_reports/CheckInRoomsReport.vue";
import BookedRoomsReport from "../components/summary_reports/BookedRoomsReport.vue";
import PaidRoomsReport from "../components/summary_reports/PaidRoomsReport.vue";
import CheckOutRoomsReport from "../components/summary_reports/CheckOutRoomsReport.vue";
import DirtyRoomsReport from "../components/summary_reports/DirtyRoomsReport.vue";
import Grc from "../components/booking/GRC.vue";

export default {
  layout({ $auth }) {
    if ($auth.user.user_type != "company" && $auth.user.is_verified == 0) {
      return "guest";
    } else {
      return "default";
    }
  },

  components: {
    InHouseReport,
    Grc,
    DirtyRoomsReport,
    CheckOutRoomsReport,
    PaidRoomsReport,
    BookedRoomsReport,
    CheckInRoomsReport,
    ExpectCheckOutReport,
    ExpectCheckInReport,
    FoodOrderReport,
    CheckInSvg,
    ExpectCheckOutSvg,
    ExpectCheckInSvg,
    CheckOutSvg,
    Booked,
    Available,
    Posting,
    PayAdvance,
    ReservationList,
    CheckIn,
    CheckOut,
    NewCheckIn,
    Dirty,
    PaidBookedSvg,
    AvailableRoomsReport,
  },
  data() {
    return {
      isActiveTab: 1,
      BookingQuickCheckInCompKey: 1,
      calenderColorCodes: [],
      tab: 0,
      filterDate: "2024-08-15",
      menu2: false,
      colors: ["#92d050", "#ff0000", "#ffc000", "#0D652D", "#174EA6"],
      key: 1,
      reservation: [],
      rightClickRoomId: "",
      selected_booked_room_id: "",
      selected_booking_id: "",
      cancelCheckInDialog: false,
      checkInCancelReason: "",
      chart: {
        eco: 35,
      },

      check_out_menu: false,
      check_out: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),

      temp: "",
      isPageLoad: false,
      loading: false,
      cancelLoad: false,
      snackbar: false,
      response: "",
      isDirty: true,
      payingAdvance: false,

      ArrivalReportDialog: false,
      CheckOutReportDialog: false,
      InHouseDialog: false,
      FoodDialog: false,
      checkInDialog: false,
      checkInKey: 1,
      checkOutDialog: false,
      GRCDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      NewBooking: false,

      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      showMenuForNewBooking: false,

      bookingStatus: 0,
      eventStatus: "",
      x: 0,
      y: 0,

      elevations: [6, 12, 18],
      first_login_auth: 1,
      loading: true,

      logs: [],

      orders: "",
      products: "",
      customers: "",
      daily_orders: "",
      weekly_orders: "",
      monthly_orders: "",
      evenIid: "",
      eventStatus: "",
      rooms: [],
      postings: [],
      dirtyRoomsList: [],
      availableRooms: [],
      Occupied: [],
      checkIn: [],
      checkOut: [],
      reservedWithoutAdvance: [],
      reason: "",
      totalTransactionAmount: 0,
      new_payment: 0,
      new_advance: 0,
      AdvancePayLoading: false,
      reference: 0,
      full_payment: 0,
      isPrintInvoice: false,
      items: [],
      transactions: [],
      checkData: {},
      roomData: null,
      customerId: "",
      bookingId: "",
      document: null,
      lastTapTime: null,
      isDbCLick: false,
      members: {
        adult: 0,
        child: 0,
        total: 0,
      },
      foodOrdersCount: null,
      expectCheckOut: "",
      headers: [
        { text: "#" },
        { text: "Bill Number" },
        { text: "Room No" },
        { text: "Room Type" },
        { text: "Customer" },
        { text: "Item" },
        { text: "QTY" },
        { text: "Amount" },
        { text: "Date" },
      ],
      newBookingRoom: {},
      isIndex: true,

      showMenu: false,

      filtered: {
        AvailableRooms: [],
      },

      searchQuery: null,
      filterQuery: ``,
      keyTabAll: 11,
      keyTabexpected_arrival: 12,
      keyTabdirty: 13,
      keyTabblocked: 18,
      keyTabavailable: 14,
      keyTabcompliment: 15,
      keyTabdirty: 16,
      keyTabOccupied: 17,
    };
  },
  watch: {
    searchQuery() {
      this.keyTabAll++;
    },
    filterDate() {
      this.keyTabAll++;
      this.room_list();
    },
    tab() {},
    checkInDialog() {
      this.formTitle = "Check In";
      this.get_data();
      ++this.checkInKey;
      this.checkInDialog ? (this.isIndex = false) : (this.isIndex = true);
    },

    NewBooking() {
      this.NewBooking ? (this.isIndex = false) : (this.isIndex = true);
    },

    postingDialog() {
      this.formTitle = "Posting";
      this.get_data();
    },

    checkOutDialog() {
      this.formTitle = "Check Out";
      this.get_data();
    },

    viewPostingDialog() {
      this.formTitle = "View Post";
      this.get_posting();
    },

    payingAdvance() {
      this.formTitle = "Advance Payment";
      this.get_data();
    },
  },
  created() {
    this.filterDate = new Date(
      Date.now() - new Date().getTimezoneOffset() * 60000
    )
      .toISOString()
      .substr(0, 10);
    this.room_list();
    this.first_login_auth = this.$auth.user.first_login;

    setInterval(() => {
      this.room_list();
      this.key = this.key + 1;
    }, 1000 * 60 * 2);

    let payload = {
      params: {
        company_id: this.$auth.user.company.id,
      },
    };
    this.$axios.get(`room-color-codes`, payload).then(({ data }) => {
      this.calenderColorCodes = data;
    });

    setTimeout(() => {
      this.key += 1;
    }, 1000);
  },

  methods: {
    handleSuccess(message) {
      this.room_list();
      this.alert("Success!", message, "success");
      this.checkInDialog = false;
      this.keyTabAll++;
    },

    handleNewSuccess() {
      this.room_list();
      this.BookingQuickCheckInCompKey += 1;
      this.keyTabAll++;
    },
    refreshRoomList() {
      this.room_list();
      this.keyTabAll++;
    },

    get_next_day() {
      // const today = new Date();
      // const tomorrow = new Date(today);
      // tomorrow.setDate(tomorrow.getDate() + 1);
      // this.check_out_date = tomorrow.toISOString().substr(0, 10);

      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const year = tomorrow.getFullYear();
      const month = String(tomorrow.getMonth() + 1).padStart(2, "0");
      const day = String(tomorrow.getDate()).padStart(2, "0");
      const formattedDate = `${year}-${month}-${day}`;

      return formattedDate;
    },
    goToBookingPage() {
      console.log(" this.newBookingRoom", this.newBookingRoom);
      let currentDate = new Date(
        Date.now() - new Date().getTimezoneOffset() * 60000
      )
        .toISOString()
        .substr(0, 10);

      this.reservation.isCalculate = true;
      this.reservation.room_id = this.newBookingRoom.id;
      this.reservation.room_type = this.newBookingRoom.room_type.name;
      this.reservation.room_no = this.newBookingRoom.room_no;
      this.reservation.check_in = currentDate;
      this.reservation.booking_status = 2;

      this.reservation.check_out = this.get_next_day();

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: this.reservation.room_type,
          room_no: this.reservation.room_no,
          checkin: this.reservation.check_in,
          checkout: this.reservation.check_out,
        },
      };

      this.$store.commit("booking_payload", payload);
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return false;
          }

          this.reservation.room_id = data.room.id;
          this.reservation.price = data.total_price;
          this.reservation.priceList = data.data;
          this.reservation.total_tax = data.total_tax;

          this.reservation.total_price_after_discount =
            data.total_price_after_discount;
          this.reservation.total_price = data.total_price;
          this.reservation.total_discount = data.total_discount;

          let commitObj = {
            ...this.reservation,
          };
          //console.log('reservation1', commitObj);
          this.$store.commit("reservation", commitObj);
          this.$router.push(`/hotel/new2`);
        });
    },
    async logout() {
      this.$axios.get(`/logout`).then(({ res }) => {
        this.$auth.logout();
        this.$router.push(`/login`);
      });
    },

    can() {
      if (
        this.$auth.user.employee_role_id > 0 &&
        this.$auth.user.is_verified == 0
      ) {
        this.logout();
        this.$router.push(`login`);
        return false;
      } else {
        return true;
      }
    },

    handleTouchstart(event, room) {
      console.log(room);
      this.touchstart(
        event,
        room?.booked_room?.id,
        room?.booked_room?.booking?.booking_status
      );
    },
    handleMouseOver(room) {
      this.mouseOver(
        room?.booked_room?.id,
        room?.booked_room?.booking?.booking_status
      );
    },
    getButtonClass(room) {
      return room.booked_room?.background ===
        "linear-gradient(135deg, #4390FC 0, #4390FC 100%)"
        ? "element"
        : "";
    },
    getBackgroundImage(room) {
      return room?.booked_room?.background || "";
    },
    isDeviceStatusActive(room) {
      return room.device?.latest_status === 1;
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    get_check_out() {
      this.checkOutDialog = true;
      this.get_transaction();
    },

    get_transaction() {
      let id = this.bookingId;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`get_transaction_by_booking_id/${id}`, payload)
        .then(({ data }) => {
          this.transactions = data.transactions;
          this.totalTransactionAmount = data.totalTransactionAmount;
        });
    },

    mouseOver(bookedRoomId, bookingStatus) {
      this.evenIid = bookedRoomId;
      this.bookingStatus = bookingStatus;
    },

    touchstart(e, bookedRoomId, bookingStatus) {
      this.evenIid = bookedRoomId;
      this.bookingStatus = bookingStatus;
      this.show(e, true);
    },

    mouseOverForAvailable(newBookingRoom) {
      // this.newBookingRoom = newBookingRoom;
      // console.log(newBookingRoom);
    },

    closeNewCheckin() {
      this.newBookingRoom = false;
      this.NewBooking = false;
    },

    get_data(jsEvent = null) {
      this.selected_booked_room_id = this.evenIid;

      let payload = {
        params: {
          id: this.evenIid,
          company_id: this.$auth.user.company.id,
        },
      };
      this.rightClickRoomId = "---";
      this.$axios.get(`get_booked_room`, payload).then(({ data }) => {
        let { booking, ...roomData } = data;
        this.checkData = data.booking;
        this.roomData = roomData;
        this.bookingId = data.booking.id;

        this.rightClickRoomId = data.booking.resourceId;

        this.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.booking.customer_id;
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
    },

    show(e, isTouch = false) {
      this.showMenuForNewBooking = false;
      e.preventDefault();
      this.get_data();
      if (isTouch) {
        const currentTime = new Date().getTime();
        const tapThreshold = 300; // milliseconds
        if (this.lastTapTime && currentTime - this.lastTapTime < tapThreshold) {
          this.$router.push(`/customer/details/${this.bookingId}`);
          return;
        }
        this.lastTapTime = currentTime;
      }

      if (isTouch) {
        const touch = e.touches[0];
        this.x = touch.clientX;
        this.y = touch.clientY;
      } else {
        this.x = e.clientX;
        this.y = e.clientY;
      }
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    makeNewBookingForTouch(e, newBookingRoom) {
      this.newBookingRoom = newBookingRoom;
      this.showMenu = false;
      e.preventDefault();
      const touch = e.touches[0];
      this.x = touch.clientX;
      this.y = touch.clientY;
      this.$nextTick(() => {
        this.showMenuForNewBooking = true;
      });
    },

    makeNewBooking(e, newBookingRoom) {
      this.newBookingRoom = newBookingRoom;

      e.preventDefault();
      this.x = e.clientX;
      this.y = e.clientY;
      this.$nextTick(() => {
        this.showMenuForNewBooking = true;
      });
    },

    roomStatus(status) {
      let payload = {
        company_id: this.$auth.user.company.id,
        room_no: this.newBookingRoom.room_no,
      };
      this.$axios
        .post(`set_room_status/${status}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    get_posting() {
      let id = this.evenIid;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`posting/${id}`, payload).then(({ data }) => {
        this.postings = data;
      });
    },
    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user && this.$auth.user.company.id,
          // check_in: new Date().toJSON().slice(0, 10),
          check_in: this.filterDate,
          filter_date: this.filterDate,
        },
      };
      this.$axios.get(`room_list_grid`, payload).then(({ data }) => {
        if (!data.status) {
          this.alert("Failure!", data.data, "error");
          return false;
        }

        this.rooms = data;
        this.availableRooms = data.availableRooms;
        this.reservedWithoutAdvance = data.reservedWithoutAdvance;
        this.Occupied = data.checkIn;
        this.expectCheckOut = data.expectCheckOut;
        this.dirtyRoomsList = data.dirtyRoomsList;

        let data1 = data.reservedWithoutAdvance.map((e) => e.room_no);
        let data2 = data.expectCheckOut.map((e) => e.room_no);
        let data3 = data.checkIn.map((e) => e.room_no);
        let data4 = data.blockedRooms.map((e) => e.room_no);
        let data5 = data.dirtyRoomsList.map((e) => e.room_no);

        let allRoomNumbers = [...data1, ...data2, ...data3, ...data4, ...data5];
        let uniqueRoomNumbers = [...new Set(allRoomNumbers)];
        this.availableRooms = data.availableRooms.filter(
          (e) => !uniqueRoomNumbers.includes(e.room_no)
        );

        this.members = data.members;
        this.foodOrdersCount = data.foodOrdersCount;

        this.isIndex = true;
        setTimeout(() => {
          this.isPageLoad = true;
        }, 100);
        this.keyTabAll = 31;
        this.keyTabexpected_arrival = 33;
        this.keyTabdirty = 34;
        this.keyTabblocked = 35;
        this.keyTabavailable = 36;
        this.keyTabcompliment = 37;
        this.keyTabdirty = 38;
        this.keyTabOccupied = 39;
      });
    },

    dblclick() {
      this.isDbCLick = true;
      this.get_data();
    },

    viewBillingDialog() {
      let id = this.bookingId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_event_by_db_click() {
      this.$router.push(`/customer/details/${this.bookingId}`);
    },
    changeCheckInAdminProcess() {
      if (this.$auth.user.role.name.toLowerCase() != "admin") {
        //alert("You are not authorized to Cancel the Checkin");

        this.alert(
          "Failure!",
          "You are not authorized to Cancel the Checkin",
          "error"
        );
        return false;
      } else {
        if (this.checkInCancelReason == "") {
          alert("Enter reason");
          return;
        }

        this.cancelLoad = true;

        let payload = {
          cancel_checkin_reason: this.checkInCancelReason,
          cancel_checkin_userid: this.$auth.user.id,
          booking_id: this.bookingId,
          company_id: this.$auth.user.company.id,
          booked_room_id: this.selected_booked_room_id,
        };

        this.$axios
          .post(`change_checkin_to_booking_admin/${this.evenIid}`, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.snackbar = data.status;
              this.response = data.message;
              this.cancelLoad = false;
              return;
            }
            this.cancelLoad = false;
            this.room_list();
            this.reason = "";
            this.cancelDialog = false;
            this.snackbar = data.status;
            this.response = data.message;
            this.cancelCheckInDialog = false;
          })
          .catch((err) => console.log(err));
      }
    },
    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id,
        bookedRoomId: this.evenIid,
      };

      this.$axios
        .post(`set_available/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    setMaintenance() {
      let payload = {
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`set_maintenance/${this.bookingId}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }

      this.cancelLoad = true;

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id,
      };
      this.$axios
        .post(`cancel_room/${this.evenIid}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = data.status;
            this.response = data.message;
            this.cancelLoad = false;
            return;
          }
          this.cancelLoad = false;
          this.room_list();
          this.reason = "";
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    succuss(
      data,
      check_in = true,
      posting = true,
      check_out = true,
      advance_payment = true
    ) {
      if (check_in) {
        this.checkData = {};
        this.checkInDialog = false;
        this.new_payment = 0;
      }
      if (check_out) {
        this.checkData = {};
        this.checkOutDialog = false;
      }
      if (posting) {
        this.posting = {};
        this.postingDialog = false;
      }

      if (advance_payment) {
        this.checkData = {};
        this.new_advance = 0;
        this.payingAdvance = false;
      }

      this.room_list();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;
    },

    close() {
      this.checkInDialog = false;
      this.new_payment = 0;
      this.new_advance = 0;
      this.payingAdvance = false;
      this.checkOutDialog = false;
      this.document = null;
    },

    closeCheckOut() {
      this.checkOutDialog = false;
    },

    closeDialogs(res) {
      this.succuss(res);
    },
  },
};
</script>
