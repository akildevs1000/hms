<template>
  <div v-if="can('calendar_create')">
    <v-dialog persistent v-model="editDialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <div v-bind="attrs" v-on="on">
          <v-icon color="blue" small> mdi-content-duplicate </v-icon>
          Clone
        </div>
      </template>
      <v-card>
        <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
          <span>Quotation Clone Information</span>
          <v-spacer></v-spacer>
          <SearchCustomer @foundCustomer="handleFoundCustomer" />
          &nbsp;
          <AssetsButtonClose @close="close" />
        </v-toolbar>
        <v-card-text>
          <v-card flat class="mt-5">
            <v-card-text v-if="customer && customer.id">
              <QuotationCustomerInfo
                :defaultCustomer="customer"
                :key="customerCompKey"
                @selectedCustomer="handleSelectedCustomer"
              />
            </v-card-text>
          </v-card>

          <table cellspacing="0" style="width: 100%">
            <AssetsTableHeader :cols="headers" />
            <tbody>
              <tr v-for="(item, index) in priceListTableView" :key="index">
                <td style="width: 50px" class="text-center py-2 border-bottom">
                  <small>{{ index + 1 }}</small>
                </td>
                <td style="width: 320px" class="text-center border-bottom">
                  <small>{{ item.room_type }}</small>
                </td>
                <td style="width: 320px" class="text-center border-bottom">
                  <small>{{ item.meal_name }}</small>
                </td>
                <td style="width: 120px" class="text-center border-bottom">
                  <small>{{ item.no_of_adult }}</small>
                </td>
                <td style="width: 120px" class="text-right border-bottom">
                  <small>{{ $utils.currency_format(item.price) }}</small>
                </td>
                <td style="width: 120px" class="text-center border-bottom">
                  <small>{{ item.no_of_rooms }}</small>
                </td>
                <td style="width: 120px" class="text-center border-bottom">
                  <small>{{ item.no_of_nights }}</small>
                </td>
                <td class="text-right py-2 border-bottom">
                  <small class="pt-2 text-right">
                    {{
                      $utils.currency_format(
                        item.price * item.no_of_rooms * item.no_of_nights
                      )
                    }}
                  </small>
                </td>
                <td class="text-right py-2 border-bottom" style="width: 50px">
                  <v-icon @click="deleteItem(index, item)" small color="red"
                    >mdi-close</v-icon
                  >
                </td>
              </tr>
            </tbody>
          </table>

          <div class="mt-2">
            <RoomDialogForQuotation @tableData="handleTableData" />
            &nbsp;
            <QuotationRoomItem
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
                    <small>Sub Total:</small>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <small>{{ $utils.convert_decimal(subTotal()) }}</small>
                  </td>
                </tr>
                <tr>
                  <td colspan="9"></td>

                  <td class="border-bottom">
                    <small>Add:</small>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        <small>
                          {{
                            $utils.convert_decimal(room.room_extra_amount || 0)
                          }}</small
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
                    <small>Discount:</small>
                  </td>
                  <td colspan="10" class="text-right pb-2 border-bottom">
                    <v-hover v-slot:default="{ hover, props }">
                      <div v-bind="props">
                        <small>
                          -{{
                            $utils.convert_decimal(room.room_discount || 0)
                          }}</small
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
                    <small>Total:</small>
                  </td>
                  <td
                    colspan="10"
                    class="text-right pb-2 border-bottomprimary--text"
                  >
                    <small>
                      {{ $utils.currency_format(processCalculation()) }}</small
                    >
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>

          <v-row class="text-right mb-3">
            <v-col>
              <AssetsButtonCancel @close="close" />
              &nbsp;
              <AssetsButtonSubmit @click="submit" />
            </v-col>
          </v-row>
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
      editDialog: false,
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
      headers: [
        {
          text: `#`,
          align: "center",
        },
        {
          text: `Room Type`,
          align: "center",
        },
        {
          text: `Food`,
          align: "center",
        },
        {
          text: `PAX`,
          align: "center",
        },
        {
          text: `Tarrif`,
          align: "right",
        },
        {
          text: `Rooms`,
          align: "center",
        },
        {
          text: `Nights`,
          align: "center",
        },
        {
          text: `Total`,
          align: "right",
        },
        {
          text: ``,
        },
      ],
    };
  },
  async created() {
    this.customer = this.item.customer;
    this.priceListTableView = this.item.items;
    this.room.room_discount = this.item.discount;
    this.room.sub_total = this.item.sub_total;
    this.processCalculation();
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
      this.editDialog = false;
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

      let arrival_date = this.formatDate(new Date());
      let departure_date = this.formatDate(new Date());

      if (!this.selectedRooms.length) {
        arrival_date = this.selectedRooms[0].check_in;
        departure_date =
          this.selectedRooms[this.selectedRooms.length - 1].check_out;
      } else {
        arrival_date = this.item.arrival_date;
        departure_date = this.item.departure_date;
      }

      let quotaion = {
        book_date: this.formatDate(new Date()),
        arrival_date: arrival_date,
        departure_date: departure_date, // Last room's check_out date
        sub_total: this.subTotal(),
        discount: this.room.room_discount,
        tax: 0,
        total: this.processCalculation(),
        customer: this.customer,
        items: this.priceListTableView,
        company_id: this.$auth.user.company_id,
        type: "room",
      };
      console.log("🚀 ~ submit ~ quotaion:", quotaion);

      this.subLoad = false;

      this.$axios
        .post("/quotation", quotaion)
        .then(({ data }) => {
          this.loading = false;
          this.$swal("Success!", "Quotation has been created", "success");
          this.$emit("response");
          this.editDialog = false;
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