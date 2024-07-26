<template>
  <v-dialog v-model="dialog" width="400">
    <template v-slot:activator="{ on, attrs }">
      <v-btn v-bind="attrs" v-on="on" color="primary" small>
        <v-icon color="white" small>mdi-plus</v-icon>
        Room
      </v-btn>
    </template>

    <v-card>
      <v-toolbar flat class="primary white--text" dense>
        Group Booking <v-spacer></v-spacer
        ><v-icon @click="dialog = false" color="white"
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
                v-model="payload.check_in"
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
                :min="addOneDay(payload.check_in)"
                v-model="payload.check_out"
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
              v-model="payload.room_type_id"
              @change="getRoomsByCategory(payload.room_type_id)"
              :items="room_types"
            ></v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              multiple
              label="Number of Rooms"
              :items="rooms"
              item-text="label"
              item-value="id"
              dense
              outlined
              v-model="payload.room_ids"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              label="Adult Per Room"
              :items="[0, 1, 2, 3]"
              dense
              outlined
              v-model="payload.adult_per_room"
              :hide-details="true"
            ></v-autocomplete>
          </v-col>
          <v-col cols="12">
            <v-autocomplete
              label="Child Per Room"
              :items="[0, 1, 2, 3]"
              dense
              outlined
              v-model="payload.child_per_room"
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
              v-model="payload.food_plan_id"
              :items="foodplans"
            ></v-autocomplete>
          </v-col>
          <v-col cols="6">
            <v-checkbox
              v-model="payload.early_check_in"
              label="Early Check In"
              :hide-details="true"
              dense
            >
            </v-checkbox>
          </v-col>
          <v-col cols="6"
            ><v-checkbox
              v-model="payload.late_check_out"
              label="Late Check Out"
              :hide-details="true"
              dense
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
              v-model="payload.extra_bed"
              :hide-details="true"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-btn
              block
              class="primary mt-2"
              small
              @click="confirm"
              :loading="loading"
              >Confirm</v-btn
            >
          </v-col>
        </v-row>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);

export default {
  props: {
    isDrawer: {
      default: false,
    },
  },
  data() {
    return {
      checkin_menu: false,
      checkout_menu: false,
      room_types: [],
      rooms: [],

      payload: {
        roomType: "",
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        room_type_id: 1,
        room_ids: [],
        adult_per_room: 1,
        child_per_room: 1,
        early_check_in: true,
        late_check_out: true,
        extra_bed: 0,
        food_plan_id: 1,
        company_id: 0,
        user_id: 0,
      },
      dialog: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      reference: "",
      errors: [],
      bookingResponse: null,
      roomPriceResponse: null,

      foodplans: [],
      fullJson: {
        payload: null,
        displayPayload: null,
      },
    };
  },

  async created() {
    this.preloader = false;

    let { data: room_types } = await this.$axios.get(
      `room_type?company_id=${this.$auth.user.company_id}`
    );
    let { data: foodplans } = await this.$axios.get(`foodplan-list`);

    this.foodplans = foodplans;

    this.room_types = room_types;

    this.payload.room_type_id = room_types[0].id || 1;

    await this.getRooms(this.payload.room_type_id);
  },

  methods: {
    async getRoomsByCategory(room_type_id) {
      await this.getRooms(room_type_id);
    },
    async getRooms(room_type_id) {
      let { data: rooms } = await this.$axios.get(
        `get_available_rooms_for_modify`,
        {
          params: {
            company_id: this.$auth.user.company.id,
            room_type_id: room_type_id,
          },
        }
      );

      this.rooms = rooms.map((e) => ({
        room_type_id: e.room_type_id,
        id: e.id,
        room_no: e.room_no,
        roomType: e.room_type.name,
        label: `${e.room_no} ${e.room_type.name}`,
      }));

      this.payload.room_ids = this.rooms.map((e) => e.id);
    },

    async getPricesByRoomId(id) {
      this.roomPriceResponse = [];
      let found = this.rooms.find((e) => e.id == id);
      if (!found) return;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: found.roomType,
          room_no: found.room_no,
          checkin: this.payload.check_in,
          checkout: this.payload.check_out,
        },
      };
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return;
          }
          this.payload.total_price = data.total_price;
        });
    },
    addOneDay(originalDate) {
      if (!originalDate) {
        return new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      date.setDate(date.getDate() + 1);

      return date.toISOString().split("T")[0];
    },
    async confirm() {
      let roomType = this.room_types.find(
        (e) => e.id == this.payload.room_type_id
      );

      let config = {
        params: {
          ...this.payload,
          company_id: this.$auth.user.company.id,
          user_id: this.$auth.user.id,
          roomType: roomType.name,
          checkin: this.payload.check_in,
          checkout: this.payload.check_out,
          is_late_check_out: this.payload.late_check_out ? 1 : 0,
          is_early_check_in: this.payload.early_check_in ? 1 : 0,
          check_in_display: this.formattedCheckinDate + " PM",
          check_out_display: this.formattedCheckOutDate + " AM",
          formattedCheckinDate: this.formattedCheckinDate,
          formattedCheckOutDate: this.formattedCheckOutDate,
        },
      };

      let { data } = await this.$axios.get(`get_room_current_price`, config);

      this.$emit("group_booking_rooms", data);

      this.dialog = false;
    },
  },

  mounted() {},

  computed: {
    formattedCheckinDate() {
      if (!this.payload.check_in) return "";

      const date = new Date(this.payload.check_in);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 12:00`;
    },
    formattedCheckOutDate() {
      if (!this.payload.check_out) return "";

      const date = new Date(this.payload.check_out);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 11:00`;
    },
  },
};
</script>
