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
            <SearchCustomer
              @foundCustomer="handleFoundCustomer"
              v-if="activeTab == 0"
            />
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
                  <BookingCustomerInfo
                    :defaultCustomer="customer"
                    :key="customerCompKey"
                    @selectedCustomer="handleSelectedCustomer"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>

            <v-tab-item class="pt-5">
              <table cellspacing="0" style="width: 100%">
                <thead style="background-color: #f2f2f2; width: 100%">
                  <tr style="background-color: #f2f2f2; width: 100%">
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Date</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Hall</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Type</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Tariff</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Adult</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Child</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Meal</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Price</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Extras</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Total</small>
                    </td>
                    <td
                      style="
                        border-top: 1px solid #bdbdbd;
                        border-bottom: 1px solid #bdbdbd;
                      "
                      class="text-center py-2"
                    >
                      <small>Action</small>
                    </td>
                  </tr>
                </thead>
                <tbody v-if="priceListTableView.length > 0">
                  <tr v-for="(item, index) in priceListTableView" :key="index">
                    <td class="text-center py-2">
                      {{ formatDate(item.date) }} <br />
                      {{ item.day }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.room_type }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.day_type }}
                    </td>

                    <td class="text-center py-2">
                      {{ item.room_price }}
                    </td>
                    <td class="text-center py-2">{{ item.no_of_adult }}</td>
                    <td class="text-center py-2">{{ item.no_of_child }}</td>
                    <td class="text-center py-2">{{ item.meal_name }}</td>
                    <td class="text-center py-2">
                      {{ convert_decimal(item.price) }}
                    </td>
                    <td class="text-center py-2">
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

                    <td class="text-right py-2">
                      {{ convert_decimal(item.total_price) }}
                    </td>
                    <td class="text-center">
                      <v-menu
                        nudge-bottom="50"
                        nudge-left="20"
                        transition="scale-transition"
                        origin="center center"
                        bottom
                        left
                        min-width="90"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn icon v-bind="attrs" v-on="on">
                            <v-icon small>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </template>

                        <v-card>
                          <v-list>
                            <v-list-item>
                              <HallDetails :selectedRooms="selectedRooms" />
                            </v-list-item>
                            <v-list-item @click="deleteItem(index, item)">
                              <v-icon
                                small
                                color="red"
                                @click="deleteItem(index)"
                                >mdi-close</v-icon
                              ><small class="ml-2">Delete</small>
                            </v-list-item>
                          </v-list>
                        </v-card>
                      </v-menu>
                    </td>
                  </tr>

                  <!-- <tr
                        v-for="(item, index) in priceListTableView"
                        :key="index"
                      >
                      
                  
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
                      </tr> -->
                </tbody>
              </table>

              <v-row>
                <v-col md="12" style="padding-top: 0px; font-weight: bold">
                  <div
                    class="d-flex justify-space-around py-3"
                    style="margin-top: 5px"
                  >
                    <v-col cols="10" class="text-right">
                      <div>Sub Total:</div>
                      <div>Discount:</div>
                      <div style="font-size: 18px; font-weight: bold">
                        Total:
                      </div>
                    </v-col>
                    <v-col cols="2" class="text-right">
                      <div>
                        {{ convert_decimal(subTotal()) }}
                      </div>
                      <div style="color: red">
                        <v-hover v-slot:default="{ hover, props }">
                          <div v-bind="props">
                            -{{ convert_decimal(room.room_discount || 0) }}
                            <v-icon
                              v-if="hover"
                              small
                              color="primary"
                              @click="
                                $refs[`DiscountComp`][`discountPopUp`] = true
                              "
                              >mdi-pencil</v-icon
                            >
                            <Discount
                              ref="DiscountComp"
                              :sub_total="room.sub_total"
                              @discountAbleAmount="
                                (e) => {
                                  room.room_discount = e;
                                }
                              "
                            />
                          </div>
                        </v-hover>
                      </div>
                      <div style="font-size: 18px; font-weight: bold">
                        {{ convert_decimal(processCalculation()) }}
                      </div>
                    </v-col>
                  </div>
                </v-col>
                <v-col md="12" class="text-right">
                  <HallDialog label="Add Hall" @tableData="handleTableData"
                /></v-col>
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
    <v-dialog v-model="searchDialog" width="450">
      <v-card>
        <v-card-title>
          Customer <v-spacer></v-spacer>
          <v-icon @click="searchDialog = false" color="primary"
            >mdi-close</v-icon
          >
        </v-card-title>

        <v-card-text>
          <v-row class="mt-2">
            <v-col cols="9">
              <v-text-field
                label="Search By Mobile Number"
                dense
                outlined
                type="text"
                v-model="search.mobile"
                :hide-details="true"
              ></v-text-field>
            </v-col>

            <v-col cols="3">
              <v-btn
                color="primary"
                @click="get_customer"
                :loading="checkLoader"
              >
                Search
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
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
      dialog: false,
      loading: false,
      advanceDialog: false,
      activeTab: 0,
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
      loading: false,
      search: { mobile: "" },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
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
      isDiff: false,
      customerCompKey: 1,
      room: {
        booking_status: 1,
        discount: 0,
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        after_discount: 0,
        sales_tax: 0,
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        company_id: this.$auth.user.company.id,
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
      customer: {},
      errors: [],
      extraPayType: "",
    };
  },
  async created() {
    this.get_reservation();
    this.runAllFunctions();
  },
  methods: {
    formatDate(date) {
      let dateObj = new Date(date);

      let day = dateObj.getDate().toString().padStart(2, "0");
      let month = dateObj
        .toLocaleString("en-GB", { month: "short" })
        .slice(0, 3);
      let year = dateObj.getFullYear();

      return `${day} ${month} ${year}`;
    },
    handleFoundCustomer(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };

      console.log("🚀 ~ handleFoundCustomer ~ this.customer:", this.customer);

      this.customerCompKey += 1;
    },
    handleSelectedCustomer({ customer, room_type }) {
      this.customer = customer;
      this.room.type = room_type;
      this.activeTab += 1;
    },
    close() {
      this.customerCompKey += 1;
      this.customer = {};
      this.room = {
        booking_status: 1,
        discount: 0,
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        after_discount: 0,
        sales_tax: 0,
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        company_id: this.$auth.user.company.id,
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
      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }

      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.$swal("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
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
            this.$emit(`success`);
            this.dialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
