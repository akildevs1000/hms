<template>
  <v-dialog v-model="dialog" width="900" style="position: relative">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-eye </v-icon>
        View
      </div>
    </template>
    <span
      style="position: absolute; z-index: 1; left: 1150px"
      :style="closeIconPosition"
    >
      <v-icon
        @click="dialog = false"
        color="white"
        class="grey"
        size="30"
        style="border-radius: 50px; width: 15px; height: 15px"
      >
        mdi-close-circle
      </v-icon>
    </span>

    <div
      class="grey lighten-3 pa-2"
      style="overflow: hidden"
      v-if="payload && payload.id"
    >
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
                      <td class="blue--text">: VN {{ payload.bill_number }}</td>
                    </tr>
                    <tr>
                      <td>Date</td>
                      <td>: {{ payload.bill_date }}</td>
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
                    <template #label> <span>View Detail</span> </template>
                    <!-- <template #search
                        ><VendorSearch @foundVendor="handleFoundVendor"
                      /></template> -->
                  </AssetsHeadDialog>
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
                    <!-- <template #row>
                        <tr>
                          <td colspan="5" class="pt-1">
                            <ExpenseItem
                              @selectedItem="
                                (e) => {
                                  payload.items.push(e);
                                  calculateOverAll();
                                }
                              "
                            />
                          </td>
                        </tr>
                      </template> -->
                  </AssetsTable>
                </v-col>

                <v-col cols="6">
                  <v-textarea
                    readonly
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
                        {{ $utils.currency_format(payload.sub_total) }}
                      </td>
                    </tr>
                    <tr>
                      <td class="border-bottom text-color">Tax:</td>
                      <td class="text-right border-bottom text-color">
                        {{ $utils.currency_format(payload.tax) }}
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

                <!-- <v-col cols="12">
                  <span class="primary--text">
                    <UploadMultipleAttachments
                      @files-selected="handleMultipleFileSelection($event)"
                    />
                  </span>
                </v-col>
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
      payload: {
        is_admin_expense: 1,
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
        // { text: ``, value: `action`, align: `center`, width: "30px" },
      ],
      vendorObject: null,
      vendorEditItem: null,
      lastThreeRecords: [],
      emptyRowLength: 3,
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
    closeIconPosition() {
      const minHeight = {
        Company: "115px",
        default: "145px",
      };

      return `top:${minHeight[this.vendorObject?.type] || minHeight.default};`;
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
    this.getLastThreeRecords(vendor.id,item.is_admin_expense);
  },

  methods: {
    async getLastThreeRecords(vendor_id,is_admin_expense) {
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
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },
    deleteItem(index) {
      this.payload.items.splice(index, 1);
    },
    async submit() {
      this.loading = true;
      try {
        await this.$axios.put(
          `${this.endpoint}/${this.payload.id}`,
          this.payload
        );
        this.close();
        this.$emit("response", "Record has been updated");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
  },
};
</script>
