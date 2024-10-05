<template>
  <v-dialog v-model="PostingDialog" width="750">
    <AssetsIconClose left="740" @click="PostingDialog = false" />
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon small color="primary">mdi-eye</v-icon></span
      >
    </template>

    <v-card style="overflow: hidden">
      <v-row no-gutter class="pa-0 grey lighten-3">
        <v-col cols="12" class="text-right"> </v-col>
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
          <v-col cols="4" class="text-right">
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
          <AssetsTable
            height="300"
            :headers="[
              {
                text: `Category`,
                value: `tax_type`,
                align: `center`,
              },
              {
                text: `Item Description`,
                value: `item`,
                align: `center`,
              },
              {
                text: `Qty`,
                value: `qty`,
                align: `center`,
              },
              {
                text: `Unit`,
                value: `unit`,
                align: `right`,
              },
              {
                text: `Sub Total`,
                value: `amount`,
                align: `right`,
              },
              {
                text: `Tax`,
                value: `tax`,
                align: `right`,
              },
              {
                text: `Total`,
                value: `total`,
                align: `right`,
              },
            ]"
            :items="fitleredItems"
          >
            <template #unit="{ item }">
              {{ $utils.currency_format(item.single_amt) || "---" }}
            </template>
            <template #amount="{ item }">
              {{ $utils.currency_format(item.amount) || "---" }}
            </template>
            <template #tax="{ item }">
              {{ $utils.currency_format(item.tax) || "---" }}
            </template>
            <template #total="{ item }">
              {{ $utils.currency_format(item.amount_with_tax) }}
            </template>
            <template #row>
              <tr>
                <td
                  style="border: none; padding: 0"
                  class="text-left pt-5"
                  colspan="6"
                ></td>
                <td  class="text-right blue--text">
                  Total Rs, {{ $utils.currency_format(getTotalAmount()) }}
                </td>
              </tr>
            </template>
          </AssetsTable>
          <table class="simple-table">
            <tbody></tbody>
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
    };
  },
  computed: {
    fitleredItems() {
      return this.items.filter((e) => e.room.room_no == this.room_no);
    },
  },
  methods: {
    getTotalAmount() {
      return this.fitleredItems.reduce((total, num) => total + num.amount_with_tax, 0);
    },
  },
};
</script>
