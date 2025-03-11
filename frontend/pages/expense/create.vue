<template>
  <v-card>
    <v-card-title> New Expense </v-card-title>
    <v-card-text>
      <style scoped="scoped">
        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 100%;
        }

        td,
        th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 8px;
        }
        .custom-text-fields input {
          text-align: right;
          padding: 0;
          margin: 0;
        }

        .input-no-border {
          font-size: 12px !important;
          color: grey !important;
          border: none;
          outline: none;
          background: transparent;
          width: 100%;
        }

        .input-no-border:focus,
        .input-no-border:hover,
        .input-no-border:active {
          border: none;
          outline: none;
          box-shadow: none;
        }
      </style>
      <v-row>
        <v-col cols="5">
          <v-container>
            <v-row>
              <v-col cols="3"> Expense Type </v-col>
              <v-col cols="9">
                <v-autocomplete
                  :items="[
                    { id: 1, name: `Manager Expense` },
                    { id: 0, name: `Non Manager Expense` },
                  ]"
                  item-text="name"
                  item-value="id"
                  outlined
                  dense
                  hide-details
                  v-model="payload.is_admin_expense"
                >
                </v-autocomplete>
              </v-col>
              <v-col cols="3"> Vendor </v-col>
              <v-col cols="9">
                <v-autocomplete
                  v-model="selectedItem"
                  :items="vendors"
                  item-text="full_name"
                  item-value="id"
                  return-object
                  dense
                  hide-details
                  outlined
                >
                </v-autocomplete>

                <v-container
                  v-if="selectedItem"
                  class="grey lighten-2 mt-2 pa-4"
                >
                  <v-row no-gutters
                    ><v-col v-if="selectedItem.type != 'Personal'">
                      <strong>Company:</strong>
                      {{ selectedItem.company_name }}</v-col
                    >
                    <v-col cols="4">
                      <strong>Account Type:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.type }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Category:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem?.vendor_category?.name }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Email:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.email }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Work Phone:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.work_phone }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Mobile:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.mobile }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Tax Number:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.tax_number }}
                    </v-col>
                    <v-col cols="4">
                      <strong>Address:</strong>
                    </v-col>
                    <v-col cols="8">
                      {{ selectedItem.country }} {{ selectedItem.state }}
                      {{ selectedItem.city }} {{ selectedItem.zip_code }}
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>
              <v-col cols="3"> Invoice Number </v-col>
              <v-col cols="9">
                <v-text-field
                  outlined
                  dense
                  hide-details
                  v-model="payload.bill_number"
                >
                </v-text-field>
              </v-col>
              <v-col cols="3"> Invoice Date </v-col>
              <v-col cols="9">
                <AssetsPickerDate
                  @date="
                    (e) => {
                      payload.bill_date = e;
                    }
                  "
                />
              </v-col>
              <v-col cols="12">
                <table style="width: 100%">
                  <thead>
                    <tr>
                      <td
                        class="primary--text border-top border-bottom"
                        style="width: 400px"
                      >
                        Item Details
                      </td>
                      <td class="primary--text border-top border-bottom">
                        Qty
                      </td>
                      <td
                        class="primary--text border-top border-bottom text-right"
                      >
                        Rate
                      </td>
                      <td class="primary--text border-top border-bottom">
                        Tax
                      </td>
                      <td
                        class="primary--text border-top border-bottom text-right"
                      >
                        Amount
                      </td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in payload.items" :key="index">
                      <td class="border-bottom text-color">
                        <div style="width: 100%">
                          <textarea
                            rows="1"
                            class="input-no-border"
                            type="text"
                            v-model="item.detail"
                          ></textarea>
                        </div>
                      </td>
                      <td style="width: 100px" class="border-bottom text-color">
                        <div>
                          <input
                            class="input-no-border"
                            style="text-align: right"
                            type="text"
                            v-model="item.qty"
                            @input="calculateAmount(item)"
                          />
                        </div>
                      </td>
                      <td
                        style="width: 100px"
                        class="border-bottom text-color text-right"
                      >
                        <div>
                          <input
                            class="input-no-border"
                            style="text-align: right"
                            type="text"
                            v-model="item.rate"
                            @input="calculateAmount(item)"
                          />
                        </div>
                      </td>
                      <td
                        style="max-width: 250px; min-width: 150px"
                        class="border-bottom text-color"
                      >
                        <div>
                          <v-autocomplete
                            dense
                            hide-details
                            outlined
                            :items="[
                              { id: 0, name: `Exempted` },
                              { id: 5, name: `5%` },
                              { id: 12, name: `12%` },
                              { id: 18, name: `18%` },
                              { id: 28, name: `28%` },
                            ]"
                            item-text="name"
                            item-value="id"
                            v-model="item.tax"
                            @input="calculateAmount(item)"
                            style="text-align: right"
                          >
                          </v-autocomplete>
                        </div>
                      </td>
                      <td
                        style="width: 100px"
                        class="border-bottom text-color text-right"
                      >
                        <div>{{item.amount}}</div>
                      </td>
                      <td
                        style="width: 20px"
                        class="border-bottom text-color text-center"
                      >
                        <div>
                          <v-icon color="red" small @click="deleteItem(index)"
                            >mdi-close</v-icon
                          >
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <v-row class="my-2">
                  <v-col cols="8">
                    <v-btn @click="addItem" small elevation="0"
                      ><v-icon small color="primary">mdi-plus-circle</v-icon
                      ><span>New Row</span></v-btn
                    >

                    <v-textarea
                      class="mt-2"
                      color="grey lighten-1"
                      outlined
                      rows="2"
                      label="Notes"
                      hide-details
                      v-model="payload.notes"
                    ></v-textarea>
                  </v-col>
                  <v-col>
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
                </v-row>

                <v-divider />
              </v-col>

              <v-col cols="12">
                <span class="primary--text">
                  <!-- Hidden File Input -->
                  <v-file-input
                    v-model="files"
                    multiple
                    hide-input
                    style="display: none"
                    ref="fileInput"
                    @change="previewImages"
                  />

                  <v-btn
                    small
                    color="primary"
                    elevation="0"
                    @click="triggerFileInput"
                  >
                    <v-icon left>mdi-upload</v-icon> Upload Files
                  </v-btn>

                  <v-menu offset-y>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        small
                        elevation="0"
                        color="grey lighten-3"
                        v-bind="attrs"
                        v-on="on"
                      >
                        Files ({{ imagePreviews.length }})
                      </v-btn>
                    </template>

                    <div
                      style="background: white"
                      v-for="(image, index) in imagePreviews"
                      :key="index"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <a
                        class="px-2 text-decoration-none text-center"
                        :href="image"
                        target="_blank"
                      >
                        <small> File {{ index + 1 }}</small>
                      </a>
                    </div>
                  </v-menu>
                </span>
              </v-col>
              <v-col cols="12" v-if="errorResponse">
                <span class="red--text">{{ errorResponse }}</span>
              </v-col>
              <v-col cols="12">
                <v-btn small color="primary" @click="submit"> Submit </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  props: ["endpoint", "model"],

  data() {
    return {
      selectedItem: null,
      searchDialogKey: 1,
      current_date: new Date(
        Date.now() - new Date().getTimezoneOffset() * 60000
      )
        .toISOString()
        .substr(0, 10),
      menu2: false,

      payload: {
        is_admin_expense: 0,
        vendor_id: 1,
        notes: "",
        tax: 0,
        bill_number: "",
        bill_date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),

        items: [
          {
            detail: "Add Item",
            rate: 0,
            qty: 0,
            tax: 0,
            amount: 0,
          },
        ],
        attachments: [],

        sub_total: 0,
        discount: 0,
        total: 0,
      },

      defaultPayload: {
        vendor_id: 1,
        notes: "",
        tax: 0,
        bill_number: "",
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
        { text: `Qty`, value: `qty`, align: `center`, width: "100px" },
        { text: `Rate`, value: `rate`, align: `right` },
        { text: `tax`, value: `tax`, align: `right` },
        { text: `Amount`, value: `amount`, align: `right` },
        { text: ``, value: `action`, align: `center`, width: "30px" },
      ],
      vendorObject: null,
      vendorEditItem: null,
      lastThreeRecords: [],
      emptyRowLength: 3,
      ReceiptNumber: null,
      files: [], // Holds the uploaded files
      imagePreviews: [],
    };
  },
  watch: {},
  computed: {
    displayedFiles() {
      return this.files.slice(0, 2); // Show only first 2 files
    },
    detailContainerHeight() {
      const minHeight = {
        Personal: "450px",
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

    this.getVendors();
  },
  methods: {
    calculateAmount(item) {
      let subAmount = (item.qty || 0) * (item.rate || 0);
      // let percent = item.tax > 0 ? subAmount + (subAmount * (item.tax || 0) / 100) : subAmount
      item.amount =  subAmount;

      this.calculateOverAll();
    },
    previewImages() {
      this.imagePreviews = [];
      if (this.files.length) {
        // this.files.forEach((file) => {
        //   if (file.type.startsWith("image/")) {
        //     const reader = new FileReader();
        //     reader.onload = (e) => {
        //       this.imagePreviews.push(e.target.result);
        //     };
        //     reader.readAsDataURL(file);
        //   }
        // });

        if (this.files.length) {
          this.files.forEach((file) => {
            const blobUrl = URL.createObjectURL(file); // Convert file to Blob URL
            this.imagePreviews.push(blobUrl);
          });
        }
      }
    },
    triggerFileInput() {
      this.$refs.fileInput.$el.querySelector("input").click();
    },
    addItem() {
      this.payload.items.push({
        detail: "Add Item",
        rate: 0,
        qty: 0,
        tax: 0,
        amount: 0,
      });
    },
    getVendors() {
      this.$axios
        .get(`vendor-list`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          if (data) {
            this.vendors = data.map((item) => ({
              ...item,
              full_name: `${item.first_name} ${item.last_name}`,
            }));
          } else {
            this.vendors = [];
          }
        })
        .catch(() => console.log(e));
    },
    resetDialog() {
      this.searchDialogKey++;
    },
    closePopup() {
      this.dialog = false;
      this.$emit("close");
    },
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
      this.searchDialogKey++;
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
      this.payload = this.defaultPayload;
      this.files = [];
    },
    deleteItem(index) {
      this.payload.items.splice(index, 1);
    },
    async submit() {
      this.errorResponse = null;
      if (!this.payload.items.length) {
        alert(`Item is not added.`);
        return;
      }
      this.loading = true;
      try {
        this.payload.vendor_id = this.selectedItem.id;
        this.payload.is_admin_expense = this.is_admin_expense;
        this.payload.company_id = this.$auth.user.company_id;
        let { data } = await this.$axios.post(`/admin-expense`, this.payload);

        if (this.files.length > 0 && data && data.record) {
          await this.processAttachments(data.record.id);
          return;
        }

        this.close();
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
    async processAttachments(id) {
      let files = this.files;

      const formData = new FormData();

      // Append each file to the formData object
      files.forEach((file) => {
        formData.append("files[]", file); // 'files[]' is the key expected by Laravel
      });

      this.$axios
        .post(`/expense-upload-files/${id}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Accept: "application/json", // You can adjust this based on your API
          },
        })
        .then(({ data }) => {
          this.close();
          this.$emit("response", "Record has been inserted");
        })
        .catch((error) => {
          console.error("Error uploading files:", error);
          this.errorResponse =
            error?.response?.data?.message || "Unknown error";
          this.loading = false;
        });
    },
  },
};
</script>
