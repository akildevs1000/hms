<template>
  <v-app>
    <v-navigation-drawer v-model="drawer" absolute left temporary>
      <v-list nav dense dark app color="#34444c" clipped="true">
        <v-list-item-group
          v-model="group"
          active-class="deep-purple--text text--accent-4"
        >
          <v-list-item>
            <v-list-item-title>
              <img
                src="https://tanjore.hyderspark.com/wp-content/uploads/2023/12/Hyders-Logo-Gold_.png"
                style="height: 40px"
              />
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
          <span style="width: 20%; float: left">
            <img
              src="https://tanjore.hyderspark.com/wp-content/uploads/2023/12/Hyders-Logo-Gold_.png"
              style="height: 40px"
            />
          </span>

          <span style="width: 60%; float: right; font-size: 10px">
            <span style="float: right" class="pr-3"
              >Check-in: {{ dateFormatDisplay(guest_check_in_time) }}</span
            >
            <span style="float: right" class="pr-3"
              >Check-out: {{ dateFormatDisplay(guest_check_out_time) }}</span
            >
          </span>
        </div>
        <div
          style="
            height: 30px;
            background-color: #fff;
            text-align: center;
            border-bottom: 1px solid #ddd;
          "
        >
          <div style="width: 100%">
            <span>Welcome Mr {{ guest_name }} </span>
            <span style="float: right" class="pr-3"
              >Room No: {{ guest_room_number }}
            </span>
          </div>
        </div>
      </div>
      <div>
        <nuxt />
      </div>
      <v-bottom-navigation
        :elevation="24"
        grow
        fill
        background-color="#393838"
        style="position: fixed; bottom: 0px; border-top: 1px solid black"
      >
        <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
          <span style="color: #fff">Home</span>
          <v-icon color="white">mdi mdi-home-outline</v-icon>
        </v-btn>
        <v-btn
          @click="goToPage('food_menu')"
          style="border-right: 0px solid #ddd"
        >
          <span style="color: #fff">Food</span>
          <v-icon color="white">mdi mdi-food</v-icon>
        </v-btn>
        <v-btn @click="goToPage('orders')" style="border-right: 0px solid #ddd">
          <span style="color: #fff">My Orders</span>
          <v-icon color="white">mdi mdi-cart</v-icon>
        </v-btn>
        <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
          <span style="color: #fff">Check-out</span>
          <v-icon color="white">mdi mdi-airplane-takeoff</v-icon>
        </v-btn>
        <v-btn @click="goToPage('home')" style="border-right: 0px solid #ddd">
          <span style="color: #fff">Phones</span>
          <v-icon color="white">mdi mdi-card-account-phone</v-icon>
        </v-btn>
      </v-bottom-navigation>
    </v-container>
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
  }),
  auth: false,
  mounted() {
    if (this.$route.name != "qrcode-id") {
      this.id = localStorage.getItem("hotelQrcodeID");
    } else {
      this.id = this.$route.params.id;
      this.$store.commit("hotelQrcodeID", this.id);
      localStorage.setItem("hotelQrcodeID", this.id);
    }
  },
  watch: {
    group() {
      this.drawer = false;
    },
  },
  created() {
    setTimeout(() => {
      try {
        if (this.$route.name == "qrcode-id") {
          this.id = this.$route.params.id;
          this.$store.commit("hotelQrcodeID", this.id);
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
      } catch (error) {
        this.pageValid = false;
      }
    }, 2000);
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
            data.record.customer.title + "" + data.record.customer.full_name;
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

<style>
body {
  /* background-image: url("../assets/qrcode/images/bg.jpg") !important; */
  background-size: cover !important;
  /* height: 100%;
  width: 100%; */
  /* width: 0; */
  margin-bottom: 70px;
}
.qrcodecontainer {
  padding: 0px !important;
  max-width: 600px !important;
  border: 1px solid #ddd;
}
.guestinfo {
  padding: 10px 0;
  background-color: #ff1b43;
  font-weight: bold;
  text-align: center;
  margin: auto;
  color: #fff;
}
.banner-title {
  text-align: center;
  margin: 0px;
  padding: 0px 0px;
  color: #fff;
  font-size: 20px;
  background-color: rgb(0 0 0 / 33%);
}
.bgimage1 {
  background-image: url("../assets/qrcode/images/banner1.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage2 {
  background-image: url("../assets/qrcode/images/banner2.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage3 {
  background-image: url("../assets/qrcode/images/banner3.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage4 {
  background-image: url("../assets/qrcode/images/banner4.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage5 {
  background-image: url("../assets/qrcode/images/banner5.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage6 {
  background-image: url("../assets/qrcode/images/banner6.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage7 {
  background-image: url("../assets/qrcode/images/banner7.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage8 {
  background-image: url("../assets/qrcode/images/banner8.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}
.bgimage9 {
  background-image: url("../assets/qrcode/images/banner9.jpg");
  height: 150px;
  background-size: cover;
  border-top: 3px solid #fff;
  padding-top: 30px;
}

.boxborder {
  padding: 35px 0px !important;
  border: 1px solid #ddd;
  height: 200px;
}

/* .v-expansion-panel--active > .v-expansion-panel-header {
  background-color: #f5b0b0;
} */
.itemname .v-expansion-panel-content__wrap {
  padding: 0px;
}

.itemname .v-expansion-panel--active > .v-expansion-panel-header {
  background-color: #7da7c8;
}
.v-expansion-panel--active > .v-expansion-panel-header {
  min-height: 40px !important;
}
.itemCatname .v-expansion-panel-content__wrap {
  padding: 10px;
}
</style>
