<template>
  <v-dialog v-model="PostingDialog" width="850">
    <style scoped>
      .simple-table {
        width: 100%;
        border-collapse: collapse;
      }
      .simple-table td {
        border-top: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        padding: 5px;
        text-align: center;
      }
    </style>
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on"> Posting </span>
    </template>

    <v-card>
      <v-card-text class="pa-3">
        <v-row class="grey lighten-3">
          <!-- <v-col cols="12" class="text-right">
            <v-icon color="primary">mdi-close-circle</v-icon>
          </v-col> -->
          <v-col cols="4">
            <v-container>
              Name: <b> {{ BookingData && BookingData.title }}</b>
              <br />
              Room No: {{ BookingData.room_no }}
            </v-container>
          </v-col>
          <v-col cols="4" class="text-center">
            <v-container>
              <b>ROOM POSTING</b>
            </v-container>
          </v-col>
          <v-col cols="4" class="text-left">
            <v-container>
              Rep No: 14275
              <br />
              Date: {{ $dateFormat.dmyhm() }}
            </v-container>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-text>
        <v-container class="pa-3">
          <table class="simple-table">
            <thead>
              <tr>
                <td class="blue--text">#</td>
                <td class="blue--text text-left">Category</td>
                <td class="blue--text text-left">Item Description</td>
                <td class="blue--text">Qty</td>
                <td class="blue--text">Unit</td>
                <td class="blue--text">Sub Total</td>
                <td class="blue--text">Tax</td>
                <td class="blue--text">Total</td>
                <td class="blue--text"></td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(posting, index) in postings" :key="index">
                <td>{{ index + 1 }}</td>
                <td style="width: 200px">
                  <v-autocomplete
                    v-model="posting.tax_type"
                    :items="[
                      { id: -1, name: 'select...' },
                      { name: 'Food' },
                      { name: 'Misc' },
                      { name: 'ExtraBed' },
                      { name: 'Others' },
                    ]"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                    hide-details
                    @change="calc(posting)"
                  ></v-autocomplete>
                </td>
                <td style="width: 150px">
                  <v-text-field
                    dense
                    outlined
                    v-model="posting.item"
                    hide-details
                  ></v-text-field>
                </td>
                <td style="width: 80px">
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.qty"
                    hide-details
                    @keyup="calc(posting)"
                  ></v-text-field>
                </td>
                <td style="width: 80px" class="text-center">
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="posting.single_amt"
                    @keyup="calc(posting)"
                    hide-details
                  ></v-text-field>
                </td>
                <td class="text-center">
                  {{ $utils.currency_format(posting.amount) }}
                </td>
                <td class="text-center">
                  {{ $utils.currency_format(posting.tax) }}
                </td>
                <td class="text-center">
                  {{ $utils.currency_format(posting.amount_with_tax) }}
                </td>
                <td class="blue--text">
                  <v-icon small color="red" @click="deleteItem(index)"
                    >mdi-close</v-icon
                  >
                </td>
              </tr>
              <tr>
                <td
                  style="border: none; padding: 0"
                  class="text-left pt-5"
                  colspan="5"
                >
                  <v-btn
                    color="grey lighten-3"
                    small
                    @click="addRow"
                    elevation="0"
                  >
                    <v-icon small color="primary"
                      >mdi-plus-circle-outline</v-icon
                    >&nbsp;New Row
                  </v-btn>
                </td>
                <td colspan="2" class="text-right blue--text">Total Rs,</td>
                <td class="blue--text">
                  {{ $utils.currency_format(totalAmount) }}
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </v-container>
        <v-row>
          <v-col cols="12" class="text-center">
            <v-hover v-slot:default="{ hover, props }">
              <span v-bind="props">
                <v-btn
                  small
                  :outlined="!hover"
                  rounded
                  color="red"
                  class="white--text"
                  @click="PostingDialog = false"
                  >Cancel</v-btn
                >
              </span>
            </v-hover>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <v-hover v-slot:default="{ hover, props }">
              <span v-bind="props">
                <v-btn
                  small
                  :outlined="!hover"
                  rounded
                  color="green"
                  class="white--text"
                  :loading="loading"
                  @click="store_posting"
                  >Submit</v-btn
                >
              </span>
            </v-hover>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["BookingData", "evenIid"],
  data() {
    return {
      PostingDialog: false,
      postings: [],
      totalAmount: 0,

      snackbar: false,
      response: "",
      preloader: false,
      loading: false,

      posting: {
        item: "",
        qty: "",
        amount: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      },

      errors: [],
    };
  },

  created() {
    this.preloader = false;
  },

  mounted() {},

  computed: {},
  methods: {
    deleteItem(index) {
      this.postings.splice(index, 1);
    },
    addRow() {
      this.postings.push({
        tax_type: "",
        item: "",
        qty: 0,
        single_amt: 0.0,
        amount: 0.0,
        tax: 0.0,
        amount_with_tax: 0.0,
      });
    },
    calc(posting) {
      // Determine tax rate based on the tax_type
      const taxRate = posting.tax_type === "Food" ? 5 : 12;

      // Ensure qty and single_amt are valid numbers, default to 0 if not
      const qty = parseFloat(posting.qty) || 0;
      const singleAmt = parseFloat(posting.single_amt) || 0;

      // Calculate amount without tax
      posting.amount = qty * singleAmt;

      // Calculate tax amount based on the tax rate
      const taxAbleAmount = (taxRate / 100) * posting.amount;

      // Calculate SGST and CGST as half of the total tax
      posting.sgst = parseFloat(taxAbleAmount) / 2 || 0;
      posting.cgst = parseFloat(taxAbleAmount) / 2 || 0;

      // Total tax
      posting.tax = taxAbleAmount || 0;

      // Calculate total amount including tax
      posting.amount_with_tax = parseFloat(posting.amount) + taxAbleAmount || 0;

      this.totalAmount = this.postings.reduce(
        (total, num) => total + num.amount_with_tax,
        0
      );

      // Optionally, you can limit decimal precision
    },

    store_posting() {
      this.loading = true;
      let completedRequests = 0; // To track completed requests
      let totalRequests = this.postings.length; // Total number of postings

      this.postings.forEach((posting, index) => {
        let payload = {
          ...posting,
          tax_type: posting.tax_type == "Food" ? 5 : 12,
          booked_room_id: this.evenIid,
          company_id: this.$auth.user.company.id,
          booking_id: this.BookingData.id,
          room_id: this.BookingData.room_id,
          room: this.BookingData.room_no,
          user_id: this.$auth.user.id,
        };

        this.$axios
          .post("/posting", payload)
          .then(({ data }) => {
            completedRequests++;
            if (!data.status) {
              this.errors = data.errors;
              this.loading = false;
            } else {
              if (completedRequests == totalRequests) {
                this.loading = false;
                this.postingDialog = false;
                this.snackbar = data.status;
                this.response = data.message;
                this.$swal("Sucees", "Postings has beed added", "success").then(
                  (e) => {
                    this.$emit("close-dialog", data);
                  }
                );
              }
            }
          })
          .catch((e) => {
            completedRequests++;
            console.log(e);
            console.log(completedRequests, `catch`);

            if (completedRequests == totalRequests) {
              this.loading = false;
            }
          });
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    reset_posting() {
      this.posting = {
        item: "",
        qty: "",
        amount: 0,
        single_amt: 0,
        bill_no: "",
        amount_with_tax: 0,
        tax: 0,
        sgst: 0,
        cgst: 0,
        tax_type: -1,
      };
    },
  },
};
</script>
