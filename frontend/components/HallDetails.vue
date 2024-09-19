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
        <v-icon x-small color="primary">mdi-eye</v-icon>
        <small style="font-size: 11px">View</small></span
      >
    </template>

    <v-card>
      <v-toolbar class="grey lighten-3 primary--text" flat dense>
        <div style="font-size: 18px">View Hall</div>
        <v-spacer></v-spacer
        ><AssetsButtonClose @close="PostingDialog = false" />
      </v-toolbar>

      <v-card-text class="pa-3">
        <v-container>
          <v-row class="">
            <v-col cols="4">
              <v-text-field
                v-model="booking.purpose"
                readonly
                label="Function Name"
                dense
                outlined
                hide-details
              ></v-text-field>
            </v-col>
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
                v-model="booking.check_in"
                readonly
                label="Check In"
                dense
                outlined
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="booking.check_out"
                readonly
                label="Check Out"
                dense
                outlined
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="4">
              <v-text-field
                v-model="booking.total_booking_hours"
                readonly
                label="Hours"
                dense
                outlined
                hide-details
              ></v-text-field>
            </v-col>
            <v-col cols="7">
              <table class="simple-table">
                <tbody>
                  <tr>
                    <td class="text-left"><small>Hall Rent</small></td>
                    <td class="text-right">
                      <small> {{ $utils.currency_format(item.price) }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Meal</small></td>
                    <td class="text-right">
                      <small>
                        {{
                          $utils.currency_format(
                            item.price_with_meal - item.price
                          )
                        }}</small
                      >
                    </td>
                  </tr>

                  <tr>
                    <td class="text-left"><small>Extra hours</small></td>
                    <td class="text-right">
                      <small>{{
                        $utils.currency_format(
                          item.extra_booking_hours_charges
                        ) || "---"
                      }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Cleaning</small></td>
                    <td class="text-right">
                      <small>{{ $utils.currency_format(item.cleaning) || "---" }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Electricity</small></td>
                    <td class="text-right">
                     <small> {{ $utils.currency_format(item.electricity) || "---" }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Generator</small></td>
                    <td class="text-right">
                      <small>{{ $utils.currency_format(item.generator) || "---" }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Projector</small></td>
                    <td class="text-right">
                      <small>{{ $utils.currency_format(item.projector) || "---" }}</small>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-left"><small>Audio</small></td>
                    <td class="text-right">
                      <small>{{ $utils.currency_format(item.audio) || "---" }}</small>
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-col>
            <v-col cols="5" class="text-center">
              <v-card outlined style="height: 270px">
                <v-card-text>
                  <div class="blue--text mt-5" style="font-size: 18px">
                    {{ $dateFormat.dmy(item.date) || "---" }}
                  </div>
                  <div class="" style="font-size: 14px">
                    {{ $dateFormat.getMyDayOnly(item.date) || "---" }}
                  </div>

                  <div class="mt-7" style="font-size: 14px">
                    {{ item.day_type || "---" }}
                  </div>
                </v-card-text>

                <v-card-text class="pb-8" style="padding-top: 50px">
                  <div class="mt-2" style="font-size: 14px">Total Rs</div>
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
  computed: {},
  methods: {},
};
</script>
