<template>
  <v-dialog persistent v-model="itemDialog" max-width="400">
    <template v-slot:activator="{ on, attrs }">
      <v-hover v-slot:default="{ hover, props }">
        <span v-bind="props">
          <v-btn v-bind="attrs" v-on="on" x-small elevation="0"
            ><v-icon x-small color="primary">mdi-plus-circle</v-icon><small>New Row</small></v-btn
          >
        </span>
      </v-hover>
    </template>
    <v-card>
      <v-alert flat class="grey lighten-3" dense>
        <v-row no-gutter>
          <v-col>Item Information</v-col>
          <v-col class="text-right"><AssetsButtonClose @close="close" /></v-col>
        </v-row>
      </v-alert>
      <v-container class="pa-5">
        <v-row>
          <v-col cols="12">
            <v-text-field
              label="Item Details"
              dense
              outlined
              v-model="item.detail"
              hide-details
              required
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Qty"
              dense
              outlined
              v-model="item.qty"
              hide-details
              required
              type="number"
              @input="calculateAmount"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="Rate"
              dense
              outlined
              v-model="item.rate"
              hide-details
              required
              type="number"
              @input="calculateAmount"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              class="right-align"
              @input="calculateAmount"
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
          </v-col>

          <v-col cols="12">
            <v-text-field
              label="Amount"
              dense
              outlined
              v-model="item.amount"
              hide-details
              required
              type="number"
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="text-center">
            <AssetsButton
              :options="{
                color: `red`,
                label: `Cancel`,
              }"
              @click="close"
            />
            &nbsp;
            <AssetsButton
              :options="{
                color: `blue`,
                label: `Confirm`,
              }"
              @click="confirm"
            />
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["label"],
  data() {
    return {
      itemDialog: false,
      item: {
        detail: "test",
        rate: 0,
        qty: 0,
        tax: 0,
        amount: 0,
      },
      defaultItem: {
        detail: "test",
        rate: 0,
        qty: 0,
        tax: 0,
        amount: 0,
      },
    };
  },
  methods: {
    calculateAmount() {
      let quantity = this.item.qty || 0;
      let rate = this.item.rate || 0;
      let tax = this.item.tax || 0;
      let totalAmount = quantity * rate;
      this.item.amount = totalAmount;
    },
    confirm() {
      this.$emit("selectedItem", this.item);
      this.close();
    },
    close() {
      this.item = this.defaultItem;
      this.itemDialog = false;
    },
  },
};
</script>
