<template>
  <v-dialog v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-eye </v-icon>
        View
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="blue white--text" dense>
        View {{ model }} <v-spacer></v-spacer
        ><v-icon @click="close" color="white">mdi-close</v-icon></v-toolbar
      >

      <v-card-text class="py-5">
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-autocomplete
                outlined
                dense
                hide-details
                item-value="id"
                item-text="first_name"
                readonly
                v-model="payload.vendor_id"
                :items="vendors"
                placeholder="Vendors"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-text-field
                outlined
                dense
                hide-details
                readonly
                v-model="payload.bill_number"
                label="Bill #"
              ></v-text-field>
            </v-col>

            <v-col cols="12">
              <v-menu
                v-model="menu2"
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
                    readonly
                    v-model="payload.bill_date"
                    label="Bill Date"
                    persistent-hint
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  readonly
                  v-model="payload.bill_date"
                  no-title
                  @input="menu2 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="12">
              <table>
                <tr>
                  <td>Item Details</td>
                  <td class="text-right">Qty</td>
                  <td class="text-right">Rate</td>
                  <td class="text-right">Tax</td>
                  <td class="text-right">Amount</td>
                </tr>
                <tr v-for="(item, index) in payload.items" :key="index">
                  <td style="width: 250px">
                    {{ item.detail }}
                  </td>
                  <td class="text-right">
                    {{ item.qty }}
                  </td>
                  <td class="text-right">
                    {{ item.rate }}
                  </td>
                  <td class="text-right">
                    {{ item.tax }}
                  </td>
                  <td class="text-right">
                    {{ item.amount }}
                  </td>
                </tr>
              </table>
            </v-col>
            <v-col cols="6">
              <v-btn disabled class="my-2" small @click="addItem()"
                ><v-icon small left>mdi-plus-circle-outline</v-icon>Add New
                Row</v-btn
              ></v-col
            >
            <v-col cols="6" class="text-right">
              <table>
                <tr>
                  <td>SubTotal</td>
                  <td class="text-right">{{ payload.sub_total }}</td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td class="text-right" style="width: 190px">
                    {{ payload.discount }}
                  </td>
                </tr>
                <tr>
                  <td><h4>Total</h4></td>
                  <td class="text-right">{{ payload.total }}</td>
                </tr>
              </table>
            </v-col>
            <v-col>
              <div v-if="item.attachments">
                <ViewMultipleAttachments :attachments="item.attachments" />
              </div>
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
      menu2: false,
      payload: {
        vendor_id: 1,
        bill_number: "234234",
        bill_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        items: [{ detail: "test", rate: 50, qty: 5, tax: 5, amount: 5 }],
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      vendors: [],
    };
  },
  async created() {
    this.payload = this.item;
    await this.getVendorCategory();
  },

  methods: {
    async getVendorCategory() {
      let { data } = await this.$axios.get(`vendor-list`);

      this.vendors = data;
    },
    calculateAmount(item) {
      let quantity = item.qty || 0;
      let rate = item.rate || 0;
      let tax = item.tax || 0;

      // Calculate the tax amount
      let taxAmount = (quantity * rate * tax) / 100;

      // Calculate the total amount including tax
      let totalAmount = quantity * rate + taxAmount;

      // Update the item's amount
      item.amount = totalAmount;

      this.calculateOverAll();
    },

    calculateOverAll() {
      this.payload.sub_total = 0;
      this.payload.items.forEach((e) => {
        this.payload.sub_total += parseFloat(e.amount);
      });

      this.calculateTotal();
    },

    calculateTotal() {
      this.payload.total =
        parseFloat(this.payload.sub_total) - parseFloat(this.payload.discount);
    },

    addItem() {
      this.payload.items.push({
        detail: "test",
        rate: 50,
        qty: 5,
        tax: 5,
        amount: 5,
      });
    },
    deleteItem(index) {
      this.payload.items.splice(index, 1);
    },
    handleAttachment(e) {
      this.payload.attachment = e;
    },
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.put(
          `${this.endpoint}/${this.payload.id}`,
          this.payload
        );
        this.close();
        this.$emit("response", "Record has been inserted");
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
