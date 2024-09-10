<template>
  <v-dialog persistent v-model="RoomDrawer" max-width="400">
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        small
        color="primary"
        class="white--text"
        dark
        v-bind="attrs"
        v-on="on"
      >
        <v-icon color="white" small> mdi-plus </v-icon> {{ label }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar flat class="primary white--text" dense>
        Hall Booking <v-spacer></v-spacer
        ><v-icon @click="close" color="white">mdi-close</v-icon></v-toolbar
      >
      <v-container>
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
          <v-col cols="12">
            <v-text-field
              label="Function Name"
              dense
              outlined
              v-model.number="temp.function_name"
              :hide-details="true"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-text-field
              label="No of Pax"
              dense
              outlined
              v-model.number="temp.no_of_adult"
              :hide-details="true"
              type="number"
            ></v-text-field>
          </v-col>
          <!-- <v-col>
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
          </v-col> -->
          <v-col cols="12">
            <v-btn block @click="selectRoom" color="primary" small>
              Confirm Hall
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
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
  props: ["label"],
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
        food_plan_price: 0,
        food_plan_id: 0,
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
        function_name: null,
        
        
        
        
        priceList: [],
      },

      defaultTemp: {
        total_booking_hours: 0,
        extra_hours_charges: 0,
        food_plan_price: 0,
        food_plan_id: 0,
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
        function_name:null,
        
        
        
        
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
    };
  },
  async created() {
    this.get_room_types();
    await this.get_food_plans();
    this.get_reservation();
    this.runAllFunctions();
    this.preloader = false;
    this.calculateHoursQty();
  },
  computed: {
    formattedCheckInDateTime() {
      return this.temp.check_in + " " + this.temp.check_in_time;
    },
    formattedCheckOutDateTime() {
      return this.temp.check_out + " " + this.temp.check_out_time;
    },
  },
  methods: {
    close() {
      this.temp = {
        total_booking_hours: 0,
        extra_hours_charges: 0,
        food_plan_price: 0,
        food_plan_id: 0,
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
        function_name:null,
        priceList: [],
      };

      this.room_type_list = {
        id: ``,
        name: `Select Hall Type`,
      };

      this.is_cleaning_charges = false;
      this.is_generator_charges = false;
      this.is_audio_charges = false;
      this.is_projector_charges = false;
      this.is_electricity_charges = false;
      this.get_reservation();
      this.calculateHoursQty();
      this.RoomDrawer = false;
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

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
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

    checkRoomAvailability(room_type) {
      if (!this.availableRooms.length) {
        this.room_type_list = {
          id: ``,
          name: `Select Hall Type`,
        };
        this.alert("Warning!", `"${room_type}" is already booked`, "error");
        return false;
      }
      return true;
    },

    confirm_hall({
      price,
      room_discount,
      room_extra_amount,
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

      //   let foodplan = this.foodplans.find((e) => e.id == this.temp.food_plan_id);

      //   if (!foodplan) {
      //     this.alert("Warning!", `Food Plan must be selected`, "error");
      //     return;
      //   }

      let extra_hours = total_booking_hours - hall_min_hours;

      let extra_booking_hours_charges = extra_hours * extra_hours_charges;

      extra_booking_hours_charges =
        extra_booking_hours_charges < 0 ? 0 : extra_booking_hours_charges;

      let extras =
        parseFloat(cleaning) +
        parseFloat(electricity) +
        parseFloat(generator) +
        parseFloat(audio) +
        parseFloat(projector) +
        parseFloat(extra_booking_hours_charges);

      let sub_total =
        price +
        extras +
        parseFloat(room_extra_amount == "" ? 0 : room_extra_amount);

      let after_discount =
        sub_total - (room_discount == "" ? 0 : room_discount);

      let payload = {
        ...this.temp,
        days: this.getDays(),
        room_discount: room_discount == "" ? 0 : room_discount,
        after_discount: after_discount,
        total: after_discount,
        grand_total: after_discount,
        room_no,
        room_id,
        extra_hours,
        extra_booking_hours_charges,
        check_in: this.formattedCheckInDateTime,
        check_out: this.formattedCheckOutDateTime,
      };

      this.runAllFunctions();
      this.$emit("tableData", payload);
      this.close();
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


    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
