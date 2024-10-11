<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="hallDialog" width="800">
      <AssetsIconClose left="790" @click="close" />
      <template v-slot:activator="{ on, attrs }">
        <div v-bind="attrs" v-on="on">
          <v-icon color="blue" small> mdi-cash </v-icon>
          Convert to INV
        </div>
      </template>
      <v-card v-if="item && item.id">
        <AssetsHeadDialog>
          <template #label>Convert to INV</template>
          <template #search
            ><SearchCustomer @foundCustomer="handleFoundCustomer"
          /></template>
        </AssetsHeadDialog>
        <v-card-text>
          <QuotationCustomerInfo
            v-if="item.customer && item.customer.id"
            :defaultCustomer="item.customer"
            :key="customerCompKey"
            @selectedCustomer="handleSelectedCustomer"
          />
          <br />
          <v-textarea
            label="description"
            dense
            outlined
            hide-details
            rows="3"
            v-model="description"
          ></v-textarea>
          <br />
          <AssetsTable
            :headers="require(`@/json/quotation_hall_headers.json`)"
            :items="priceListTableView"
          >
            <template #unit_price="{ item }">
              {{ $utils.currency_format(item.unit_price) }}
            </template>
            <template #total="{ item }">
              {{ $utils.currency_format(item.total_price) }}
            </template>
            <template #action="{ item }">
              <v-icon @click="deleteItem(item)" small color="red"
                >mdi-close</v-icon
              >
            </template>
          </AssetsTable>
          <div>
            <HallDialogForQuotation @tableData="handleTableData" />
            &nbsp;
            <QuotationHallItem
              @selectedItem="
                (e) => {
                  priceListTableView.push(e);
                }
              "
            />
          </div>

          <v-row>
            <v-col cols="9"></v-col>
            <v-col>
              <table style="width: 100%">
                <tr>
                  <td colspan="9"></td>
                  <td class="border-bottom">
                    <span>Sub Total:</span>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <span>{{ $utils.convert_decimal(subTotal()) }}</span>
                  </td>
                </tr>
                <tr>
                  <td colspan="9"></td>

                  <td class="border-bottom">
                    <span>Add:</span>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        <span>
                          {{
                            $utils.convert_decimal(room.room_extra_amount || 0)
                          }}</span
                        >
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
                  </td>
                </tr>
                <tr>
                  <td colspan="9"></td>
                  <td class="border-bottom">
                    <span>Discount:</span>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        <span>
                          -{{
                            $utils.convert_decimal(room.room_discount || 0)
                          }}</span
                        >
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
                  </td>
                </tr>
                <tr>
                  <td colspan="9"></td>
                  <td border-bottom class="primary--text">
                    <span>Total:</span>
                  </td>
                  <td
                    colspan="10"
                    class="text-right pb-2 border-bottomprimary--text"
                  >
                    <span>
                      {{ $utils.currency_format(processCalculation()) }}</span
                    >
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>

          <v-row class="text-right">
            <v-col>
              <AssetsButtonCancel @close="close" />
              &nbsp;
              <AssetsButtonSubmit @click="submit" />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="advanceDialog" width="600">
      <v-card>
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
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
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["model", "endpoint", "item"],
  components: {},
  data() {
    return {
      hallDialog: false,
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
      priceListTableView: [
        {
          description: "Enter item description",
          qty: 1,
          unit_price: 0,
          total_price: 0,
        },
      ],
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
      description: null,
      useOldDates: true,
    };
  },
  async created() {
    this.priceListTableView = this.item.items;
    this.room.room_discount = this.item.discount;
    this.room.sub_total = this.item.sub_total;
    this.description = this.item.description;

    this.runAllFunctions();
  },
  methods: {
    handleTableData(payload) {
      this.useOldDates = false;
      let isSelect = this.selectedRooms.find(
        (e) => e.room_id == payload.room_id
      );

      if (!isSelect) {
        this.selectedRooms.push(payload);

        this.priceListTableView.push({
          description: `${payload.room_type} (${payload.total_booking_hours} Hours)`,
          room_type: `${payload.room_type}`,
          total_booking_hours: payload.total_booking_hours,
          qty: 1,
          unit_price: payload.price,
          total_price: 1 * payload.price,
          function_name: payload.function_name,
        });
      }
    },
    calculateItemTotal(item) {
      item.total_price = item.qty * item.unit_price;
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
      this.hallDialog = false;
    },
    deleteItem(item) {
      const index = this.priceListTableView.findIndex((e) => e.id == item.id);
      this.priceListTableView.splice(index, 1);
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
        (total, num) => total + num.total_price,
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

      let arrival_date = this.formatDate(new Date());
      let departure_date = this.formatDate(new Date());

      if (!this.useOldDates) {
        arrival_date = this.selectedRooms[0].check_in;
        departure_date =
          this.selectedRooms[this.selectedRooms.length - 1].check_out;
      } else {
        arrival_date = this.item.arrival_date;
        departure_date = this.item.departure_date;
      }

      let quotation = {
        book_date: this.formatDate(new Date()),
        arrival_date: arrival_date,
        departure_date: departure_date, // Last room's check_out date
        sub_total: this.subTotal(),
        discount: this.room.room_discount,
        tax: 0,
        total: this.processCalculation(),
        customer: this.customer,
        items: this.priceListTableView,
        description: this.description,
        invoice_type: "hall",
        company_id: this.$auth.user.company_id,
        quotation_id: this.item.id,
      };

      this.$axios
        .post(this.endpoint, quotation)
        .then(({ data }) => {
          this.loading = false;
          this.$swal("Success!", "Converted into Invoice", "success");
          this.$emit("response");
          this.hallDialog = false;
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
