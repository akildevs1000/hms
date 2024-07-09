<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <!-- <v-dialog v-model="dateConfirmationDialog" persistent max-width="290">
      <template v-slot:activator="{ on, attrs }">
        <v-btn color="primary" dark v-bind="attrs" v-on="on">
          Open Dialog
        </v-btn>
      </template>
      <v-card>
        <v-card-title class="text-h5">
          Use Google's location service?
        </v-card-title>
        <v-card-text
          >Let Google help apps determine location. This means sending anonymous
          location data to Google, even when no apps are running.</v-card-text
        >
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" text @click="dialog = false">
            Disagree
          </v-btn>
          <v-btn color="green darken-1" text @click="dialog = false">
            Agree
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> -->

    <v-dialog v-model="createReservationDialog" max-width="1200px">
      <Reservation :reservation="reservation" />
    </v-dialog>
    <!-- <v-dialog v-model="checkInDialog" persistenta max-width="700px">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>{{ formTitle }}ddd</span>
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
                <td style="width:300px">
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
                  <span class="error--text">*</span>
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
                <td>
                  {{ checkData && checkData.total_price }}
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>
                  Advance Price
                  <span class="error--text">*</span>
                </th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="checkData.advance_price"
                    :hide-details="true"
                    @keyup="get_remaining(checkData.advance_price)"
                  ></v-text-field>
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>Remaining Balance</th>
                <td>
                  {{ checkData.remaining_price }}
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
            :loading="loading"
            >Save</v-btn
          >
          <v-btn class="error" small @click="close"> Cancel </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> -->
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
                <td style="width:300px">
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
                  <span class="error--text">*</span>
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
              <tr style="background-color:white">
                <th>
                  Full Payment
                  <span class="error--text">*</span>
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
              <!-- <tr style="background-color:white">
                <th>
                  Balance
                </th>
                <td>
                  <span>{{ getBalance }}</span>
                </td>
              </tr> -->
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
          <v-btn class="primary" small :loading="loading" @click="cancelItem">
            Yes
          </v-btn>
          <v-btn class="error" small @click="cancelDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="postingDialog" persistent max-width="700px">
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
                <th>Bill No</th>
                <td style="width:300px">
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
                <td style="width:300px">
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
              <tr style="background-color:white">
                <th>
                  Item
                  <span class="error--text">*</span>
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
              <tr style="background-color:white">
                <th>
                  QTY
                  <span class="error--text">*</span>
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
              <tr style="background-color:white">
                <th>
                  Amount
                  <span class="error--text">*</span>
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
              <tr style="background-color:white">
                <th>
                  Type
                  <span class="error--text">*</span>
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
                  <span class="error--text">*</span>
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
              <tr style="background-color:white">
                <th>
                  Amount With Tax
                  <span class="error--text">*</span>
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
          <v-btn class="primary" small @click="store_posting" :loading="loading"
            >submit</v-btn
          >
          <v-btn class="error" small @click="postingDialog = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
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
                v-if="loading"
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
    <v-dialog v-model="checkInDialog" persistent max-width="700px">
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
                <td style="width:300px">
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
                  <span class="error--text">*</span>
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
                <th>
                  Advance Payed (Rs.)
                </th>
                <td>
                  {{ checkData.advance_price }}.00
                  <!-- <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="checkData.advance_price"
                    :hide-details="true"
                    @keyup="get_remaining(checkData.advance_price)"
                  ></v-text-field> -->
                </td>
              </tr>
              <tr></tr>
              <tr>
                <th>Remaining Balance (Rs.)</th>
                <td>{{ checkData.remaining_price }}.00</td>
              </tr>
              <tr style="background-color:white">
                <th>
                  New Payment
                </th>
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
              <tr style="background-color:white">
                <th>
                  Document
                  <span class="error--text">*</span>
                </th>
                <td>
                  <div v-if="checkData.document">
                    <!-- {{ checkData.document }} -->
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
                      <v-chip v-if="index < 2" color="primary" dark label small>
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
            :loading="loading"
            >Save</v-btn
          >
          <v-btn class="error" small @click="close"> Cancel </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="payingAdvance" persistent max-width="700px">
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
                <td style="width:300px">
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
                  <span class="error--text">*</span>
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
              <!-- <tr style="background-color:white">
                <th>
                  Advance Price
                </th>
                <td>
                  {{ checkData.advance_price }}
                </td>
              </tr> -->
              <tr>
                <th>Remaining Balance</th>
                <td>{{ checkData.remaining_price }}.00</td>
              </tr>

              <tr style="background-color:white">
                <th>
                  New Advance
                  <span class="error--text">*</span>
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
            :loading="loading"
            >Save</v-btn
          >
          <v-btn class="error" small @click="payingAdvance = false">
            Cancel
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <div>
      <v-tooltip
        bottom
        color="background"
        :position-x="tx"
        :position-y="ty"
        absolute
        offset-y
        v-model="showTooltip"
      >
        <table style=" border: none!important">
          <tr class="bg-background">
            <th>Customer Name</th>
            <td style="width:300px">
              {{ checkData && checkData.title }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Room No</th>
            <td>
              {{ checkData.room_no }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Room Type</th>
            <td>
              {{ checkData.room_type }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Check In</th>
            <td>
              {{ checkData && checkData.check_in }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Check Out</th>
            <td>
              {{ checkData && checkData.check_out }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Total Amount</th>
            <td>
              {{ checkData && checkData.total_price }}
            </td>
          </tr>
          <tr class="bg-background">
            <th>Remaining Balance</th>
            <td>
              {{ checkData.remaining_price }}
            </td>
          </tr>
          <tr></tr>
        </table>
      </v-tooltip>
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
              <v-list-item-title>Cancel Room </v-list-item-title>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </v-menu>
    </div>

    <v-dialog v-model="LoadingDialog" hide-overlay persistent width="300">
      <v-card color="primary" dark>
        <v-card-text class="py-3">
          Loading...
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row>
      <v-col cols="12">
        <FullCalendar :options="calendarOptions" style="background:#fff;" />
      </v-col>
    </v-row>
  </div>
</template>
<script>
import FullCalendar from "@fullcalendar/vue";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";

export default {
  components: {
    FullCalendar
  },
  data() {
    return {
      dateConfirmationDialog: false,
      LoadingDialog: false,
      loading: false,
      snackbar: false,
      response: "",
      isDirty: true,
      createReservationDialog: false,
      payingAdvance: false,
      checkInDialog: false,
      checkOutDialog: false,
      postingDialog: false,
      viewPostingDialog: false,
      cancelDialog: false,
      formTitle: "",
      selectedItem: 0,
      showMenu: false,
      showTooltip: false,
      tx: 0,
      ty: 0,
      x: 0,
      y: 0,
      calendarOptions: {
        plugins: [interactionPlugin, dayGridPlugin, resourceTimelinePlugin],
        // now: "2022-11-07",
        now: "",
        editable: true,
        aspectRatio: 1.8,
        scrollTime: "00:00",
        displayEventTime: false,
        selectable: true,
        initialView: "resourceTimelineMonth",

        navLinks: true,
        resourceAreaWidth: "12%",

        resourceAreaColumns: [
          {
            headerContent: "Room",
            field: "room_no",
            width: "3%"
          },
          {
            headerContent: "Room Type",
            field: "room_type",
            width: "5%"
          }
        ],
        resources: [
          // { id: "103", room_no: "103", room_type: "king", eventColor: "green" },
          // { id: "104", room_no: "104", eventColor: "orange" }
        ],

        events: [
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "104",
          //   start: "2022-11-17 01:00:00",
          //   end: "2022-11-18 23:00:00",
          //   title: "e",
          //   background: "linear-gradient(135deg, #23bdb8 0, #65a986 100%)",
          //   eventBorderColor: "red"
          // }
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "104",
          //   start: "2022-11-09 00:00:00",
          //   end: "2022-11-11 06:00:00",
          //   title: "e"
          // },
          // {
          //   id: "1",
          //   room_id: "1",
          //   resourceId: "103",
          //   start: "2022-11-09 00:00:00",
          //   end: "2022-11-11 06:00:00",
          //   title: "e"
          // }
        ],

        eventDidMount: arg => {
          const eventId = arg.event.id;
          const bookingStatus = arg.event.extendedProps.status;
          if (arg.event.extendedProps.background) {
            arg.el.style.background = arg.event.extendedProps.background;
          }

          arg.el.addEventListener("contextmenu", jsEvent => {
            this.showTooltip = false;
            this.show(eventId, jsEvent);
            jsEvent.preventDefault();
          });

          // arg.el.addEventListener("mouseover", jsEvent => {
          //   this.get_event_by_mouse_hover(eventId, jsEvent);
          // });

          arg.el.addEventListener("mouseleave", jsEvent => {
            this.showTooltip = false;
          });
        },

        select: (date, jsEvent, view, resourceObj, vv) => {
          let obj = date.resource.extendedProps;
          if (date.startStr < this.currentDate) {
            alert("please select current date or future date");
            return;
          }
          this.create_reservation(date, obj);
        },

        eventResize: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_date_format(arg.event.end),
            roomId: arg.event._def.resourceIds[0]
          };

          this.change_date_by_drag(obj);
        },
        eventDrop: (arg, delta) => {
          let obj = {
            eventId: arg.event.id,
            start: this.convert_date_format(arg.event.start),
            end: this.convert_date_format(arg.event.end),
            roomId: arg.event._def.resourceIds[0]
          };
          this.change_room_by_drag(obj);
        }
      },
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
      ],
      errors: [],
      data: [],
      postings: [],
      checkData: {},
      evenIid: "",
      bookingStatus: "",
      reason: "",
      customerId: "",

      document: null,
      new_payment: 0,
      new_advance: 0,
      reservation: {},
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
      RoomList: [],
      reservation: {
        check_in: null,
        check_out: null,
        room_no: "",
        room_id: "",
        room_type: "",
        price: "",
        origin_price: "",
        room_id: "",
        isCalculate: false
      }
    };
  },

  created() {
    this.currentDate;
  },

  mounted() {
    this.room_list();
    this.get_events();
  },

  watch: {
    checkInDialog() {
      this.formTitle = "Check In";
      this.new_payment = 0;
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
      this.new_advance = 0;
      this.get_data();
    }
  },
  computed: {
    getBalance() {
      let full_pay = this.checkData.full_payment;
      let remainingPrice = this.checkData.remaining_price;
      let balance = full_pay - remainingPrice;
      if (balance >= 0) {
        return balance;
      }
    },

    currentDate() {
      return (this.calendarOptions.now = new Date().toJSON().slice(0, 10));
    }
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

    show(id, jsEvent) {
      this.LoadingDialog = true;
      this.evenIid = id;
      console.log(this.evenIid);
      this.get_data(jsEvent);
    },

    show_context_menu(jsEvent) {
      if (!jsEvent) {
        return;
      }
      this.LoadingDialog = false;
      this.x = jsEvent.clientX;
      this.y = jsEvent.clientY;
      this.$nextTick(() => {
        this.showMenu = true;
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

    get_data(jsEvent = null) {
      let payload = {
        params: {
          id: this.evenIid
        }
      };
      this.$axios.get(`get_booking`, payload).then(({ data }) => {
        this.checkData = data;
        this.checkData.full_payment = "";
        this.bookingStatus = data.booking_status;
        this.customerId = data.customer_id;
        console.log(this.checkData);
        this.show_context_menu(jsEvent);
      });
    },

    get_event_by_mouse_hover(id, jsEvent) {
      console.log(id);
      this.evenIid = id;
      this.tx = jsEvent.clientX;
      this.ty = jsEvent.clientY;
      this.$nextTick(() => {
        this.showTooltip = true;
      });
      this.get_data();
    },

    create_reservation(e, obj) {
      this.get_room_types(e, obj);
    },

    get_room_types(e, obj) {
      this.reservation.isCalculate = true;

      this.reservation.room_id = this.RoomList.find(
        e => e.room_no == obj.room_no
      ).id;

      this.reservation.room_type = obj.room_type;
      this.reservation.room_no = obj.room_no;
      this.reservation.check_in = e.startStr;
      this.reservation.check_out = this.convert_checkout_date_format(e.endStr); //this.convert_date_format(e.end);

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: obj.room_type,
          room_no: obj.room_no
        }
      };
      this.$axios.get(`get_data_by_select`, payload).then(({ data }) => {
        this.reservation.room_id = data.id;
        this.reservation.price = data.room_type.price;

        let commitObj = {
          ...this.reservation
        };
        this.$store.commit("reservation", commitObj);
        this.$router.push(`/hotel/new`);
      });
    },

    viewBillingDialog() {
      let id = this.customerId;
      this.$router.push(`/customer/details/${id}`);
    },

    get_remaining(val) {
      // let total = this.checkData.total_price;
      let total = this.checkData.remaining_price;
      let advance_price = val;
      // this.checkData.remaining_price = total - advance_price;
    },

    room_list() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_list`, payload).then(({ data }) => {
        this.calendarOptions.resources = data;
        this.RoomList = data;
      });
    },

    get_events() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`events_list`, payload).then(({ data }) => {
        this.calendarOptions.events = data;
      });
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

    convert_checkout_date_format(val) {
      const date = new Date(val);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate() - 1).padStart(2, "0");
      return [year, month, day].join("-");
    },

    convert_date_format(val) {
      return new Date(val - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },

    store_check_out() {
      if (this.checkData.full_payment == "") {
        alert("enter full payment");
        return true;
      }

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

    redirect_to_invoice(id) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `http://127.0.0.1:8000/api/invoice/${id}`);
      document.body.appendChild(element);
      element.click();
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

    store_check_in(data) {
      if (
        this.new_payment == "" ||
        this.new_payment == 0 ||
        (data.document ? "" : this.document == null)
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

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.document);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          }
        })
        .catch(e => console.log(e));
    },

    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
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
          this.get_events();
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
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
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
          this.get_events();
          this.cancelDialog = false;
          this.snackbar = data.status;
          this.response = data.message;
        })
        .catch(err => console.log(err));
    },

    change_room_by_drag(obj) {
      this.$axios
        .post("/change_room_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch(e => console.log(e));
    },

    change_date_by_drag(obj) {
      this.$axios
        .post("/change_date_by_drag", obj)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = data.message;
            this.response = data.message;
            this.get_events();
          }
        })
        .catch(e => console.log(e));
    },

    close() {
      this.checkInDialog = false;
    },
    validate_payment() {
      if (
        this.checkData.advance_price == 0 ||
        this.checkData.advance_price == ""
      ) {
        alert("enter advance price ");
        return true;
      }
      if (this.checkData.payment_mode_id == "") {
        alert("select payment mode");
        return true;
      }
      return false;
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

      this.get_events();
      this.errors = [];
      this.loading = false;
      this.snackbar = true;
      this.response = data.message;
    }
  }
};
</script>

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
