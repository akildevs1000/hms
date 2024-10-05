<template>
  <div>
    <v-dialog persistent v-model="groupBookingDialog" width="1000">
      <AssetsIconClose left="990" @click="close" />
      <template v-slot:activator="{ on, attrs }">
        <div style="text-align: center">
          <v-btn
            dense
            x-small
            v-bind="attrs"
            v-on="on"
            class="text-center"
            title="Group"
            color="#34444c"
            style="width: 37px; height: 26px"
          >
            <v-icon color="white">mdi-account-group</v-icon>
            <span v-if="!onlyButton"> Group</span>
          </v-btn>
          <div v-if="onlyButton" style="font-size: 10px; text-align: center">
            Group
          </div>
        </div>
      </template>
      <v-card>
        <AssetsHeadDialog>
          <template #label>Group Booking Information</template>
          <template #search
            ><SearchCustomer @foundCustomer="handleFoundCustomer"
          /></template>
        </AssetsHeadDialog>
        <v-stepper v-model="e1">
          <v-stepper-header style="display: none">
            <v-stepper-step :complete="e1 > 1" step="1">
              Name of step 1
            </v-stepper-step>

            <v-divider></v-divider>

            <v-stepper-step :complete="e1 > 2" step="2">
              Name of step 2
            </v-stepper-step>
          </v-stepper-header>

          <v-stepper-items>
            <v-stepper-content step="1">
              <BookingCustomerInfo
                :isGroupBooking="true"
                :defaultCustomer="customer"
                :key="customerCompKey"
                @selectedCustomer="handleSelectedCustomer"
              />
            </v-stepper-content>

            <v-stepper-content step="2">
              <AssetsTable
                height="350"
                :headers="[
                  {
                    text: `Date`,
                    value: `date`,
                    align: `center`,
                  },
                  {
                    text: `Room`,
                    value: `room`,
                    align: `center`,
                  },
                  {
                    text: `Tariff`,
                    value: `tariff`,
                    align: `center`,
                  },
                  {
                    text: `Adult`,
                    value: `no_of_adult`,
                    align: `center`,
                  },
                  {
                    text: `Child`,
                    value: `no_of_child`,
                    align: `center`,
                  },
                  {
                    text: `Meal`,
                    value: `meal_name`,
                    align: `center`,
                  },
                  {
                    text: `E. Bed`,
                    value: `extra_bed_qty`,
                    align: `center`,
                  },
                  {
                    text: `E. C/in`,
                    value: `early_check_in`,
                    align: `center`,
                  },
                  {
                    text: `L. C/out`,
                    value: `late_check_out`,
                    align: `center`,
                  },
                  {
                    text: `Total`,
                    value: `total`,
                    align: `right`,
                  },
                  {
                    text: ``,
                    value: `action`,
                    width: `10px`,
                  },
                ]"
                :items="priceListTableView"
              >
                <template #date="{ item }">
                  {{ $dateFormat.dmy(item.date) }} <br />
                  {{ item.day }}
                </template>
                <template #room="{ item }">
                  {{ item.room_no }} <br />
                  {{ item.room_type }}
                </template>
                <template #tariff="{ item }">
                  {{ item.day_type }}
                </template>
                <template #early_check_in="{ item }">
                  {{
                    item.early_check_in || item.early_check_in > 0 ? "Yes" : "-"
                  }}
                </template>
                <template #late_check_out="{ item }">
                  {{ item.late_check_out > 0 ? "Yes" : "-" }}
                </template>
                <template #total="{ item }">
                  {{ $utils.currency_format(item.total_price) }}
                </template>
                <template #row>
                  <RoomDialogForGroup
                    label="Room"
                    @tableData="handleTableData"
                  />
                </template>
                <template #action="{ item }">
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

                    <v-list dense>
                      <v-list-item-group>
                        <v-list-item>
                          <v-list-item-content>
                            <v-list-item-title>
                              <RoomDetails :item="item" :booking="room" />
                            </v-list-item-title>
                          </v-list-item-content>
                        </v-list-item>
                        <v-list-item @click="deleteItem(index, item)">
                          <v-list-item-content>
                            <v-list-item-title
                              ><v-icon x-small color="red">mdi-close</v-icon>
                              <small style="font-size: 11px"
                                >Delete</small
                              ></v-list-item-title
                            >
                          </v-list-item-content>
                        </v-list-item>
                      </v-list-item-group>
                    </v-list>
                  </v-menu>
                </template>
              </AssetsTable>
              <v-row no-gutters>
                <v-col cols="10"></v-col>
                <v-col class="pt-5">
                  <table style="width: 100%">
                    <tr>
                      <td colspan="9"></td>
                      <td class="border-bottom text-color">Sub Total:</td>
                      <td
                        colspan="10"
                        class="text-right pb-2 border-bottom text-color"
                      >
                        {{ $utils.currency_format(subTotal()) }}
                      </td>
                    </tr>
                    <tr>
                      <td colspan="9"></td>

                      <td class="border-bottom text-color">Add:</td>
                      <td
                        colspan="10"
                        class="text-right pb-2 border-bottom text-color"
                      >
                        <v-hover v-slot:default="{ hover, props }">
                          <div v-bind="props">
                            {{
                              $utils.currency_format(
                                room.room_extra_amount || 0
                              )
                            }}
                            <v-icon
                              v-if="hover"
                              small
                              color="primary"
                              @click="
                                $refs[`ExtraAmountComp`][
                                  `extraAmountPopUp`
                                ] = true
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
                      </td>
                    </tr>
                    <tr>
                      <td colspan="9"></td>
                      <td class="border-bottom text-color">Discount:</td>
                      <td
                        colspan="10"
                        class="text-right pb-2 border-bottom text-color"
                      >
                        <v-hover v-slot:default="{ hover, props }">
                          <div v-bind="props">
                            -{{
                              $utils.currency_format(room.room_discount || 0)
                            }}
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
                      </td>
                    </tr>
                    <tr>
                      <td colspan="9"></td>
                      <td class="primary--text">Total:</td>
                      <td colspan="10" class="text-right pb-2 primary--text">
                        {{ $utils.currency_format(processCalculation()) }}
                      </td>
                    </tr>
                  </table>
                </v-col>
              </v-row>
              <v-row class="mt-5">
                <v-col cols="5">
                  <v-hover v-slot:default="{ hover, props }">
                    <span v-bind="props">
                      <v-btn
                        x-small
                        :outlined="!hover"
                        :class="hover ? `white--text` : `primary--text`"
                        rounded
                        color="primary"
                        @click="e1 = 1"
                        ><v-icon small>mdi-chevron-left</v-icon>Back</v-btn
                      >
                    </span>
                  </v-hover>
                </v-col>
                <v-col cols="7">
                  <v-hover v-slot:default="{ hover, props }">
                    <span v-bind="props">
                      <v-btn
                        x-small
                        :outlined="!hover"
                        rounded
                        color="red"
                        class="white--text"
                        @click="RoomDrawer = false"
                        >Cancel</v-btn
                      >
                    </span>
                  </v-hover>
                  &nbsp;
                  <v-hover v-slot:default="{ hover, props }">
                    <span v-bind="props">
                      <v-btn
                        x-small
                        :outlined="!hover"
                        rounded
                        color="green"
                        class="white--text"
                        @click="store"
                        :loading="subLoad"
                        >Submit</v-btn
                      >
                    </span>
                  </v-hover>
                </v-col>
              </v-row>
            </v-stepper-content>
          </v-stepper-items>
        </v-stepper>
      </v-card>
    </v-dialog>
  </div>
  <!-- <NoAccess v-else /> -->
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["onlyButton"],
  components: {},
  data() {
    return {
      e1: 1,
      groupBookingDialog: false,
      loading: false,
      activeTab: 0,
      subLoad: false,
      loading: false,
      selectedRooms: [],
      rooms: [],
      priceListTableView: [],
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
      this.e1 = 2;
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

      this.groupBookingDialog = false;
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
      this.selectedRooms.splice(index, 1);
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

    store() {
      let payload = {
        ...this.room,
        customer_type: this.customer.customer_type,
        selectedRooms: this.selectedRooms,
        ...this.customer,
        user_id: this.$auth.user.id,
        group_name: "yes",
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
            this.groupBookingDialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
