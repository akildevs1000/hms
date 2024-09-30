<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="dialog" width="900">
        <template v-slot:activator="{ on, attrs }">
        <div v-bind="attrs" v-on="on">
          <v-icon color="blue" small> mdi-eye </v-icon>
          View
        </div>
      </template>
      <v-card v-if="item && item.id">
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <span>Quotation Information</span>
          <v-spacer></v-spacer>
          <AssetsButtonClose @close="close" />
        </v-toolbar>
        <v-card-text>
            <v-card flat class="mt-5">
                <v-card-text>
                  <QuotationCustomerInfo
                    :defaultCustomer="item.customer"
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
                  <tr v-for="(item, index) in item.items" :key="index">
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

              <div
                class="d-flex justify-space-around py-3"
                style="margin-top: 5px"
              >
                <v-col cols="10" class="text-right">
                  <div>Sub Total:</div>
                  <div>Add :</div>
                  <div>Discount :</div>
                  <div style="font-size: 18px; font-weight: bold">Total :</div>
                </v-col>
                <v-col cols="2" class="text-right">
                  <div>
                    {{ convert_decimal(item.sub_total) }}
                  </div>

                  <div>
                    {{ convert_decimal(item.extra || 0) }}
                  </div>
                  <div style="color: red">
                    -{{ convert_decimal(item.discount || 0) }}
                  </div>
                  <div style="font-size: 18px; font-weight: bold">
                    {{ item.total }}
                  </div>
                </v-col>
              </div>
        </v-card-text>
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
  components: {
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
    this.runAllFunctions();

    this.get_customer();
  },
  methods: {
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
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer_by_id/${this.item.customer_id}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            alert("Customer not found");
            return;
          }

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };
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
      if (!this.room.purpose) {
        this.$swal("Warning!", "Purpose is required", "error");
        return;
      }
      if (!customer.contact_no) {
        this.$swal("Warning!", "Customer contact name is required", "error");
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
      };
      console.log(quotaion);

      // return;

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
