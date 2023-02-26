<template>
  <div v-if="isPageLoad">
    <link href="matrix/dist/css/style.min.css" rel="stylesheet" v-if="isIndex" />
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top absolute color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <!-- dialogs -->
    <div>
      <v-dialog v-model="checkInDialog" persistent :width="1366" class="checkin-models">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="close">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-card-text>
            <check-in :BookingData="checkData" @close-dialog="closeDialogs"></check-in>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end check in dialog -->

      <!-- posting dialog -->
      <v-dialog v-model="postingDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="postingDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <Posting :BookingData="checkData" :evenIid="evenIid" @close-dialog="closeDialogs">
              </Posting>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
      <!-- end posting dialog -->

      <!--  viewPosting dialog -->
      <v-dialog v-model="viewPostingDialog" persistent max-width="900px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <table>
                <tr>
                  <th v-for="(item, index) in headers" :key="index">
                    <span v-html="item.text"></span>
                  </th>
                </tr>
                <v-progress-linear v-if="false" :active="loading" :indeterminate="loading" absolute
                  color="primary"></v-progress-linear>
                <tr v-for="(item, index) in postings" :key="index">
                  <td>{{ ++index }}</td>
                  <td>{{ caps(item.bill_no) }}</td>
                  <td>{{ caps(item && item.booked_room.room_no) }}</td>
                  <td>{{ caps(item && item.booked_room.room_type) }}</td>
                  <td>{{ caps(item && item.booked_room.title) }}</td>
                  <td>{{ caps(item.item) }}</td>
                  <td>{{ caps(item.qty) }}</td>
                  <td>{{ caps(item.amount) }}</td>
                  <td>{{ caps(item.posting_date) }}</td>
                </tr>
              </table>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-btn class="error" small @click="viewPostingDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <!--   viewPosting dialog -->

      <!-- pay advance  -->
      <v-dialog v-model="payingAdvance" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="payingAdvance = false">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-card-text>
            <PayAdvance :BookingData="checkData" @close-dialog="closeDialogs"></PayAdvance>
          </v-card-text>
        </v-card>
      </v-dialog>
      <!-- end pay advance  -->

      <!-- check out  -->
      <v-dialog v-model="checkOutDialog" persistent max-width="1000px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="checkOutDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <check-out :BookingData="checkData" @close-dialog="closeDialogs" />
          </v-card-text>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end check out  -->

      <!-- cancel room  -->
      <v-dialog v-model="cancelDialog" persistent max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            Are you sure you want to cancel this
          </v-card-title>
          <v-container grid-list-xs>
            <v-textarea placeholder="Reason" rows="3" dense outlined v-model="reason"></v-textarea>
          </v-container>
          <v-card-actions>
            <v-btn class="primary" small :loading="false" @click="cancelItem">
              Yes
            </v-btn>
            <v-btn class="error" small @click="cancelDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end cancel room -->

      <!-- New Booking room  -->
      <v-dialog v-model="NewBooking" persistent width="90%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="NewBooking = false">mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <new-check-in :reservation="newBookingRoom" />
          </v-card-text>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
    <!--end dialogs -->

    <div>
      <v-row class="flex" justify="center"> </v-row>
      <v-menu v-model="showMenu" :position-x="x" :position-y="y" absolute offset-y>
        <v-list>
          <v-list-item-group v-model="selectedItem">
            <v-list-item v-if="bookingStatus == 1" link @click="checkInDialog = true">
              <v-list-item-title>Check In</v-list-item-title>
            </v-list-item>

            <v-list-item v-else-if="bookingStatus == 2" link @click="get_check_out">
              <v-list-item-title>Check Out</v-list-item-title>
            </v-list-item>

            <div v-else-if="bookingStatus == 3">
              <v-list-item link @click="setAvailable">
                <v-list-item-title>Make Available</v-list-item-title>
              </v-list-item>
              <v-list-item link @click="setMaintenance">
                <v-list-item-title>Make Maintenance</v-list-item-title>
              </v-list-item>
            </div>

            <div v-else>
              <v-list-item link @click="setAvailable">
                <v-list-item-title>Make Available</v-list-item-title>
              </v-list-item>
            </div>

            <div v-if="bookingStatus == 2">
              <v-list-item link @click="postingDialog = true">
                <v-list-item-title>Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewPostingDialog = true">
                <v-list-item-title>View Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewBillingDialog">
                <v-list-item-title>View Billing</v-list-item-title>
              </v-list-item>
            </div>
            <v-list-item link @click="payingAdvance = true" v-if="bookingStatus <= 2 && checkData.paid_by != 2">
              <v-list-item-title>Pay Advance</v-list-item-title>
            </v-list-item>

            <v-list-item link @click="cancelDialog = true" v-if="bookingStatus == 1">
              <v-list-item-title>Cancel Room</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
      <v-menu v-model="showMenuForNewBooking" :position-x="x" :position-y="y" absolute offset-y>
        <v-list>
          <v-list-item-group>
            <v-list-item link @click="NewBooking = true">
              <v-list-item-title>CheckIn</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <div class="page-wrapper mb-0 pb-0">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="row">
              <div class="col-md-12 col-12 col-lg-4 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center bg-cyan">
                    <div class="d-flex justify-space-around py-0 my-0">
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-male fx-1"></i>
                        <h5>
                          {{ members.adult }} | {{ members.child }} |
                          {{ members.baby }}
                        </h5>
                        <h6>Customers</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-coffee fx-1"></i>
                        <h5>
                          {{ onlyBreakfast.adult }} |
                          {{ onlyBreakfast.child }} |
                          {{ onlyBreakfast.baby }}
                        </h5>
                        <h6>Breakfast</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-concierge-bell"></i>
                        <h5>
                          {{ onlyLunch.adult }} | {{ onlyLunch.child }} |
                          {{ onlyLunch.baby }}
                        </h5>
                        <h6>Lunch</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-hamburger"></i>
                        <h5>
                          {{ onlyDinner.adult }} | {{ onlyDinner.child }} |
                          {{ onlyDinner.baby }}
                        </h5>
                        <h6>Dinner</h6>
                      </h1>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #32a15c">
                    <h1 class="font-light text-white">
                      <i class="fas fa-door-open"></i>
                      <h5>
                        {{ (availableRooms && availableRooms.length) || 0 }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Available</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-5 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #ffbe00">
                    <h1 class="font-light text-white">
                      <i class="fas fa-door-closed"></i>
                      <h5>
                        {{
                          (notAvailableRooms && notAvailableRooms.length) || 0
                        }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Booked Room</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #02ada4">
                    <h1 class="font-light text-white">
                      <i class="fas fa-money-bill"></i>
                      <h5>
                        {{ confirmedBooking || "---" }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Advance Paid Booking</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #ff0000">
                    <h1 class="font-light text-white">
                      <i class="fas fa-door-closed"></i>
                      <h5>
                        {{ dirtyRooms || "---" }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Dirty Room</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box bg-primary text-center" style="background-color: #18069e">
                    <h1 class="font-light text-white">
                      <i class="fas fa-plane-arrival" style="
                            -webkit-transform: scaleX(-1);
                            transform: scaleX(-1);
                          "></i>
                      <h5>
                        {{ expectCheckIn.length || 0 }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Expect Check In</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #4390fc">
                    <h1 class="font-light text-white">
                      <i class="fas fa-plane-departure"></i>
                      <h5>
                        {{ expectCheckOut.length || 0 }}
                      </h5>
                    </h1>
                    <h6 class="text-white">Expect Checked Out</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #800000">
                    <h1 class="font-light text-white">
                      <svg viewBox="0 0 576 512" fill="#ffff" width="50px" height="40px" style="
                            -webkit-transform: scaleX(-1);
                            transform: scaleX(-1);
                          ">
                        <path
                          d="M432 96c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zM347.7 200.5c1-.4 1.9-.8 2.9-1.2l-16.9 63.5c-5.6 21.1-.1 43.6 14.7 59.7l70.7 77.1 22 88.1c4.3 17.1 21.7 27.6 38.8 23.3s27.6-21.7 23.3-38.8l-23-92.1c-1.9-7.8-5.8-14.9-11.2-20.8l-49.5-54 19.3-65.5 9.6 23c4.4 10.6 12.5 19.3 22.8 24.5l26.7 13.3c15.8 7.9 35 1.5 42.9-14.3s1.5-35-14.3-42.9L505 232.7l-15.3-36.8C472.5 154.8 432.3 128 387.7 128c-22.8 0-45.3 4.8-66.1 14l-8 3.5c-32.9 14.6-58.1 42.4-69.4 76.5l-2.6 7.8c-5.6 16.8 3.5 34.9 20.2 40.5s34.9-3.5 40.5-20.2l2.6-7.8c5.7-17.1 18.3-30.9 34.7-38.2l8-3.5zm-30 135.1l-25 62.4-59.4 59.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L340.3 441c4.6-4.6 8.2-10.1 10.6-16.1l14.5-36.2-40.7-44.4c-2.5-2.7-4.8-5.6-7-8.6zM256 274.1c-7.7-4.4-17.4-1.8-21.9 5.9l-32 55.4L147.7 304c-15.3-8.8-34.9-3.6-43.7 11.7L40 426.6c-8.8 15.3-3.6 34.9 11.7 43.7l55.4 32c15.3 8.8 34.9 3.6 43.7-11.7l64-110.9c1.5-2.6 2.6-5.2 3.3-8L261.9 296c4.4-7.7 1.8-17.4-5.9-21.9z" />
                      </svg>
                      <h5>{{ checkIn.length || "0" }}</h5>
                    </h1>
                    <h6 class="text-white">Check In</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-3 col-xlg-3 py-0">
                <div class="card card-hover">
                  <div class="box text-center" style="background-color: #74166d">
                    <h1 class="font-light text-white">
                      <svg viewBox="0 0 576 512" fill="#ffff" width="50px" height="40px">
                        <path
                          d="M432 96c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zM347.7 200.5c1-.4 1.9-.8 2.9-1.2l-16.9 63.5c-5.6 21.1-.1 43.6 14.7 59.7l70.7 77.1 22 88.1c4.3 17.1 21.7 27.6 38.8 23.3s27.6-21.7 23.3-38.8l-23-92.1c-1.9-7.8-5.8-14.9-11.2-20.8l-49.5-54 19.3-65.5 9.6 23c4.4 10.6 12.5 19.3 22.8 24.5l26.7 13.3c15.8 7.9 35 1.5 42.9-14.3s1.5-35-14.3-42.9L505 232.7l-15.3-36.8C472.5 154.8 432.3 128 387.7 128c-22.8 0-45.3 4.8-66.1 14l-8 3.5c-32.9 14.6-58.1 42.4-69.4 76.5l-2.6 7.8c-5.6 16.8 3.5 34.9 20.2 40.5s34.9-3.5 40.5-20.2l2.6-7.8c5.7-17.1 18.3-30.9 34.7-38.2l8-3.5zm-30 135.1l-25 62.4-59.4 59.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L340.3 441c4.6-4.6 8.2-10.1 10.6-16.1l14.5-36.2-40.7-44.4c-2.5-2.7-4.8-5.6-7-8.6zM256 274.1c-7.7-4.4-17.4-1.8-21.9 5.9l-32 55.4L147.7 304c-15.3-8.8-34.9-3.6-43.7 11.7L40 426.6c-8.8 15.3-3.6 34.9 11.7 43.7l55.4 32c15.3 8.8 34.9 3.6 43.7-11.7l64-110.9c1.5-2.6 2.6-5.2 3.3-8L261.9 296c4.4-7.7 1.8-17.4-5.9-21.9z" />
                      </svg>
                      <h5>{{ checkOut.length || "0" }}</h5>
                    </h1>
                    <h6 class="text-white">Check Out</h6>
                  </div>
                </div>
              </div>
              <div class="col-md-12 col-lg-6 col-xlg-12 py-0">
                <div class="card card-hover p-0 m-0">
                  <div class="text-center p-0 m-0" style="background-color: white; height: 120px !important">
                    <h1 class="font-light p-0 m-0 text-black">
                      <NewPie :renderChartData="renderChartData()" />
                    </h1>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="col-12 col-md-3">
            <div class="col-md-12 col-lg-12 col-xlg-12 py-0">
              <div class="card card-hover p-0 m-0">
                <div
                  class="text-center p-0 m-0"
                  style="background-color: white"
                >
                  <h1 class="font-light p-0 m-0 text-black">
                    <NewPie :renderChartData="renderChartData" />
                  </h1>
                </div>
              </div>
            </div>
          </div> -->
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card elevation-0">
              <div class="card-body">
                <div class="d-md-flex align-items-center">
                  <div>
                    <h4 class="card-title">Rooms</h4>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-12 pt-0">
                    <v-row>
                      <v-col :class="noAvailableRoom.id" lg="1" md="4" sm="12" cols="12"
                        v-for="(noAvailableRoom, i) in notAvailableRooms" :key="i">
                        <v-card @contextmenu="show" :elevation="0" @mouseover="
                          mouseOver(
                            noAvailableRoom &&
                            noAvailableRoom.booked_room &&
                            noAvailableRoom.booked_room.id,
                            noAvailableRoom &&
                            noAvailableRoom.booked_room &&
                            noAvailableRoom.booked_room.booking
                              .booking_status
                          )
                        " @dblclick="dblclick" class="ma-0 px-md-1 py-md-2" :class="
  noAvailableRoom.booked_room.background ==
    'linear-gradient(135deg, #4390FC      0, #4390FC 100%)'
    ? 'element'
    : ''
" dark :style="`background-image:${(noAvailableRoom &&
    noAvailableRoom.booked_room &&
    noAvailableRoom.booked_room.background) ||
  ''
  }`">
                          <div class="text-center">
                            {{ caps(noAvailableRoom.room_type.name) }}
                          </div>
                          <div class="text-center">
                            {{ noAvailableRoom.room_no }}
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col :class="room.id" lg="1" md="4" sm="12" cols="12" v-for="(room, index) in availableRooms"
                        :key="index">
                        <v-card @contextmenu="makeNewBooking" @mouseover="mouseOverForAvailable(room)" :elevation="0"
                          class="ma-0 px-md-1 py-md-2" style="background-color: #32a15c" dark>
                          <div class="text-center">
                            {{ caps(room.room_type.name) }}
                          </div>
                          <div class="text-center">
                            {{ room.room_no }}
                          </div>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <ReservationList />
        </div>
      </div>
    </div>
  </div>
  <div v-else>
    <div class="text-center" style="width: 50px; margin: 25% auto">
      <v-img src="/preloaders/1.gif"></v-img>
    </div>
  </div>
</template>
<script>
import Posting from "../components/booking/Posting";
import PayAdvance from "../components/booking/PayAdvance";
import CheckIn from "../components/booking/CheckIn.vue";
import CheckOut from "../components/booking/CheckOut.vue";
import NewCheckIn from "../components/booking/NewCheckIn.vue";
import ReservationList from "../components/reservation/ReservationList.vue";
export default {
  components: {
    Posting,
    PayAdvance,
    ReservationList,
    CheckIn,
    CheckOut,
    NewCheckIn,
  },
  data() {
    return {
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
      snackbar: false,
      response: "",
      isDirty: true,
      payingAdvance: false,

      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      NewBooking: false,

      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      showMenuForNewBooking: false,

      bookingStatus: "",
      eventStatus: "",
      x: 0,
      y: 0,

      elevations: [6, 12, 18],
      first_login_auth: 1,
      loading: true,

      logs: [],

      total_items: [],
      items_by_cities: [],
      test_headers: [
        {
          text: "Customer",
          align: "left",
          sortable: false,
          value: "company_name",
        },
        {
          text: "Order Total",
          align: "left",
          sortable: false,
          value: "order_total",
        },
      ],
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
      notAvailableRooms: [],
      availableRooms: [],
      checkIn: [],
      checkOut: [],
      confirmedBooking: "",
      waitingBooking: "",
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
      customerId: "",
      bookingId: "",
      document: null,

      isDbCLick: false,
      members: {
        adult: 0,
        child: 0,
        baby: 0,
      },
      onlyBreakfast: {},
      onlyLunch: {},
      onlyDinner: {},
      dirtyRooms: 0,
      expectCheckIn: "",
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
    };
  },
  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.get_data();
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
    this.room_list();
    this.first_login_auth = this.$auth.user.first_login;
  },

  computed: {},
  mounted() {
    const screenResolution = window.innerWidth + " x " + window.innerHeight;
  },
  methods: {
    renderChartData() {
      let arr = [
        this.expectCheckIn.length,
        this.expectCheckOut.length,
        this.notAvailableRooms.length,
        this.availableRooms.length,
      ];
      return arr;
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

    mouseOverForAvailable(newBookingRoom) {
      this.newBookingRoom = newBookingRoom;
      console.log(newBookingRoom);
    },

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.bookingId = data.id;
        this.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.customer_id;
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
    },

    show(e) {
      e.preventDefault();
      this.get_data();
      this.x = e.clientX;
      this.y = e.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
      });
    },

    makeNewBooking(e) {
      e.preventDefault();
      // this.get_data();
      this.x = e.clientX;
      this.y = e.clientY;
      this.$nextTick(() => {
        this.showMenuForNewBooking = true;
      });
    },

    get_remaining(val) {
      let total = this.checkData.remaining_price;
      let advance_price = val;
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

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          check_in: new Date().toJSON().slice(0, 10),
        },
      };
      this.$axios.get(`room_list_grid`, payload).then(({ data }) => {
        this.rooms = data;
        this.onlyBreakfast = {
          ...data.fooForCustomers.breakfast,
        };
        this.onlyLunch = {
          ...data.fooForCustomers.lunch,
        };
        this.onlyDinner = {
          ...data.fooForCustomers.dinner,
        };
        this.dirtyRooms = data.dirtyRooms;
        this.notAvailableRooms = data.notAvailableRooms;
        this.availableRooms = data.availableRooms;
        this.confirmedBooking = data.confirmedBooking;
        this.waitingBooking = data.waitingBooking;
        this.expectCheckIn = data.expectCheckIn;
        this.expectCheckOut = data.expectCheckOut;
        this.checkIn = data.checkIn;
        this.checkOut = data.checkOut;

        this.renderChartData();
        this.members = {
          ...data.members,
        };
        this.isIndex = true;
        setTimeout(() => {
          this.isPageLoad = true;
        }, 100);
      });
    },

    dblclick() {
      this.isDbCLick = true;
      this.get_data();
    },

    getRelaventColor(status) {
      switch (parseInt(status)) {
        case 1:
          return "booked";
        case 2:
          return "checkedIn";
        case 3:
          return "checkedOut";
        case 4:
          return "background";
        case 5:
          return "grey";
        default:
          return "available";
      }
    },

    viewBillingDialog() {
      let id = this.bookingId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_event_by_db_click() {
      this.$router.push(`/customer/details/${this.bookingId}`);
    },

    store_check_in(data) {
      if (
        // this.new_payment == "" ||
        // this.new_payment == 0 ||
        data.document ? "" : this.document == null
      ) {
        alert("Enter required fields");
        return;
      }
      this.loading = true;
      let bookingId = data.id;
      let payload = {
        new_payment: this.new_payment,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
      };
      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, true, false);
            data.document ? "" : this.store_document(bookingId);
          }
        })
        .catch((e) => console.log(e));
    },

    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id,
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
            return;
          }
          this.room_list();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch((err) => console.log(err));
    },

    store_check_out() {
      this.loading = true;
      let payload = {
        booking_id: this.checkData.id,
        grand_remaining_price: this.checkData.grand_remaining_price,
        remaining_price: this.checkData.remaining_price,
        full_payment: this.full_payment,
        payment_mode_id: this.checkData.payment_mode_id,
        company_id: this.$auth.user.company.id,
        isPrintInvoice: this.isPrintInvoice,
      };
      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, true);
            if (this.isPrintInvoice) {
              this.redirect_to_invoice(data.bookingId);
            }
          }
        })
        .catch((e) => console.log(e));
    },

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `http://127.0.0.1:8000/api/invoice/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
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

    closeDialogs(res) {
      this.succuss(res);
    },
  },
};
</script>
<style src="@/assets/custom/check.css"></style>
<style scoped src="@/assets/custom/dash.css"></style>
<!-- <style scoped src="@/assets/dashtem.css"></style> -->
