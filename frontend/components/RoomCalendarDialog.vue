<template>
  <div>
    <v-dialog persistent v-model="RoomDrawer" max-width="400">
    <AssetsIconClose left="390" @click="close" />
    <v-card>
      <v-alert flat class="grey lighten-3 white--text" dense>
        <span class="text-color">Calendar Booking</span>
        </v-alert
      >
      <v-card-text>
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
                @input="addOneDay(temp.check_in)"
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
                v-model="temp.check_out"
                no-title
                @input="checkout_menu = false"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              readonly
              label="Room Type"
              outlined
              dense
              hide-details
              item-value="id"
              item-text="name"
              v-model="room_type_object"
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
              readonly
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
          <v-col cols="4">
            <v-autocomplete
              label="Adult"
              :items="[0, 1, 2, 3]"
              dense
              outlined
              v-model="temp.no_of_adult"
              :hide-details="true"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="4">
            <v-autocomplete
              label="Child"
              :items="[0, 1, 2, 3]"
              dense
              outlined
              v-model="temp.no_of_child"
              :hide-details="true"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="4">
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

          <v-col cols="12" class="text-center">
            <AssetsButton
              :options="{
                label: `Confirm Room`,
                color: `blue`,
              }"
              @click="add_room(temp)"
            />
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
  </v-dialog>
  </div>
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["label", "reservation", "Independent"],
  data() {
    return {
      Model: "Reservation",
      additional_charges: {},
      is_early_check_in: false,
      is_late_check_out: false,
      dialog: false,
      foodplans: [],
      multipleRoomId: null,
      checkin_menu: false,
      checkout_menu: false,
      room_type_object: null,
      loading: false,
      selectRoomLoading: false,
      RoomDrawer: null,
      isSelectRoom: true,
      preloader: false,
      loading: false,
      roomTypes: [],
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      priceListTableView: [],
      temp: {
        food_plan_price: 0,
        extra_bed_qty: 0,
        food_plan_id: 0,
        early_check_in: 0,
        late_check_out: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
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

        no_of_adult: null,
        no_of_child: 0,
        no_of_baby: 0,
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      },
    };
  },
  async created() {
    this.room_type_object = {
      id: this.reservation.room_type_id,
      name: this.reservation.room_type,
    };

    this.multipleRoomId = {
      id: this.reservation.room_id,
      room_no: this.reservation.room_no,
    };
    this.temp = {
      ...this.temp,
      check_in: this.reservation.check_in,
      check_out: this.reservation.check_out,
      priceList: this.reservation.priceList,
      room_no: this.reservation.room_no,
      room_id: this.reservation.room_id,
      room_type: this.reservation.room_type,
      price: this.reservation.price,
      total_price: this.reservation.total_price,
    };

    this.get_available_rooms({ id: this.reservation.room_type_id });

    this.get_room_types();
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
    close() {
      this.temp = {
        food_plan_price: 0,
        extra_bed_qty: 0,
        food_plan_id: 0,
        early_check_in: 0,
        late_check_out: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
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

        no_of_adult: null,
        no_of_child: 0,
        no_of_baby: 0,
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      };

      this.room_type_object = null;
      this.multipleRoomId = null;
      this.is_early_check_in = false;
      this.is_late_check_out = false;
      this.extra_bed = 0;

      this.RoomDrawer = false;
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
        this.temp.check_out = new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      date.setDate(date.getDate() + 1);

      this.temp.check_out = date.toISOString().split("T")[0];

      this.checkin_menu = false;
    },

    getDays() {
      let ci = new Date(this.temp.check_in);
      let co = new Date(this.temp.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      return days;
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
      extra_bed_qty,
      bed_amount,
      priceList,
      no_of_adult,
      no_of_child,
    }) {
      if (!this.room_type_object || !this.multipleRoomId) {
        this.alert("Error!", "Room type or Room not selected", "error");
        return;
      }

      if (!no_of_adult) {
        this.alert("Error!", "Adult must be selected", "error");
        return;
      }

      let selected_food_plan = this.getFoodCalculation(this.temp);

      if (!selected_food_plan) {
        this.alert("Error!", "Food plan not found!", "error");
        return;
      }

      let selectedRoomsForTableView = [];

      let meal_price = selected_food_plan.food_plan_price * this.getDays();

      let arrToMerge = priceList.map((e) => ({
        room_no: this.multipleRoomId.room_no,
        room_id: this.multipleRoomId.id,
        ...e,
        day: this.getFullDay(e.day),
        ...selected_food_plan,
        room_type,
        no_of_adult,
        no_of_child,
        early_check_in,
        late_check_out,
        bed_amount,
        extra_bed_qty,
        total_price:
          e.price +
          early_check_in +
          late_check_out +
          bed_amount +
          selected_food_plan.food_plan_price,
      }));

      let payload = {
        ...this.temp,
        ...selected_food_plan,
        food_plan_price: meal_price,
        days: this.getDays(),
        total_price:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        grand_total:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        room_no: this.multipleRoomId.room_no,
        room_id: this.multipleRoomId.id,
        room_type_object: this.room_type_object,
        priceList: arrToMerge,
      };

      selectedRoomsForTableView.push(payload);
      this.selectedRooms.push(payload);

      selectedRoomsForTableView.push(payload);

      this.alert("Success!", "success selected room", "success");
      this.isSelectRoom = false;

      this.$emit("tableData", {
        arrToMerge,
        payload,
      });
      this.close();
    },
    getFullDay(day) {
      let json = {
        Mon: "Monday",
        Tue: "Tuesday",
        Wed: "Wednesday",
        Thu: "Thursday",
        Fri: "Friday",
        Sat: "Saturday",
        Sun: "Sunday",
      };

      return json[day] ?? "unknown";
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
          this.selectRoom({
            name: this.reservation.room_type,
            room_no: this.reservation.room_no,
          });
          this.getDays();
        });
    },
    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
