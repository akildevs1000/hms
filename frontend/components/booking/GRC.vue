<template>
  <div>
    <v-row>
      <v-col md="4">
        <h5>HYDERS PARK</h5>
        <span class="dheader-txt-span">{{ data && data.company && data.company.company_code || '---' }}</span>
      </v-col>
      <v-col md="4">
        <img :src="data && data.company && data.company.logo" height="100px" width="100" />
      </v-col>
      <v-col md="4">
        <div style="text-align:left; margin-left:70px" class="header-txt-address">
          <small> GST No: {{ data && data.company && data.company.mol_id || '---' }} </small><br>
          <small> Telephone No: {{
            data && data.company && data.company && data.company.contact && data.company.contact.number || '---'
          }} </small><br>
          <small> Email: {{
            data && data.company && data.company && data.company.user && data.company.user.email || '---'
          }}</small><br>
          <small> Guest Registration Date : {{ currentDate }}</small>
        </div>
      </v-col>
    </v-row>
    <v-divider></v-divider>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">Reservation Number:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box" style="float: left">
          <p> {{ data.reservation_no || '---' }} </p>
        </div>
      </v-col>
      <v-col md="3" class="text-right">
        <label for="name" class="text-right">Rooms:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box" style="float: left">
          <p> {{ data.rooms || '---' }} </p>
        </div>
      </v-col>
    </v-row>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">First Name:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box">
          <p> {{ data && data.customer && data.customer.first_name }} </p>
        </div>
      </v-col>
      <v-col md="3" class="text-right">
        <label for="name" class="label-txt">Last Name:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box pr-2 mr-2">
          <p> {{ data && data.customer && data.customer.last_name || '---' }} </p>
        </div>
      </v-col>
    </v-row>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">Arrival Date:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box">
          <p> {{ data && data.check_in || '---' }} </p>
        </div>
      </v-col>
      <v-col md="3" class="text-right">
        <label for="name" class="label-txt">Departure Name:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box pr-2 mr-2">
          <p> {{ data && data.check_out || '---' }} </p>
        </div>
      </v-col>
    </v-row>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">Room Type:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box">
          <p> {{ data && data.booked_rooms && data.booked_rooms[0].room_type || '---' }} </p>
        </div>
      </v-col>
      <v-col md="3" class="text-right">
        <label for="name" class="label-txt">Current Balance:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box pr-2 mr-2">
          <p> {{ trans['balance'] || '---' }} </p>
          <!-- <pre>{{ data }}</pre> -->
        </div>
      </v-col>
    </v-row>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">Room/Tagged
          Advance:</label>
      </v-col>
      <v-col md="9">
        <div class="text-box">
          <p> {{ trans['sumCredit'] || '---' }} </p>
        </div>
      </v-col>
    </v-row>
    <v-row class="row mt-3">
      <v-col md="3">
        <label for="name" class="label-txt">Approach Stay Cost:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box">
          <p> {{ trans['sumDebit'] || '---' }} </p>
        </div>
      </v-col>
      <v-col md="3" class="text-right">
        <label for="name" class="label-txt">Remaining Balance
          Due:</label>
      </v-col>
      <v-col md="3">
        <div class="text-box pr-2 mr-2">
          <p> {{ trans['balance'] || '---' }} </p>
          <!-- <pre>{{ data }}</pre> -->
        </div>
      </v-col>
    </v-row>
    <v-divider></v-divider>
    <div class="d-flex justify-center mt-4">
      <v-btn small class="pt-4 pb-4 mr-1 elevation-0" color="#ECF0F4" @click="process('grc_report_print')">
        Print
        <v-icon right>mdi-printer</v-icon>
      </v-btn>
      <v-btn small class="pt-4 pb-4 elevation-0" color="#ECF0F4" @click="process('grc_report_download')">
        Download
        <v-icon right>mdi-file</v-icon>
      </v-btn>
    </div>

  </div>
</template>
<script>
export default {
  props: ["bookingId"],
  data() {
    return {
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      new_payment: 0,
      reference: "",
      totalTransactionAmount: 0,
      data: [],
      trans: [],
      errors: [],
      checkOutDialog: false,
    };
  },

  watch: {
    BookingData() {
      this.get_booking();
    },
  },

  created() {
    this.preloader = false;
  },

  mounted() {
    this.get_booking();
  },

  computed: {
    currentDate() {
      return (new Date().toJSON().slice(0, 10));
    },
  },

  methods: {
    get_booking() {
      let id = this.bookingId;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios
        .get(`grc_by_checkin/${id}`, payload)
        .then(({ data }) => {
          this.data = data.booking;
          this.trans = data.trans;
          console.log(this.data);
        });
    },

    process(url) {
      let id = this.bookingId;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${process.env.BACKEND_URL}${url}/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>

<style scoped>
.text-box {
  border: 1px solid rgb(215, 211, 211);
  /* padding: 0px 10px 0px 10px; */
  /* margin: 0px 20px; */
  position: relative;
  border-radius: 5px;
  width: 100%;
}

.text-box p {
  margin: 5px;
}
</style>

