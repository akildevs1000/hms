<template>
  <v-card dense>
    <v-card-text>
      <v-row no-gutter>
        <v-col> <v-icon left color="blue">mdi-laptop</v-icon> Online Booking </v-col>
        <v-col cols="12">
          <v-simple-table dense>
            <template v-slot:default>
              <tbody>
                <tr>
                  <td style="font-size: 11px">Booking.com</td>
                  <td style="font-size: 11px">12 AUG 24</td>
                  <td style="font-size: 11px">13 AUG 24</td>
                  <td style="font-size: 11px">2</td>
                </tr>
                <tr>
                  <td style="font-size: 11px">Website</td>
                  <td style="font-size: 11px">14 AUG 24</td>
                  <td style="font-size: 11px">16 AUG 24</td>
                  <td style="font-size: 11px">4</td>
                </tr>
                <tr>
                  <td style="font-size: 11px">Agoda</td>
                  <td style="font-size: 11px">12 AUG 24</td>
                  <td style="font-size: 11px">14 AUG 24</td>
                  <td style="font-size: 11px">1</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "OnlineBookingTable",
};
</script>
