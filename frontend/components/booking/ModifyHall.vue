<template>
  <div v-if="can('calendar_create')">
    <v-dialog v-model="RoomDrawer" max-width="400">
      <template v-slot:activator="{ on, attrs }">
        <span v-bind="attrs" v-on="on"> Modify Booking </span>
      </template>
      <v-card>
        <v-toolbar flat class="primary white--text" dense>
          Hall Booking <v-spacer></v-spacer
          ><v-icon @click="RoomDrawer = false" color="white"
            >mdi-close</v-icon
          ></v-toolbar
        >
        <v-container>
          <!-- <pre>
            {{ restResponse }}
          </pre> -->
          <v-row>
            <v-col cols="6">
              <v-menu
                v-model="checkin_date_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check In Date"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="temp.check_in"
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
                  @input="checkin_date_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="6">
              <v-menu
                ref="menu"
                v-model="checkin_time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="temp.check_in_time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    v-model="temp.check_in_time"
                    label="Check In Time"
                    append-icon="mdi-clock-time-four-outline"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    hide-details
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="checkin_time_menu"
                  v-model="temp.check_in_time"
                  no-title
                  @click:minute="$refs.menu.save(temp.check_in_time)"
                  format="24hr"
                ></v-time-picker>
              </v-menu>
            </v-col>
            <v-col cols="6">
              <v-menu
                v-model="checkout_date_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check Out Date"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="temp.check_out"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  :max="addOneDay(temp.check_in)"
                  :min="temp.check_in"
                  v-model="temp.check_out"
                  no-title
                  @input="checkout_date_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="6">
              <v-menu
                ref="menu2"
                v-model="checkout_time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="temp.check_out_time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    outlined
                    dense
                    v-model="temp.check_out_time"
                    label="Check Out Time"
                    append-icon="mdi-clock-time-four-outline"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    hide-details
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="checkout_time_menu"
                  v-model="temp.check_out_time"
                  no-title
                  @click:minute="
                    () => {
                      $refs.menu2.save(temp.check_out_time);
                      calculateHoursQty();
                    }
                  "
                  format="24hr"
                ></v-time-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-text-field
                readonly
                label="Total Hours"
                outlined
                hide-details
                outline
                dense
                v-model="temp.total_booking_hours"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                label="Hall"
                outlined
                dense
                hide-details
                item-value="id"
                item-text="name"
                v-model="room_type_list"
                @change="
                  ($event) => {
                    get_available_rooms($event);
                  }
                "
                :items="[{ id: ``, name: `Select Hall Type` }, ...roomTypes]"
                return-object
              ></v-autocomplete>
            </v-col>
            <!-- <v-col cols="12">
                <v-autocomplete
                  v-model="temp.room_no"
                  hide-details
                  :items="availableRooms"
                  item-value="room_no"
                  item-text="room_no"
                  label="Select Hall"
                  dense
                  outlined
                >
                </v-autocomplete>
              </v-col> -->
            <v-col cols="6">
              <v-text-field
                label="Adult Per Room"
                dense
                outlined
                v-model.number="temp.no_of_adult"
                :hide-details="true"
                type="number"
              ></v-text-field>
            </v-col>
            <v-col cols="6">
              <v-text-field
                label="Child Per Room"
                dense
                outlined
                v-model.number="temp.no_of_child"
                :hide-details="true"
                type="number"
              ></v-text-field>
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
                :items="[{ id: ``, title: `Select Food Plan` }, ...foodplans]"
              ></v-autocomplete>
            </v-col>
            <v-col>
              <v-checkbox
                v-model="is_cleaning_charges"
                :label="`Cleaning`"
                :hide-details="true"
                dense
              >
              </v-checkbox>
            </v-col>
            <v-col
              ><v-checkbox
                v-model="is_electricity_charges"
                :label="`Electricity`"
                :hide-details="true"
                dense
              >
              </v-checkbox>
            </v-col>
            <v-col
              ><v-checkbox
                v-model="is_generator_charges"
                :label="`Generator `"
                :hide-details="true"
                dense
              >
              </v-checkbox>
            </v-col>

            <v-col
              ><v-checkbox
                v-model="is_audio_charges"
                :label="`Audio `"
                :hide-details="true"
                dense
              >
              </v-checkbox>
            </v-col>

            <v-col
              ><v-checkbox
                v-model="is_projector_charges"
                :label="`Projector Charges`"
                :hide-details="true"
                dense
              >
              </v-checkbox>
            </v-col>
            <v-col cols="12">
             <table>
              <tr>
                <td>Old Grand Total</td>
                <td class="text-right">
                  {{ old.booking_total_price }}
                </td>
              </tr>
             </table>
            <!-- <pre>{{ old }}</pre> -->
            </v-col>
            <v-col cols="12">
              <v-btn block @click="selectRoom" color="primary" small>
                Confirm Hall
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
const today = new Date();
const tomorrow = new Date(today);
const checkoutTime = new Date(today); // Copy check-in time

tomorrow.setDate(tomorrow.getDate() + 1);
function formatTime(date) {
  let hours = date.getHours().toString().padStart(2, "0");
  let minutes = date.getMinutes().toString().padStart(2, "0");
  return `${hours}:${minutes}`;
}
checkoutTime.setHours(today.getHours() + 4);

export default {
  props: ["BookedRoomId"],

  data() {
    return {
      additional_charges: {},
      is_cleaning_charges: false,
      is_electricity_charges: false,
      is_generator_charges: false,
      is_audio_charges: false,
      is_projector_charges: false,
      dialog: false,
      foodplans: [],
      checkin_date_menu: false,
      checkout_date_menu: false,
      checkin_time_menu: false,
      checkout_time_menu: false,
      room_type_list: ``,
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
      RoomDrawer: false,
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
        total_booking_hours: 0,
        extra_hours_charges: 0,
        food_plan_price: 1,
        food_plan_id: 1,
        cleaning: 0,
        electricity: 0,
        generator: 0,
        audio: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        check_in_time: null,
        check_out_time: null,
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

      defaultTemp: {
        food_plan_price: 1,
        food_plan_id: 1,
        cleaning: 0,
        electricity: 0,
        generator: 0,
        audio: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        check_in_time: null,
        check_out_time: null,
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
        customer_type: `Walking`,
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
        check_out: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_in_time: formatTime(today), // show time like mm:ss
        check_out_time: formatTime(checkoutTime), // show time like mm:ss
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
      bookingResponse: null,
      customerResponse: null,
      roomResponse: null,
      restResponse: null,
      old: {
        room_price: 0,
        food_plan_price: 0,
        booking_total_price: 0,
        room_id: 0,
        room_type_id: 0,
      },
    };
  },
  async created() {
    await this.get_booking();

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

    this.calculateHoursQty();

    this.get_business_sources();
  },
  computed: {
    formattedCheckInDateTime() {
      return this.temp.check_in + " " + this.temp.check_in_time;
    },
    formattedCheckOutDateTime() {
      return this.temp.check_out + " " + this.temp.check_out_time;
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
    async get_booking() {
      let payload = {
        params: {
          id: this.BookedRoomId,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booking_for_modify`, payload).then(({ data }) => {
        let { booking, customer, room, ...rest } = data;

        this.old = {
          room_price: rest.price,
          food_plan_price: rest.food_plan_price,
          booking_total_price: booking.total_price,
          room_id: room.id,
          room_type_id: room.room_type_id,
        };

        let { cleaning, electricity, generator, audio, projector } = rest;

        this.temp.cleaning = ~~cleaning;
        this.temp.generator = ~~generator;
        this.temp.audio = ~~audio;
        this.temp.projector = ~~projector;
        this.temp.electricity = ~~electricity;

        this.is_cleaning_charges = ~~cleaning == 0 ? false : true;
        this.is_generator_charges = ~~cleaning == 0 ? false : true;
        this.is_audio_charges = ~~cleaning == 0 ? false : true;
        this.is_projector_charges = ~~cleaning == 0 ? false : true;
        this.is_electricity_charges = ~~cleaning == 0 ? false : true;

        this.temp.check_in = rest.checkin_date_only;
        this.temp.check_out = rest.checkout_date_only;
        this.temp.check_in_time = rest.check_in_time;
        this.temp.check_out_time = rest.check_out_time;
        this.temp.no_of_adult = rest.no_of_adult;
        this.temp.no_of_child = rest.no_of_child;
        this.temp.food_plan_id = rest.food_plan_id;
        this.room_type_list = data.room.room_type;
        this.temp.room_id = room.id;
        this.temp.room_no = room.room_no;
        this.temp.room_type = room.room_type.name;

        // this.bookingResponse = booking;
        // this.customerResponse = customer;
        // this.roomResponse = room;
        // this.restResponse = rest;
      });
    },
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
    calculateHoursQty() {
      // Define the two time strings
      const startTime = this.temp.check_in_time;
      const endTime = this.temp.check_out_time;
      // Define the two time strings
      // Split the time strings into hours and minutes
      const [startHours, startMinutes] = startTime.split(":").map(Number);
      const [endHours, endMinutes] = endTime.split(":").map(Number);

      // Create Date objects for both times (use a common date)
      const date = new Date(); // Use today's date

      const start = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate(),
        startHours,
        startMinutes
      );
      let end = new Date(
        date.getFullYear(),
        date.getMonth(),
        date.getDate(),
        endHours,
        endMinutes
      );

      // Check if the end time is earlier than the start time, indicating it falls on the next day
      if (end < start) {
        // Add 24 hours (in milliseconds) to the end time
        end = new Date(end.getTime() + 24 * 60 * 60 * 1000);
      }

      const differenceInMs = end - start;

      this.temp.total_booking_hours = Math.ceil(
        differenceInMs / (1000 * 60 * 60)
      );

      this.checkout_time_menu = false;
    },
    deleteItem(index) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms.splice(index, 1);
    },

    async get_food_plans() {
      let { data: foodplans } = await this.$axios.get(`foodplan-list`, {
        params: {
          company_id: this.$auth.user.company_id,
          is_for_hall: 1,
        },
      });

      this.foodplans = foodplans;
    },
    addOneDay(originalDate) {
      if (!originalDate) {
        return new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      this.temp.check_out = date.toISOString().split("T")[0];

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
    store_document_new() {
      this.documentDialog = false;
      return;
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

      this.temp.check_in_time = this.reservation.check_in_time;
      this.temp.check_out_time = this.reservation.check_out_time;

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
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          type: "hall",
        },
      };
      this.$axios.get(`room_type_for_hall`, payload).then(({ data }) => {
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
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },
    get_Corporate() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_corporate`, payload).then(({ data }) => {
        this.CorporateList = data;
      });
    },

    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
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

    get_cs_gst(amount) {
      let gst = parseFloat(amount) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
    },

    selectRoom() {
      this.selectRoomLoading = true;

      if (!this.temp.room_type) {
        this.alert("Warning", "Hall must be selected");
        return;
      }

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: this.temp.room_type,
          room_no: this.temp.room_no,
          checkin: this.temp.check_in,
          checkout: this.temp.check_out,
        },
      };

      this.$axios.get(`get_hall_pricing_list`, payload).then(({ data }) => {
        this.temp.hall_min_hours = data.room_type_data.hall_min_hours;
        this.temp.extra_hours_charges = data.room_type_data.extra_hours_charges;

        this.temp.cleaning = this.is_cleaning_charges
          ? data.room_type_data.cleaning_charges
          : 0;

        this.temp.generator = this.is_generator_charges
          ? data.room_type_data.generator_charges
          : 0;

        this.temp.audio = this.is_audio_charges
          ? data.room_type_data.audio_charges
          : 0;

        this.temp.projector = this.is_projector_charges
          ? data.room_type_data.projector_charges
          : 0;

        this.temp.electricity = this.is_electricity_charges
          ? data.room_type_data.electricity_charges
          : 0;

        this.selectRoomLoading = false;
        this.temp.company_id = this.$auth.user.company.id;
        this.temp.price = data.total_price;
        this.temp.priceList = data.data;
        this.temp.room_tax = data.total_tax;
        this.get_cs_gst(data.total_tax);

        this.confirm_hall(this.temp);
      });
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
    },

    checkRoomAvailability(room_type) {
      // if (!this.availableRooms.length) {
      //   this.room_type_list = {
      //     id: ``,
      //     name: `Select Hall Type`,
      //   };
      //   this.alert("Warning!", `"${room_type}" is already booked`, "error");
      //   return false;
      // }
      return true;
    },

    confirm_hall({
      price,
      room_discount,
      room_extra_amount,
      priceList,
      no_of_adult,
      no_of_child,
      room_type,
      room_id,
      room_no,
      hall_min_hours,
      cleaning,
      generator,
      audio,
      projector,
      electricity,
      total_booking_hours,
      extra_hours_charges,
    }) {
      if (!this.checkRoomAvailability(room_type)) {
        return;
      }

      let foodplan = this.foodplans.find((e) => e.id == this.temp.food_plan_id);

      if (!foodplan) {
        this.alert("Warning!", `Food Plan must be selected`, "error");
        return;
      }

      let extra_hours = total_booking_hours - hall_min_hours;

      let extra_booking_hours_charges = extra_hours * extra_hours_charges;

      extra_booking_hours_charges =
        extra_booking_hours_charges < 0 ? 0 : extra_booking_hours_charges;

      let selectedRoomsForTableView = [];

      let adult_food_charges = foodplan.unit_price * no_of_adult;
      let child_food_charges = (foodplan.unit_price * no_of_child) / 2;
      let total_food_charges = adult_food_charges + child_food_charges;

      let extras =
        parseFloat(cleaning) +
        parseFloat(electricity) +
        parseFloat(generator) +
        parseFloat(audio) +
        parseFloat(projector) +
        parseFloat(extra_booking_hours_charges) +
        parseFloat(total_food_charges);

      let sub_total =
        price +
        extras +
        parseFloat(room_extra_amount == "" ? 0 : room_extra_amount);

      let after_discount =
        sub_total - (room_discount == "" ? 0 : room_discount);

      this.room.check_in = this.formattedCheckInDateTime;
      this.room.check_out = this.formattedCheckOutDateTime;

      let payload = {
        ...this.temp,
        meal: "------",
        days: this.getDays(),
        room_discount: room_discount == "" ? 0 : room_discount,
        after_discount: after_discount,
        total: after_discount,
        grand_total: after_discount,
        room_no,
        room_id,
        extra_hours,
        extra_booking_hours_charges,
      };

      console.log(payload);

      return;

      if (!isSelect) {
        selectedRoomsForTableView.push(payload);
        this.selectedRooms.push(payload);

        this.runAllFunctions();
        this.alert("Success!", "success selected room", "success");
        this.isSelectRoom = false;
        this.RoomDrawer = false;

        let arrToMerge = priceList.map((e) => ({
          ...e,
          price_with_meal: e.price + total_food_charges,
          no_of_rooms: selectedRoomsForTableView.length,
          room_type,
          no_of_adult,
          no_of_child,
          meal_name: `${foodplan.title} (${total_food_charges})`,
          extras,

          cleaning,
          electricity,
          generator,
          audio,
          projector,

          extra_booking_hours_charges,

          total_price: (e.price + extras) * selectedRoomsForTableView.length,
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
      this.temp.room_type = item.name;

      if (item.id == ``) {
        this.alert(
          "Warning!",
          `"${this.temp.room_type}" must be selected`,
          "error"
        );
        return;
      }

      this.$axios
        .get(`get_available_rooms_by_date_and_room_type`, {
          params: {
            check_in: this.temp.check_in,
            check_out: this.temp.check_out,
            room_type_id: item.id,
            type: "hall",
            company_id: this.$auth.user.company.id,
          },
        })
        .then(({ data }) => {
          this.availableRooms = data;

          if (!this.checkRoomAvailability(this.temp.room_type)) {
            return;
          }

          this.temp.room_id = data[0].id || "";
          this.temp.room_no = data[0].room_no || "";
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
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer/${contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            // this.customer = {};
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            return;
          }

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };
          this.customer.id_card_type_id = parseInt(
            this.customer.id_card_type_id
          );

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
      let payload = {
        ...this.room,
        ...this.customer,
      };
      this.$axios
        .post("/booking_validate1", payload)
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
      alert(`Hall Modify is Pending`);
      return 
      let payload = {
        ...this.room,
        customer_type: this.customer.customer_type,
        selectedRooms: this.selectedRooms,
        ...this.customer,
        user_id: this.$auth.user.id,
      };

      this.subLoad = false;

      this.$axios
        .post("/hall-booking", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.selectedRooms = [];
            this.priceListTableView = [];
            this.room_type_list = {};
            this.$emit(`close-calender-hall`);
            this.dialog = false;

            // this.store_document(data.data);
            // this.alert("Success!", "Successfully room added", "success");

            // if (this.reservation.booking_status == 2) {
            //   this.$router.push("/reservation/in_house");
            //   return "";
            // }
            // this.$router.push("/");
          }
        })
        .catch((e) => console.log(e));
    },

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.room.document);
      payload.append("image", this.customer.image);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          }
        })
        .catch((e) => console.log(e));
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
<style scoped src="@/assets/css/tableStyles.css"></style>
