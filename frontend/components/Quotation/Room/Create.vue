<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="dialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="primary"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> New
        </v-btn>
      </template>
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Quotation Information</span>
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
            <v-tab v-if="customer.id > 0">
              <v-icon> mdi mdi-clipboard-text-clock </v-icon>
            </v-tab>
            <v-tabs-slider color="#1259a7"></v-tabs-slider>
            <v-tab-item>
              <v-card flat class="mt-5">
                <v-card-text>
                  <QuotationCustomerInfo
                    :defaultCustomer="customer"
                    :key="customerCompKey"
                    @selectedCustomer="handleSelectedCustomer"
                  />
                </v-card-text>
              </v-card>

              <table class="table" style="width: 100%">
                <thead>
                  <tr>
                    <td class="primary white--text text-center">#</td>
                    <td class="primary white--text">Room Type</td>
                    <td class="primary white--text">Food</td>

                    <td class="primary white--text">Tarrif</td>
                    <td class="primary white--text">PAX</td>
                    <td class="primary white--text">Rooms</td>
                    <td class="primary white--text">Nights</td>
                    <td class="primary white--text text-center">Total</td>
                    <td class="primary"></td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in priceListTableView" :key="index">
                    <template v-if="item.is_custom_line">
                      <td style="width: 50px" class="text-center">
                        {{ index + 1 }}
                      </td>
                      <td style="width: 320px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.room_type"
                        ></v-text-field>
                      </td>
                      <td style="width: 120px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.meal_name"
                        ></v-text-field>
                      </td>

                      <td style="width: 120px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.price"
                          @input="calculateItemTotal(item)"
                        ></v-text-field>
                      </td>

                      <td style="width: 120px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.no_of_adult"
                        ></v-text-field>
                      </td>
                      <td style="width: 120px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.no_of_rooms"
                          @input="calculateItemTotal(item)"
                        ></v-text-field>
                      </td>
                      <td style="width: 120px">
                        <v-text-field
                          outlined
                          dense
                          hide-details
                          v-model="item.no_of_nights"
                          @input="calculateItemTotal(item)"
                        ></v-text-field>
                      </td>

                      <td class="text-center">
                        {{
                          convert_decimal(
                            item.price * item.no_of_rooms * item.no_of_nights
                          )
                        }}
                      </td>
                      <td class="text-center">
                        <v-icon
                          @click="deleteItem(index, item)"
                          small
                          color="red"
                          >mdi-close</v-icon
                        >
                      </td>
                    </template>

                    <template v-else>
                      <td style="width: 50px" class="text-center">
                        {{ index + 1 }}
                      </td>
                      <td style="width: 320px">
                        {{ item.room_type }}
                      </td>
                      <td style="width: 120px">
                        {{ item.meal_name }}
                      </td>

                      <td style="width: 120px">
                        {{ item.price }}
                      </td>

                      <td style="width: 120px">
                        {{ item.no_of_adult }}
                      </td>
                      <td style="width: 120px">{{ item.no_of_rooms }}</td>
                      <td style="width: 120px">
                        {{ item.no_of_nights }}
                      </td>

                      <td class="text-center">
                        {{
                          convert_decimal(
                            item.price * item.no_of_rooms * item.no_of_nights
                          )
                        }}
                      </td>
                      <td class="text-center">
                        <v-icon
                          @click="deleteItem(index, item)"
                          small
                          color="red"
                          >mdi-close</v-icon
                        >
                      </td>
                    </template>
                  </tr>
                </tbody>
              </table>
              <v-dialog v-model="discountDialog" max-width="400" persistent>
                <v-card>
                  <v-card-title>
                    Apply Discount <v-spacer></v-spacer>
                    <v-icon color="primary" @click="discountDialog = false">
                      mdi-close
                    </v-icon>
                  </v-card-title>

                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col cols="8" dense>
                          <v-text-field
                            label="Discount Amount"
                            dense
                            outlined
                            type="number"
                            v-model="room.room_discount"
                            :hide-details="true"
                          ></v-text-field>
                        </v-col>
                        <v-col dense>
                          <v-icon
                            color="primary"
                            @click="
                              () => {
                                processCalculation();
                                discountDialog = false;
                              }
                            "
                            >mdi-floppy</v-icon
                          >
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>
                </v-card>
              </v-dialog>
              <div
                class="d-flex justify-space-around py-3"
                style="margin-top: 5px"
              >
                <v-col cols="10" class="text-right">
                  <div>Sub Total:</div>
                  <!-- <div>Discount:</div> -->
                   <div><Discount /></div>
                  <div style="font-size: 18px; font-weight: bold">Total:</div>
                </v-col>
                <v-col cols="2" class="text-right">
                  <div>
                    {{ convert_decimal(subTotal()) }}
                  </div>
                  <div style="color: red">
                    -{{ convert_decimal(room.room_discount || 0) }}
                  </div>
                  <div style="font-size: 18px; font-weight: bold">
                    {{ convert_decimal(processCalculation()) }}
                  </div>
                </v-col>
              </div>
              <v-row class="mt-3">
                <v-col md="2" sm="12" cols="12" dense>
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
                    v-model="room.room_discount"
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
                    v-model="room.discount_reason"
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
                    v-model="room.room_extra_amount"
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
                    v-model="room.extra_amount_reason"
                    :hide-details="true"
                  ></v-text-field>
                </v-col>
              </v-row>
              <v-row class="text-right mb-3">
                <v-col>
                  <RoomDialogForQuotation
                    label="Room"
                    @tableData="handleTableData"
                  />

                  <v-btn small class="blue" @click="addItem" dark
                    ><v-icon small class="mt-1">mdi-plus</v-icon> Add Row</v-btn
                  >

                  <v-btn
                    small
                    class="primary"
                    @click="submit"
                    :loading="subLoad"
                    dark
                    ><v-icon small class="mt-1">mdi-floppy</v-icon>
                    Submit</v-btn
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
                  : $swal('Warning', 'Select room', 'error');
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
import History from "@/components/customer/History.vue";
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["onlyButton"],
  components: {
    History,
  },
  data() {
    return {
      discountDialog: false,
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
      isDiff: false,
      roomDetailsCompKey: 1,
      customerCompKey: 1,
      room: {
        room_extra_amount: 0,
        room_discount: 0,
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
      customer: {},
      errors: [],
      extraPayType: "",
    };
  },
  async created() {
    this.runAllFunctions();
  },
  methods: {
    handleTableData({ arrToMerge, payload }) {
      let isSelect = this.selectedRooms.find(
        (e) => e.room_id == payload.room_id
      );

      if (!isSelect) {
        this.selectedRooms.push(payload);

        this.priceListTableView = this.mergeEntries(
          this.priceListTableView.concat(arrToMerge),
          payload
        );
        // this.roomDetailsCompKey += 1;
      }
    },
    mergeEntries(entries, payload) {
      const result = [];

      entries.forEach((entry) => {
        const existingEntry = result.find(
          (e) => e.room_type === entry.room_type
        );

        if (existingEntry) {
          existingEntry.total_price += entry.total_price;
        } else {
          result.push({
            ...entry,
            no_of_nights: entries.length,
          });
        }
      });

      return result;
    },
    calculateItemTotal(item) {
      item.total_price = item.no_of_rooms * item.no_of_nights * item.price;
    },
    addItem() {
      this.priceListTableView.push({
        room_type: "",
        meal_name: "",
        price: 0,
        no_of_adult: 0,
        no_of_rooms: 0,
        no_of_nights: 0,
        total_price: 0,
        is_custom_line: true,
      });
    },
    handleFoundCustomer(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
      this.customerCompKey += 1;
    },
    formatDate(date) {
      let dateObj = new Date(date);

      let day = dateObj.getDate().toString().padStart(2, "0");
      let month = dateObj
        .toLocaleString("en-GB", { month: "short" })
        .slice(0, 3);
      let year = dateObj.getFullYear();

      return `${day} ${month} ${year}`;
    },
    handleSelectedCustomer({ customer, booking }) {
      this.customer = customer;
      this.room = {
        ...this.room,
        ...booking,
      };
      // this.activeTab += 1;
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
      this.dialog = false;
    },
    deleteItem(index, item) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms = this.selectedRooms.filter(
        (e) => e.room_type !== item.room_type
      );
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
      let ci = new Date(this.room.check_in);
      let co = new Date(this.room.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      if (days > 0) {
        return (this.room.total_days = days);
      }
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
      let discount = parseFloat(this.room.room_discount) || 0;
      let room_extra_amount = parseFloat(this.room.room_extra_amount) || 0;
      let sub_total = parseFloat(this.room.sub_total) || 0;

      let advance_price = parseFloat(this.room.advance_price) || 0;

      let afterExtraAmount = sub_total + room_extra_amount;
      let afterDiscount = afterExtraAmount - discount;

      this.room.remaining_price = afterDiscount - advance_price;

      return (this.room.total_price = afterDiscount);
    },

    subTotal() {
      return (this.room.sub_total = this.priceListTableView.reduce(
        (total, num) => total + num.price * num.no_of_rooms * num.no_of_nights,
        0
      ));
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
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            this.searchDialog = false;
            this.checkLoader = false;
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

    submit() {
      let customer = this.customer;
      if (!customer.first_name) {
        this.$swal("Warning!", "Customer first name is required", "error");
        return;
      }
      if (!customer.last_name) {
        this.$swal("Warning!", "Customer last name is required", "error");
        return;
      }
      if (!customer.contact_no) {
        this.$swal("Warning!", "Customer contact name is required", "error");
        return;
      }

      if (!this.priceListTableView.length) {
        this.$swal("Error", "Item list cannot be empty", "error");
        return;
      }

      let quotaion = {
        book_date: this.formatDate(new Date()),
        arrival_date: this.formatDate(new Date()),
        departure_date: this.formatDate(new Date()),
        sub_total: this.subTotal(),
        discount: this.room.discount,
        tax: 0,
        total: this.processCalculation(),
        customer: this.customer,
        items: this.priceListTableView,
        company_id: this.$auth.user.company_id,
        type: "room",
      };

      this.subLoad = false;

      this.$axios
        .post("/quotation", quotaion)
        .then(({ data }) => {
          this.loading = false;
          this.$swal("Success!", "Quotation has been created", "success");
          this.$emit("response");
          this.priceListTableView = [];
          this.dialog = false;
        })
        .catch((e) => {
          console.log("🚀 ~ store_booking ~ e:", e.response.data);
          this.$swal("Error", e.response.data, "error");
          this.errors = data.errors;
          this.subLoad = false;
        });
    },
  },
};
</script>
<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
