<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" absolute left temporary>
      <v-list nav dense dark app color="#1cae81" clipped="true">
        <v-list-item-group
          v-model="group"
          active-class="deep-purple--text text--accent-4"
        >
          <v-list-item>
            <v-list-item-title>
              <img src="../static/logo.png" style="height: 40px" />
            </v-list-item-title>
          </v-list-item>
          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-home-outline </v-icon>
            </v-list-item-icon>

            <v-list-item-title> Home</v-list-item-title>
          </v-list-item>

          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-food </v-icon>
            </v-list-item-icon>
            <v-list-item-title> Food Menu </v-list-item-title>
          </v-list-item>
          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-cart </v-icon>
            </v-list-item-icon>
            <v-list-item-title> Cart Items</v-list-item-title>
          </v-list-item>

          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-home-outline </v-icon>
            </v-list-item-icon>
            <v-list-item-title>Biils/Orders</v-list-item-title>
          </v-list-item>
          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-airplane-takeoff </v-icon>
            </v-list-item-icon>
            <v-list-item-title> Check-out</v-list-item-title>
          </v-list-item>
          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-card-account-phone </v-icon>
            </v-list-item-icon>
            <v-list-item-title> Phones</v-list-item-title>
          </v-list-item>

          <v-list-item style="padding: 5px 0 0 0px">
            <v-list-item-icon class="ma-2">
              <v-icon>mdi mdi-food </v-icon>
            </v-list-item-icon>
            <v-list-item-title style="color: red">Logout</v-list-item-title>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>

    <v-container class="qrcodecontainer">
      <div>
        <div class="guestinfo" style="height: 70px">
          <span style="width: 20%; float: left">
            <v-app-bar-nav-icon
              style="color: #fff"
              @click.stop="drawer = !drawer"
            ></v-app-bar-nav-icon>
          </span>
          <span style="width: 80%; float: left; color: #fff">
            Hi
            <span
              ><h3>{{ guest_name }}</h3></span
            >
          </span>
          <!-- <span style="width: 20%; float: left">
            <img src="../assets/logo.png" style="height: 40px" />
          </span>

          <span style="width: 60%; float: right; font-size: 10px">
            <span style="float: right" class="pr-3"
              >Check-in: {{ dateFormatDisplay(guest_check_in_time) }}</span
            >
            <span style="float: right" class="pr-3"
              >Check-out: {{ dateFormatDisplay(guest_check_out_time) }}</span
            >
          </span> -->
        </div>
        <!-- <div
          style="
            height: 30px;
            background-color: #fff;
            text-align: center;
            border-bottom: 1px solid #ddd;
          "
        >
          <div style="width: 100%">
            <span>Welcome   {{ guest_name }} </span>
            <span style="float: right" class="pr-3"
              >Room No: {{ guest_room_number }}
            </span>
          </div>
        </div> -->
      </div>
      <div class="header-bottom-image"></div>
      <div>
        <nuxt />
      </div>
    </v-container>
    <v-bottom-navigation
      :elevation="24"
      grow
      fill
      background-color="#FFF"
      fixed
      bottom
      right
      style="
        position: fixed;
        bottom: 0px;
        border-top: 1px solid #1cae81;
        z-index: 9999;
      "
    >
      <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
        <!-- <span class="qrcode-color">Home</span> -->
        <v-avatar size="40" class="qrcode-bgcolor1">
          <v-icon style="color: #1cae81 !important"
            >mdi mdi-home-outline</v-icon
          >
        </v-avatar>
      </v-btn>
      <v-btn
        @click="goToPage('food_menu')"
        style="border-right: 0px solid #ddd"
      >
        <!-- <span class="qrcode-color">Food</span> -->
        <v-avatar size="40" class="qrcode-bgcolor1">
          <v-icon style="color: #1cae81 !important"
            >mdi mdi-food</v-icon
          ></v-avatar
        >
      </v-btn>
      <v-btn @click="goToPage('orders')" style="border-right: 0px solid #ddd">
        <!-- <span class="qrcode-color">My Orders</span> -->
        <v-avatar size="40" class="qrcode-bgcolor1"
          ><v-icon style="color: #1cae81 !important"
            >mdi mdi-cart</v-icon
          ></v-avatar
        >
      </v-btn>
      <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
        <!-- <span class="qrcode-color">Check-out</span> -->
        <v-avatar size="40" class="qrcode-bgcolor1">
          <v-icon style="color: #1cae81 !important"
            >mdi mdi-airplane-takeoff</v-icon
          ></v-avatar
        >
      </v-btn>
      <!-- <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
          <span class="qrcode-color">Phones</span>
          <v-icon class="qrcode-color">mdi mdi-card-account-phone</v-icon>
        </v-btn> -->
    </v-bottom-navigation>
  </v-app>
</template>

<script>
export default {
  data: () => ({
    drawer: false,
    group: null,
    id: "",
    pageValid: true,
    guest_check_in_time: "Check-In: ---",
    guest_check_out_time: "Check-In: ---",
    guest_room_number: "---",
    guest_name: "---",
    guest_whatsapp_number: "---",
    // hideMenu: false,
  }),
  auth: false,
  mounted() {
    console.log("this.$route.name M", this.$route.name);
    if (this.$route.name != "qrcode-id") {
      this.id = localStorage.getItem("hotelQrcodeID");
    } else {
      this.id = this.$route.params.id;
      this.$store.commit("hotelQrcodeID", this.id);
      if (localStorage) localStorage.setItem("hotelQrcodeID", this.id);
    }
  },
  watch: {
    group() {
      this.drawer = false;
    },
  },
  created() {
    setTimeout(() => {
      // try {
      console.log("this.$route.name C", this.$route.name);
      if (this.$route.name == "qrcode-id") {
        this.id = this.$route.params.id;
        this.$store.commit("hotelQrcodeID", this.id);
        //this.hideMenu = false;
      } else {
        // this.hideMenu = true;
        this.id = this.$store.state.hotelQrcodeID;
        console.log(
          " this.$store.state.hotelQrcodeID",
          this.$store.state.hotelQrcodeID
        );
        try {
          if (localStorage) this.id = localStorage.getItem("hotelQrcodeID");
        } catch (e) {}
      }

      let IdArray = this.id.split("-");

      if (IdArray.length == 3) {
        this.getGuestDetails(IdArray[0], IdArray[2], IdArray[1]);
      } else {
        this.pageValid = false;
        this.guest_room_number = "";
        this.guest_check_out_time = "";
        this.guest_check_in_time = "";
        this.guest_name = "";
        this.$store.commit("hotelQrcodeRequestId", null);
        this.$store.commit("hotelQrcodeCompanyId", null);
        this.$store.commit("hotelQrcodeRoomNumber", null);
        this.$store.commit("hotelQrcodeRoomId", null);
        //this.$router.push("/qrcode");
      }
      // } catch (error) {
      //   this.pageValid = false;
      // }
    }, 1000);
  },
  methods: {
    goToPage(name) {
      this.$router.push("/qrcode/" + name);
    },
    dateFormatDisplay(date) {
      const currentDate = new Date(date);
      const options = {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        hour12: true,
      };

      return currentDate.toLocaleString("en-US", options);
    },
    getGuestDetails(company_id, roomId, roomNo) {
      let options = {
        params: {
          company_id: company_id,
          room_id: roomId,
        },
      };

      this.$axios.get(`get_checkin_customer_data`, options).then(({ data }) => {
        if (data.status) {
          this.guest_check_in_time = data.record.check_in;
          this.guest_check_out_time = data.record.check_out;
          this.guest_room_number = data.record.room_no;

          this.guest_name =
            data.record.customer.title + " " + data.record.customer.full_name;
          this.guest_whatsapp_number = data.record.customer.whatsapp;

          this.$store.commit("hotelQrcodeRequestId", this.id);
          this.$store.commit("hotelQrcodeCompanyId", company_id);
          this.$store.commit("hotelQrcodeRoomNumber", roomNo);
          this.$store.commit("hotelQrcodeRoomId", roomId);
          this.$store.commit(
            "hotelQrcodeWhatsappNumber",
            data.record.customer.whatsapp
          );

          localStorage.setItem("hotelQrcodeCompanyId", company_id);
          localStorage.setItem("hotelQrcodeRoomNumber", roomNo);
          localStorage.setItem("hotelQrcodeRoomId", roomId);
          localStorage.setItem("hotelQrcodeBookingId", data.record.booking_id);
        } else if (data.status == false) {
        }
      });
    },
  },
};
</script>