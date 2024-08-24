<template>
  <div v-if="can('calendar_create')">
    <v-dialog v-model="dialog" max-width="1020">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          small
          color="primary"
          class="white--text"
          dark
          v-bind="attrs"
          v-on="on"
        >
          <v-icon color="white" small> mdi-plus </v-icon> {{ model }}
        </v-btn>
      </template>
      <v-card>
        <v-toolbar color="primary" dense flat dark>
          <span>Quotaion Create</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="dialog = false"> mdi-close </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-container class="pa-5">
            <v-row>
              <v-col md="3" cols="12">
                <div class="">
                  <v-btn
                    small
                    class="mb-2"
                    color="primary"
                    @click="searchDialog = true"
                  >
                    Search
                    <v-icon right dark>mdi-magnify</v-icon>
                  </v-btn>
                </div>
                <div class="text-center">
                  <v-avatar size="150">
                    <v-img
                      :src="customer.captured_photo || '/no-profile-image.jpg'"
                    ></v-img>
                  </v-avatar>
                </div>
              </v-col>
              <v-col md="9" cols="12">
                <v-container>
                  <v-row>
                    <v-col md="6" cols="12" sm="12">
                      <v-select
                        v-model="customer.title"
                        :items="titleItems"
                        label="Title *"
                        dense
                        item-text="name"
                        item-value="name"
                        :hide-details="errors && !errors.title"
                        :error-messages="
                          errors && errors.title ? errors.title[0] : ''
                        "
                        outlined
                      ></v-select>
                    </v-col>
                    <v-col md="6" cols="12" sm="12">
                      <v-text-field
                        dense
                        label="Email *"
                        outlined
                        type="email"
                        v-model="customer.email"
                        :hide-details="errors && !errors.email"
                        :error-messages="
                          errors && errors.email ? errors.email[0] : ''
                        "
                      ></v-text-field>
                    </v-col>

                    <v-col md="6" cols="12" sm="12">
                      <v-text-field
                        label="First Name *"
                        dense
                        outlined
                        type="text"
                        v-model="customer.first_name"
                        :hide-details="errors && !errors.first_name"
                        :error-messages="
                          errors && errors.first_name
                            ? errors.first_name[0]
                            : ''
                        "
                      ></v-text-field>
                    </v-col>
                    <v-col md="6" cols="12" sm="12">
                      <v-text-field
                        label="Last Name"
                        dense
                        :hide-details="true"
                        outlined
                        type="text"
                        v-model="customer.last_name"
                      ></v-text-field>
                    </v-col>

                    <v-col md="6" cols="12" sm="12">
                      <v-text-field
                        dense
                        label="Contact No *"
                        outlined
                        max="1111111111111"
                        type="number"
                        v-model="customer.contact_no"
                        :hide-details="errors && !errors.contact_no"
                        :error-messages="
                          errors && errors.contact_no
                            ? errors.contact_no[0]
                            : ''
                        "
                        @keyup="mergeContact"
                      ></v-text-field>
                    </v-col>
                    <v-col md="6" cols="12" sm="12">
                      <v-text-field
                        dense
                        label="Whatsapp No"
                        outlined
                        max="1111111111111"
                        type="number"
                        v-model="customer.whatsapp"
                        :hide-details="errors && !errors.whatsapp"
                        :error-messages="
                          errors && errors.whatsapp ? errors.whatsapp[0] : ''
                        "
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <FullAddress @location="handleFullAddress" />
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>
            </v-row>
            <v-container>
              <v-row>
                <v-col md="12">
                  <table class="styled-table py-0 my-0">
                    <thead>
                      <tr>
                        <td><small>Date</small></td>
                        <td><small>Day</small></td>
                        <td><small>Room Type</small></td>
                        <td><small>Type</small></td>
                        <td><small>Tariff</small></td>
                        <td><small>Adult</small></td>
                        <td><small>Child</small></td>
                        <td><small>Meal</small></td>
                        <td><small>No of Rooms</small></td>
                        <td><small>Price</small></td>
                        <td><small>Early Checkin</small></td>
                        <td><small>Late Checkout</small></td>
                        <td><small>Extra Bed</small></td>
                        <td><small>Total</small></td>
                        <td style="color: #4390fc">------</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(item, index) in priceListTableView"
                        :key="index"
                      >
                        <td>
                          {{ item.date }}
                        </td>
                        <td>
                          {{ item.day }}
                        </td>
                        <td>
                          {{ item.room_type }}
                        </td>
                        <td>
                          {{ item.day_type }}
                        </td>
                        <td>
                          {{ item.room_price }}
                        </td>
                        <td>{{ item.no_of_adult }}</td>
                        <td>{{ item.no_of_child }}</td>
                        <td>{{ item.meal_name }}</td>
                        <td>{{ item.no_of_rooms }}</td>
                        <td>
                          {{ convert_decimal(item.price) }}
                        </td>
                        <td>
                          {{ convert_decimal(item.early_check_in) }}
                        </td>
                        <td>
                          {{ convert_decimal(item.late_check_out) }}
                        </td>
                        <td>
                          {{ convert_decimal(item.bed_amount) }}
                        </td>
                        <td>
                          {{ convert_decimal(item.total_price) }}
                        </td>
                        <td class="text-center">
                          <v-icon color="red" @click="deleteItem(index)"
                            >mdi-close</v-icon
                          >
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </v-col>
                <v-col md="12" style="padding-top: 0px; font-weight: bold">
                  <div
                    class="d-flex justify-space-around py-3 styled-table"
                    style="margin-top: 5px"
                  >
                    <v-col cols="10" class="text-right">
                      <div>Sub Total:</div>
                      <div>Add :</div>
                      <div>Discount :</div>
                      <v-divider color="#4390FC"></v-divider>
                      <div style="font-size: 18px; font-weight: bold">
                        Total :
                      </div>
                    </v-col>
                    <v-col cols="2" class="text-right">
                      <div>
                        {{ convert_decimal(subTotal()) }}
                      </div>

                      <div>
                        {{ convert_decimal(temp.room_extra_amount) }}
                      </div>
                      <div style="color: red">
                        -{{ convert_decimal(temp.room_discount) }}
                      </div>
                      <v-divider color="#4390FC"></v-divider>
                      <div style="font-size: 18px; font-weight: bold">
                        {{ convert_decimal(processCalculation()) }}
                      </div>
                    </v-col>
                  </div>
                </v-col>
                <v-col md="12" class="text-right">
                  <v-btn color="primary" @click="get_available_rooms" small>
                    <v-icon color="white" small>mdi-plus</v-icon>
                    Add Room
                  </v-btn>
                  <v-btn
                    style="background-color: #5fafa3"
                    @click="store"
                    small
                    dark
                  >
                    <v-icon color="white" small>mdi-floppy</v-icon>
                    Submit
                  </v-btn>
                </v-col>
              </v-row>
            </v-container>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="error--text">*</span>
              </label>
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
            <v-icon right dark>mdi mdi-magnify</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="RoomDrawer" max-width="400">
      <v-card>
        <v-toolbar flat class="primary white--text" dense>
          Individual Booking <v-spacer></v-spacer
          ><v-icon @click="RoomDrawer = false" color="white"
            >mdi-close</v-icon
          ></v-toolbar
        >
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-menu
                v-model="checkin_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check In"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="formattedCheckinDate"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  :min="new Date().toISOString().substr(0, 10)"
                  v-model="temp.check_in"
                  no-title
                  @input="checkin_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-menu
                v-model="checkout_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check Out"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="formattedCheckOutDate"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  :min="addOneDay(temp.check_in)"
                  v-model="temp.check_out"
                  no-title
                  @input="checkout_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                label="Room Type"
                outlined
                dense
                hide-details
                item-value="id"
                item-text="name"
                v-model="room_type_id"
                @change="
                  ($event) => {
                    get_available_rooms($event);
                    selectRoom($event);
                  }
                "
                :items="roomTypes"
                return-object
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                v-model="multipleRoomId"
                hide-details
                :items="availableRooms"
                item-value="id"
                item-text="room_no"
                label="Select Room"
                dense
                outlined
                return-object
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Adult Per Room"
                :items="[0, 1, 2, 3]"
                dense
                outlined
                v-model="temp.no_of_adult"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Child Per Room"
                :items="[0, 1, 2, 3]"
                dense
                outlined
                v-model="temp.no_of_child"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                label="Food Plan"
                outlined
                dense
                hide-details
                item-value="id"
                item-text="title"
                v-model="temp.food_plan_id"
                :items="foodplans"
                @change="
                  selectRoom({ name: temp.room_type, room_no: temp.room_no })
                "
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-checkbox
                v-model="is_early_check_in"
                label="Early Check In"
                :hide-details="true"
                dense
                @change="set_additional_charges"
              >
              </v-checkbox>
            </v-col>
            <v-col cols="6"
              ><v-checkbox
                v-model="is_late_check_out"
                label="Late Check Out"
                :hide-details="true"
                dense
                @change="set_additional_charges"
              >
              </v-checkbox>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Extra Bed"
                min="0"
                dense
                outlined
                type="number"
                v-model="temp.extra_bed_qty"
                :hide-details="true"
                @keyup="set_additional_charges"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-btn block @click="add_room(temp)" color="primary" small>
                Confirm Room
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
  <NoAccess v-else />
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["model"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      additional_charges: {},
      is_early_check_in: false,
      is_late_check_out: false,
      dialog: false,
      foodplans: [],
      multipleRoomObjects: [],
      multipleRoomId: null,
      checkin_menu: false,
      checkout_menu: false,
      room_type_id: {},
      documentDialog: false,
      // -------customer history---------------
      customer: "",
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,
      advanceDialog: false,
      selectRoomLoading: false,
      roomTab: null,
      headers: [
        {
          text: "#",
        },
        {
          text: "Type",
        },
        {
          text: "Source",
        },
        {
          text: "Rooms",
        },
        {
          text: "Booking Date",
        },
        {
          text: "Check In",
        },
        {
          text: "Check Out",
        },
        {
          text: "Total Price",
        },
      ],
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      // ------------------

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
        "Marriage",
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
      isSelectRoom: true,
      isBed: false,
      subLoad: false,
      isDiscount: false,
      isExtra: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      roomTypes: [],
      types: [
        "Online",
        "Walking",
        "Travel Agency",
        "Complimentary",
        "Corporate",
      ],

      search: {
        mobile: "",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],

      agentList: [],
      CorporateList: [],
      // room_extra_amount: 0,
      idCards: [],
      imgView: false,
      priceListTableView: [],

      temp: {
        food_plan_price: 1,
        extra_bed_qty: 0,
        food_plan_id: 1,
        early_check_in: 0,
        late_check_out: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        // meal: [],
        bed_amount: 0,
        room_extra_amount: 0,
        extra_amount_reason: "",
        room_discount: 0,
        after_discount: 0, //(price - room_discount)
        room_tax: 0,
        total_with_tax: 0, //(after_discount * room_tax)
        total: 0, //(total_with_tax * bed_amount)
        grand_total: 0, //(total * days)
        company_id: this.$auth.user.company.id,

        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      },
      merge_food_in_room_price: "",
      gst_calculation: {
        recal_basePrice: 0,
        recal_gst_percentage: 0,
        recal_gst_total: 0,
        recal_final: 0,
      },
      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      isCorporate: false,
      isAgent: false,
      isDiff: false,
      search_available_room: "",
      room: {
        customer_type: "",
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: "Walking",
        source: "walking",
        agent_name: "",
        booking_status: 1,
        check_in: null,
        check_out: null,
        discount: 0,
        reference_number: "",
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
        purpose: "Tour",
        // priceList: [],
      },
      reservation: {
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        room_no: "",
        room_id: 82,
        room_type: "",
        price: 0,
        origin_price: "",
        isCalculate: true,
        priceList: [],
        total_tax: 0,
        total_price_after_discount: 0,
        total_price: 0,
        total_discount: 0,
      },
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

      customer: {
        customer_type: "Walking",
        title: "Mr",
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
        //  new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        //   .toISOString()
        //   .substr(0, 10)
      },
      id_card_type_id: 0,
      errors: [],
      tempAdult: {
        tot_ab: 0,
        tot_al: 0,
        tot_ad: 0,
      },
      tempChild: {
        tot_cb: 0,
        tot_cl: 0,
        tot_cd: 0,
      },

      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
      extraPayType: "",
      allFood: [],

      breakfast: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      lunch: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      dinner: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      documentObj: {
        fileExtension: null,
        file: null,
      },
      business_sources: [],

      isValid: false,

      seletedFoodPlan: null,
    };
  },
  async created() {
    this.get_reservation();
    this.get_room_types();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
    this.get_Corporate();
    // this.getImage();
    this.preloader = false;

    await this.get_food_plans();

    await this.get_additional_charges();

    await this.get_business_sources();
  },
  computed: {
    formattedCheckinDate() {
      if (!this.temp.check_in) return "";

      const date = new Date(this.temp.check_in);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 12:00`;
    },
    formattedCheckOutDate() {
      if (!this.temp.check_out) return "";

      const date = new Date(this.temp.check_out);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 11:00`;
    },
    showImage() {
      if (!this.customer.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },
  },
  methods: {
    async get_business_sources() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      let { data } = await this.$axios.get("business-source-list", config);
      this.business_sources = data;
    },
    handleFullAddress(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
    },
    deleteItem(index) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms.splice(index, 1);
    },
    set_additional_charges() {
      this.temp.early_check_in = this.is_early_check_in
        ? this.additional_charges.early_check_in
        : 0;
      this.temp.late_check_out = this.is_late_check_out
        ? this.additional_charges.late_check_out
        : 0;

      this.temp.bed_amount = this.temp.extra_bed_qty
        ? this.temp.extra_bed_qty * this.additional_charges.extra_bed
        : 0;
    },
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },
    async get_food_plans() {
      let { data: foodplans } = await this.$axios.get(`foodplan-list`);

      this.foodplans = foodplans;
    },
    addOneDay(originalDate) {
      if (!originalDate) {
        return new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      date.setDate(date.getDate() + 1);

      return date.toISOString().split("T")[0];
    },

    nextTab() {
      // if (this.activeTab) {

      if (this.reservation.booking_status == 2) {
        if (
          this.customer.document == null ||
          this.customer.document == undefined
        ) {
          this.alert("Missing!", "Select document", "error");
          this.subLoad = false;
          return;
        }
      }

      if (this.room.type == "") {
        this.alert("oops", "Select Source Type", "error");

        return false;
      }
      this.activeTab += 1;
      // }
    },
    prevTab() {
      if (this.activeTab > 0) {
        this.activeTab -= 1;
      }
    },

    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.customer.image = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    preview(file) {
      if (file.name) {
        file = file.name;
      }
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    runAllFunctions() {
      this.getDays();
      this.subTotal();
      this.processCalculation();
    },

    getDays() {
      let ci = new Date(this.temp.check_in);
      let co = new Date(this.temp.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      if (days > 0) {
        return (this.room.total_days = days);
      }
    },

    get_reservation() {
      this.temp.room_id = this.reservation.room_id;
      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type;
      this.temp.price = this.reservation.price;
      this.temp.check_in = this.reservation.check_in;
      this.temp.check_out = this.reservation.check_out;
      this.temp.room_tax = this.reservation.total_tax;
      this.room.check_in = this.reservation.check_in;
      this.room.check_out = this.reservation.check_out;
      this.temp.priceList = this.reservation.priceList;
      this.get_cs_gst(this.temp.room_tax);
    },

    redirect() {
      this.$router.push("/");
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
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

    processCalculation() {
      let discount = parseFloat(this.temp.room_discount) || 0;
      let room_extra_amount = parseFloat(this.temp.room_extra_amount) || 0;
      let sub_total = parseFloat(this.room.sub_total) || 0;

      let advance_price = parseFloat(this.room.advance_price) || 0;

      let afterExtraAmount = sub_total + room_extra_amount;
      let afterDiscount = afterExtraAmount - discount;

      this.room.remaining_price = afterDiscount - advance_price;

      return (this.room.total_price = afterDiscount);
    },

    subTotal() {
      return (this.room.sub_total = this.priceListTableView.reduce(
        (total, num) => total + num.total_price,
        0
      ));
    },

    getType(val) {
      if (val == "Online") {
        this.isOnline = true;
        this.isCorporate = false;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isCorporate = false;
        this.isOnline = false;
        this.isAgent = true;
        return;
      }
      if (val == "Corporate") {
        this.isOnline = false;
        this.isAgent = false;
        this.isCorporate = true;
        return;
      }

      if (val == "Walking") {
        this.room.source = "walking";
      }

      if (val == "Complimentary") {
        this.room.source = "complimentary";
      }

      this.isOnline = false;
      this.isAgent = false;
    },

    get_room_types() {
      this.$axios
        .get(`room_type`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.roomTypes = data;
        });
    },

    get_gst(item, type) {
      // agent
      // online
      // corporate

      switch (type) {
        case "agent":
          this.customer.gst_number = this.agentList.find(
            (e) => e.name == item
          ).gst;
          break;
        case "online":
          this.customer.gst_number = this.sources.find(
            (e) => e.name == item
          ).gst;
          break;
        case "corporate":
          this.customer.gst_number = this.CorporateList.find(
            (e) => e.name == item
          ).gst;
          break;
        default:
          break;
      }
    },

    get_agents() {
      this.$axios
        .get(`get_agent`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.agentList = data;
        });
    },
    get_Corporate() {
      this.$axios
        .get(`get_corporate`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.CorporateList = data;
        });
    },

    get_online() {
      this.$axios
        .get(`get_online`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.sources = data;
        });
    },

    get_id_cards() {
      this.$axios
        .get(`get_id_cards`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.idCards = data;
        });
    },

    get_cs_gst(amount) {
      let gst = parseFloat(amount) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
    },
    selectRoom(item) {
      this.selectRoomLoading = true;

      let filterObject = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: item.name,
          room_no: item.room_no,
          checkin: this.temp.check_in,
          checkout: this.temp.check_out,
        },
      };

      this.$axios
        .get(`get_data_by_select_with_tax`, filterObject)
        .then(({ data }) => {
          this.selectRoomLoading = false;
          this.temp.room_type = item.name;
          this.temp.company_id = this.$auth.user.company.id;
          this.temp.price = data.total_price;
          this.temp.priceList = data.data;
          this.temp.room_tax = data.total_tax;

          this.get_cs_gst(data.total_tax);
        });
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
    },

    getFoodCalculation({ no_of_adult, no_of_child, food_plan_id }) {
      let selectedFP = this.foodplans.find((e) => e.id == food_plan_id);

      if (!selectedFP) {
        return null;
      }

      let total_members = no_of_adult + no_of_child;

      let { title, unit_price } = selectedFP;

      let food_plan_price =
        unit_price * no_of_adult + (unit_price * no_of_child) / 2;

      return {
        meal: "------",
        meal_name: `${title} (${food_plan_price})`,
        food_plan_price: food_plan_price,
        breakfast: selectedFP.breakfast ? total_members : 0,
        lunch: selectedFP.lunch ? total_members : 0,
        dinner: selectedFP.dinner ? total_members : 0,
      };
    },

    add_room({
      room_type,
      price,
      early_check_in,
      late_check_out,
      room_discount,
      room_extra_amount,
      bed_amount,
      priceList,
      no_of_adult,
      no_of_child,
    }) {
      if (!this.room_type_id || !this.multipleRoomId) {
        this.alert("Error!", "Room type or Room not selected", "error");
        return;
      }

      let selected_food_plan = this.getFoodCalculation(this.temp);

      if (!selected_food_plan) {
        this.alert("Error!", "Food plan not found!", "error");
        return;
      }

      let extras =
        early_check_in +
        late_check_out +
        bed_amount +
        selected_food_plan.food_plan_price;

      let sub_total =
        extras +
        price +
        parseFloat(room_extra_amount == "" ? 0 : room_extra_amount);

      let after_discount =
        sub_total - (room_discount == "" ? 0 : room_discount);

      let room_no = this.multipleRoomId.room_no;

      let isSelect = this.selectedRooms.find((e) => e.room_no == room_no);

      if (!isSelect) {
        let selectedRoomsForTableView = [];

        this.room.check_in = this.temp.check_in;
        this.room.check_out = this.temp.check_out;

        // console.log(after_discount);return;

        let payload = {
          ...this.temp,
          ...selected_food_plan,

          days: this.getDays(),
          room_discount: room_discount == "" ? 0 : room_discount,
          after_discount: after_discount,
          total: after_discount,
          grand_total: after_discount,
          room_no: this.multipleRoomId.room_no,
          room_id: this.multipleRoomId.id,
        };

        selectedRoomsForTableView.push(payload);
        this.selectedRooms.push(payload);

        this.runAllFunctions();
        this.alert("Success!", "success selected room", "success");
        this.isSelectRoom = false;
        this.RoomDrawer = false;

        let no_of_rooms = selectedRoomsForTableView.length || 0;

        let arrToMerge = priceList.map((e) => ({
          ...e,
          ...selected_food_plan,
          no_of_rooms,
          room_type,
          no_of_adult,
          no_of_child,
          early_check_in,
          late_check_out,
          bed_amount,
          total_price: after_discount * no_of_rooms,
        }));

        this.priceListTableView = this.mergeEntries(
          this.priceListTableView.concat(arrToMerge)
        );
      }
    },

    mergeEntries(entries) {
      const result = [];

      entries.forEach((entry) => {
        const existingEntry = result.find(
          (e) => e.room_type === entry.room_type && e.date === entry.date
        );

        if (existingEntry) {
          existingEntry.no_of_rooms += entry.no_of_rooms;
          existingEntry.total_price += entry.total_price;
        } else {
          result.push({ ...entry });
        }
      });

      return result;
    },

    get_available_rooms(item) {
      if (this.temp.check_in == undefined || this.temp.check_out == undefined) {
        alert("Please select date");
        this.RoomDrawer = false;
        return;
      }

      this.RoomDrawer = true;
      this.$axios
        .get(`get_available_rooms_by_date_and_room_type`, {
          params: {
            check_in: this.temp.check_in,
            check_out: this.temp.check_out,
            room_type_id: item.id,
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.availableRooms = data;
        });
      this.runAllFunctions();
    },

    get_customer() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.search.mobile;
      if (contact_no == undefined || contact_no == "") {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }

      this.$axios
        .get(`get_customer/${contact_no}`, {
          params: {
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            return;
          }

          this.customer.contact_no = data.data.customer.contact_no;
          this.customer.whatsapp = data.data.customer.whatsapp;
          this.customer.title = data.data.customer.title;
          this.customer.first_name = data.data.customer.first_name;
          this.customer.last_name = data.data.customer.last_name;
          this.customer.email = data.data.customer.email;
          this.customer.company_id = data.data.customer.company_id;
          this.customer.country = data.data.customer.country;
          this.customer.state = data.data.customer.state;
          this.customer.city = data.data.customer.city;
          this.customer.zip_code = data.data.customer.zip_code;

          this.customer = data.data;

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },

    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    store() {
      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }

      if (this.reservation.booking_status == 2) {
        if (
          this.customer.document == null ||
          this.customer.document == undefined
        ) {
          this.alert("Missing!", "Select document", "error");
          this.subLoad = false;
          return;
        }
      }

      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.alert("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
      }

      if (this.reservation.booking_status == 2) {
        this.room.booking_status = 2;
      }

      let rooms = this.selectedRooms.map((e) => e.room_no);
      this.room.rooms = rooms.toString();
      this.$axios
        .post("/booking_validate1", {
          ...this.room,
          ...this.customer,
        })
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert(
              "No reservation created!",
              "Some fields are missing or invalid",
              "error"
            );
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.errors = [];
            this.store_booking();
          }
        })
        .catch((e) => console.log(e));
    },

    store_booking() {
      let quotaion = {
        book_date: this.reservation.check_in,
        arrival_date: this.room.check_in,
        departure_date: this.room.check_out,
        sub_total: this.subTotal(),
        discount: this.room.discount,
        tax: this.selectedRooms.reduce(
          (acc, { room_tax }) => acc + room_tax,
          0
        ),
        total: this.processCalculation(),
        customer: this.customer,
      };

      this.subLoad = false;

      this.$axios
        .post("/quotation", quotaion)
        .then(({ data }) => {
          this.loading = false;
          this.alert("Success!", "Quotation has been created", "success");
          this.selectedRooms = [];
          this.priceListTableView = [];
          this.room_type_id = {};
          this.dialog = false;
        })
        .catch((e) => {
          this.errors = data.errors;
          this.subLoad = false;
        });
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
