<template>
  <v-dialog v-model="PostingDialog" width="650">
    <AssetsIconClose left="640" @click="PostingDialog = false" />
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
      <v-alert class="grey lighten-3 primary--text" flat dense>
        View Booking
      </v-alert>

      <v-card-text class="pa-3">
        <v-container>
          <v-row class="">
            <v-col cols="4">
              <v-text-field
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
                v-model="item.check_in"
                readonly
                label="Check In"
                dense
                outlined
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="item.check_out"
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
                    <td class="text-left">Room</td>
                    <td class="text-right">
                      {{ $utils.currency_format(item.price) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Meal</td>
                    <td class="text-right">
                      {{ $utils.currency_format(item.food_plan_price) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Extra Bed</td>
                    <td class="text-right">
                      {{ $utils.currency_format(item.bed_amount) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Early Check In</td>
                    <td class="text-right">
                      {{ $utils.currency_format(item.early_check_in) }}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Late Check Out</td>
                    <td class="text-right">
                      {{ $utils.currency_format(item.late_check_out) }}
                    </td>
                  </tr>
                  <!-- <tr>
                    <td class="text-left">Discount</td>
                    <td class="text-right">
                      {{
                        $utils.currency_format(item.room_discount)
                      }}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Add</td>
                    <td class="text-right">
                      {{
                        $utils.currency_format(item.room_extra_amount || 0)
                      }}
                    </td>
                  </tr> -->
                </tbody>
              </table>
            </v-col>
            <v-col cols="5" class="text-center">
              <v-card outlined>
                <v-card-text>
                  <div class="blue--text" style="font-size: 18px">
                    {{ $dateFormat.dmy(item.date) || "---" }}
                  </div>
                  <div class="" style="font-size: 14px">
                    {{ item.day || "---" }}
                  </div>

                  <div class="py-4" style="font-size: 14px">
                    {{ item.tariff || "---" }}
                  </div>
                  <div style="font-size: 14px">Total Rs</div>
                  <div class="blue--text" style="font-size: 18px">
                    {{
                      $utils.currency_format(
                        parseFloat(item.price) +
                          parseFloat(item.food_plan_price) +
                          parseFloat(item.bed_amount) +
                          parseFloat(item.early_check_in) +
                          parseFloat(item.late_check_out)
                      ) || "---"
                    }}
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
    // fitleredItems() {
    //   return this.items.filter((e) => e.bill_no == this.bill_no);
    // },
  },
  methods: {
    // getTotalAmount() {
    //   return this.items.reduce((total, num) => total + num.amount_with_tax, 0);
    // },
  },
};
</script>
