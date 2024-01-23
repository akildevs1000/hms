<template>
  <div v-if="pageValid == 'true'">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ snackbarMessage }}
      </v-snackbar>
    </div>
    <v-dialog v-model="dialogPreviewImage" width="600px">
      <v-card>
        <v-card-title dense class="primary white--text background">
          <v-spacer></v-spacer>
          <v-icon
            @click="dialogPreviewImage = false"
            outlined
            dark
            color="white"
          >
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <img :src="imagePreviewSrc" style="width: 500px; height: auto"
          /></v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="cartItemDialog"
      width="100%"
      max-width="100%"
      style="max-width: 100% !important; margin: 0px !important"
    >
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span> Food Items - {{ this.cartItems.length }} </span>

          <v-spacer></v-spacer>
          <v-icon @click="cartItemDialog = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text style="padding: 0px" class="pt-2">
          <v-row
            :key="index"
            v-for="(items, index) in cartItems"
            style="border-bottom: 1px solid #ddd"
          >
            <v-col cols="5"> {{ items.name }} </v-col>
            <v-col cols="4" class="">
              <v-select
                v-model="items.qty"
                label="Qty"
                :items="[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 20]"
                dense
                outlined
                small
              ></v-select>
            </v-col>
            <v-col cols="3" class="pl-0 pr-0">
              <div style="font-size: 12px">
                {{ items.amount }} X {{ items.qty }}
              </div>
              <div style="font-weight: bold; text-align: center">
                {{ (items.amount * items.qty).toFixed(2) }}
              </div>
              <!-- <v-btn
                style="padding: 5px"
                desne
                small
                @click="addToCart(items, true)"
                dark
                filled
                color="green"
              >
                Update</v-btn
              > -->
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
          <!-- <v-divider></v-divider> -->
          <v-row>
            <v-col cols="4" style="color: red; font-size: 12px"
              >*Excluding GST</v-col
            >
            <v-col cols="4" style="font-weight: bold; text-align: center"
              >Total :</v-col
            >
            <v-col cols="4" style="font-weight: bold; text-align: center">
              <v-chip color="green" label outlined>{{
                cartTotalAmount.toFixed(2)
              }}</v-chip>
            </v-col>
          </v-row>

          <v-card-actions class="mt-5">
            <!-- <v-btn @click="cartItemDialog = false" dark filled color="red"
              >Close</v-btn
            > -->
            <v-spacer></v-spacer>
            <v-btn
              v-if="cartTotalAmount > 0"
              @click="confirmToOrder()"
              small
              dark
              filled
              color="primary"
              >Confirm to Order</v-btn
            >
          </v-card-actions>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div class="text-center pb-5" style="margin-top: -20px">
      <h3>{{ categoryName }} List</h3>
    </div>
    <div class="text-center d-flex pb-4">
      <v-btn
        style="margin-top: 1px !important"
        color="green"
        class="ma-2 white--text"
        @click="goBackToCategories()"
        text
        outlined
        ><v-icon right dark> mdi mdi-step-backward </v-icon>
        Categories
      </v-btn>
      <!-- <v-btn
        v-if="menu_open"
        color="green"
        class="ma-2 white--text"
        @click="all"
      >
        Menu
        <v-icon right dark> mdi mdi-book-open-variant</v-icon>
      </v-btn>
      <v-btn
        v-else="menu_open"
        color="red"
        class="ma-2 white--text"
        @click="none"
      >
        Menu
        <v-icon right dark> mdi mdi-arrow-collapse-up </v-icon>
      </v-btn> -->
      <v-spacer></v-spacer>
      <span style="float: right">
        <v-autocomplete
          v-model="itemNameSearch"
          :items="foodMenuList"
          dense
          small
          outlined
          label="Search Item Name"
          placeholder="Item name"
          clearable
          item-text="name"
          item-value="name"
          @keyup="getSearchDataApi()"
          @change="getSearchDataApi()"
        >
        </v-autocomplete>
      </span>
    </div>

    <div style="text-align: center" v-if="loading">
      <img src="../../../static/loading.gif" width="200px" />
    </div>

    <v-row style="width: 100%; text-align: center; margin: 0px; z-index: 10">
      <v-col
        :key="index"
        xs="12"
        sm="6"
        md="6"
        cols="12"
        v-for="(item, index) in data"
      >
        <v-row>
          <v-col cols="6">
            <img
              elevation="24"
              class="p-5 boxshadow"
              :src="item.image"
              width="100%"
              style="max-width: 100%; height: 150px"
            />
          </v-col>
          <v-col cols="6">
            <h3 class="qrcode-color">{{ item.name }}</h3>
            <div width="100%" style="height: 50px" class="overflow-auto">
              {{ item.description }}
            </div>
            <h3>₹ {{ item.amount }}</h3>
            <div style="font-size: 20px" class="qrcode-color">
              <span><v-icon>mdi mdi-minus-box</v-icon></span
              ><span style="font-weight: bold"> {{ qty[item.id] ?? 0 }} </span
              ><span><v-icon>mdi mdi-plus-box</v-icon></span>
              <span
                ><v-icon
                  color="error"
                  size="20"
                  style="float: right; margin-top: 5px"
                  >mdi mdi-close-circle</v-icon
                ></span
              >
            </div>
          </v-col>
        </v-row>
        <v-divider></v-divider>
      </v-col>
    </v-row>

    <v-row v-if="data.length == 0"> No Items are Availalbe. </v-row>

    <v-card-text style="height: 100px; margin-bottom: 200px">
      <v-btn
        style="margin-bottom: 100px"
        @click="viewCart()"
        color="pink"
        dark
        fixed
        bottom
        right
        fab
      >
        {{ cartItems.length }}<v-icon>mdi mdi-cart-plus</v-icon>
      </v-btn>
    </v-card-text>
  </div>
  <div v-else style="padding: 25%">UnAuthorised Access</div>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    qty: [],
    categoryId: "",
    categoryName: "",
    cartTotalAmount: 0,
    cartItemDialog: false,
    cartSelectDropdown: [],
    foodMenuList: [],
    itemNameSearch: "",
    key: 1,
    menu_open: true,
    data: [],
    id: "",
    panel1: [],
    panel2: [],
    panel3: [],
    items: 5,
    groupedCategories: [],
    groupedItems: [],
    cart_total_items: 0,
    cartItems: [],
    cartItemsObj: {},
    snackbar: false,
    snackbarMessage: "",
    loading: true,
    pageValid: false,
    imagePreviewSrc: "",
    dialogPreviewImage: false,
    timingsList: [],
    company_id: "",
    category_id: "",
  }),
  auth: false,
  watch: {
    cartItems: {
      handler() {
        let TotalAmount = 0;
        this.cartItems.forEach((element) => {
          TotalAmount = TotalAmount + element.amount * element.qty;
        });
        this.cartTotalAmount = TotalAmount;
      },
      deep: true,
    },
    // options_history: {
    //   handler() {
    //     this.getDataFromApi_history(this.viewHistoryItem);
    //   },
    //   deep: true,
    // },
  },
  mounted() {
    console.log(
      "hotelQRCodeOTPverified",
      localStorage.getItem("hotelQRCodeOTPverified")
    );
    if (localStorage)
      this.cartItems = JSON.parse(localStorage.getItem("QRCodeCartItems"));
    this.pageValid = localStorage.getItem("hotelQRCodeOTPverified");

    this.company_id = localStorage.getItem("hotelQrcodeCompanyId");
    //this.categoryId = this.$route.params.id;

    this.categoryId = this.$route.params.id.split("-")[0];
    this.categoryName = this.$route.params.id.split("-")[1];
    this.getDataFromApi();
    this.getMenuItemsList();
  },
  created() {
    this.company_id = this.$store.state.hotelQrcodeCompanyId;
  },
  methods: {
    getMenuItemsList() {
      let options = {
        params: {
          company_id: this.company_id,
        },
      };

      this.$axios.get(`hotel_food_dropdown_list`, options).then(({ data }) => {
        this.foodMenuList = data;

        data.forEach((element1) => {
          this.cartSelectDropdown[element1.id] = 1;
        });
      });
    },
    goBackToCategories() {
      this.$router.push(
        "/qrcode/food_categories/" +
          this.categoryId +
          "-" +
          this.$store.state.timingName
      );
    },
    goToItemsPage(category) {
      this.$router.push(
        "/qrcode/food_items/" + category.id + "-" + category.name
      );
    },
    getDataFromApi() {
      let options = {
        params: {
          company_id: this.company_id,
          category_id: this.categoryId,
        },
      };

      this.$axios
        .get(`get_food_items_by_category`, options)
        .then(({ data }) => {
          this.data = data;
          this.loading = false;
        });
    },
    getSearchDataApi() {
      if (!this.itemNameSearch) {
        return this.getDataFromApi();
      }
      let options = {
        params: {
          company_id: this.company_id,
          item_name: this.itemNameSearch,
        },
      };

      this.$axios.get(`get_food_items_by_search`, options).then(({ data }) => {
        this.data = data;
        this.loading = false;
      });
    },
  },
};
</script>
<style scoped></style>
