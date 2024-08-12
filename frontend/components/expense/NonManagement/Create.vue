<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="blue"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> {{ model }}
        </v-btn>
      </template>

      <v-card>
        <v-toolbar flat class="blue white--text" dense>
          Create {{ model }}<v-spacer></v-spacer
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
                      v-model="payload.bill_date"
                      label="Bill Date"
                      persistent-hint
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
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
                    <td v-if="payload.items.length > 1"></td>
                  </tr>
                  <tr v-for="(item, index) in payload.items" :key="index">
                    <td style="width: 250px">
                      <v-text-field
                        hide-details
                        dense
                        outlined
                        v-model="item.detail"
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
                        id="right-align"
                        @input="calculateAmount(item)"
                        hide-details
                        dense
                        outlined
                        v-model="item.rate"
                        type="number"
                      ></v-text-field>
                    </td>
                    <td class="text-right">
                      <v-autocomplete
                        class="right-align"
                        @input="calculateAmount(item)"
                        outlined
                        dense
                        hide-details
                        item-value="id"
                        item-text="name"
                        v-model="item.tax"
                        :items="[
                          {
                            id: 0,
                            name: `Exempted`,
                          },
                          {
                            id: 5,
                            name: `5%`,
                          },
                          {
                            id: 12,
                            name: `12%`,
                          },
                          {
                            id: 18,
                            name: `18%`,
                          },
                          {
                            id: 28,
                            name: `28%`,
                          },
                        ]"
                      ></v-autocomplete>
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
                        readonly
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
                  <tr>
                    <td><h4>Total</h4></td>
                    <td class="text-right">
                      <v-text-field
                        readonly
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
              <v-col cols="12">
                <span class="primary--text">
                  <UploadMultipleAttachments
                    @files-selected="handleMultipleFileSelection($event)"
                  />
                </span>
              </v-col>
              <v-col cols="12" class="text-right">
                <v-btn
                  small
                  color="grey"
                  class="white--text"
                  dark
                  @click="close"
                >
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
  </div>
</template>
<script>
export default {
  props: ["endpoint", "model"],

  data() {
    return {
      menu2: false,
      payload: {
        vendor_id: 1,
        bill_number: "234234",
        bill_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        items: [{ detail: "test", rate: 0, qty: 0, tax: 0, amount: 0 }],
        attachments: [],

        sub_total: 0,
        discount: 0,
        total: 0,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      vendors: [],
    };
  },
  async created() {
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

    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    addItem() {
      this.payload.items.push({
        detail: "test",
        rate: 0,
        qty: 0,
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
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        console.log(error);
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
