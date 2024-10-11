<template>
  <div v-if="pageValid == 'true'">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ snackbarMessage }}
      </v-snackbar>
    </div>
    <v-card>
      <v-card-title
        style="padding: 4px; font-size: 14px"
        dense
        class="primary white--text background"
      >
        <span>Food Items - {{ this.cartItems.length }} </span>

        <v-spacer></v-spacer>
        Total Price {{ cartTotalAmount }}
      </v-card-title>
      <v-card-text class="pt-2" style="font-size: 12px">
        <v-row
          :key="index"
          v-for="(items, index) in cartItems"
          style="border-bottom: 1px solid #ddd"
        >
          <v-col cols="5"> {{ items.name }} </v-col>
          <v-col cols="4" class="">
            <v-autocomplete
              @change="calculateTotal(items)"
              v-model="items.qty"
              label="Qty"
              :items="[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20]"
              dense
              outlined
              small
            ></v-autocomplete>
          </v-col>
          <v-col cols="3" class="pl-0 pr-0">
            <div style="font-size: 12px; text-align: right">
              {{ items.amount }} X {{ items.qty }}
            </div>
            <div style="font-weight: bold; text-align: right">
              {{ (items.amount * items.qty).toFixed(2) }}
            </div>
          </v-col>
          <!-- <v-col cols="2">
              <v-btn
                icon
                desne
                small
                @click="cartItemDialog = false"
                dark
                filled
                color="green"
              >
                <v-icon>mdi mdi-checkbox-marked-circle</v-icon></v-btn
              >
            </v-col> -->
        </v-row>
        <v-row>
          <v-col cols="2" style="color: red; font-size: 12px"> </v-col>
          <v-col cols="5" style="font-weight: bold; text-align: center"
            >Total :</v-col
          >
          <v-col cols="5" style="font-weight: bold; text-align: center">
            {{ cartTotalAmount.toFixed(2) }}
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="2" class="pa-0" style="color: red; font-size: 12px">
          </v-col>
          <v-col
            cols="5"
            class="pa-0"
            style="font-weight: normal; text-align: center"
            >GST :</v-col
          >
          <v-col
            cols="5"
            class="pa-0"
            style="font-weight: normal; text-align: center"
          >
            + {{ cartGSTAmount.toFixed(2) }}
          </v-col>
        </v-row>
        <v-row style="font-size: 18px">
          <v-col cols="2" style="color: red; font-size: 12px"> </v-col>
          <v-col cols="5" style="font-weight: bold; text-align: center"
            >Grand Total :</v-col
          >
          <v-col cols="5" style="font-weight: bold; text-align: center">
            <v-chip color="green" label outlined>{{
              cartGrandTotalAmount.toFixed(2)
            }}</v-chip>
          </v-col>
        </v-row>
        <!-- <v-card-actions class="mt-5 text-center">
           <v-btn @click="cartItemDialog = false" dark filled color="red"
              >Close</v-btn
            >  
        <v-spacer></v-spacer>  
          <p></p
        ></v-card-actions> -->

        <div class="text-right ma-5">
          <v-btn
            v-if="cartGrandTotalAmount > 0"
            @click="confirmToOrder()"
            dark
            filled
            color="primary"
            >Click to Confirm Order</v-btn
          >
        </div>
      </v-card-text>
    </v-card>
  </div>
  <div v-else style="padding: 25%">UnAuthorised Access</div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    cartTotalAmount: 0,
    cartGSTAmount: 0,
    cartGrandTotalAmount: 0,
    id: "",
    cartSelectDropdown: [],
    data: [],
    cart_total_items: 0,
    cartItems: [],
    snackbar: false,
    snackbarMessage: "",
    food_tax: 1,
    company_id: "",
    room_id: "",
    booking_id: "",
    room_number: "",
    pageValid: false,
  }),
  auth: false,
  mounted() {
    if (localStorage) {
      this.getCompanyDetails(localStorage.getItem("hotelQrcodeCompanyId"));
      this.company_id = localStorage.getItem("hotelQrcodeCompanyId");
      this.room_id = localStorage.getItem("hotelQrcodeRoomId");
      this.booking_id = localStorage.getItem("hotelQrcodeBookingId");
      this.room_number = localStorage.getItem("hotelQrcodeRoomNumber");
      this.pageValid = localStorage.getItem("hotelQRCodeOTPverified");
    } else {
    }
  },
  watch: {
    cartItems: {
      handler() {
        this.calculateTotal();
      },
      deep: true,
    },
  },
  created() {
    // this.displyCartItems();
  },
  methods: {
    isPageValid() {
      return this.pageValid;
    },
    confirmToOrder() {
      let options = {
        params: {
          company_id: this.company_id,
          cart_items: this.cartItems,
          room_id: this.room_id,
          booking_id: this.booking_id,
          room_number: this.room_number,
        },
      };
      this.loading = true;
      this.$axios
        .post(`hotel_orders_add_food_items`, options.params)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = true;
            this.snackbarMessage = data.message;
          } else {
            //localStorage.setItem("QRCodeCartItems", JSON.stringify([]));
            //this.$store.commit("hotelQRCodeCartItems", []);
            this.$router.push("/qrcode/orders");
          }
        });
    },
    displyCartItems() {
      if (localStorage) {
        this.cartItems = JSON.parse(localStorage.getItem("QRCodeCartItems"));
      } else {
        this.cartItems = this.$store.state.hotelQRCodeCartItems;
      }
    },
    getCompanyDetails(company_id) {
      let options = {
        params: {
          company_id: company_id,
        },
      };
      this.loading = true;
      this.$axios.get(`company/${company_id}`, options).then(({ data }) => {
        if (data.record) {
          this.food_tax = data.record.food_tax;

          this.displyCartItems();
        }
      });
    },
    viewCart() {
      this.cartItemDialog = true;
    },
    calculateTotal(item = null) {
      let TotalAmount = 0;
      this.cartItems.forEach((element) => {
        TotalAmount = TotalAmount + element.amount * element.qty;
      });
      this.cartTotalAmount = TotalAmount;
      if (TotalAmount > 0)
        this.cartGSTAmount = (TotalAmount * this.food_tax) / 100;

      this.cartGrandTotalAmount = TotalAmount + this.cartGSTAmount;
      if (item) {
        this.snackbar = true;
        this.snackbarMessage = item.name + "  - Cart Items are updated";
      }
      localStorage.setItem("QRCodeCartItems", JSON.stringify(this.cartItems));
      //this.$store.commit("hotelQRCodeCartItems", this.cartItems);

      console.log("this.cartItems", this.cartItems);
    },
  },
};
</script>
