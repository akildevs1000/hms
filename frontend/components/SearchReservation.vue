<template>
  <v-dialog v-model="searchDialog" width="350">
    <template v-slot:activator="{ on, attrs }">
      <div style="width: 50px" class="ma-2 text-center">
        <v-hover v-slot:default="{ hover, props }">
          <span v-bind="props">
            <v-icon
              class="zoom-on-hover"
              v-bind="attrs"
              v-on="on"
              :outlined="!hover"
              :color="hover ? `` : `primary`"
              :style="{ color: hover ? '#6946dd' : '' }"
              >mdi-magnify</v-icon
            >
          </span>
        </v-hover>
      </div>
    </template>
    <v-card>
      <!-- <v-alert class="primary" dense dark>
        <v-row>
          <v-col> Customer </v-col>
          <v-col>
            <div class="text-right">
              <v-icon @click="searchDialog = false">mdi-close</v-icon>
            </div>
          </v-col>
        </v-row>
      </v-alert> -->

      <v-container>
        <v-text-field
          class="ma-0 pa-0"
          label="Enter Reservation No"
          dense
          outlined
          v-model="reservation_no"
          hide-details
        >
          <template v-slot:append>
            <v-icon
              style="
                border-radius: 5px;
                padding: 7px;
                top: 0px;
                position: absolute;
                right: 1px;
                height: 31px;
              "
              class="primary white--text"
              :loading="checkLoader"
              @click="searchBookingId"
              >mdi-magnify</v-icon
            >
          </template>
        </v-text-field>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  data: () => ({
    searchDialog: false,
    checkLoader: false,
    reservation_no: null,
  }),
  created() {},
  methods: {
    searchBookingId() {
      this.response = "";
      if (this.reservation_no == "") {
        return false;
      }
      let lostItemId = "";
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          lostItemId: lostItemId,
        },
      };

      this.$axios
        .post(
          `lost_and_found_items/search_by_reference/${this.reservation_no}`,
          options.params
        )
        .then(({ data }) => {
          let booking = data.booking;

          if (booking) {
            if (booking.booking_status < 0) {
              alert(
                "Check-in is not avaialle. You can not add Missing Item details"
              );
            }
          } else {
            alert("Booking Details are not avaialbe");
          }
          this.$emit("reservation", booking);
          this.reservation_no = null;
          this.searchDialog = false;
        });
    },
  },
};
</script>
