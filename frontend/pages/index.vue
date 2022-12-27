<template>
  <div>
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
      <!-- check in dialog -->
      <v-dialog v-model="checkInDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <table>
                <v-progress-linear
                  v-if="false"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr>
                  <th>Customer Name</th>
                  <td style="width: 300px">
                    {{ checkData && checkData.title }}
                  </td>
                </tr>
                <tr>
                  <th>Room No</th>
                  <td>
                    {{ checkData.room_no }}
                  </td>
                </tr>
                <tr>
                  <th>Room Type</th>
                  <td>
                    {{ checkData.room_type }}
                  </td>
                </tr>
                <tr>
                  <th>Check In</th>
                  <td>
                    {{ checkData && checkData.check_in }}
                  </td>
                </tr>
                <tr>
                  <th>Check Out</th>
                  <td>
                    {{ checkData && checkData.check_out }}
                  </td>
                </tr>
                <tr>
                  <th>
                    Payment Mode
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-select
                      v-model="checkData.payment_mode_id"
                      :items="[
                        { id: 1, name: 'Cash' },
                        { id: 2, name: 'Card' },
                        { id: 3, name: 'Online' },
                        { id: 4, name: 'Bank' },
                        { id: 5, name: 'UPI' },
                        { id: 6, name: 'Cheque' }
                      ]"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      :hide-details="true"
                      :height="1"
                    ></v-select>
                  </td>
                </tr>
                <tr>
                  <th>Total Amount (Rs.)</th>
                  <td>{{ checkData && checkData.total_price }}.00</td>
                </tr>
                <tr></tr>
                <tr>
                  <th>Advance Payed (Rs.)</th>
                  <td>{{ checkData.advance_price }}.00</td>
                </tr>
                <tr></tr>
                <tr>
                  <th>Remaining Balance (Rs.)</th>
                  <td>{{ checkData.remaining_price }}.00</td>
                </tr>
                <tr style="background-color: white">
                  <th>New Payment</th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="new_payment"
                      :hide-details="true"
                      @keyup="get_remaining(new_payment)"
                    ></v-text-field>
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Document
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <div v-if="checkData.document">
                      <v-btn
                        small
                        dark
                        class="primary pt-4 pb-4"
                        @click="preview(checkData.document)"
                      >
                        Preview
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                    </div>
                    <v-file-input
                      v-else
                      v-model="document"
                      color="primary"
                      counter
                      placeholder="Select your files"
                      prepend-icon="mdi-paperclip"
                      outlined
                      :show-size="1000"
                    >
                      <template v-slot:selection="{ index, text }">
                        <v-chip
                          v-if="index < 2"
                          color="primary"
                          dark
                          label
                          small
                        >
                          {{ text }}
                        </v-chip>

                        <span
                          v-else-if="index === 2"
                          class="text-overline grey--text text--darken-3 mx-2"
                        >
                          +{{ document.length - 2 }} File(s)
                        </span>
                      </template>
                    </v-file-input>
                  </td>
                </tr>
                <tr></tr>
              </table>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-btn
              class="primary"
              small
              @click="store_check_in(checkData)"
              :loading="false"
              >Save</v-btn
            >
            <v-btn class="error" small @click="close"> Cancel </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end check in dialog -->

      <!-- posting dialog -->
      <v-dialog v-model="postingDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <br />
              <table>
                <v-progress-linear
                  v-if="false"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr>
                  <th>Bill No</th>
                  <td style="width: 300px">
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="posting.bill_no"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <th>Customer Name</th>
                  <td style="width: 300px">
                    {{ checkData && checkData.title }}
                  </td>
                </tr>
                <tr>
                  <th>Room No</th>
                  <td>
                    {{ checkData.room_no }}
                  </td>
                </tr>
                <tr>
                  <th>Room Type</th>
                  <td>
                    {{ checkData.room_type }}
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Item
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="text"
                      v-model="posting.item"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    QTY
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="posting.qty"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Amount
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="posting.amount"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Type
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-select
                      v-model="posting.tax_type"
                      :items="[
                        { id: -1, name: 'select..' },
                        { name: 'Food' },
                        { name: 'Mess' },
                        { name: 'Bed' }
                      ]"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      :hide-details="true"
                      :height="1"
                      @change="get_amount_with_tax(posting.tax_type)"
                    ></v-select>
                  </td>
                </tr>
                <tr>
                  <th>
                    Payment Mode
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-select
                      v-model="posting.payment_mode_id"
                      :items="[
                        { id: 1, name: 'Cash' },
                        { id: 2, name: 'Card' },
                        { id: 3, name: 'Online' },
                        { id: 4, name: 'Bank' },
                        { id: 5, name: 'UPI' },
                        { id: 6, name: 'Cheque' }
                      ]"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      :hide-details="true"
                      :height="1"
                    ></v-select>
                  </td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Amount With Tax
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    {{ posting.amount_with_tax }}
                  </td>
                </tr>
                <tr></tr>
              </table>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-btn class="primary" small @click="store_posting" :loading="false"
              >submit</v-btn
            >
            <v-btn class="error" small @click="postingDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
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
                <v-progress-linear
                  v-if="false"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
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
          </v-toolbar>
          <v-card-text>
            <v-container>
              <table>
                <v-progress-linear
                  v-if="false"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr>
                  <th>Customer Name</th>
                  <td style="width: 300px">
                    {{ checkData && checkData.title }}
                  </td>
                </tr>
                <tr>
                  <th>Room No</th>
                  <td>
                    {{ checkData.room_no }}
                  </td>
                </tr>
                <tr>
                  <th>Room Type</th>
                  <td>
                    {{ checkData.room_type }}
                  </td>
                </tr>
                <tr>
                  <th>Check In</th>
                  <td>
                    {{ checkData && checkData.check_in }}
                  </td>
                </tr>
                <tr>
                  <th>Check Out</th>
                  <td>
                    {{ checkData && checkData.check_out }}
                  </td>
                </tr>
                <tr>
                  <th>
                    Payment Mode
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-select
                      v-model="checkData.payment_mode_id"
                      :items="[
                        { id: 1, name: 'Cash' },
                        { id: 2, name: 'Card' },
                        { id: 3, name: 'Online' },
                        { id: 4, name: 'Bank' },
                        { id: 5, name: 'UPI' },
                        { id: 6, name: 'Cheque' }
                      ]"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      :hide-details="true"
                      :height="1"
                    ></v-select>
                  </td>
                </tr>
                <tr>
                  <th>Total Amount</th>
                  <td>{{ checkData && checkData.total_price }}.00</td>
                </tr>
                <tr>
                  <th>Remaining Balance</th>
                  <td>{{ checkData.remaining_price }}.00</td>
                </tr>

                <tr style="background-color: white">
                  <th>
                    New Advance
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="new_advance"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr></tr>
              </table>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-btn
              class="primary"
              small
              @click="store_advance(checkData)"
              :loading="false"
              >Save</v-btn
            >
            <v-btn class="error" small @click="payingAdvance = false">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <!-- end pay advance  -->

      <!-- check out  -->
      <v-dialog v-model="checkOutDialog" persistent max-width="700px">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>{{ formTitle }}</span>
          </v-toolbar>
          <v-card-text>
            <v-container>
              <table>
                <v-progress-linear
                  v-if="loading"
                  :active="loading"
                  :indeterminate="loading"
                  absolute
                  color="primary"
                ></v-progress-linear>
                <tr>
                  <th>Customer Name</th>
                  <td style="width: 300px">
                    {{ checkData && checkData.title }}
                  </td>
                </tr>
                <tr>
                  <th>Room No</th>
                  <td>
                    {{ checkData.room_no }}
                  </td>
                </tr>
                <tr>
                  <th>Room Type</th>
                  <td>
                    {{ checkData.room_type }}
                  </td>
                </tr>
                <tr>
                  <th>Check In</th>
                  <td>
                    {{ checkData && checkData.check_in }}
                  </td>
                </tr>
                <tr>
                  <th>Check Out</th>
                  <td>
                    {{ checkData && checkData.check_out }}
                  </td>
                </tr>
                <tr>
                  <th>
                    Payment Mode
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-select
                      v-model="checkData.payment_mode_id"
                      :items="[
                        { id: 1, name: 'Cash' },
                        { id: 2, name: 'Card' },
                        { id: 3, name: 'Online' },
                        { id: 4, name: 'Bank' },
                        { id: 5, name: 'UPI' },
                        { id: 6, name: 'Cheque' }
                      ]"
                      item-text="name"
                      item-value="id"
                      dense
                      outlined
                      :hide-details="true"
                      :height="1"
                    ></v-select>
                  </td>
                </tr>
                <tr>
                  <th>Total Amount</th>
                  <td>{{ checkData && checkData.total_price }}.00</td>
                </tr>
                <tr></tr>

                <tr></tr>
                <tr>
                  <th>Remaining Balance</th>
                  <td>{{ checkData.remaining_price }}.00</td>
                </tr>
                <tr style="background-color: white">
                  <th>
                    Full Payment
                    <span class="text-danger">*</span>
                  </th>
                  <td>
                    <v-text-field
                      dense
                      outlined
                      type="number"
                      v-model="checkData.full_payment"
                      :hide-details="true"
                    ></v-text-field>
                  </td>
                </tr>
                <tr></tr>
              </table>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-btn class="primary" small @click="store_check_out">Save</v-btn>
            <v-btn class="error" small @click="checkOutDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
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
            <v-btn class="primary" small :loading="false" @click="cancelItem">
              Yes
            </v-btn>
            <v-btn class="error" small @click="cancelDialog = false">
              Cancel
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- end cancel room  -->
    </div>
    <!--end dialogs -->

    <v-row>
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Available Rooms</h4>
              <span class="data-1">
                {{ (notAvailableRooms && notAvailableRooms.length) || 0 }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 booked">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-closed"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Booked Room</h4>
              <span class="data-1">
                {{ (notAvailableRooms && notAvailableRooms.length) || 0 }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Advance Paid Booking</h4>
              <span class="data-1">
                {{ confirmedBooking || "---" }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 available">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-open"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Only Booking</h4>
              <span class="data-1">
                {{ waitingBooking || "---" }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 checkedIn">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-door-closed"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Expect Check In</h4>
              <span class="data-1">
                {{ expectCheckIn.length || 0 }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-2 col-lg-6 text-uppercase">
        <div class="card px-2 checkedOut">
          <div class="card-statistic-3">
            <div class="card-icon card-icon-large">
              <i class="fas fa-walking"></i>
            </div>
            <div class="card-content">
              <h4 class="card-title text-capitalize">Expect Checked Out</h4>
              <span class="data-1">
                {{ expectCheckOut.length || 0 }}
              </span>
              <p class="mb-0 text-sm">
                <span class="mr-2">
                  <v-icon dark small>mdi-arrow-right</v-icon>
                </span>
                <a class="text-nowrap text-white" target="_blank">
                  <span class="text-nowrap">View Report</span>
                </a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </v-row>

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
            <v-list-item
              v-if="bookingStatus == 1"
              link
              @click="checkInDialog = true"
            >
              <v-list-item-title>Check In</v-list-item-title>
            </v-list-item>

            <v-list-item
              v-else-if="bookingStatus == 2"
              link
              @click="checkOutDialog = true"
            >
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
            <v-list-item
              link
              @click="payingAdvance = true"
              v-if="bookingStatus <= 2"
            >
              <v-list-item-title>Pay Advance</v-list-item-title>
            </v-list-item>

            <v-list-item
              link
              @click="cancelDialog = true"
              v-if="bookingStatus == 1"
            >
              <v-list-item-title>Cancel Room</v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <v-row class="mt-0">
      <v-col md="10" sm="12" cols="12">
        <v-card class="pa-5 mt-0">
          <h6>Rooms</h6>
          <v-row>
            <!-- {{ rooms.notAvailableRooms }} -->
            <v-col
              :class="noAvailableRoom.id"
              lg="1"
              md="4"
              sm="12"
              cols="12"
              v-for="(noAvailableRoom, i) in notAvailableRooms"
              :key="i"
            >
              <v-card
                @contextmenu="show"
                :elevation="0"
                @mouseover="
                  mouseOver(
                    noAvailableRoom.booked_room.id,
                    noAvailableRoom.booked_room.booking.booking_status
                  )
                "
                @dblclick="dblclick"
                class="ma-0 px-md-1 py-md-2"
                dark
                :style="
                  `background-image:${noAvailableRoom.booked_room.background}`
                "
                ><div class="text-center">
                  {{ caps(noAvailableRoom.room_type.name) }}
                </div>
                <div class="text-center">
                  {{ noAvailableRoom.room_no }}
                </div>
              </v-card>
            </v-col>
          </v-row>
          <v-row>
            <v-col
              :class="room.id"
              lg="1"
              md="4"
              sm="12"
              cols="12"
              v-for="(room, index) in availableRooms"
              :key="index"
            >
              <v-card
                :elevation="0"
                class="ma-0 px-md-1 py-md-2"
                :class="getRelaventColor(room.status)"
                dark
                ><div class="text-center">{{ caps(room.room_type.name) }}</div>
                <div class="text-center">
                  {{ room.room_no }}
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
      <v-col md="2" sm="12" cols="12">
        <div class="col-xl-12 col-lg-12 text-uppercase">
          <div class="card px-2 available">
            <div class="card-statistic-3">
              <div class="card-icon card-icon-large">
                <i style="font-size: 78px" class="fas fa-users"></i>
              </div>
              <div class="card-content">
                <h4 class="card-title text-capitalize">Checked In</h4>
                <span class="data-1">
                  <small>Adults : {{ members.adult }}</small
                  ><br />
                  <small>Child : {{ members.child }}</small
                  ><br />
                  <small>Babies : {{ members.baby }}</small>
                </span>
                <p class="mb-0 text-sm">
                  <span class="mr-2">
                    <v-icon dark small>mdi-arrow-right</v-icon>
                  </span>
                  <a class="text-nowrap text-white" target="_blank">
                    <span class="text-nowrap">View Report</span>
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
    <ReservationList />
  </div>
</template>
<script>
import ReservationList from "../components/reservation/ReservationList.vue";
export default {
  components: { ReservationList },
  data() {
    return {
      temp: "",
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

      formTitle: "",
      selectedItem: 0,
      showMenu: false,
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
          value: "company_name"
        },
        {
          text: "Order Total",
          align: "left",
          sortable: false,
          value: "order_total"
        }
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
      confirmedBooking: "",
      waitingBooking: "",
      reason: "",
      new_payment: 0,
      new_advance: 0,
      items: [],
      checkData: {},
      customerId: "",
      bookingId: "",
      document: null,
      posting: {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1
      },
      isDbCLick: false,
      members: {
        adult: 0,
        child: 0,
        baby: 0
      },
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
        { text: "Date" }
      ]
    };
  },
  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.get_data();
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
    }
  },
  created() {
    this.room_list();
    this.first_login_auth = this.$auth.user.first_login;
  },

  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },

    mouseOver(bookedRoomId, bookingStatus) {
      this.evenIid = bookedRoomId;
      this.bookingStatus = bookingStatus;
      console.log(this.evenIid);
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

    get_remaining(val) {
      let total = this.checkData.remaining_price;
      let advance_price = val;
    },

    get_posting() {
      let id = this.evenIid;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`posting/${id}`, payload).then(({ data }) => {
        this.postings = data;
      });
    },

    get_amount_with_tax(clause) {
      let per = clause == "Food" ? 5 : 12;
      let res = this.getPercentage(this.posting.amount, per);
      let gst = parseInt(res) / 2;
      this.posting.sgst = gst;
      this.posting.cgst = gst;
      this.posting.tax = res;
      this.posting.amount_with_tax =
        parseInt(res) + parseInt(this.posting.amount);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          check_in: new Date().toJSON().slice(0, 10)
        }
      };
      this.$axios.get(`room_list_grid`, payload).then(({ data }) => {
        this.rooms = data;

        this.notAvailableRooms = data.notAvailableRooms;
        this.availableRooms = data.availableRooms;
        this.confirmedBooking = data.confirmedBooking;
        this.waitingBooking = data.waitingBooking;

        this.expectCheckIn = data.expectCheckIn;
        this.expectCheckOut = data.expectCheckOut;

        this.members = {
          ...data.members
        };

        console.log(this.notAvailableRooms.length);
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
      let id = this.customerId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid
        }
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.bookingId = data.id;
        this.checkData.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.customer_id;
        console.log(this.checkData);
        if (this.isDbCLick) {
          this.get_event_by_db_click();
        }
      });
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
        payment_mode_id: data.payment_mode_id
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
        .catch(e => console.log(e));
    },

    store_posting() {
      let rule =
        Object.keys(this.posting.item).length == 0 ||
        Object.keys(this.posting.amount).length == 0 ||
        Object.keys(this.posting.qty).length == 0 ||
        Object.keys(this.posting.bill_no).length == 0 ||
        this.posting.tax_type == -1;

      if (rule) {
        alert("Please enter required fields");
        return;
      }
      this.loading = true;
      let per = this.posting.tax_type == "Food" ? 5 : 12;
      let payload = {
        ...this.posting,
        booked_room_id: this.evenIid,
        company_id: this.$auth.user.company.id,
        booking_id: this.checkData.id,
        room_id: this.checkData.room_id,
        room: this.checkData.room_no,
        tax_type: per
      };

      this.$axios
        .post("/posting", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, true);
          }
        })
        .catch(e => console.log(e));
    },

    store_advance(data) {
      if (this.new_advance == "") {
        alert("Enter advance amount");
        return;
      }
      // this.loading = true;
      let payload = {
        new_advance: this.new_advance,
        booking_id: data.id,
        remaining_price: data.remaining_price,
        payment_mode_id: data.payment_mode_id,
        company_id: this.$auth.user.company.id
      };
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, false, true);
          }
        })
        .catch(e => console.log(e));
    },

    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_available/${this.evenIid}`, payload)
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
        .catch(err => console.log(err));
    },

    setMaintenance() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_maintenance/${this.evenIid}`, payload)
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
        .catch(err => console.log(err));
    },

    cancelItem() {
      if (this.reason == "") {
        alert("Enter reason");
        return;
      }

      let payload = {
        reason: this.reason,
        cancel_by: this.$auth.user.id
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
        .catch(err => console.log(err));
    },

    store_check_out() {
      // if (this.checkData.full_payment == "") {
      //   alert("enter full payment");
      //   return true;
      // }

      this.loading = true;
      let payload = {
        booking_id: this.checkData.id,
        remaining_price: this.checkData.remaining_price,
        full_payment: this.checkData.full_payment,
        payment_mode_id: this.checkData.payment_mode_id
      };
      // return;
      this.$axios
        .post("/check_out_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.succuss(data, false, false, true);
            this.redirect_to_invoice(data.data);
          }
        })
        .catch(e => console.log(e));
    },
    setAvailable() {
      let payload = {
        cancel_by: this.$auth.user.id
      };
      this.$axios
        .post(`set_available/${this.evenIid}`, payload)
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
        .catch(err => console.log(err));
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
      check_in = false,
      posting = false,
      check_out = false,
      advance_payment = false
    ) {
      if (check_in) {
        this.checkData = {};
        this.checkInDialog = false;
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
        this.payingAdvance = false;
      }

      this.room_list();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;

      console.log(this.snackbar);
      console.log(data.message);
    },

    close() {
      this.checkInDialog = false;
    }
  }
};
</script>

<style scoped src="@/assets/dashtem.css"></style>

<style scoped>
.app {
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}

.portrait.v-card {
  margin: 0 auto;
  max-width: 600px;
  width: 100%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #e9e9e9;
}
td,
th {
  text-align: left;
  padding: 8px;
  border: 1px solid #e9e9e9;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}
.fc-license-message {
  display: none !important;
}
.bg-background {
  background-color: #34444c !important;
}

.bg-background th,
td {
  border-top: none !important;
  border-right: none !important;
  border-left: none !important;
}
</style>
