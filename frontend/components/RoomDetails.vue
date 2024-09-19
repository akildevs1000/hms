<template>
  <v-dialog v-model="PostingDialog" width="450">
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
        <v-icon x-small color="primary">mdi-eye</v-icon>
        <small style="font-size: 11px">View</small></span
      >
    </template>

    <v-card>
      <v-toolbar class="grey lighten-3 primary--text" flat dense>
        <div style="font-size: 18px">View Room</div>
        <v-spacer></v-spacer
        ><AssetsButtonClose @close="PostingDialog = false" />
      </v-toolbar>

      <v-card-text class="pa-3">
        <v-container>
          <v-row class="">
            <v-col cols="4">
              <v-text-field
                style="font-size: 13px;height: 10px;"
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
                    <td class="text-left"><small>Room</small></td>
                    <td class="text-right">
                      <small>{{ $utils.currency_format(item.price) }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Meal</small></td>
                    <td class="text-right">
                      <small>{{
                        $utils.currency_format(item.food_plan_price)
                      }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Extra Bed</small></td>
                    <td class="text-right">
                      <small>{{
                        $utils.currency_format(item.bed_amount)
                      }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Early Check In</small></td>
                    <td class="text-right">
                      <small>{{
                        $utils.currency_format(item.early_check_in)
                      }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Late Check Out</small></td>
                    <td class="text-right">
                      <small>{{
                        $utils.currency_format(item.late_check_out)
                      }}</small>
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
                    {{ $utils.currency_format(booking.total_price) || "---" }}
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  props: ["item", "booking"],
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
  },
  methods: {
    // getTotalAmount() {
    //   return this.items.reduce((total, num) => total + num.amount_with_tax, 0);
    // },
  },
};
</script>
