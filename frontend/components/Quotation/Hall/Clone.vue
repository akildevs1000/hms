<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="dialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <div v-bind="attrs" v-on="on">
          <v-icon color="blue" small> mdi-content-duplicate </v-icon>
          Clone
        </div>
      </template>
      <v-card v-if="item && item.id">
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Quotation Information</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="close"> mdi-close </v-icon>
        </v-toolbar>
        <v-card-text>
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
                <td class="primary white--text">Description</td>
                <td class="primary white--text text-center">Qty</td>
                <td class="primary white--text text-center">Unit Price</td>
                <td class="primary white--text text-center">Total</td>
                <td class="primary"></td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in priceListTableView" :key="index">
                <td style="width: 50px" class="text-center">
                  {{ index + 1 }}
                </td>
                <td style="width: 320px">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model="item.description"
                  ></v-text-field>
                </td>
                <td style="width: 120px">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model.number="item.qty"
                    @input="calculateItemTotal(item)"
                    type="number"
                  ></v-text-field>
                </td>
                <td style="width: 120px">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model.number="item.unit_price"
                    @input="calculateItemTotal(item)"
                    type="number"
                  ></v-text-field>
                </td>

                <td class="text-center">
                  {{ convert_decimal(item.total_price) }}
                </td>
                <td class="text-center">
                  <v-icon @click="deleteItem(index, item)" small color="red"
                    >mdi-close</v-icon
                  >
                </td>
              </tr>
            </tbody>
          </table>

          <div class="d-flex justify-space-around py-3" style="margin-top: 5px">
            <v-col cols="10" class="text-right">
              <div>Sub Total:</div>
              <!-- <div>Add :</div> -->
              <div>Discount :</div>
              <div style="font-size: 18px; font-weight: bold">Total :</div>
            </v-col>
            <v-col cols="2" class="text-right">
              <div>
                {{ convert_decimal(subTotal()) }}
              </div>

              <!-- <div>
                {{ convert_decimal(room.room_extra_amount || 0) }}
              </div> -->
              <div style="color: red">
                <v-hover v-slot:default="{ hover, props }">
                  <div v-bind="props">
                    -{{ convert_decimal(room.room_discount || 0) }}
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
              <div style="font-size: 18px; font-weight: bold">
                {{ convert_decimal(processCalculation()) }}
              </div>
            </v-col>
          </div>

          <v-row class="text-right mb-3">
            <v-col>

              <HallDialogForQuotation
                label="Hall"
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
                ><v-icon small class="mt-1">mdi-floppy</v-icon> Submit</v-btn
              >
            </v-col>
          </v-row>
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
  props: ["model", "endpoint", "item"],
  components: {
    History,
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
    };
  },
  async created() {
    this.priceListTableView = this.item.items;
    this.room.room_discount = this.item.discount;
    this.room.sub_total = this.item.sub_total;
    this.runAllFunctions();
  },
  methods: {
    handleTableData(payload) {
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
    addItem() {
      this.priceListTableView.push({
        // date: this.formatDate(new Date()),
        description: "Enter item description",
        qty: 1,
        unit_price: 0,
        total_price: 0,
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
      this.dialog = false;
    },
    deleteItem(index, item) {
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
        return n + ".00".replace(".00.00", ".00");
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

      let quotaion = {
        book_date: this.formatDate(new Date()),
        arrival_date: this.formatDate(new Date()),
        departure_date: this.formatDate(new Date()),
        sub_total: this.subTotal(),
        discount: this.room.room_discount,
        tax: 0,
        total: this.processCalculation(),
        customer: this.customer,
        items: this.priceListTableView,
        company_id: this.$auth.user.company_id,
        type: "hall",
      };

      this.$axios
        .post("/quotation", quotaion)
        .then(({ data }) => {
          this.loading = false;
          this.$swal("Success!", "Quotation has been cloned", "success");
          this.$emit("response");
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
