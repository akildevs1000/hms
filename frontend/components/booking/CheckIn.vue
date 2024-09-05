<template>
  <div>
    <v-dialog v-model="imgView">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>ID</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false">mdi-close</v-icon>
        </v-toolbar>
        <v-container>
          <ImagePreview :docObj="documentObj"></ImagePreview>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="m-0 p-0 mt-1">
      <v-col md="12">
        <v-tabs right v-model="activeTab">
          <!-- <div class="py-3" style="background-color: #1259a7">
            <span class="mx-2">Check In</span>
          </div> -->
          <!-- <v-spacer></v-spacer> -->
          <v-tab>
            <v-icon> mdi mdi-account-tie </v-icon>
          </v-tab>
          <v-tab v-if="customer.id > 0">
            <v-icon> mdi-bed </v-icon>
          </v-tab>
          <v-tabs-slider color="#1259a7"></v-tabs-slider>
          <v-tab-item>
            <v-card flat class="mt-5" v-if="customer && customer.id">
              <v-card-text>
                <BookingCustomerInfo
                  :defaultCustomer="customer"
                  :key="customerCompKey"
                  @selectedCustomer="handleSelectedCustomer"
                />
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <CustomerBookedRooms
                  @checkedin-success="handleCheckedInSuccess"
                  :key="customerCompKey"
                  :BookingData="BookingData"
                  :customer="customer"
                ></CustomerBookedRooms>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>

    <!---------------------------------------------------------------->

    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="error--text">*</span></label
              >
              <v-text-field
                dense
                outlined
                type="text"
                v-model="search.mobile"
                :hide-details="true"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="get_customer" :loading="checkLoader">
            Search
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="advanceDialog" width="600">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="advanceDialog = false">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-row class="px-5 mt-2">
            <div class="input-group input-group-sm px-1">
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="height: 44px; width: 215px"
              >
                <v-select
                  v-model="room.payment_mode_id"
                  :items="[
                    { id: 1, name: 'Cash' },
                    { id: 2, name: 'Card' },
                    { id: 3, name: 'Online' },
                    { id: 4, name: 'Bank' },
                    { id: 5, name: 'UPI' },
                    { id: 6, name: 'Cheque' },
                  ]"
                  item-text="name"
                  item-value="id"
                  :outlined="false"
                  cache-items
                  dense
                  flat
                  hide-no-data
                  solo
                  elevation="0"
                  background-color="#E9ECEF"
                  :disabled="room.paid_by == '2' ? true : false"
                  :hide-details="errors && !errors.payment_mode_id"
                  :error="errors && errors.payment_mode_id"
                  :error-messages="
                    errors && errors.payment_mode_id
                      ? errors.payment_mode_id[0]
                      : ''
                  "
                  style="font-size: 13px"
                ></v-select>
              </span>
              <input
                type="number"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 44px"
                v-model="new_payment"
              />
            </div>
            <div
              class="input-group input-group-sm px-1"
              v-if="room.payment_mode_id != 1"
            >
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="height: 44px; width: 215px"
              >
                Reference No
              </span>
              <input
                v-model="reference_number"
                type="text"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="
                  height: 44px;
                  text-align: left !important;
                  text-transform: lowercase !important ;
                "
              />
            </div>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="advanceDialog = false">
            Pay
            <!-- <v-icon right dark>mdi mdi-magnify</v-icon> -->
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
export default {
  props: ["BookingData"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      countries: require("@/json/countries.json"),
      states: [],
      cities: [],
      customerDocs: null,
      GRCDialog: false,
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      documentDialog: false,
      // ------------------

      checkIn: {
        id_card_type_id: "",
        id_card_no: "",
        exp: "",
        checkIn_document: null,
      },

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
      ],

      selectMeal: [],
      wantNewDoc: false,
      row: null,
      calIn: {},
      calOut: {},
      searchDialog: false,
      RoomDrawer: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" },
      ],
      val: 1,
      Model: "Reservation",
      // ---------booked data from parent-------------
      document: "",
      new_payment: 0,
      remaining_price: 0,
      reference_number: 0,
      imgView: 0,
      isImg: false,
      isPdf: false,
      // ----------------------
      documentObj: {
        fileExtension: null,
        file: null,
      },
      isSelectRoom: true,
      isBed: false,
      subLoad: false,
      isDiscount: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      roomTypes: [],
      types: ["Online", "Walking", "Travel Agency", "Complimentary"],
      search: {
        mobile: "0752388923",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],
      agentList: [],
      idCards: [],
      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      advanceDialog: false,
      isAgent: false,
      isDiff: false,
      customerCompKey: 1,
      search_available_room: "",
      room: {
        customer_type: "",
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: "",
        source: "",
        agent_name: "",
        check_in: null,
        check_out: null,
        discount: 0,
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        sub_total: 0,
        after_discount: 0,
        sales_tax: 0,
        total_price: 0,
        remaining_price: 0,
        request: "",
        company_id: this.$auth.user.company.id,
        remark: "",
        rooms: "",
        reference_no: "",
        paid_by: "",
        purpose: "Visiting",
      },
      reservation: {},
      countryList: [],
      foodPriceList: [],
      person_type_arr: [],

      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],

      guest: {
        title: "",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        exp_menu: false,
        exp: null,
      },

      customer: {
        title: "",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        exp_menu: false,
        exp: null,
      },
      id_card_type_id: 0,
      errors: [],
      errorsForSubCustomer: [],
      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
    };
  },

  created() {
    this.get_countries();
    this.get_customer();
    this.get_id_cards();
    this.preloader = false;
  },
  computed: {
    showImage() {
      if (this.BookingData.group_name) {
        if (!this.guest.image && !this.previewImage) {
          // return "/no-image.PNG";
          return "/no-profile-image.jpg";
        } else if (this.previewImage) {
          return this.previewImage;
        }
        return this.guest.image;
      }
      if (!this.customer.image && !this.previewImage) {
        // return "/no-image.PNG";
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },
  },
  methods: {
    handleCheckedInSuccess() {
      this.$swal("Success", "Checked In successful", "success").then(() => {
        this.$emit(`close-dialog`);
      });
    },
    handleSelectedCustomer({ customer }) {
      this.customer = customer;
      this.activeTab += 1;
    },

    get_customer() {
      this.errors = [];
      this.errorsForSubCustomer = [];
      this.checkLoader = true;
      let customer_id = this.BookingData.customer_id;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer_by_id/${customer_id}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            alert("Customer not found");
            return;
          }
          this.room.purpose = this.BookingData.purpose;

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },

    preview(file) {
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },

    newWhatsapp() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      } else {
        this.customer.whatsapp = "";
      }
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },
  },
};
</script>
