<template>
  <v-dialog v-model="PostingDialog" width="650">
    <AssetsIconClose left="640" @click="closeDialog" />

    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon small color="primary">mdi-eye</v-icon>
      </span>
    </template>

    <v-card>
      <v-alert class="grey lighten-3 primary--text" flat dense>
        View Booking
      </v-alert>

      <v-card-text class="pa-3">
        <v-container>
          <v-row>
            <v-col cols="4">
              <v-text-field
                v-model="item.room_type"
                readonly
                label="Room Type"
                dense
                outlined
                hide-details
              />
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="item.room_no"
                readonly
                label="Room Number"
                dense
                outlined
                hide-details
              />
            </v-col>
            <v-col cols="2">
              <v-text-field
                v-model="item.no_of_adult"
                readonly
                label="Adult"
                dense
                outlined
                hide-details
              />
            </v-col>
            <v-col cols="2">
              <v-text-field
                v-model="item.no_of_child"
                readonly
                label="Child"
                dense
                outlined
                hide-details
              />
            </v-col>

            <v-col cols="4">
              <v-text-field
                v-model="item.check_in"
                readonly
                label="Check In"
                dense
                outlined
                hide-details
              />
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="item.check_out"
                readonly
                label="Check Out"
                dense
                outlined
                hide-details
              />
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="booking.total_days"
                readonly
                label="Nights"
                dense
                outlined
                hide-details
              />
            </v-col>

            <v-col cols="7">
              <table class="simple-table">
                <tbody>
                  <tr>
                    <td class="text-left">Room Price</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.price || 0))}}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">SGST</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.sgst || 0))}}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">CGST</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.cgst || 0))}}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Extra Bed</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.bed_amount || 0))}}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Early Check In</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.early_check_in || 0))}}
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left">Late Check Out</td>
                    <td class="text-right">
                      {{ $utils.currency_format(parseFloat(item.late_check_out || 0))}}
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-col>
            <v-col cols="5" class="text-center">
              <v-card outlined>
                <v-card-text>
                  <div class="blue--text text-lg">{{ formattedDate }}</div>
                  <div class="text-sm">{{ item.day || "---" }}</div>
                  <div class="py-7 text-sm">{{ item.tariff || "---" }}</div>
                  <div class="text-sm">Total Rs</div>
                  <div class="blue--text text-lg">
                    {{ totalPrice }}
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
    };
  },
  computed: {
    formattedDate() {
      return this.$dateFormat.dmy(this.item.date) || "---";
    },
    totalPrice() {
      const keys = [
        "after_discount",
        "bed_amount",
        "early_check_in",
        "late_check_out",
      ];
      const total = keys.reduce(
        (sum, key) => sum + parseFloat(this.item[key] || 0),
        0
      );
      return this.$utils.currency_format(total) || "---";
    },
  },
  methods: {
    closeDialog() {
      this.PostingDialog = false;
    },
  },
};
</script>

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
.text-lg {
  font-size: 18px;
}
.text-sm {
  font-size: 14px;
}
</style>
