<template>
  <v-dialog v-model="PostingDialog" width="550">
    <AssetsIconClose left="540" @click="PostingDialog = false" />
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon x-small color="primary">mdi-eye</v-icon>
        View</span
      >
    </template>

    <v-card>
      <v-alert class="grey lighten-3 primary--text" flat dense>
        View Room
      </v-alert>

      <v-card-text>
        <v-row class="">
          <v-col cols="4">
            <v-text-field
              style="font-size: 13px; height: 10px"
              v-model="item.room_type"
              readonly
              label="Room Type"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="item.room_no"
              readonly
              label="Room Number"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="item.no_of_adult"
              readonly
              label="Adult"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="item.no_of_child"
              readonly
              label="Child"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>

          <v-col cols="4">
            <v-text-field
              v-model="formattedCheckIn"
              readonly
              label="Check In"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="formattedCheckOut"
              readonly
              label="Check Out"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="booking.total_days"
              readonly
              label="Nights"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>

          <v-col cols="7">
            <table class="simple-table">
              <tbody>
                <tr>
                  <td class="border-top border-bottom py-1 text-left">
                    Room with tax
                  </td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{ $utils.currency_format(item.price) }}
                  </td>
                </tr>

                <tr>
                  <td class="border-top border-bottom py-1 text-left">Meal</td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{ $utils.currency_format(item.food_plan_price) }}
                  </td>
                </tr>
                <tr>
                  <td class="border-top border-bottom py-1 text-left">
                    Extra Bed
                  </td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{ $utils.currency_format(item.bed_amount) }}
                  </td>
                </tr>
                <tr>
                  <td class="border-top border-bottom py-1 text-left">
                    Early Check In
                  </td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{ $utils.currency_format(item.early_check_in) }}
                  </td>
                </tr>
                <tr>
                  <td class="border-top border-bottom py-1 text-left">
                    Late Check Out
                  </td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{ $utils.currency_format(item.late_check_out) }}
                  </td>
                </tr>

                <tr>
                  <td class="border-top border-bottom py-1 text-left">Add</td>
                  <td class="border-top border-bottom py-1 text-right">
                    {{
                      $utils.currency_format(
                        parseFloat(booking.room_extra_amount) / totalRooms
                      )
                    }}
                  </td>
                </tr>
                <tr>
                  <td class="border-top border-bottom py-1 text-left">
                    Discount
                  </td>
                  <td
                    class="border-top border-bottom py-1 text-right red--text"
                  >
                    -{{
                      $utils.currency_format(
                        parseFloat(booking.room_discount) / totalRooms
                      )
                    }}
                  </td>
                </tr>
              </tbody>
            </table>
          </v-col>
          <v-col cols="5" class="text-center">
            <v-card outlined>
              <v-card-text>
                <div class="blue--text" style="font-size: 18px">
                  {{ $dateFormat.dmy(item.date) || "---" }}
                </div>
                <div style="font-size: 14px">
                  {{ item.day || "---" }}
                </div>

                <div class="py-4" style="font-size: 14px">
                  {{ item.day_type || "---" }}
                </div>

                <div style="font-size: 14px">Total Rs</div>
                <div class="blue--text" style="font-size: 18px">
                  {{ $utils.currency_format(totalPrice) || "---" }}
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "booking", "totalRooms"],
  data() {
    return {
      PostingDialog: false,
      items: [],
    };
  },
  computed: {
    formattedCheckIn() {
      return this.booking.check_in + " 12:00";
    },
    formattedCheckOut() {
      return this.booking.check_in + " 11:00";
    },
    totalPrice() {
      let ext = this.booking.room_extra_amount / this.totalRooms;
      let dis = this.booking.room_discount / this.totalRooms;

      let price = this.item.price;
      let food_plan_price = this.item.food_plan_price;
      let bed_amount = this.item.bed_amount;
      let early_check_in = this.item.early_check_in;
      let late_check_out = this.item.late_check_out;

      return (
        price +
        food_plan_price +
        bed_amount +
        early_check_in +
        late_check_out +
        ext -
        dis
      );
    },
  },
  methods: {
    // getTotalAmount() {
    //   return this.items.reduce((total, num) => total + num.amount_with_tax, 0);
    // },
  },
};
</script>
