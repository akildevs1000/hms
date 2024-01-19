<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ snackbarMessage }}
      </v-snackbar>
    </div>
    <v-card>
      <div style="text-align: center" v-if="loading">
        <img src="../../static/loading.gif" width="200px" />
      </div>
      <v-card-title
        style="padding: 4px; font-size: 14px; background-color: #b3b3b3"
        dense
      >
        <span>Ordered Items - {{ this.cartItems.length }} </span>

        <v-spacer></v-spacer>
        Total Price {{ cartGrandTotalAmount }}
      </v-card-title>
      <v-card-text class="pt-2" style="font-size: 12px">
        <v-row
          v-for="(items, index) in cartItems"
          style="border-bottom: 1px solid #ddd"
        >
          <v-col cols="1" style="padding-right: 0px"> {{ index + 1 }} </v-col>
          <v-col cols="4"> {{ items.food.name }} </v-col>

          <v-col cols="3" class="pl-0 pr-0">
            <div style="font-size: 12px">
              {{ items.food_price }} X {{ items.qty }}
            </div>
            <div style="font-weight: bold">
              {{ (items.food_price * items.qty).toFixed(2) }}
            </div>
          </v-col>
          <v-col cols="3" class="">
            <div v-if="items.status == 0">Placed Order</div>
            <div v-else-if="items.status == 1" style="color: ##93ab6d">
              Preparing
            </div>
            <div v-else-if="items.status == 2" style="color: green">Served</div>
            <div v-else-if="items.status == 3" style="color: red">
              Cancelled
            </div>
          </v-col>
          <v-col cols="1">
            <v-icon
              @click="cancelItem(items)"
              title="Click to Cancel the Item"
              v-if="items.status == 0"
              color="red"
              >mdi mdi-close-circle</v-icon
            >
          </v-col>
        </v-row>
        <!-- <v-row>
          <v-col cols="4" style="color: red; font-size: 12px"> </v-col>
          <v-col cols="4" style="font-weight: bold; text-align: center"
            >Total :</v-col
          >
          <v-col cols="4" style="font-weight: bold; text-align: center">
            {{ cartTotalAmount.toFixed(2) }}
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="4" style="color: red; font-size: 12px"> </v-col>
          <v-col cols="4" style="font-weight: normal; text-align: center"
            >GST :</v-col
          >
          <v-col cols="4" style="font-weight: normal; text-align: center">
            + {{ cartGSTAmount.toFixed(2) }}
          </v-col>
        </v-row>
        <v-row style="font-size: 18px">
          <v-col cols="4" style="color: red; font-size: 12px"> </v-col>
          <v-col cols="4" style="font-weight: bold; text-align: center"
            >Grand Total :</v-col
          >
          <v-col cols="4" style="font-weight: bold; text-align: center">
            <v-chip color="green" label outlined>{{
              cartGrandTotalAmount.toFixed(2)
            }}</v-chip>
          </v-col>
        </v-row> -->
        <!-- <v-card-actions class="mt-5 text-center">
           <v-btn @click="cartItemDialog = false" dark filled color="red"
              >Close</v-btn
            >  
        <v-spacer></v-spacer>  
          <p></p
        ></v-card-actions> -->
      </v-card-text>
    </v-card>
  </div>
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
    loading: true,
  }),
  auth: false,
  mounted() {
    if (localStorage) {
      this.company_id = localStorage.getItem("hotelQrcodeCompanyId");
      this.room_id = localStorage.getItem("hotelQrcodeRoomId");
      this.booking_id = localStorage.getItem("hotelQrcodeBookingId");
      this.room_number = localStorage.getItem("hotelQrcodeRoomNumber");
      this.getOrderedList();
    } else {
    }
  },
  watch: {},
  created() {
    // this.displyCartItems();

    setInterval(() => {
      this.getOrderedList();
    }, 1000 * 60);
  },
  methods: {
    getOrderedList() {
      let options = {
        params: {
          company_id: this.company_id,
          booking_id: this.booking_id,
          room_id: this.room_id,
        },
      };
      this.loading = true;
      this.$axios
        .get(`hotel_orders_get_food_items`, options)
        .then(({ data }) => {
          this.cartItems = data;
          this.calculateTotal();
          this.loading = false;
        });
    },
    calculateTotal(item = null) {
      let TotalAmount = 0;
      this.cartItems.forEach((element) => {
        TotalAmount = TotalAmount + parseFloat(element.food_total);
      });
      this.cartGrandTotalAmount = TotalAmount.toFixed(2);
    },

    cancelItem(item) {
      if (
        confirm("Are you sure you want to cancel Item " + item.food.name + "?")
      ) {
        let options = {
          params: {
            company_id: item.company_id,
            item_id: item.id,

            booking_id: item.booking_id,
            room_id: item.room_id,
          },
        };
        this.$axios
          .post(`hotel_orders_cancel_food_item`, options.params)
          .then(({ data }) => {
            this.snackbar = true;
            this.snackbarMessage = data.message;
            if (!data.status) {
            } else {
              this.getOrderedList();
            }
          });
      }
    },
    //-------------------------------------------
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
        .post(`hotel_orders_food_items`, options.params)
        .then(({ data }) => {
          if (!data.status) {
            this.snackbar = true;
            this.snackbarMessage = data.message;
          } else {
            localStorage.setItem("QRCodeCartItems", JSON.stringify([]));
            this.$store.commit("hotelQRCodeCartItems", []);
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
  },
};
</script>
