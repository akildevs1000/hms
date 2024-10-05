<template>
  <span>
    <AssetsTable
      :headers="[
        {
          text: 'Staff',
          value: 'staff',
          align: 'center',
        },
        {
          text: 'Date',
          value: 'date',
          align: 'center',
        },
        {
          text: 'payment Mode',
          value: 'payment_mode',
          align: 'center',
        },
        {
          text: 'Reference',
          value: 'reference_number',
          align: 'center',
        },
        {
          text: 'Description',
          value: 'desc',
          align: 'center',
        },
        {
          text: 'Debit',
          value: 'debit',
          align: 'right',
        },
        {
          text: 'Credit',
          value: 'credit',
          align: 'right',
        },
        {
          text: 'Balance',
          value: 'balance',
          align: 'right',
        },
      ]"
      :items="transactions"
    >
      <template #staff="{ item }">
        {{ item.user?.name || "---" }}
        <br />
        {{ item.user?.last_name }}
      </template>
      <template #payment_mode="{ item }">
        {{ (item && item.payment_mode && item.payment_mode.name) || "---" }}
      </template>
      <template #debit="{ item }">
        {{
          item && item.debit == 0 ? "---" : $utils.currency_format(item.debit)
        }}
      </template>
      <template #credit="{ item }">
        {{
          item && item.credit == 0 ? "---" : $utils.currency_format(item.credit)
        }}
      </template>
      <template #balance="{ item }">
        {{
          item && item.balance == 0
            ? "---"
            : $utils.currency_format(item.balance)
        }}
      </template>
      <template #row>
        <tr style="font-size: 13px">
          <td colspan="7" class="text-right blue--text">Balance</td>
          <td class="text-right red--text">
            {{ $utils.currency_format(totalTransactionAmount) }}
          </td>
        </tr>
      </template>
    </AssetsTable>

    <v-row>
      <v-col>
        <v-tabs right dense>
          <v-tab>Payment</v-tab>
          <v-tab>Rooms</v-tab>
          <v-tab-item>
            <v-card outlined>
              <v-container>
                <v-row>
                  <v-col cols="8">
                    <v-row>
                      <v-col cols="4">
                        <v-autocomplete
                          label="Mode"
                          v-model="payment.payment_mode"
                          :items="[
                            'Cash',
                            'Card',
                            'Online',
                            'Bank',
                            'UPI',
                            'Cheque',
                          ]"
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
                          v-model="totalTransactionAmount"
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
          </v-tab-item>
          <v-tab-item>
            <v-card outlined>
              <v-container v-if="bookedRooms.length">
                <v-row no-gutters>
                  <v-col
                    cols="1"
                    v-for="(room, index) in bookedRooms"
                    :key="index"
                    dark
                  >
                    <div
                      :class="getRelatedClass(room.booking_status)"
                      style="border-radius: 5px"
                      class="text-center white--text pa-1 ma-1"
                    >
                      {{ room.room_no }}
                    </div>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>
  </span>
</template>
<script>
export default {
  props: ["transactions", "totalTransactionAmount","bookedRooms"],
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
  methods: {
    setAfterDiscount(discount) {
      this.payment.after_discount_balance =
        parseFloat(this.totalTransactionAmount) - parseFloat(discount);
    },
    setNewBalance({ after_discount_balance, paid }) {
      this.payment.balance =
        parseFloat(after_discount_balance) - parseFloat(paid);
    },
    submit() {
      // let full_payment = parseFloat(this.full_payment);
      // if (full_payment <= 0) {
      //   this.alert("Warning", "Payment should be greater than zero","error");
      //   return;
      // }
      let payload = {
        grand_remaining_price: this.grand_remaining_price,
        new_advance: parseFloat(this.full_payment),
        reference_number: this.reference,
        booking_id: this.BookingData.id,
        remaining_price: this.remaining_price,
        payment_mode_id: this.payment_mode_id,
        company_id: this.$auth.user.company.id,
        user_id: this.$auth.user.id,
      };

      // this.loading = true;
      this.$axios
        .post("/paying_advance", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.loading = false;
            this.$emit("close-dialog");
            this.$swal("Success!", "Payment has been done", "success");
          }
        })
        .catch((e) => console.log(e));
    },
    getRelatedClass(status_id) {
      let status = {
        0: "available",
        1: "booked",
        2: "occupied",
        3: "checked_out",
        4: "blocked",
      };

      return status[status_id || ""];
    },
  },
};
</script>
