<template>
  <v-dialog v-model="dialog" width="800">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-cash-multiple </v-icon>
        Invoice
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="blue white--text" dense>
        {{ model }} -> Invoice <v-spacer></v-spacer
        ><v-icon @click="close" color="white">mdi-close</v-icon></v-toolbar
      >

      <v-card-text v-if="payload && payload.id" class="py-5">
        <v-container>
          <v-row>
            <v-col cols="6">
              <v-autocomplete
                outlined
                dense
                hide-details
                item-value="id"
                item-text="first_name"
                v-model="payload.customer_id"
                :items="customers"
                label="Customers"
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-text-field
                hide-details
                dense
                outlined
                v-model="payload.lpo_number"
                label="LPO Number"
              ></v-text-field>
            </v-col>
            <v-col cols="4">
              <v-menu
                v-model="book_date_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model="payload.book_date"
                    label="Bill Date"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.book_date"
                  no-title
                  @input="book_date_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="4">
              <v-menu
                v-model="arrival_date_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model="payload.arrival_date"
                    label="Arrival Date"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.arrival_date"
                  no-title
                  @input="arrival_date_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="4">
              <v-menu
                v-model="departure_date_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    hide-details
                    v-model="payload.departure_date"
                    label="Departure Date"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.departure_date"
                  no-title
                  @input="departure_date_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="12">
              <table>
                <tr>
                  <td>Item</td>
                  <td>Food Plan</td>
                  <td class="text-right">Unit Price</td>
                  <td class="text-right">Qty</td>
                  <td class="text-right">Total Unit</td>
                  <td class="text-right">Tax</td>
                  <td class="text-right">Amount</td>
                  <td v-if="payload.items.length > 1"></td>
                </tr>
                <tr v-for="(item, index) in payload.items" :key="index">
                  <td style="width: 200px">
                    <v-autocomplete
                      @input="calculateAmount(item)"
                      outlined
                      dense
                      hide-details
                      item-value="id"
                      item-text="name"
                      v-model="item.product_id"
                      :items="rooms"
                      placeholder="Items"
                    ></v-autocomplete>
                  </td>
                  <td>
                    <v-autocomplete
                      @input="calculateAmount(item)"
                      outlined
                      dense
                      hide-details
                      item-value="id"
                      item-text="title"
                      v-model="item.foodplan_id"
                      :items="foodplans"
                      placeholder="Items"
                    ></v-autocomplete>
                  </td>
                  <td class="text-right">
                    <v-text-field
                      @input="calculateAmount(item)"
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="item.rate"
                      type="number"
                    ></v-text-field>
                  </td>
                  <td class="text-right">
                    <v-text-field
                      @input="calculateAmount(item)"
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="item.qty"
                      type="number"
                    ></v-text-field>
                  </td>
                  <td class="text-right">
                    <v-text-field
                      @input="calculateAmount(item)"
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="item.total_unit"
                      type="number"
                    ></v-text-field>
                  </td>
                  <td class="text-right">
                    <v-text-field
                      @input="calculateAmount(item)"
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="item.tax"
                      type="number"
                    ></v-text-field>
                  </td>
                  <td class="text-right">
                    <v-text-field
                      class="right-align"
                      readonly
                      hide-details
                      dense
                      outlined
                      v-model="item.amount"
                      type="number"
                    ></v-text-field>
                  </td>
                  <td v-if="payload.items.length > 1">
                    <v-icon
                      v-if="index > 0"
                      @click="deleteItem(index)"
                      small
                      color="red"
                      >mdi-close</v-icon
                    >
                  </td>
                </tr>
              </table>
            </v-col>

            <v-col cols="6">
              <v-btn class="my-2" small @click="addItem()"
                ><v-icon small left>mdi-plus-circle-outline</v-icon>Add New
                Row</v-btn
              >
            </v-col>
            <v-col cols="6" class="text-right">
              <table>
                <tr>
                  <td>SubTotal</td>
                  <td class="text-right">
                    <v-text-field
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="payload.sub_total"
                      type="number"
                    ></v-text-field>
                  </td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td class="text-right" style="width: 190px">
                    <v-text-field
                      color="red"
                      class="right-align"
                      @input="calculateTotal"
                      hide-details
                      dense
                      outlined
                      v-model="payload.discount"
                      type="number"
                    ></v-text-field>
                  </td>
                </tr>
                <!-- <tr>
                    <td>Tax</td>
                    <td class="text-right" style="width: 190px">
                      <v-text-field
                        class="right-align"
                        @input="calculateTotal"
                        hide-details
                        dense
                        outlined
                        v-model="payload.tax"
                        type="number"
                      ></v-text-field>
                    </td>
                  </tr> -->
                <tr>
                  <td><h4>Total</h4></td>
                  <td class="text-right">
                    <v-text-field
                      class="right-align"
                      hide-details
                      dense
                      outlined
                      v-model="payload.total"
                      type="number"
                    ></v-text-field>
                  </td>
                </tr>
              </table>
            </v-col>

            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <!-- <v-col cols="12">
                <span class="primary--text">
                  <UploadMultipleAttachments
                    @files-selected="handleMultipleFileSelection($event)"
                  />
                </span>
              </v-col> -->
            <v-col cols="12" class="text-right">
              <v-btn small color="grey" class="white--text" dark @click="close">
                Close
              </v-btn>
              <v-btn
                :loading="loading"
                small
                color="blue"
                class="white--text"
                dark
                @click="submit"
              >
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "endpoint", "model"],

  data() {
    return {
      book_date_menu: false,
      arrival_date_menu: false,
      departure_date_menu: false,

      payload: {
        customer_id: 0,
        book_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        arrival_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        departure_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        items: [
          {
            product_id: 0,
            foodplan_id: 0,
            rate: 0,
            qty: 1,
            total_unit: 0,
            tax: 0,
            amount: 0,
          },
        ],

        attachments: [],

        sub_total: 0,
        tax: 0,
        discount: 0,
        total: 0,

        bank_details: null,
        terms_and_conditions: null,
        status: null,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      customers: [],
      rooms: [],
      foodplans: [],
    };
  },
  async created() {
    this.payload = this.item;
    await this.getVendorCategory();
  },
  methods: {
    handleMultipleFileSelection(e) {
      e.forEach((v, i) => {
        const attachmentExists = this.payload.attachments.some(
          (att) => att.name === v.name
        );
        if (!attachmentExists) {
          this.payload.attachments.push({
            name: v.name,
            attachment: v.preview,
          });
        }
      });
    },
    async getVendorCategory() {
      let { data: customers } = await this.$axios.get(`customer-list`);
      let { data: rooms } = await this.$axios.get(
        `room_type?company_id=${this.$auth.user.company_id}`
      );
      let { data: foodplans } = await this.$axios.get(`foodplan-list`);

      this.customers = customers;
      this.rooms = rooms;
      this.foodplans = foodplans;
    },
    calculateAmount(item) {
      let room = this.rooms.find((e) => e.id == item.product_id);
      let foodplan = this.foodplans.find((e) => e.id == item.foodplan_id);

      let room_unit_price = (room && room.price) || 0;
      let room_tax = (room && room.tax) || 0;
      let foodplan_unit_price = (foodplan && foodplan.unit_price) || 0;
      let quantity = item.qty || 1;
      let unit = item.total_unit || 0;

      item.rate = room_unit_price + foodplan_unit_price;

      item.tax = (item.rate * quantity * unit * room_tax) / 100;

      item.amount = item.rate * quantity * unit + item.tax;

      this.calculateOverAll();
    },

    calculateOverAll() {
      this.payload.tax = 0;
      this.payload.sub_total = 0;

      this.payload.items.forEach((e) => {
        this.payload.sub_total += parseFloat(e.amount);
        this.payload.tax += parseFloat(e.tax);
      });

      this.calculateTotal();
    },

    calculateTotal() {
      // this.payload.sub_total = this.payload.sub_total - this.payload.tax;

      this.payload.total =
        parseFloat(this.payload.sub_total) - parseFloat(this.payload.discount);
    },

    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    addItem() {
      this.payload.items.push({
        product_id: 0,
        foodplan_id: 0,
        rate: 0,
        qty: 1,
        total_unit: 0,
        tax: 0,
        amount: 0,
      });
    },
    deleteItem(index) {
      this.payload.items.splice(index, 1);
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.post(this.endpoint, this.payload);
        this.close();
        this.$router.push("/invoices");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
@import url("@/assets/css/tableStyles.css");
</style>
