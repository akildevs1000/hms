<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="dialog" width="1000">
      <template v-slot:activator="{ on, attrs }">
        <div style="text-align: center">
          <v-btn
            dense
            x-small
            v-bind="attrs"
            v-on="on"
            class="text-center"
            title="Individual"
            color="#34444c"
            style="width: 37px; height: 26px"
          >
            <v-icon color="white">mdi-account</v-icon>
            <span v-if="!onlyButton"> Individual</span>
          </v-btn>
          <div v-if="onlyButton" style="font-size: 10px; text-align: center">
            Indiv
          </div>
        </div>
      </template>
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Individual Booking Information</span>
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
              <v-card flat class="mt-5">
                <v-card-text>
                  <BookingCustomerInfo
                    :defaultCustomer="customer"
                    :key="customerCompKey"
                    @selectedCustomer="handleSelectedCustomer"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>

            <v-tab-item class="mt-5">
              <table cellspacing="0" style="width: 100%">
                <TableHeader
                  :cols="[
                    `Date`,
                    `Room`,
                    `Tariff`,
                    `Adult`,
                    `Child`,
                    `Meal`,
                    `E. Bed`,
                    `E. C/in`,
                    `L. C/out`,
                    `Total`,
                    ``,
                  ]"
                />
                <tbody v-if="priceListTableView.length > 0">
                  <tr v-for="(item, index) in priceListTableView" :key="index">
                    <td class="text-center py-2">
                      {{ $dateFormat.dmy(item.date) }} <br />
                      {{ item.day }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.room_no }} <br />
                      {{ item.room_type }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.day_type }}
                    </td>
                    <td class="text-center py-2">{{ item.no_of_adult }}</td>
                    <td class="text-center py-2">{{ item.no_of_child }}</td>
                    <td class="text-center py-2">{{ item.meal_name }}</td>
                    <td class="text-center py-2">
                      {{ item.extra_bed_qty || "-" }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.early_check_in > 0 ? "Yes" : "-" }}
                    </td>
                    <td class="text-center py-2">
                      {{ item.late_check_out > 0 ? "Yes" : "-" }}
                    </td>
                    <td class="text-right py-2">
                      {{ $utils.convert_decimal(item.total_price) }}
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
                              <RoomDetails
                                :key="roomDetailsCompKey"
                                :room_type="item.room_type"
                                :selectedRooms="selectedRooms"
                              />
                            </v-list-item>
                            <!-- <v-list-item>
                              <RoomEditDialog
                                :key="roomDetailsCompKey"
                                label="Edit"
                                :options="item"
                                @tableData="handleTableData"
                              />
                            </v-list-item> -->
                            <v-list-item @click="deleteItem(index, item)">
                              <v-icon small color="red">mdi-close</v-icon
                              ><small class="ml-2">Delete</small>
                            </v-list-item>
                          </v-list>
                        </v-card>
                      </v-menu>
                    </td>
                  </tr>
                </tbody>
              </table>

              <div
                class="d-flex justify-space-around py-3"
                style="margin-top: 5px"
              >
                <v-col cols="10" class="text-right">
                  <div class="my-1">Sub Total:</div>
                  <div class="my-1">Add:</div>
                  <div class="my-1">Discount:</div>
                  <div class="my-1"><b>Total:</b></div>
                </v-col>
                <v-col cols="2" class="text-right">
                  <div class="my-1">
                    {{ $utils.convert_decimal(subTotal()) }}
                  </div>
                  <div class="my-1">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        {{
                          $utils.convert_decimal(room.room_extra_amount || 0)
                        }}
                        <v-icon
                          v-if="hover"
                          small
                          color="primary"
                          @click="
                            $refs[`ExtraAmountComp`][`extraAmountPopUp`] = true
                          "
                          >mdi-pencil</v-icon
                        >
                        <ExtraAmount
                          ref="ExtraAmountComp"
                          :sub_total="room.sub_total"
                          @extraAddedAmount="
                            (e) => {
                              room.room_extra_amount = e;
                            }
                          "
                        />
                      </div>
                    </v-hover>
                  </div>
                  <div class="my-1 red--text">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        -{{ $utils.convert_decimal(room.room_discount || 0) }}
                        <v-icon
                          v-if="hover"
                          small
                          color="primary"
                          @click="$refs[`DiscountComp`][`discountPopUp`] = true"
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
                  <div class="my-1">
                    <b>{{ $utils.convert_decimal(processCalculation()) }}</b>
                  </div>
                </v-col>
              </div>

              <v-row class="text-right mb-3">
                <v-col>
                  <RoomDialog label="Room" @tableData="handleTableData" />
                  <v-btn
                    small
                    class="blue"
                    @click="store"
                    :loading="subLoad"
                    dark
                    ><v-icon small class="mt-1">mdi-floppy</v-icon> Confirm
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
  </div>
  <NoAccess v-else />
</template>
<script>
import History from "../../components/customer/History.vue";
import RoomDetails from "../RoomDetails.vue";
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
      dialog: false,
      loading: false,
      activeTab: 0,
      searchDialog: false,
      RoomDrawer: null,
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
  methods: {
    handleFoundCustomer(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
      this.customerCompKey += 1;
    },
    handleSelectedCustomer({ customer, booking }) {
      this.customer = customer;
      this.room = {
        ...this.room,
        ...booking,
      };
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
      this.room = {
        ...this.room,
        ...payload,
        total_days: payload.days,
      };
      // room_type
      let isSelect = this.selectedRooms.find(
        (e) => e.room_id == payload.room_id
      );

      if (!isSelect) {
        this.selectedRooms.push(payload);
        this.priceListTableView = this.priceListTableView.concat(arrToMerge);
        this.roomDetailsCompKey += 1;
      }
    },
    deleteItem(index, item) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms = this.selectedRooms.filter(
        (e) => e.room_type !== item.room_type
      );
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
        (total, num) => total + num.total_price,
        0
      ));
    },

    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    store() {
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
      };

      this.subLoad = false;

      this.$axios
        .post("/group-booking", payload)
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
