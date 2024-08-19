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
        v-model="checkInDialog"
        persistent
        :width="1366"
        class="checkin-models"
      >
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="close">mdi mdi-close-box</v-icon>
          </v-toolbar>
          <v-card-text>
            <check-in
              :key="checkInKey"
              :BookingData="checkData"
              @close-dialog="closeCheckInAndOpenGRC"
            ></check-in>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

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
              >mdi mdi-close-box</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <Grc :bookingId="checkData.id"> </Grc>
          </v-card-text>
          <v-container></v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="BookedRoomReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Reserved Room Report (Unpaid)</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="BookedRoomReportDialog = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <!-- <FoodOrderRooms @close-dialog="closeDialogs"> </FoodOrderRooms> -->
              <BookedRoomsReport
                :data="reservedWithoutAdvance"
                @close-dialog="closeDialogs"
              />
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
              <PaidRoomsReport
                :data="confirmedBookingList"
                @close-dialog="closeDialogs"
              />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="ArrivalReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar color="primary" dense flat dark>
            <small>Arrival</small>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="ArrivalReportDialog = false">
              mdi-close
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-tabs right dense>
                <v-tab>Pending</v-tab>
                <v-tab>Arrival</v-tab>
                <v-tab-item>
                  <ExpectCheckInReport
                    :data="expectCheckIn"
                    @close-dialog="closeDialogs"
                  />
                </v-tab-item>
                <v-tab-item>
                  <CheckInRoomsReport
                    :data="Occupied"
                    @close-dialog="closeDialogs"
                  />
                </v-tab-item>
              </v-tabs>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="CheckOutReportDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar color="primary" dense flat dark>
            <span>Checkout</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="CheckOutReportDialog = false">
              mdi-close
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <v-tabs right dense>
                <v-tab>Pending</v-tab>
                <v-tab>Arrival</v-tab>
                <v-tab-item>
                  <ExpectCheckOutReport
                    :data="expectCheckOut"
                    @close-dialog="closeDialogs"
                  />
                </v-tab-item>
                <v-tab-item>
                  <CheckOutRoomsReport
                    :data="checkOut"
                    @close-dialog="closeDialogs"
                  />
                </v-tab-item>
              </v-tabs>
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
              <DirtyRoomsReport
                :data="dirtyRoomsList"
                @close-dialog="closeDialogs"
              />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog
        v-model="AvailableRoomsReportDialog"
        persistent
        max-width="700px"
      >
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Available Rooms Report</span>
            <v-spacer></v-spacer>
            <v-icon
              dark
              class="pa-0"
              @click="AvailableRoomsReportDialog = false"
            >
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <AvailableRoomsReport
                :data="availableRooms"
                @close-dialog="closeDialogs"
              />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-dialog v-model="FoodDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="primary" dense flat dark>
            <span>Food Orders</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="FoodDialog = false">
              mdi mdi-close
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
              <Posting
                :BookingData="checkData"
                :evenIid="evenIid"
                @close-dialog="closeDialogs"
              >
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
                  <th v-for="(item, index) in headers" :key="index + 30">
                    <span v-html="item.text"></span>
                  </th>
                </tr>
                <v-progress-linear
                  v-if="false"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr v-for="(item, index) in postings" :key="index + 40">
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
            <v-icon dark class="pa-0" @click="payingAdvance = false"
              >mdi mdi-close-box</v-icon
            >
          </v-toolbar>
          <v-card-text>
            <PayAdvance
              :BookingData="checkData"
              @close-dialog="closeDialogs"
            ></PayAdvance>
          </v-card-text>
        </v-card>
      </v-dialog>
      <!-- end pay advance  -->

      <!-- check out  -->
      <v-dialog v-model="checkOutDialog" persistent max-width="1000px">
        <v-card>
          <v-toolbar class="rounded-md" color="primary" dense flat dark>
            <span>{{ formTitle }}</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="closeCheckOut">
              mdi-close
            </v-icon>
          </v-toolbar>
          <v-card-text>
            <check-out
              :BookingData="checkData"
              :roomData="roomData"
              @close-dialog="closeDialogs"
            />
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
            <v-textarea
              placeholder="Reason"
              rows="3"
              dense
              outlined
              v-model="reason"
            ></v-textarea>
          </v-container>
          <v-card-actions>
            <v-btn
              class="primary"
              small
              :loading="cancelLoad"
              @click="cancelItem"
            >
              Yes
            </v-btn>
            <v-btn class="error" small @click="cancelDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="cancelCheckInDialog" persistent max-width="500">
        <v-card>
          <v-card-title class="text-h6">
            Are you sure you want to cancel CheckIn?
          </v-card-title>
          <v-container grid-list-xs>
            <v-textarea
              placeholder="Reason"
              rows="3"
              dense
              outlined
              v-model="checkInCancelReason"
            ></v-textarea>
          </v-container>
          <v-card-actions>
            <v-btn
              class="primary"
              small
              :loading="cancelLoad"
              @click="changeCheckInAdminProcess()"
            >
              Yes
            </v-btn>
            <v-btn class="error" small @click="cancelCheckInDialog = false">
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
            <v-icon dark class="pa-0" @click="closeNewCheckin"
              >mdi mdi-close-box
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
      <v-menu
        v-model="showMenu"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
      >
        <v-list>
          <v-list-item-group v-model="selectedItem">
            <v-list-item>
              <v-list-item-title style="color: green"
                >Room: {{ rightClickRoomId }}</v-list-item-title
              >
            </v-list-item>

            <template v-if="bookingStatus == 1">
              <v-list-item link @click="checkInDialog = true">
                <v-list-item-title>Check In</v-list-item-title>
              </v-list-item>
              <v-list-item link @click="cancelDialog = true">
                <v-list-item-title>Cancel Room</v-list-item-title>
              </v-list-item>
              <v-list-item link @click="payingAdvance = true">
                <v-list-item-title>Pay Advance </v-list-item-title>
              </v-list-item>
            </template>

            <template v-else-if="bookingStatus == 2">
              <v-list-item link @click="get_check_out">
                <v-list-item-title>Check Out</v-list-item-title>
              </v-list-item>
              <v-list-item link @click="postingDialog = true">
                <v-list-item-title>Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewPostingDialog = true">
                <v-list-item-title>View Posting</v-list-item-title>
              </v-list-item>

              <v-list-item link @click="viewBillingDialog">
                <v-list-item-title>View Billing</v-list-item-title>
              </v-list-item>
              <v-list-item
                link
                @click="cancelCheckInDialog = true"
                v-if="$auth?.user?.role?.name.toLowerCase() == 'admin'"
              >
                <v-list-item-title color="red"
                  >Cancel Check-in(admin)
                </v-list-item-title>
              </v-list-item>
            </template>

            <template v-else-if="bookingStatus == 3">
              <v-list-item link @click="setAvailable">
                <v-list-item-title>Make Available</v-list-item-title>
              </v-list-item>
              <!-- <v-list-item link @click="setMaintenance">
                  <v-list-item-title>Make Maintenance</v-list-item-title>
                </v-list-item> -->
            </template>
          </v-list-item-group>
        </v-list>
      </v-menu>
      <v-menu
        v-model="showMenuForNewBooking"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
      >
        <v-list>
          <v-list-item-group>
            <!-- {{ newBookingRoom.status }}  NewBooking=true -->
            <!-- <v-list-item link v-if="newBookingRoom.status == 0" @click="goToBookingPage();">
                  <v-list-item-title>CheckIn</v-list-item-title>
                </v-list-item>-->
            <v-list-item
              link
              v-if="newBookingRoom.status == 0"
              @click="NewBooking = true"
            >
              <v-list-item-title>CheckIn</v-list-item-title>
            </v-list-item>
            <v-list-item
              link
              v-if="newBookingRoom.status == 0"
              @click="roomStatus('1')"
            >
              <v-list-item-title>Block</v-list-item-title>
            </v-list-item>
            <v-list-item
              link
              v-if="newBookingRoom.status == 1"
              @click="roomStatus('0')"
            >
              <v-list-item-title>Unblock</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>
    <v-row>
      <!-- <v-row
      v-if="tabFilter == 'All' || tabFilter == 'occupied'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Occupied ({{ filteredRooms(Occupied).length }})</div>
      </v-col> -->

      <div
        v-if="filteredRooms(Occupied).length == 0 && tabFilter == 'occupied'"
      >
        Not Available
      </div>

      <div
        v-if="tabFilter == 'All' || tabFilter == 'occupied'"
        cols="1"
        class="roombox1"
        v-for="(occupied, index) in filteredRooms(Occupied)"
        :key="index + 50"
      >
        <v-card
          @mouseenter="showMenu = false"
          @mousedown="showMenu = false"
          @mouseup="showMenu = false"
          @contextmenu="show"
          @touchstart="
            touchstart(
              $event,
              occupied && occupied.booked_room && occupied.booked_room.id,
              occupied &&
                occupied.booked_room &&
                occupied.booked_room.booking &&
                occupied.booked_room.booking.booking_status
            )
          "
          :elevation="0"
          @mouseover="
            mouseOver(
              occupied && occupied.booked_room && occupied.booked_room.id,
              occupied &&
                occupied.booked_room &&
                occupied.booked_room.booking &&
                occupied.booked_room.booking.booking_status
            )
          "
          @dblclick="dblclick"
          :class="` darken-2`"
          dark
        >
          <v-card-text
            class="p-3 roombox"
            style="padding: 0px; background-color: #db0000"
            title="Occupied"
          >
            <div class="text-center white--text boxheight boxheight">
              <v-icon
                :color="
                  occupied.device && occupied.latest_status == 1 ? 'red' : ''
                "
                >mdi-bed</v-icon
              >
              <div>{{ occupied?.room_no || "---" }}</div>
              <div>
                {{ occupied ? caps(occupied.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row>
    <v-row
      v-if="tabFilter == 'expected_checkout' || tabFilter == 'All'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Expected Checkout({{ filteredRooms(expectCheckOut).length }})</div>
      </v-col> -->
      <div
        v-if="
          filteredRooms(expectCheckOut).length == 0 &&
          tabFilter == 'expected_checkout'
        "
      >
        Not Available
      </div>
      <div
        v-if="tabFilter == 'expected_checkout' || tabFilter == 'All'"
        cols="1"
        class="roombox1"
        v-for="(noAvailableRoom, index) in filteredRooms(expectCheckOut)"
        :key="index + 60"
      >
        <v-card
          @mouseenter="showMenu = false"
          @mousedown="showMenu = false"
          @mouseup="showMenu = false"
          @contextmenu="show"
          @touchstart="handleTouchstart($event, noAvailableRoom)"
          @mouseover="handleMouseOver(noAvailableRoom)"
          @dblclick="dblclick"
          :class="` darken-2`"
          dark
        >
          <v-card-text
            class="orange p-3 roombox"
            style="padding: 0px"
            title="Expected Checkout"
          >
            <div class="text-center white--text boxheight">
              <v-icon
                :color="
                  noAvailableRoom.device &&
                  noAvailableRoom.device.latest_status == 1
                    ? 'red'
                    : ''
                "
                >mdi-bed</v-icon
              >
              <div>
                {{ noAvailableRoom?.room_no || "---" }}
              </div>
              <div>
                {{
                  noAvailableRoom ? caps(noAvailableRoom.room_type.name) : "---"
                }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row>
   
      <v-row
      v-if="tabFilter == 'expected_arrival' || tabFilter == 'All'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Expected Arrival ({{ filteredRooms(expectCheckIn).length }})</div>
      </v-col> -->
      <div
        v-if="
          filteredRooms(expectCheckIn).length == 0 &&
          tabFilter == 'expected_arrival'
        "
      >
        Not Available
      </div>
      <div
        v-if="tabFilter == 'expected_arrival' || tabFilter == 'All'"
        cols="1"
        class="roombox1"
        v-for="(expCheckIn, index) in filteredRooms(expectCheckIn)"
        :key="index + 70"
      >
        <v-card
          dark
          @mouseenter="showMenu = false"
          @mousedown="showMenu = false"
          @mouseup="showMenu = false"
          @contextmenu="show"
          @touchstart="handleTouchstart($event, expCheckIn)"
          @mouseover="handleMouseOver(expCheckIn)"
          @dblclick="dblclick"
          class=""
        >
          <v-card-text
            class="success p-3 roombox"
            style="padding: 0px"
            title="Expected Arrival"
          >
            <div class="text-center white--text boxheight">
              <v-icon
                :color="
                  expCheckIn.device && expCheckIn.device.latest_status == 1
                    ? 'red'
                    : ''
                "
                >mdi-bed</v-icon
              >
              <div>{{ expCheckIn?.room_no || "---" }}</div>
              <div>
                {{ expCheckIn ? caps(expCheckIn.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row>
    <v-row
      v-if="tabFilter == 'expected_arrival' || tabFilter == 'All'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Reserved ({{ filteredRooms(reservedWithoutAdvance).length }})</div>
      </v-col> -->
      <div
        v-if="
          filteredRooms(reservedWithoutAdvance).length == 0 &&
          tabFilter == 'expected_arrival'
        "
      >
        Not Available
      </div>
      <div
        v-if="tabFilter == 'expected_arrival' || tabFilter == 'All'"
        class="roombox1"
        :class="noAvailableRoom.id"
        v-for="(noAvailableRoom, i) in filteredRooms(reservedWithoutAdvance)"
      >
        <v-card
          @mouseenter="showMenu = false"
          @mousedown="showMenu = false"
          @mouseup="showMenu = false"
          @contextmenu="show"
          @touchstart="handleTouchstart($event, noAvailableRoom)"
          @mouseover="handleMouseOver(noAvailableRoom)"
          @dblclick="dblclick"
          :class="` `"
          dark
        >
          <v-card-text
            class="blue p-3 roombox"
            style="padding: 0px"
            title="Expected Arrival"
          >
            <div class="text-center white--text boxheight">
              <!-- <v-icon
                v-if="isDeviceStatusActive(noAvailableRoom)"
                dark
                class="pa-0 room-inperson-status"
              >
                mdi mdi-bed
              </v-icon> -->
              <v-icon
                :color="
                  noAvailableRoom.device &&
                  noAvailableRoom.device.latest_status == 1
                    ? 'red'
                    : ''
                "
              >
                mdi mdi-bed
              </v-icon>
              <div>
                {{ noAvailableRoom?.room_no || "---" }}
              </div>
              <div>
                {{
                  noAvailableRoom ? caps(noAvailableRoom.room_type.name) : "---"
                }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row>
    <v-row
      v-if="tabFilter == 'available' || tabFilter == 'All'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Available ({{ filteredRooms(availableRooms).length }})</div>
      </v-col> -->
      <div
        v-if="
          filteredRooms(availableRooms).length == 0 && tabFilter == 'available'
        "
      >
        Not Available
      </div>
      <div
        class="roombox1"
        v-if="tabFilter == 'available' || tabFilter == 'All'"
        v-for="(room, index) in filteredRooms(availableRooms)"
      >
        <v-card
          :class="` darken-2 `"
          dark
          @contextmenu="makeNewBooking($event, room)"
          @mouseover="mouseOverForAvailable(room)"
          @touchstart="makeNewBookingForTouch($event, room)"
        >
          <v-card-text
            class="green p-3 roombox"
            style="padding: 0px"
            :title="
              room.device && room.device.latest_status == 1
                ? 'Available and Light On'
                : 'Available'
            "
          >
            <div class="text-center white--text boxheight">
              <v-icon
                :color="
                  room.device && room.device.latest_status == 1 ? 'red' : ''
                "
              >
                mdi mdi-bed
              </v-icon>
              <div>{{ room?.room_no || "---" }}</div>
              <div>
                {{ room ? caps(room.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <div
        v-if="filteredRooms(blockedRooms).length == 0 && tabFilter == 'blocked'"
      >
        Not Available
      </div>
      <div
        class="roombox1"
        v-if="tabFilter == 'blocked' || tabFilter == 'All'"
        v-for="(blockedRoom, index) in filteredRooms(blockedRooms)"
      >
        <v-card
          :class="` darken-2 `"
          dark
          @contextmenu="makeNewBooking($event, blockedRoom)"
          @mouseover="mouseOverForAvailable(blockedRoom)"
          @touchstart="makeNewBookingForTouch($event, blockedRoom)"
        >
          <v-card-text
            class="purple p-3 roombox"
            style="padding: 0px"
            title=" Blocked
            "
          >
            <div class="text-center white--text boxheight">
              <v-icon
                :color="
                  blockedRoom.device && blockedRoom.device.latest_status == 1
                    ? 'red'
                    : ''
                "
              >
                mdi mdi-bed
              </v-icon>
              <div>{{ blockedRoom?.room_no || "---" }}</div>
              <div>
                {{ blockedRoom ? caps(blockedRoom.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row>
    <v-row
      v-if="tabFilter == 'blocked' || tabFilter == 'All'"
      no-gutters
      class="mt-0"
    >
      <v-col cols="12">
        <div>Blocked ({{ filteredRooms(blockedRooms).length }})</div>
      </v-col> -->
      <!-- <div
        v-if="filteredRooms(blockedRooms).length == 0 && tabFilter == 'blocked'"
      >
        Not Available
      </div>
      <div
        v-if="tabFilter == 'blocked' || tabFilter == 'All'"
        class="roombox1"
        v-for="(blockedRoom, index) in filteredRooms(blockedRooms)"
      >
        <v-card
          :class="` darken-2`"
          dark
          @contextmenu="makeNewBooking($event, blockedRoom)"
          @mouseover="mouseOverForAvailable(blockedRoom)"
          @touchstart="makeNewBookingForTouch($event, blockedRoom)"
        >
          <v-card-text
            class="purple p-3 roombox"
            style="padding: opx"
            title="Blocked"
          >
            <div class="text-center white--text roombox">
              <v-icon
                :color="
                  blockedRoom.device && blockedRoom.device.latest_status == 1
                    ? 'red'
                    : ''
                "
              >
                mdi mdi-bed
              </v-icon>
              <div>{{ blockedRoom?.room_no || "---" }}</div>
              <div>
                {{ room ? caps(blockedRoom.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div> -->
      <!--</v-row>

    <v-row
      v-if="tabFilter == 'All' || tabFilter == 'dirty'"
      no-gutters
      class="mt-0"
    > -->
      <!-- <v-col cols="12">
        <div>Dirty ({{ filteredRooms(Occupied).length }})</div>
      </v-col> -->
      <div v-if="filteredRooms(Occupied).length == 0 && tabFilter == 'dirty'">
        Not Available
      </div>
      <div
        v-if="tabFilter == 'All' || tabFilter == 'dirty'"
        class="roombox1"
        v-for="(occupied, index) in filteredRooms(Occupied)"
      >
        <v-card
          @mouseenter="showMenu = false"
          @mousedown="showMenu = false"
          @mouseup="showMenu = false"
          @contextmenu="show"
          @touchstart="
            touchstart(
              $event,
              occupied && occupied.booked_room && occupied.booked_room.id,
              occupied &&
                occupied.booked_room &&
                occupied.booked_room.booking &&
                occupied.booked_room.booking.booking_status
            )
          "
          :elevation="0"
          @mouseover="
            mouseOver(
              occupied && occupied.booked_room && occupied.booked_room.id,
              occupied &&
                occupied.booked_room &&
                occupied.booked_room.booking &&
                occupied.booked_room.booking.booking_status
            )
          "
          @dblclick="dblclick"
          :class="` darken-2`"
          dark
        >
          <v-card-text
            class="roombox p-3 roombox"
            style="padding: 0px; background-color: #db0000"
            title="Dirty"
          >
            <div class="text-center white--text boxheight">
              <v-icon
                :color="
                  occupied.device && occupied.device.latest_status == 1
                    ? 'red'
                    : ''
                "
                >mdi-bed</v-icon
              >
              <div>{{ occupied?.room_no || "---" }}</div>
              <div>
                {{ room ? caps(occupied.room_type.name) : "---" }}
              </div>
            </div>
          </v-card-text>
        </v-card>
      </div>
      <!-- </v-row> -->
    </v-row>
  </div>
  <Preloader v-else />
</template>
<script>
import Posting from "../booking/Posting.vue";
import PayAdvance from "../booking/PayAdvance.vue";
import CheckIn from "../booking/CheckIn.vue";
import CheckOut from "../booking/CheckOut.vue";
import NewCheckIn from "../booking/NewCheckIn.vue";
import ReservationList from "../reservation/ReservationList.vue";
import Available from "../svg/Available.vue";
import Dirty from "../svg/Dirty.vue";
import Booked from "../svg/Booked.vue";
import CheckOutSvg from "../svg/CheckOutSvg.vue";
import PaidBookedSvg from "../svg/PaidBookedSvg.vue";
import ExpectCheckInSvg from "../svg/ExpectCheckInSvg.vue";
import ExpectCheckOutSvg from "../svg/ExpectCheckOutSvg.vue";
import CheckInSvg from "../svg/CheckInSvg.vue";
// import FoodOrderRooms from "../food/FoodOrderRooms.vue";
import ExpectCheckInReport from "../summary_reports/ExpectCheckInReport.vue";
import ExpectCheckOutReport from "../summary_reports/ExpectCheckOutReport.vue";
import AvailableRoomsReport from "../summary_reports/AvailableRoomsReport.vue";
import CheckInRoomsReport from "../summary_reports/CheckInRoomsReport.vue";
import BookedRoomsReport from "../summary_reports/BookedRoomsReport.vue";
import PaidRoomsReport from "../summary_reports/PaidRoomsReport.vue";
import CheckOutRoomsReport from "../summary_reports/CheckOutRoomsReport.vue";
import DirtyRoomsReport from "../summary_reports/DirtyRoomsReport.vue";
import Grc from "../booking/GRC.vue";

export default {
  props: ["searchQuery", "tabFilter", "data"],
  layout({ $auth }) {
    if ($auth.user.user_type != "company" && $auth.user.is_verified == 0) {
      return "guest";
    } else {
      return "default";
    }
  },

  components: {
    Grc,
    DirtyRoomsReport,
    CheckOutRoomsReport,
    PaidRoomsReport,
    BookedRoomsReport,
    CheckInRoomsReport,
    ExpectCheckOutReport,
    ExpectCheckInReport,
    // FoodOrderRooms,
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
      room: "",
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

      DirtyRoomsReportDialog: false,
      PaidRoomReportDialog: false,
      BookedRoomReportDialog: false,
      ArrivalReportDialog: false,
      CheckOutReportDialog: false,
      AvailableRoomsReportDialog: false,
      ExpectCheckOutReportDialog: false,
      ExpectCheckInReportDialog: false,
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
      blockedRooms: [],
      Occupied: [],
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
      roomData: null,
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
      foodplan: null,
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

      showMenu: false,

      filtered: {
        AvailableRooms: [],
      },

      //searchQuery: null,
      //tabFilter: ``,
    };
  },
  watch: {
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
    this.room_list();
    this.first_login_auth = this.$auth.user.first_login;

    setInterval(() => {
      this.room_list();
      this.key = this.key + 1;
    }, 1000 * 60 * 2);

    this.get_food_plan();
  },

  methods: {
    handleReload() {
      this.room_list();
    },
    getRoomsByFilter() {},
    filteredRooms(rooms) {
      if (!this.searchQuery) return rooms; // Early return for empty search

      // Optimized search function for multiple customer fields
      const searchCustomer = (customer, searchQuery) => {
        const searchFields = [
          "first_name",
          "last_name",
          "contact_no",
          "whatsapp",
          "email",
          "nationality",
          "address",
          "full_name",
        ];

        const searchText = searchQuery.toLowerCase();
        return searchFields.some((field) =>
          customer[field]?.toLowerCase().includes(searchText)
        );
      };

      return rooms.filter((room) => {
        const { booked_room: { customer } = {} } = room; // Destructuring with default value for customer

        // Combine room number and customer search, using toLowerCase() for case-insensitive search
        return (
          room.room_no.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (customer && searchCustomer(customer, this.searchQuery))
        );
      });
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
      console.log("this.evenIid", this.evenIid);
      if (this.evenIid == false) return false;
      this.selected_booked_room_id = this.evenIid;

      let payload = {
        params: {
          id: this.evenIid,
          company_id: this.$auth.user.company.id,
        },
      };
      this.rightClickRoomId = "---";
      this.$axios.get(`get_booked_room`, payload).then(({ data }) => {
        this.checkData = data.booking;
        this.roomData = data;
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

    get_remaining(val) {
      let total = this.checkData.remaining_price;
      let advance_price = val;
    },

    get_food_plan() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`foodplan-count`, payload).then(({ data }) => {
        this.foodplan = data;
      });
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
      if (this.data) {
        let data = this.data;
        this.rooms = data;

        this.dirtyRooms = data.dirtyRooms;
        this.availableRooms = data.availableRooms;
        this.notAvailableRooms = data.notAvailableRooms;
        this.blockedRooms = data.blockedRooms;

        this.confirmedBooking = data.confirmedBooking;
        this.waitingBooking = data.waitingBooking;
        this.expectCheckIn = data.expectCheckIn;
        this.expectCheckOut = data.expectCheckOut;
        this.Occupied = data.checkIn;
        this.checkOut = data.checkOut;
        this.confirmedBookingList = data.confirmedBookingList;
        this.dirtyRoomsList = data.dirtyRoomsList;
        this.reservedWithoutAdvance = data.reservedWithoutAdvance;

        this.members = {
          ...data.members,
        };
        this.isIndex = true;
        setTimeout(() => {
          this.isPageLoad = true;
        }, 100);
        return false;
      } else {
        let payload = {
          params: {
            company_id: this.$auth.user && this.$auth.user.company.id,
            // check_in: new Date().toJSON().slice(0, 10),
            check_in: new Date(
              Date.now() - new Date().getTimezoneOffset() * 60000
            )
              .toISOString()
              .substr(0, 10),
          },
        };
        this.$axios.get(`room_list_grid`, payload).then(({ data }) => {
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return false;
          }

          this.rooms = data;

          this.dirtyRooms = data.dirtyRooms;
          this.availableRooms = data.availableRooms;
          this.notAvailableRooms = data.notAvailableRooms;
          this.blockedRooms = data.blockedRooms;

          this.confirmedBooking = data.confirmedBooking;
          this.waitingBooking = data.waitingBooking;
          this.expectCheckIn = data.expectCheckIn;
          this.expectCheckOut = data.expectCheckOut;
          this.Occupied = data.checkIn;
          this.checkOut = data.checkOut;
          this.confirmedBookingList = data.confirmedBookingList;
          this.dirtyRoomsList = data.dirtyRoomsList;
          this.reservedWithoutAdvance = data.reservedWithoutAdvance;

          this.members = {
            ...data.members,
          };
          this.isIndex = true;
          setTimeout(() => {
            this.isPageLoad = true;
          }, 100);
        });
      }
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

    closeCheckInAndOpenGRC() {
      this.room_list();
      this.checkInDialog = false;
      // this.GRCDialog = true;
    },
  },
};
</script>
