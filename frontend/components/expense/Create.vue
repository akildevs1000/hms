<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="900">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="primary"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> New
        </v-btn>
      </template>
      <AssetsIconClose @click="dialog = false" />

      <div class="grey lighten-3 pa-2" style="overflow: hidden">
        <v-row>
          <v-col cols="4">
            <v-card
              style="border: 3px solid white"
              :style="`min-height:${
                vendorObject?.type == `Personal` ? '447px' : '397px'
              }`"
            >
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
                        <td class="blue--text">: VN {{ ReceiptNumber }}</td>
                      </tr>
                      <tr>
                        <td>Date</td>
                        <td>: {{ $dateFormat.dmy(current_date) }}</td>
                      </tr>
                      <tr>
                        <td>Category</td>
                        <td>
                          :
                          {{ vendorObject?.vendor_category?.name || "---" }}
                        </td>
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
                        <td>
                          Tel :
                          {{
                            vendorObject && vendorObject.type == "Company"
                              ? vendorObject?.work_phone
                              : vendorObject?.mobile
                          }}
                        </td>
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
                    <br v-if="vendorObject && vendorObject.type == 'Company'" />
                    <table
                      v-if="vendorObject && vendorObject.type == 'Company'"
                    >
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
                      <tr v-for="(n, index) in emptyRowLength" :key="index">
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
                      <template #label> <span>Detail</span> </template>
                      <template #search
                        ><VendorSearch @foundVendor="handleFoundVendor"
                      /></template>
                    </AssetsHeadDialog>
                  </v-col>
                  <v-col>
                    <v-text-field
                      outlined
                      dense
                      hide-details
                      label="Customer Invoice Number"
                      v-model="payload.bill_number"
                    >
                    </v-text-field>
                  </v-col>
                  <v-col>
                    <AssetsPickerDate
                      label="Invoice Date"
                      @date="
                        (e) => {
                          payload.bill_date = e;
                        }
                      "
                    />
                  </v-col>
                  <v-col cols="12">
                    <AssetsTable :headers="headers" :items="payload.items">
                      <template #rate="{ item }">
                        {{ $utils.currency_format(item.rate) }}
                      </template>
                      <template #tax="{ item }"> {{ item.tax }}% </template>
                      <template #amount="{ item }">
                        {{ $utils.currency_format(item.amount) }}
                      </template>
                      <template #action="{ item }">
                        <v-icon
                          style="cursor: pointer"
                          @click="deleteItem(index, item)"
                          small
                          color="red"
                          >mdi-close</v-icon
                        >
                      </template>
                      <template #row>
                        <tr>
                          <td colspan="5" class="pt-1">
                            <v-row no-gutter>
                              <v-col cols="2">
                                <ExpenseItem
                                  @selectedItem="
                                    (e) => {
                                      payload.items.push(e);
                                      calculateOverAll();
                                    }
                                  "
                                />
                              </v-col>
                            </v-row>
                          </td>
                        </tr>
                      </template>
                    </AssetsTable>
                  </v-col>

                  <v-col cols="6">
                    <v-textarea
                      color="grey lighten-1"
                      outlined
                      rows="2"
                      label="Notes"
                      hide-details
                      v-model="payload.notes"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="3"></v-col>
                  <v-col cols="3" class="text-right">
                    <table cellspacing="0" style="width: 100%">
                      <tr>
                        <td class="border-top border-bottom text-color">
                          Sub Total:
                        </td>
                        <td
                          class="text-right border-top border-bottom text-color"
                        >
                          {{ $utils.convert_decimal(payload.sub_total) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="border-bottom text-color">Tax:</td>
                        <td class="text-right border-bottom text-color">
                          {{ $utils.convert_decimal(payload.tax) }}
                        </td>
                      </tr>
                      <tr>
                        <td class="border-bottom primary--text">Total:</td>
                        <td class="text-right border-bottom primary--text">
                          {{ $utils.currency_format(payload.total) }}
                        </td>
                      </tr>
                    </table>
                  </v-col>
                  <v-col cols="12">
                  <span class="primary--text">
                    <UploadMultipleAttachments
                      @files-selected="handleMultipleFileSelection($event)"
                    />
                  </span>
                </v-col>
                  <v-col cols="12" v-if="errorResponse">
                    <span class="red--text">{{ errorResponse }}</span>
                  </v-col>
                  <v-col
                    cols="12"
                    class="text-center"
                    style="position: absolute; bottom: 0"
                  >
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
              </v-container>
            </v-card>
          </v-col>
        </v-row>
      </div>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["endpoint", "model", "is_admin_expense"],

  data() {
    return {
      current_date: new Date(
        Date.now() - new Date().getTimezoneOffset() * 60000
      )
        .toISOString()
        .substr(0, 10),
      menu2: false,
      payload: {
        vendor_id: 1,
        notes: "test",
        tax: 0,
        bill_number: "11133334555",
        bill_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),

        items: [],
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
      headers: [
        {
          text: `Item Details`,
          value: `detail`,
          align: `left`,
          width: "250px",
        },
        { text: `Qty`, value: `qty`, align: `right` },
        { text: `Rate`, value: `rate`, align: `right` },
        { text: `tax`, value: `tax`, align: `right` },
        { text: `Amount`, value: `amount`, align: `right` },
        { text: ``, value: `action`, align: `center`, width: "30px" },
      ],
      vendorObject: null,
      vendorEditItem: null,
      lastThreeRecords: [],
      emptyRowLength: 3,
      ReceiptNumber:null,
    };
  },
  computed: {
    detailContainerHeight() {
      const minHeight = {
        Company: "515px",
        default: "450px",
      };

      return `min-height:${
        minHeight[this.vendorObject?.type] || minHeight.default
      };`;
    },
  },

  async created() {
    let config = {
      params: {
        company_id: this.$auth.user.company_id,
      },
    };
    let { data } = await this.$axios.get(`expense-last-number`, config);

    this.ReceiptNumber = data;
  },
  methods: {
    async getLastThreeRecords(vendor_id) {
      let config = {
        params: {
          vendor_id: vendor_id,
          is_admin_expense: this.is_admin_expense,
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
    handleFoundVendor(e) {
      this.vendorObject = {
        ...e,
        full_name: `${e.title}. ${e.first_name} ${e.last_name}`,
        address: `${e.city} ${e.state}, ${e.country}`,
      };
      this.vendorEditItem = e;
      this.payload.vendor_id = e.id;

      this.getLastThreeRecords(e.id);
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

    calculateOverAll() {
      this.payload.sub_total = 0;
      this.payload.tax = 0;
      this.payload.total = 0;
      this.payload.items.forEach((e) => {
        this.payload.sub_total += parseFloat(e.amount);
        this.payload.tax += parseFloat((e.qty * e.rate * e.tax) / 100);
      });
      this.payload.total = this.payload.sub_total + this.payload.tax;
    },

    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    deleteItem(index) {
      this.payload.items.splice(index, 1);
    },
    async submit() {
      this.errorResponse = null;
      if(!this.payload.items.length) {
        alert(`Item is not added.`);
        return;
      }
      this.loading = true;
      try {
        this.payload.is_admin_expense = this.is_admin_expense;
        await this.$axios.post(this.endpoint, this.payload);
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
