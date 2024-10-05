<template>
  <v-dialog v-model="dialog" width="1100">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-cash </v-icon>
        Payment
      </div>
    </template>
    <style scoped>
      textarea:focus {
        border-color: #42a5f5; /* Replace with your preferred focus color */
        outline: none; /* Remove default outline */
      }
    </style>

    <AssetsIconClose :left="1090" @click="close" />

    <div
      class="grey lighten-3 pa-2"
      style="overflow: hidden"
      v-if="payload && payload.id"
    >
      <v-row>
        <v-col cols="4">
          <v-card style="border: 3px solid white; min-height: 437px">
            <v-card-text>
              <v-row no-gutter>
                <v-col cols="12" class="pa-0 ma-0">
                  <AssetsHeadDialog>
                    <template #label>
                      <span>Local Expense</span>
                    </template>
                  </AssetsHeadDialog>
                </v-col>
                <v-col cols="12">
                  <table>
                    <tr>
                      <td class="blue--text">Receipt Number</td>
                      <td class="blue--text">
                        : {{ $utils.add_zeros(payload.id) }}
                      </td>
                    </tr>
                    <tr>
                      <td>Date</td>
                      <td>: {{ $dateFormat.dmy(payload.created_at) }}</td>
                    </tr>
                    <tr>
                      <td>Category</td>
                      <td>
                        :
                        {{ vendorObject?.vendor_category?.name || "---" }}
                      </td>
                    </tr>
                    <tr>
                      <td>Customer Invoice</td>
                      <td>: {{ payload.bill_number }}</td>
                    </tr>
                    <tr>
                      <td>Invoice Date</td>
                      <td>: {{ $dateFormat.dmy(payload.bill_date) }}</td>
                    </tr>
                    <tr>
                      <td>Prepared By</td>
                      <td>: {{ $auth.user.name }}</td>
                    </tr>
                  </table>
                </v-col>
                <v-col cols="12">
                  <table cellspacing="0" style="width: 100%">
                    <tr>
                      <td class="blue--text">
                        <span v-if="vendorObject">
                          {{
                            vendorObject?.type == "Personal"
                              ? vendorObject?.full_name
                              : vendorObject?.company_name
                          }}
                        </span>
                        <span v-else>---</span>
                      </td>
                      <td class="blue--text text-right">
                        <VendorEdit
                          v-if="vendorEditItem && vendorEditItem.id"
                          :ley="vendorEditItem.id"
                          :model="null"
                          :endpoint="`vendor`"
                          :item="vendorEditItem"
                          @updated_vendor="handleFoundVendor"
                        />
                      </td>
                    </tr>
                    <tr>
                      <td>Tel : {{ vendorObject?.work_phone || "---" }}</td>
                    </tr>
                    <tr>
                      <td>Email : {{ vendorObject?.email || "---" }}</td>
                    </tr>
                    <tr>
                      <td>
                        GST Number :
                        {{ vendorObject?.tax_number || "---" }}
                      </td>
                    </tr>
                    <tr>
                      <td>Address : {{ vendorObject?.address }}</td>
                    </tr>
                  </table>
                </v-col>

                <v-col
                  cols="12"
                  v-if="vendorObject && vendorObject.type == 'Company'"
                >
                  <table cellspacing="0" style="width: 100%">
                    <tr>
                      <td class="blue--text">
                        {{ vendorObject?.full_name }}
                      </td>
                    </tr>
                    <tr>
                      <td>Mobile : {{ vendorObject?.mobile || "---" }}</td>
                    </tr>
                  </table>
                </v-col>

                <v-col cols="12">
                  <table cellspacing="0" style="width: 100%">
                    <tr>
                      <td colspan="3" class="blue--text">
                        Last Invoice Detail
                      </td>
                    </tr>
                    <tr>
                      <td class="" style="width: 33%">INV</td>
                      <td class="">Date</td>
                      <td class="text-right">Amount</td>
                    </tr>

                    <tr
                      v-for="(record, index) in lastThreeRecords"
                      :key="index"
                    >
                      <td>{{ record.vn }}</td>
                      <td>{{ record.date }}</td>
                      <td class="text-right">{{ record.amount }}</td>
                    </tr>
                    <tr v-for="(n, emptyRowIndex) in emptyRowLength" :key="emptyRowIndex">
                      <td>---</td>
                      <td>---</td>
                      <td class="text-right">---</td>
                    </tr>
                  </table>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="8">
          <v-card
            style="border: 3px solid white"
            :style="detailContainerHeight"
          >
            <v-container>
              <v-row>
                <v-col cols="12" class="pa-0 ma-0">
                  <AssetsHeadDialog>
                    <template #label> <span>Payment Detail</span> </template>
                    <!-- <template #search
                        ><VendorSearch @foundVendor="handleFoundVendor"
                      /></template> -->
                  </AssetsHeadDialog>
                </v-col>
                <v-col cols="12">
                  <AssetsTable :headers="headers" :items="items">
                    <template #amount="{ item }">
                      {{ $utils.currency_format(item.amount) }}
                    </template>
                    <template #discount="{ item }">
                      {{ $utils.currency_format(item.discount) }}
                    </template>
                    <template #paid="{ item }">
                      {{ $utils.currency_format(item.paid) }}
                    </template>
                    <template #balance="{ item }">
                      {{ $utils.currency_format(item.balance) }}
                    </template>
                    <template #action="{ item }">
                      <ExpenseVoucher
                        :model="`Voucher`"
                        :endpoint="endpoint"
                        :item="item"
                      />
                    </template>
                  </AssetsTable>
                </v-col>

                <v-col cols="7">
                  <v-card outlined>
                    <v-card-text>
                      <v-row>
                        <v-col cols="4">
                          <v-autocomplete
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.payment_mode"
                            :items="[
                              `Cash`,
                              `Card`,
                              `Online`,
                              `Bank`,
                              `UPI`,
                              `Cheque`,
                            ]"
                            label="Mode"
                          ></v-autocomplete>
                        </v-col>
                        <v-col cols="8">
                          <v-text-field
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.payment_mode_ref"
                            label="Reference"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="6">
                          <v-text-field
                            readonly
                            outlined
                            dense
                            hide-details
                            v-model="OpeningBalance"
                            label="Balance"
                          ></v-text-field>
                        </v-col>

                        <v-col cols="6">
                          <v-text-field
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.discount"
                            @input="calculateNewBalance(paymentJson)"
                            label="Discount"
                          ></v-text-field>
                        </v-col>

                        <!-- <v-col cols="6">
                          <v-text-field
                            readonly
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.amount"
                            label="After Discount"
                          ></v-text-field>
                        </v-col> -->
                        <v-col cols="12">
                          <v-text-field
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.paid"
                            label="Amount to Pay"
                            @input="calculateNewBalance(paymentJson)"
                          ></v-text-field>
                        </v-col>

                        <!-- <v-col cols="6">
                          <v-text-field
                            outlined
                            dense
                            hide-details
                            v-model="paymentJson.balance"
                            label="New Balance"
                          ></v-text-field>
                        </v-col> -->

                        <v-col cols="12" v-if="errorResponse">
                          <span class="red--text">{{ errorResponse }}</span>
                        </v-col>
                        <v-col cols="12" class="text-center">
                          <AssetsButton
                            :options="{
                              color: `red`,
                              label: `cancel`,
                            }"
                            @click="close"
                          />
                          <AssetsButton
                            :options="{
                              color: `green`,
                              label: `Submit`,
                            }"
                            @click="submit"
                          />
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-col>

                <v-col cols="5">
                  <fieldset
                    style="
                      border: 1px solid rgb(224, 224, 224);
                      padding: 10px;
                      border-radius: 4px;
                      margin-top: -11px;
                    "
                  >
                    <legend class="text-color">Note</legend>
                    <textarea
                      style="
                        height: 208px;
                        width: 100%;
                        border: none !important;
                        border-radius: 2px;
                      "
                      outlined
                      v-model="paymentJson.note"
                    ></textarea>
                  </fieldset>
                </v-col>

                <!-- <v-col cols="12">
                  <span class="primary--text">
                    {{ paymentJson }}
                    <UploadMultipleAttachments
                      @files-selected="handleMultipleFileSelection($event)"
                    />
                  </span>
                </v-col> -->
              </v-row>
            </v-container>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "endpoint", "model"],
  data() {
    return {
      menu2: false,
      OpeningBalance: 0,
      payload: {
        vendor_id: 1,
        notes: "test",
        tax: 0,
        bill_number: null,
        bill_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        items: [],
        attachments: [],

        sub_total: 0,
        discount: 0,
        total: 0,
      },
      paymentJson: {
        vendor_id: 1,
        payment_number: "----",
        payment_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        payment_mode: `Cash`,
        payment_mode_ref: ``,
        attachments: [],
        note: "test",

        amount: 0,
        discount: 0,
        paid: 0,
        balance: 0,
      },

      defaultPaymentJson: {
        vendor_id: 1,
        payment_number: "----",
        payment_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        payment_mode: `Cash`,
        payment_mode_ref: `111111`,
        attachments: [],
        note: "test",

        amount: 0,
        discount: 0,
        paid: 0,
        balance: 0,
      },
      dialog: false,
      loading: false,
      successResponse: null,
      errorResponse: null,
      vendors: [],
      headers: [
        {
          text: `Date`,
          value: `payment_date`,
          align: `left`,
          width: "250px",
        },
        { text: `Receipt`, value: `id`, align: `right` },
        { text: `Amount`, value: `amount`, align: `right` },
        { text: `Discount`, value: `discount`, align: `right` },
        { text: `paid`, value: `paid`, align: `right` },
        { text: `Balance`, value: `balance`, align: `right` },
        { text: ``, value: `action`, align: `center` },
      ],
      vendorObject: null,
      vendorEditItem: null,
      lastThreeRecords: [],
      emptyRowLength: 3,
      tempBalance: 0,
      items: [],
    };
  },
  computed: {
    detailContainerHeight() {
      const minHeight = {
        Company: "515px",
        default: "490px",
      };

      return `min-height:${
        minHeight[this.vendorObject?.type] || minHeight.default
      };`;
    },
  },
  async created() {
    let { vendor, ...item } = this.item;
    // attachments
    this.payload = item;
    this.vendorObject = {
      ...vendor,
      full_name: `${vendor.title}. ${vendor.first_name} ${vendor.last_name}`,
      address: `${vendor.city} ${vendor.state}, ${vendor.country}`,
    };

    this.paymentJson.vendor_id = vendor.id;

    this.getLastThreeRecords(vendor.id, item.is_admin_expense);

    await this.getPayments();
  },

  methods: {
    calculateNewBalance({ discount, paid, amount }) {
      this.paymentJson.amount = this.OpeningBalance - discount;
      this.paymentJson.balance = amount - paid;
    },
    async getLastThreeRecords(vendor_id, is_admin_expense) {
      let config = {
        params: {
          vendor_id: vendor_id,
          is_admin_expense: is_admin_expense,
        },
      };
      let { data } = await this.$axios.get(`get-last-three-records`, config);
      this.lastThreeRecords = data.map((e) => ({
        vn: e.id,
        date: e.bill_date,
        amount: this.$utils.currency_format(e.total),
      }));

      let desiredLength = 3;

      if (this.lastThreeRecords.length < desiredLength) {
        this.emptyRowLength = desiredLength - this.lastThreeRecords.length;
      } else if (this.lastThreeRecords.length >= desiredLength) {
        this.emptyRowLength = 0;
      }
    },
    async getPayments() {
      let config = {
        params: {
          admin_expense_id: this.item.id,
        },
      };
      let { data } = await this.$axios.get(`expense-payment`, config);
      this.items = data;
      this.calculateTotalPreviosBalance(data);
    },
    handleFoundVendor(e) {
      this.vendorObject = {
        ...e,
        full_name: `${e.title}. ${e.first_name} ${e.last_name}`,
        address: `${e.city} ${e.state}, ${e.country}`,
      };
      this.vendorEditItem = e;
      this.payload.vendor_id = e.id;
    },
    calculateTotalPreviosBalance(data) {
      if (data.length == 0) {
        this.OpeningBalance = this.item.total;
        return;
      }
      this.OpeningBalance = 0;
      data.forEach((e) => {
        this.OpeningBalance = parseFloat(e.balance);
      });
    },

    calculateTotal() {
      this.payload.total =
        parseFloat(this.payload.sub_total) - parseFloat(this.payload.discount);
    },
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
    close() {
      this.paymentJson = this.defaultPaymentJson;
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    deleteItem(index) {
      this.items.splice(index, 1);
    },
    async submit() {
      this.loading = true;
      let payload = this.paymentJson;
      payload.admin_expense_id = this.item.id;
      if (this.items.length == 0) {
        payload.amount = this.item.total;
      }

      try {
        await this.$axios.post(`expense-payment`, payload);
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
