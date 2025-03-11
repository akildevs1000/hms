<template>
  <v-dialog v-model="dialog" width="800">
    <style scoped>
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
    <AssetsIconClose left="790" @click="closePopup()" />

    <v-card>
      <v-alert dense flat dark class="primary">New Expense</v-alert>
      <v-card-text>
        <v-container>
          <v-row class="pa-3">
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
                v-model="selectedVendor"
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
                v-if="selectedVendor"
                class="grey lighten-2 mt-2 pa-4"
              >
                <v-row no-gutters
                  ><v-col v-if="selectedVendor.type != 'Personal'">
                    <strong>Company:</strong>
                    {{ selectedVendor.company_name }}</v-col
                  >
                  <v-col cols="4">
                    <strong>Account Type:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.type }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Category:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor?.vendor_category?.name }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Email:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.email }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Work Phone:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.work_phone }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Mobile:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.mobile }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Tax Number:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.tax_number }}
                  </v-col>
                  <v-col cols="4">
                    <strong>Address:</strong>
                  </v-col>
                  <v-col cols="8">
                    {{ selectedVendor.country }} {{ selectedVendor.state }}
                    {{ selectedVendor.city }} {{ selectedVendor.zip_code }}
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
                      style="border: 1px solid #dddddd; padding: 8px"
                      class="primary--text border-top border-bottom"
                    >
                      Item Details
                    </td>
                    <td
                      style="border: 1px solid #dddddd; padding: 8px"
                      class="primary--text border-top border-bottom"
                    >
                      Qty
                    </td>
                    <td
                      style="border: 1px solid #dddddd; padding: 8px"
                      class="primary--text border-top border-bottom text-right"
                    >
                      Rate
                    </td>
                    <td class="primary--text border-top border-bottom">Tax</td>
                    <td
                      style="border: 1px solid #dddddd; padding: 8px"
                      class="primary--text text-right"
                    >
                      Amount
                    </td>
                    <td style="border: 1px solid #dddddd; padding: 8px"></td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in payload.items" :key="index">
                    <td
                      style="
                        width: 500px;
                        border: 1px solid #dddddd;
                        padding: 8px;
                      "
                      class="text-color"
                    >
                      <div style="width: 100%">
                        <textarea
                          rows="1"
                          class="input-no-border"
                          type="text"
                          v-model="item.detail"
                        ></textarea>
                      </div>
                    </td>
                    <td
                      style="
                        width: 100px;
                        border: 1px solid #dddddd;
                        padding: 8px;
                      "
                      class="text-color"
                    >
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
                      style="
                        width: 100px;
                        border: 1px solid #dddddd;
                        padding: 8px;
                      "
                      class="text-color text-right"
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
                      style="
                        border: 1px solid #dddddd;
                        padding: 8px;
                        max-width: 250px;
                        min-width: 150px;
                      "
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
                      style="
                        min-width: 100px;
                        max-width: 100px;
                        border: 1px solid #dddddd;
                        padding: 8px;
                      "
                      class="text-color text-right"
                    >
                      <div>{{ item.amount }}</div>
                    </td>
                    <td
                      style="border: 1px solid #dddddd; padding: 8px"
                      class="text-color text-center"
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
                  <table cellspacing="0" style="width: 100%" class="mt-2">
                    <tr>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="text-color pa-1"
                      >
                        Sub Total:
                      </td>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="text-right text-color pa-1"
                      >
                        {{ $utils.currency_format(payload.sub_total) }}
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="text-color pa-1"
                      >
                        Tax:
                      </td>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="text-right text-color pa-1"
                      >
                        {{ $utils.currency_format(payload.tax) }}
                      </td>
                    </tr>
                    <tr>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="primary--text pa-1"
                      >
                        Total:
                      </td>
                      <td
                        style="border: 1px solid #dddddd;"
                        class="text-right primary--text pa-1"
                      >
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
                  v-model="filesToUpload"
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
                    v-for="(image, index) in imagePreviews"
                    :key="index"
                    class="d-flex align-center justify-space-between px-2 py-1"
                    style="background: white"
                  >
                    <a
                      class="text-decoration-none text-center flex-grow-1"
                      :href="image"
                      target="_blank"
                    >
                      <small>File {{ index + 1 }}</small>
                    </a>

                    <v-icon
                      small
                      color="red"
                      class="ml-2"
                      @click.stop="removeImage(index)"
                    >
                      mdi-close
                    </v-icon>
                  </div>
                </v-menu>
              </span>
            </v-col>
            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn small color="grey lighten-3" @click="submit">
                Close
              </v-btn>
              <v-btn small color="primary" @click="submit"> Submit </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
let initialPayload = {
  is_admin_expense: 0,
  vendor_id: 0,
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
};
export default {
  props: ["endpoint", "model"],

  data() {
    return {
      selectedVendor: null,
      searchDialogKey: 1,
      current_date: new Date(
        Date.now() - new Date().getTimezoneOffset() * 60000
      )
        .toISOString()
        .substr(0, 10),
      menu2: false,

      payload: initialPayload,

      defaultPayload: initialPayload,

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
      filesToUpload: [],
      files: [], // Holds the uploaded files
      imagePreviews: [],
    };
  },
  watch: {},
  computed: {
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
    removeImage(index) {
      this.imagePreviews.splice(index, 1);
      this.files.splice(index, 1);
    },
    calculateAmount(item) {
      let subAmount = (item.qty || 0) * (item.rate || 0);
      // let percent = item.tax > 0 ? subAmount + (subAmount * (item.tax || 0) / 100) : subAmount
      item.amount = subAmount;

      this.calculateOverAll();
    },

    previewImages(event) {
      if (!event || !event.length) return;

      Array.from(event).forEach((file) => {
        this.files.push(file); // Append new files
        const blobUrl = URL.createObjectURL(file);
        this.imagePreviews.push(blobUrl); // Create preview
      });
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
      this.$emit("close");
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
      this.payload = this.defaultPayload;
      this.files = [];
      this.imagePreviews = [];
      this.selectedVendor = null;
      this.dialog = false;
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
        this.payload.vendor_id = this.selectedVendor?.id ?? null;
        this.payload.company_id = this.$auth.user.company_id;
        let { data } = await this.$axios.post(`/admin-expense`, this.payload);

        if (this.files.length > 0 && data && data.record) {
          await this.processAttachments(data.record.id);
          return;
        }

        this.closePopup();
        this.$emit("response", "Expense has been inserted");
      } catch (error) {
        console.log("🚀 ~ submit ~ error:", error)
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
          this.closePopup();
          this.$emit("response", "Expense has been inserted");
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
