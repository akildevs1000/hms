<template>
  <v-dialog v-model="PostingDialog" width="650">
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
      <v-toolbar class="grey lighten-3 primary--text" flat dense>
        <div style="font-size: 18px">View Booking</div>
        <v-spacer></v-spacer
        ><v-icon @click="PostingDialog = false" color="primary"
          >mdi-close-circle</v-icon
        ></v-toolbar
      >

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
              label="Check Out"
              dense
              outlined
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="booking.total_days"
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
                  <td class="text-right">{{ item.total }}</td>
                </tr>
                <tr>
                  <td class="text-left">Meal</td>
                  <td class="text-right">
                    {{
                      item?.foodplan?.title +
                        " (" +
                        item?.foodplan?.unit_price +
                        ") " || "---"
                    }}
                  </td>
                </tr>
                <tr>
                  <td class="text-left">Extra Bed</td>
                  <td class="text-right">{{ item.bed_amount || "---" }}</td>
                </tr>
                <tr>
                  <td class="text-left">Early Check In</td>
                  <td class="text-right"> {{ item.early_check_in || "---" }}</td>
                </tr>
                <tr>
                  <td class="text-left">Late Check Out</td>
                  <td class="text-right"> {{ item.late_check_out || "---" }}</td>
                </tr>
                <tr>
                  <td class="text-left">Discount</td>
                  <td class="text-right">{{ item.room_discount || "---" }}</td>
                </tr>
                <tr>
                  <td class="text-left">Add</td>
                  <td class="text-right">{{ item.room_extra_amount || "---" }}</td>
                </tr>
              </tbody>
            </table>
          </v-col>
          <v-col cols="5" class="text-center">
            <v-card outlined>
              <v-container>
                <div class="blue--text mt-5" style="font-size: 18px">
                  {{ $dateFormat.dmy(item.date) || "---" }}
                </div>
                <div class="" style="font-size: 14px">
                  {{ item.day || "---" }}
                </div>

                <div class="mt-7" style="font-size: 14px">
                  {{ item.tariff || "---" }}
                </div>
              </v-container>

              <v-container class="pb-8">
                <div class="mt-2 " style="font-size: 14px">
                 Total Rs
                </div>
                <div class="blue--text" style="font-size: 18px">
                  {{ $utils.currency_format(booking.total_price) || "---" }}
                </div>
              </v-container>
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
