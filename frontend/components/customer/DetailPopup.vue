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
      <span v-bind="attrs" v-on="on">
        <v-icon small color="primary">mdi-eye</v-icon></span
      >
    </template>

    <v-card>
      <v-row no-gutter class="pa-0 grey lighten-3">
        <v-col cols="12" class="text-right">
          <v-icon color="primary" @click="PostingDialog = false"
            >mdi-close-circle</v-icon
          >
        </v-col>
      </v-row>
      <v-card-text class="pa-3">
        <v-row class="grey lighten-3">
          <!-- <v-col cols="12" class="text-right">
            <v-icon color="primary">mdi-close-circle</v-icon>
          </v-col> -->
          <v-col cols="4">
            <v-container>
              Name: <b> {{ full_name }}</b>
              <br />
              Room No: {{ room_no || "---" }}
            </v-container>
          </v-col>
          <v-col cols="4" class="text-center">
            <v-container>
              <b>ROOM POSTING</b>
            </v-container>
          </v-col>
          <v-col cols="3" class="text-left">
            <v-container>
              Bill No: {{ bill_no || "---" }}
              <br />
              Date: {{ items[0]?.created_at || "---" }}
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
              </tr>
            </thead>
            <tbody>
              <tr v-for="(posting, index) in fitleredItems" :key="index">
                <td>{{ index + 1 }}</td>
                <td style="width: 200px">
                  {{ posting.tax_type }}
                </td>
                <td style="width: 150px">
                  {{ posting.item }}
                </td>
                <td style="width: 80px">
                  {{ posting.qty }}
                </td>
                <td style="width: 80px" class="text-center">
                  {{ $utils.currency_format(posting.single_amt) }}
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
              </tr>
              <tr>
                <td
                  style="border: none; padding: 0"
                  class="text-left pt-5"
                  colspan="5"
                ></td>
                <td colspan="2" class="text-right blue--text">Total Rs,</td>
                <td class="blue--text">
                  {{ $utils.currency_format(getTotalAmount()) }}
                </td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["items", "full_name", "bill_no", "room_no"],
  data() {
    return {
      PostingDialog: false,
      postings: [],
    };
  },
  computed: {
    fitleredItems() {
      return this.items.filter((e) => e.bill_no == this.bill_no);
    },
  },
  methods: {
    getTotalAmount() {
      return this.items.reduce((total, num) => total + num.amount_with_tax, 0);
    },
  },
};
</script>
