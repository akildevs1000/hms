<template>
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
        @change="selectRoom({ name: temp.room_type, room_no: temp.room_no })"
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
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["label"],
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
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,
      advanceDialog: false,
      selectRoomLoading: false,
      roomTab: null,
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
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

      extraPayType: "",
      allFood: [],

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
    this.runAllFunctions();
    // this.getImage();
    this.preloader = false;

    await this.get_food_plans();

    await this.get_additional_charges();
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
  },
  methods: {
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
        meal_name: title,
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
        alert("Room type or Room not selected");
        return;
      }

      let selected_food_plan = this.getFoodCalculation(this.temp);

      if (!selected_food_plan) {
        alert("Food plan not found");
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

      let selectedRoomsForTableView = [];

      this.room.check_in = this.temp.check_in;
      this.room.check_out = this.temp.check_out;

      let meal_price = selected_food_plan.food_plan_price * this.getDays();

      let payload = {
        ...this.temp,
        ...selected_food_plan,
        food_plan_price: meal_price,
        days: this.getDays(),
        room_discount: room_discount == "" ? 0 : room_discount,
        after_discount:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        total_price:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        grand_total:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        room_no: this.multipleRoomId.room_no,
        room_id: this.multipleRoomId.id,
      };

      selectedRoomsForTableView.push(payload);
      this.selectedRooms.push(payload);

      selectedRoomsForTableView.push(payload);

      this.runAllFunctions();
      // this.alert("Success!", "success selected room", "success");
      // alert();
      this.isSelectRoom = false;

      let arrToMerge = priceList.map((e) => ({
        ...e,
        ...selected_food_plan,
        room_type,
        no_of_adult,
        no_of_child,
        early_check_in,
        late_check_out,
        bed_amount,
        extras,
        room_price_with_tax:e.price,
        total_price:
          e.price +
          early_check_in +
          late_check_out +
          bed_amount +
          selected_food_plan.food_plan_price,
      }));
      this.$emit("emitRoomData", {
        arrToMerge,
        payload,
      });
      this.RoomDrawer = false;
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
    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
