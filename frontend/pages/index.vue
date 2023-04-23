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
            <check-in :BookingData="checkData" @close-dialog="closeCheckInAndOpenGRC"></check-in>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="GRCDialog" persistent :width="900" class="checkin-models">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="GRCDialog = false">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-card-text>
            <Grc :bookingId="checkData.id">
            </Grc>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- <v-btn color="success" @click="GRCDialog = true">text</v-btn> -->

      <!-- end check in dialog -->

      <v-dialog v-model="ExpectCheckInReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Expect Checkin Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="ExpectCheckInReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <ExpectCheckInReport :data="expectCheckIn" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="ExpectCheckOutReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Expect Checkout Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="ExpectCheckOutReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <ExpectCheckOutReport :data="expectCheckOut" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="BookedRoomReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Booked Room Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="BookedRoomReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <BookedRoomsReport :data="reservedWithoutAdvance" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="PaidRoomReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Paid Room Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="PaidRoomReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <PaidRoomsReport :data="confirmedBookingList" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="CheckInReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Checkin Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="CheckInReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <CheckInRoomsReport :data="checkIn" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="CheckOutReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Checkout Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="CheckOutReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <CheckOutRoomsReport :data="checkOut" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="DirtyRoomsReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Dirty Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="DirtyRoomsReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <DirtyRoomsReport :data="dirtyRoomsList" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="AvailableRoomsReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Expect Checkout Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="AvailableRoomsReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <AvailableRoomsReport :data="availableRooms" @close-dialog="closeDialogs" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="FoodDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Food Order Rooms List</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="FoodDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

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
            <v-btn class="primary" small :loading="cancelLoad" @click="cancelItem">
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
            <v-icon dark class="pa-0" @click="closeNewCheckin">mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text v-if="NewBooking">
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
            <!-- {{ newBookingRoom.status }} -->
            <v-list-item link v-if="newBookingRoom.status == 0" @click="NewBooking = true">
              <v-list-item-title>CheckIn</v-list-item-title>
            </v-list-item>
            <v-list-item link v-if="newBookingRoom.status == 0" @click="roomStatus('1')">
              <v-list-item-title>Block</v-list-item-title>
            </v-list-item>
            <v-list-item link v-if="newBookingRoom.status == 1" @click="roomStatus('0')">
              <v-list-item-title>Unblock</v-list-item-title>
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
                <div class="card card-hover mx-0">
                  <a @click="FoodDialog = true" class="box text-center ipad-font-food-grid"
                    style="padding: 7px 0; margin: 0px -5px;background-color: #6722B9;">
                    <div class="d-flex justify-space-around py-0 my-0">
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="mdi mdi-human-male-female-child fx-1 food-icon-size"></i>
                        <h5>
                          {{ members.adult }} | {{ members.child }} |
                          {{ members.baby }}
                        </h5>
                        <h6>Guest</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-coffee fx-1 food-icon-size"></i>
                        <h5>
                          {{ onlyBreakfast.adult }} |
                          {{ onlyBreakfast.child }} |
                          {{ onlyBreakfast.baby }}
                        </h5>
                        <h6>B/fast</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-concierge-bell food-icon-size"></i>
                        <h5>
                          {{ onlyLunch.adult }} | {{ onlyLunch.child }} |
                          {{ onlyLunch.baby }}
                        </h5>
                        <h6>Lunch</h6>
                      </h1>
                      <h1 class="font-light text-white py-0 my-0">
                        <i class="fas fa-hamburger food-icon-size"></i>
                        <h5>
                          {{ onlyDinner.adult }} | {{ onlyDinner.child }} |
                          {{ onlyDinner.baby }}
                        </h5>
                        <h6>Dinner</h6>
                      </h1>
                    </div>
                  </a>
                </div>
              </div>
              <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="AvailableRoomsReportDialog = true">
                  <v-row class="box text-center" style="background-color: #32a15c">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <Available />
                        <h4 class="text-white pb-0 mb-0 mt-4 text-left dash-font-size">
                          Available
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="big-screen laptop-font-grid ipad-font-qty-grid">
                        <!-- (availableRooms && availableRooms.length) -->
                        {{ get_availableRooms(availableRooms) || 0 }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="BookedRoomReportDialog = true">
                  <v-row class="box text-center" style="background-color: #ffbe00">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <Booked />
                        <h4 class="text-white pb-0 mb-0 mt-4 text-left dash-font-size">
                          Reserved
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="big-screen laptop-font-grid ipad-font-qty-grid">
                        {{
                          // (notAvailableRooms && notAvailableRooms.length) || 0
                          (reservedWithoutAdvance &&
                            reservedWithoutAdvance.length) ||
                          0
                        }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="PaidRoomReportDialog = true">
                  <v-row class="box text-center" style="background-color: #02ada4">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <PaidBookedSvg />
                        <h4
                          class="pb-0 mb-0 text-left dash-font-size ipad-font-grid ipad-font-paid-grid laptop-font-paid-grid">
                          Booked
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="big-screen laptop-font-grid ipad-font-qty-grid" style="font-size: 65px">
                        {{ confirmedBooking || 0 }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-2 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="DirtyRoomsReportDialog = true">
                  <v-row class="box text-center" style="background-color: #ff0000">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <Dirty />
                        <h4 class="text-white pb-0 mb-0 mt-3 text-left dash-font-size">
                          Dirty
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="big-screen laptop-font-grid ipad-font-qty-grid" style="font-size: 65px">
                        {{ dirtyRooms || 0 }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-3 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="ExpectCheckInReportDialog = true">
                  <v-row class="box text-center" style="background-color: #18069e">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <ExpectCheckInSvg />
                        <h4 class="text-white pb-0 mb-0 mt-2 text-left dash-font-size">
                          Expect C/In
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="text-white" style="font-size: 65px">
                        {{ expectCheckIn.length || 0 }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-3 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="ExpectCheckOutReportDialog = true">
                  <v-row class="box text-center" style="background-color: #4390fc">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <ExpectCheckOutSvg />
                        <h4 class="text-white pb-0 mb-0 mt-2 text-left dash-font-size">
                          Expect C/Out
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="text-white" style="font-size: 65px">
                        {{ expectCheckOut.length || 0 }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-3 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="CheckInReportDialog = true">
                  <v-row class="box text-center" style="background-color: #800000">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <CheckInSvg />
                        <h4 class="text-white pb-0 mb-0 mt-5 dash-font-size text-left">
                          Check In
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="text-white" style="font-size: 65px">
                        {{ checkIn.length || "0" }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
              <div class="col-md-6 col-lg-3 col-xlg-3 py-0">
                <a class="card card-hover mx-1" @click="CheckOutReportDialog = true">
                  <v-row class="box text-center" style="background-color: #74166d">
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="font-light text-white">
                        <CheckOutSvg />
                        <h4 class="text-white pb-0 mb-0 mt-5 dash-font-size text-left">
                          Check Out
                        </h4>
                      </h1>
                    </v-col>
                    <v-col md="6" class="p-0 m-0">
                      <h1 class="text-white" style="font-size: 65px">
                        {{ checkOut.length }}
                      </h1>
                    </v-col>
                  </v-row>
                </a>
              </div>
            </div>
          </div>
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
                      <v-col :class="noAvailableRoom.id" lg="1" md="4" sm="12" cols="12" class="available-room-list"
                        v-for="(noAvailableRoom, i) in notAvailableRooms" :key="i">
                        <v-card @contextmenu="show" @touchstart="
                          touchstart(
                            $event,
                            noAvailableRoom &&
                            noAvailableRoom.booked_room &&
                            noAvailableRoom.booked_room.id,
                            noAvailableRoom &&
                            noAvailableRoom.booked_room &&
                            noAvailableRoom.booked_room.booking
                              .booking_status
                          )
                        " :elevation="0" @mouseover="
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
                      <v-col :class="room.id" lg="1" md="4" sm="12" cols="12" class="available-room-list"
                        v-for="(room, index) in availableRooms" :key="index">
                        <v-card @contextmenu="makeNewBooking($event, room)" @mouseover="mouseOverForAvailable(room)"
                          @touchstart="makeNewBookingForTouch($event, room)" :elevation="0" class="ma-0 px-md-1 py-md-2"
                          :style="
                            room.status == 1
                              ? 'background-color: #D60078'
                              : 'background-color: #32a15c'
                          " dark>
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
import Available from "../components/svg/Available.vue";
import Dirty from "../components/svg/Dirty.vue";
import Booked from "../components/svg/Booked.vue";
import CheckOutSvg from "../components/svg/CheckOutSvg.vue";
import PaidBookedSvg from "../components/svg/PaidBookedSvg.vue";
import ExpectCheckInSvg from "../components/svg/ExpectCheckInSvg.vue";
import ExpectCheckOutSvg from "../components/svg/ExpectCheckOutSvg.vue";
import CheckInSvg from "../components/svg/CheckInSvg.vue";
import FoodOrderRooms from "../components/food/FoodOrderRooms.vue";
import ExpectCheckInReport from "../components/summary_reports/ExpectCheckInReport.vue";
import ExpectCheckOutReport from "../components/summary_reports/ExpectCheckOutReport.vue";
import AvailableRoomsReport from "../components/summary_reports/AvailableRoomsReport.vue";
import CheckInRoomsReport from "../components/summary_reports/CheckInRoomsReport.vue";
import BookedRoomsReport from "../components/summary_reports/BookedRoomsReport.vue";
import PaidRoomsReport from "../components/summary_reports/PaidRoomsReport.vue";
import CheckOutRoomsReport from "../components/summary_reports/CheckOutRoomsReport.vue";
import DirtyRoomsReport from "../components/summary_reports/DirtyRoomsReport.vue";
import Grc from '../components/booking/GRC.vue';
export default {
  components: {
    Grc,
    DirtyRoomsReport,
    CheckOutRoomsReport,
    PaidRoomsReport,
    BookedRoomsReport,
    CheckInRoomsReport,
    ExpectCheckOutReport,
    ExpectCheckInReport,
    FoodOrderRooms,
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

      DirtyRoomsReportDialog: false,
      PaidRoomReportDialog: false,
      BookedRoomReportDialog: false,
      CheckInReportDialog: false,
      CheckOutReportDialog: false,
      AvailableRoomsReportDialog: false,
      ExpectCheckOutReportDialog: false,
      ExpectCheckInReportDialog: false,
      FoodDialog: false,
      checkInDialog: false,
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
      dirtyRoomsList: [],
      confirmedBookingList: [],
      notAvailableRooms: [],
      availableRooms: [],
      checkIn: [],
      checkOut: [],
      reservedWithoutAdvance: [],
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
      lastTapTime: null,
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
  mounted() { },
  methods: {
    handleTouchStart(event) {
      if (event.touches.length > 1) {
        // if the user is touching with more than one finger, ignore the event
        return;
      }

      // capture the target element that was touched
      const touch = event.touches[0];
      const target = document.elementFromPoint(touch.clientX, touch.clientY);

      // prevent the default context menu behavior for this element
      target.addEventListener("contextmenu", (event) => {
        event.preventDefault();
      });
    },

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
      console.log("closeNewCheckin");
      this.newBookingRoom = false;
      this.NewBooking = false;
    },

    get_availableRooms(rooms) {
      let numberOfBlockedRooms = rooms.filter((e) => e.status == 1).length;
      console.log(numberOfBlockedRooms);
      return this.availableRooms.length - numberOfBlockedRooms;
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
          // check_in: new Date().toJSON().slice(0, 10),
          check_in: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
            .toISOString()
            .substr(0, 10),
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
        this.confirmedBookingList = data.confirmedBookingList;
        this.dirtyRoomsList = data.dirtyRoomsList;
        this.reservedWithoutAdvance = data.reservedWithoutAdvance;

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

    closeCheckInAndOpenGRC() {
      this.room_list();
      this.checkInDialog = false;
      this.GRCDialog = true;
    },
  },
};
</script>
<style src="@/assets/custom/check.css"></style>
<style scoped src="@/assets/custom/dash.css"></style>
<!-- <style scoped src="@/assets/dashtem.css"></style> -->
<style>
.dash-font-size {
  font-size: 13px;
}

.big-screen {
  font-size: 65px;
  color: white;
}

.food-icon-size {
  font-size: 30px !important;
}

@media only screen and (min-width: 1025px) and (max-width: 1199px) {

  /* Adjust layout for iPad pro landscape mode */
  .ipad-font-grid {
    font-size: 12px !important;
    color: white;
    margin-top: 10px !important;
  }
}

@media only screen and (min-width: 1366px) and (max-width: 1366px) and (min-height: 768px) and (max-height: 768px) {
  .laptop-font-grid {
    font-size: 60px !important;
    color: white !important;
    margin-top: 7px !important;
  }

  .laptop-font-paid-grid {
    font-size: 11px !important;
    color: white;
    margin-top: 17px !important;
    font-weight: bold;
  }

  .available-room-list {
    width: 13.333333% !important;
  }
}

@media only screen and (min-width: 1024px) and (max-width: 1024px) and (min-height: 768px) and (max-height: 768px) {

  /* ipad mini Air */
  .ipad-font-qty-grid {
    font-size: 55px !important;
    color: white;
  }

  .ipad-font-paid-grid {
    font-size: 11px !important;
    color: white;
    margin-top: 17px !important;
    font-weight: bold;
  }

  .ipad-font-food-grid {
    color: red !important;
  }

  .available-room-list {
    width: 13.333333% !important;
  }
}
</style>
