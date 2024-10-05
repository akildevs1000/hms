<template>
  <span>
    <AssetsTable
      height="150"
      :headers="[
        {
          text: `Date`,
          value: `posting_date`,
          align: `center`,
        },
        {
          text: `Room Type`,
          value: `room_type`,
          align: `center`,
        },
        {
          text: `Room`,
          value: `room`,
          align: `center`,
        },
        {
          text: `Amount`,
          value: `amount`,
          align: `right`,
        },
        {
          text: `Sgst`,
          value: `sgst`,
          align: `right`,
        },

        {
          text: `Cgst`,
          value: `cgst`,
          align: `right`,
        },

        {
          text: `Total`,
          value: `total`,
          align: `right`,
        },

        {
          text: ``,
          value: `action`,
          align: `center`,
          width: `10px`,
        },
      ]"
      :items="items"
    >
      <template #posting_date="{ item }">
        {{ $dateFormat.dmy(item.posting_date) || "---" }}
      </template>

      <template #room_type="{ item }">
        {{
          (item.room && item.room.room_type && item.room.room_type.name) ||
          "---"
        }}
      </template>
      <template #room="{ item }">
        {{ (item.room && item.room.room_no) || "---" }}
      </template>
      <template #amount="{ item }">
        {{ $utils.currency_format(item.single_amt) || "---" }}
      </template>
      <template #sgst="{ item }">
        {{ $utils.currency_format(item.sgst) || "---" }}
      </template>
      <template #cgst="{ item }">
        {{ $utils.currency_format(item.cgst) || "---" }}
      </template>
      <template #total="{ item }">
        {{ $utils.currency_format(item.amount_with_tax) || "---" }}
      </template>
      <template #action="{ item }">
        <CustomerDetailPopup
          :bill_no="item.bill_no"
          :room_no="item?.room?.room_no"
          :full_name="full_name"
          :items="postings"
        />
      </template>
    </AssetsTable>
    <v-card v-if="room_no > 0" outlined class="mt-5">
      <v-container>
        <v-row>
          <v-col cols="8">
            <v-row>
              <v-col cols="4">
                <v-autocomplete
                  label="Mode"
                  v-model="payment.payment_mode"
                  :items="['Cash', 'Card', 'Online', 'Bank', 'UPI', 'Cheque']"
                  item-text="name"
                  item-value="id"
                  dense
                  outlined
                  hide-details
                ></v-autocomplete>
              </v-col>
              <v-col cols="8">
                <v-text-field
                  label="Reference"
                  dense
                  outlined
                  type="text"
                  v-model="payment.reference"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                readonly
                  v-model="old_balance"
                  label="Balance"
                  outlined
                  dense
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="payment.discount"
                  label="Discount"
                  outlined
                  dense
                  hide-details
                  @keyup="setAfterDiscount(payment.discount)"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                readonly
                  v-model="payment.after_discount_balance"
                  label="After Discount"
                  outlined
                  dense
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col>
                <v-text-field
                  v-model="payment.paid"
                  label="Amount to Pay"
                  outlined
                  dense
                  hide-details
                  @keyup="setNewBalance(payment)"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-col>
          <v-col
            cols="4"
            class="d-flex align-center justify-center text-center"
          >
            <v-btn :color="'blue'" class="white--text" @click="submit">
              <small>{{ "PAY" }}</small>
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </span>
</template>
<script>
export default {
  props: ["postings", "full_name", "room_no", "otherPayload", "old_balance"],
  data: () => ({
    payment: {
      paid: 0,
      balance: 0,
      payment_mode: "Cash",
      reference: "REF123456",
      discount: 0,
      after_discount_balance: 0,
    },
  }),
  watch: {
    // Detect changes in room_no
    room_no(newRoomNo, oldRoomNo) {
      if (newRoomNo !== oldRoomNo) {
        this.payment.balance = this.items.reduce(
          (acc, cur) => acc + cur.amount_with_tax,
          0
        );
      }
    },
  },
  created() {
    this.payment = {
      ...this.payment,
      ...this.otherPayload,
    };
  },
  computed: {
    items() {
      if (this.room_no > 0) {
        return this.postings.filter((e) => e.room.room_no == this.room_no);
      }
      return this.postings;
    },
  },
  methods: {
    setAfterDiscount(discount) {
      this.payment.after_discount_balance =
        parseFloat(this.old_balance) - parseFloat(discount);
    },
    setNewBalance({ after_discount_balance, paid }) {
      this.payment.balance = parseFloat(after_discount_balance) - parseFloat(paid);
    },
    async submit() {
      try {
        await this.$axios.post(`posting-payment`, this.payment);
        this.$emit("response");
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>
