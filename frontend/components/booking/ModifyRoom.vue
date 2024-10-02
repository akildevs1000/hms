<template>
  <div class="text-center">
    <v-dialog persistent v-model="dialog" width="850">
      <template v-slot:activator="{ on, attrs }">
        <span v-bind="attrs" v-on="on"> Modify Booking </span>
      </template>

      <v-card>
        <v-toolbar class="grey lighten-3 primary--text" flat dense>
          <div style="font-size: 18px">Modify Booking</div>
          <v-spacer></v-spacer><AssetsButtonClose @close="dialog = false" />
        </v-toolbar>

        <v-card-text class="py-5">
          <v-container v-if="payload && payload.booking_id">
            <v-row v-if="bookingResponse">
              <!-- Left Column -->
              <v-col cols="12" md="6">
                <v-row no-gutter>
                  <v-col cols="12" class="text-center">
                    <v-avatar size="125">
                      <img
                        class="pa-2"
                        style="border: 1px solid grey"
                        :src="
                          bookingResponse?.customer?.captured_photo ||
                          'https://i.pinimg.com/474x/e4/c5/9f/e4c59fdbb41ccd0f87dc0be871d91d98.jpg'
                        "
                        alt="Profile Image"
                      />
                    </v-avatar>
                  </v-col>
                </v-row>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <!-- Form Fields -->
                      <v-text-field
                        readonly
                        v-model="bookingResponse.customer.full_name"
                        label="Full Name"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.customer.contact_no"
                        label="Phone Number"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.booking.source"
                        label="Source"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        readonly
                        v-model="formattedCheckinDate"
                        label="Check In"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        readonly
                        v-model="formattedCheckOutDate"
                        label="Check Out"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="4">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.no_of_adult"
                        label="Adults"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.no_of_child"
                        label="Children"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.extra_bed_qty"
                        label="Extra Bed"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.room_type"
                        label="Room Type"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="6">
                      <v-text-field
                        readonly
                        v-model="bookingResponse.room_no"
                        label="Room Number"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="12">
                      <v-text-field
                        readonly
                        v-model="formattedFoodPlan"
                        label="Food Plan"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="12">
                      <v-row justify="center">
                        <v-col cols="4" class="d-flex justify-center">
                          <v-checkbox
                            readonly
                            label="Early C/I"
                            hide-details
                            dense
                          ></v-checkbox>
                        </v-col>
                        <v-col cols="4" class="d-flex justify-center">
                          <v-checkbox
                            readonly
                            label="Late C/O"
                            hide-details
                            dense
                          ></v-checkbox>
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>
              <v-divider vertical></v-divider>
              <!-- Right Column -->
              <v-col cols="12" md="6">
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <Heading label="Edit Booking" />
                    </v-col>
                    <v-col cols="6">
                      <!-- <tr>
                <th>Early Check In</th>
                <td>
                  <v-checkbox
                    v-model="is_early_check_in"
                    :label="`Early Check In (${early_check_in})`"
                    :hide-details="true"
                    dense
                  ></v-checkbox>
                </td>
              </tr> -->
                      <!-- <tr>
                <th>Late Check Out</th>
                <td>
                  <v-checkbox
                    v-model="is_late_check_out"
                    :label="`Early Check In (${late_check_out})`"
                    :hide-details="true"
                    dense
                    @change="
                      () => {
                        late_check_out = is_late_check_out
                          ? payload.late_check_out
                          : 0;
                      }
                    "
                  ></v-checkbox>
                </td>
              </tr> -->

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
                          @input="
                            () => {
                              checkin_menu = false;
                              getPricesByRoomId(payload.room_id);
                            }
                          "
                        ></v-date-picker>
                      </v-menu>
                    </v-col>

                    <v-col cols="6">
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
                          @input="
                            () => {
                              checkout_menu = false;
                              getPricesByRoomId(payload.room_id);
                            }
                          "
                        ></v-date-picker>
                      </v-menu>
                    </v-col>

                    <v-col cols="4">
                      <v-text-field
                        readonly
                        v-model="payload.no_of_adult"
                        label="Adults"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        readonly
                        v-model="payload.no_of_child"
                        label="Children"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>
                    <v-col cols="4">
                      <v-text-field
                        readonly
                        min="0"
                        @keyup="adjust_bed_charges"
                        v-model="payload.extra_bed_qty"
                        label="Extra Bed"
                        dense
                        hide-details
                        outlined
                      />
                    </v-col>

                    <v-col cols="6">
                      <v-autocomplete
                        v-model="payload.room_type_id"
                        @change="getFilteredRooms"
                        label="Room Type"
                        outlined
                        dense
                        hide-details
                        item-value="id"
                        item-text="name"
                        :items="roomTypes"
                        return-object
                      ></v-autocomplete>
                    </v-col>

                    <v-col cols="6">
                      <v-autocomplete
                        @change="getPricesByRoomId(payload.room_id)"
                        :items="filteredRooms"
                        item-text="room_no"
                        item-value="id"
                        dense
                        outlined
                        v-model="payload.room_id"
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
                        @change="getPricesByRoomId(payload.room_id)"
                      ></v-autocomplete>
                    </v-col>

                    <v-col cols="12">
                      <v-row justify="center">
                        <v-col cols="4" class="d-flex justify-center">
                          <v-checkbox
                            readonly
                            label="Early C/I"
                            hide-details
                            dense
                          ></v-checkbox>
                        </v-col>
                        <v-col cols="4" class="d-flex justify-center">
                          <v-checkbox
                            readonly
                            label="Late C/O"
                            hide-details
                            dense
                          ></v-checkbox>
                        </v-col>
                      </v-row>
                    </v-col>

                    <v-col cols="12">
                      <v-textarea
                        label="Notes"
                        dense
                        hide-details
                        outlined
                        rows="2"
                      />
                    </v-col>

                    <v-col cols="12" class="my-3">
                      <Heading label="New Price" />
                    </v-col>
                    <v-col>
                      <table style="width: 100%" cellspacing="3">
                        <tr>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            <b>Old Booking</b>
                          </td>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            <b>{{
                              $utils.currency_format(old.booking_total_price)
                            }}</b>
                          </td>
                          <td colspan="2" class="text-center">Old Balance</td>
                        </tr>
                        <tr>
                          <td>Advance Paid</td>
                          <td>
                            {{ $utils.currency_format(payload.advance_price) }}
                          </td>
                          <td colspan="2" class="text-center">
                            <b style="font-size: 22px" class="blue--text">{{
                              $utils.currency_format(
                                old.booking_total_price - payload.advance_price
                              )
                            }}</b>
                          </td>
                        </tr>
                      </table>
                      <v-divider></v-divider>
                      <table style="width: 100%">
                        <tr>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            New Booking
                          </td>
                          <td
                            style="
                              width: 110px;
                              border-bottom: 1px solid #eeeeee;
                            "
                          >
                            {{
                              $utils.currency_format(
                                payload.booking_total_price
                              )
                            }}
                          </td>
                          <td colspan="2" class="text-center">Old Balance</td>
                        </tr>
                        <tr>
                          <td>Advance Adj</td>
                          <td>
                            {{ $utils.currency_format(payload.advance_price) }}
                          </td>
                          <td colspan="2" class="text-center">
                            <b style="font-size: 22px" class="red--text">{{
                              $utils.currency_format(
                                payload.booking_total_price -
                                  payload.advance_price
                              )
                            }}</b>
                          </td>
                        </tr>
                      </table>
                    </v-col>
                  </v-row>
                </v-container>
              </v-col>

              <v-col cols="6">
                <v-textarea
                  readonly
                  v-model="bookingResponse.booking.request"
                  label="Guest Request"
                  dense
                  hide-details
                  outlined
                  rows="2"
                />
              </v-col>
              <v-divider vertical></v-divider>
              <v-col cols="6" class="text-center mt-5">
                <AssetsButtonCancel @close="dialog = false" />
                &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                <AssetsButtonSubmit @click="submit" />
              </v-col>
            </v-row>
          </v-container>
          <v-container v-else>
            <div
              class="d-flex justify-center align-center"
              style="height: 400px"
            >
              <div>Loading......</div>
            </div>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["BookedRoomId"],
  data() {
    return {
      room_orders: [],

      is_early_check_in: false,
      is_late_check_out: false,

      early_check_in: 0,
      late_check_out: 0,
      foodplans: [],
      checkin_menu: false,
      checkout_menu: false,
      rooms: [],
      filteredRooms: [],
      old: {
        food_plan_price: 0,
        room_price: 0,
        booking_total_price: 0,
        room_id: 0,
        room_type_id: 0,
        room_no: 0,
        room_type: null,
      },
      payload: {
        booking_remaining_price: 0,
        booking_total_price: 0,
        room_type_id: 0,
        extra_bed_qty: 0,
        bed_amount: 0,
        check_in: "",
        check_out: "",
        total_price: 0,
        old_remaining_price: 0,
        remaining_price: 0,
        company_id: 0,
        booking_id: 0,
        room_id: 0,
        user_id: 0,
        advance_price: 0,
      },
      customer: {
        title: null,
        group: null,
        agent_name: null,
        contact_no: null,
      },
      dialog: false,
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      reference: "",
      errors: [],
      roomTypes: [],
      checkOutDialog: false,
      bookingResponse: null,
      roomPriceResponse: null,
      additional_charges: {},
    };
  },

  async created() {
    this.preloader = false;

    await this.get_food_plans();

    await this.get_booking();

    await this.get_additional_charges();

    await this.get_room_types();

    await this.adjust_bed_charges();
  },

  mounted() {},

  computed: {
    formattedFoodPlan() {
      return (
        this.foodplans.find((e) => e.id == this.bookingResponse.food_plan_id)
          ?.title || "---"
      );
    },
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

  methods: {
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },

    get_food_charges(id) {
      let { unit_price } = this.foodplans.find((e) => e.id == id);

      return unit_price;
    },

    async adjust_bed_charges() {
      // this.temp.early_check_in = this.is_early_check_in
      //   ? this.additional_charges.early_check_in || 0
      //   : 0;
      // this.temp.late_check_out = this.is_late_check_out
      //   ? this.additional_charges.late_check_out || 0
      //   : 0;

      this.payload.bed_amount = this.payload.extra_bed_qty
        ? this.payload.extra_bed_qty * (this.additional_charges.extra_bed || 0)
        : 0;

      this.getPricesByRoomId(this.payload.room_id);
    },

    async get_food_plans() {
      let { data: foodplans } = await this.$axios.get(`foodplan-list`);

      this.foodplans = foodplans;
    },
    async get_rooms(room_id) {
      let { data: rooms } = await this.$axios.get(
        `get_available_rooms_for_modify`,
        {
          params: {
            company_id: this.$auth.user.company.id,
          },
        }
      );

      this.filteredRooms = rooms.filter((e) => e.id == room_id);
    },

    async getFilteredRooms(item) {
      let { data: rooms } = await this.$axios.get(
        `get_available_rooms_by_date_and_room_type`,
        {
          params: {
            company_id: this.$auth.user.company.id,
            check_in: this.payload.check_in,
            check_out: this.payload.check_out,
            room_type_id: item.id,
          },
        }
      );

      this.filteredRooms = rooms;
    },
    async get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },
    getTotalAfterDiscount() {
      let { room_discount, remaining_price } = this.payload;
      this.total_price =
        parseFloat(remaining_price) - parseFloat(room_discount);
    },
    async getPricesByRoomId(id) {
      this.roomPriceResponse = [];
      let found = this.filteredRooms.find((e) => e.id == id);
      if (!found) return;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: found.room_type.name || "",
          room_no: found.room_no,
          checkin: this.payload.check_in,
          checkout: this.payload.check_out,
          BookedRoomId: this.BookedRoomId,
        },
      };
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return;
          }

          this.room_orders = data.data;

          let unit_price = this.get_food_charges(this.payload.food_plan_id);
          let total_days = data.data.length || 0;
          let food_plan_price_for_all_days = unit_price * total_days;
          let new_room_price_single_day = data.total_price / total_days;
          let room_price_with_meal = unit_price + new_room_price_single_day;

          this.payload = {
            ...this.payload,
            food_plan_price: unit_price,
            room_no: found.room_no,
            room_type_id: data.room.room_type_id,
            room_id: data.room.id,
            total_days: total_days,
            room_price: new_room_price_single_day,
            total_price: room_price_with_meal,
            total_tax: data.total_tax,
            room_tax: data.total_tax / total_days,
            booking_total_price:
              parseFloat(this.payload.bed_amount) +
              parseFloat(data.total_price) +
              parseFloat(food_plan_price_for_all_days),
            booking_remaining_price:
              this.payload.booking_remaining_price - this.payload.advance_price,
            remaining_price: room_price_with_meal - this.payload.advance_price,
            total_discount: data.total_discount,
          };

          if (this.bookingResponse.booking.group_name) {
            if (
              this.old.booking_total_price !== this.payload.booking_total_price
            ) {
              let roomPrice = this.old.food_plan_price + this.old.room_price;
              let restOfPrices = this.old.booking_total_price - roomPrice;
              this.payload.booking_total_price = this.currency_format(
                this.payload.booking_total_price + restOfPrices
              );
            }
          }
        });
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },
    async get_booking() {
      let payload = {
        params: {
          id: this.BookedRoomId,
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_booking_for_modify`, payload).then(({ data }) => {
        this.bookingResponse = data;

        this.old = {
          room_price: data.price,
          food_plan_price: data.food_plan_price,
          extra_bed_qty: data.extra_bed_qty,
          bed_amount: data.bed_amount,
          booking_total_price: data.booking.total_price,
          room_id: data.room_id,
          room_type_id: data.room_type_id,
          room_type: data?.room?.room_type?.name,
          room_no: data.room_no,
          days: data.days || 0,
          check_in: data.check_in,
          check_out: data.check_out,
        };

        // this.early_check_in = data.early_check_in;
        // this.late_check_out = data.late_check_out;

        // this.is_early_check_in = this.early_check_in > 0 ? true : false;
        // this.is_late_check_out = this.late_check_out > 0 ? true : false;

        this.payload = {
          id: data.id,
          booking_id: data.booking_id,
          room_id: data.room_id,

          food_plan_id: parseInt(data.food_plan_id),
          early_check_in: this.early_check_in,
          late_check_out: this.late_check_out,
          extra_bed_qty: data.extra_bed_qty,
          no_of_adult: data.no_of_adult,
          no_of_child: data.no_of_child,
          bed_amount: data.bed_amount,
          check_in: data.checkin_date_only,
          check_out: data.checkout_date_only,
          total_price: data.grand_total,
          company_id: this.$auth.user.company_id,
          user_id: this.$auth.user.id,
          total_discount: data.booking.total_discount || 0,
          advance_price: data.booking.advance_price || 0,
          remaining_price:
            parseFloat(data.grand_total) -
            parseFloat(data.booking.advance_price),

          old_remaining_price:
            parseFloat(data.grand_total) -
            parseFloat(data.booking.advance_price),

          total_days: data.days || 0,
          total_tax: data.room_tax * data.days,
          room_tax: data.room_tax,
          room_price: data.price,
          room_no: data.room_no,
          room_type_id: data.room.room_type_id,
          room_id: data.room_id,
          booking_remaining_price: data.booking.remaining_price,
          booking_total_price: data.booking.total_price,
          food_plan_price: data.food_plan_price,
        };

        this.get_rooms(data.room_id);
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
    submit() {
      if (!this.room_orders.length) {
        this.alert("Warning!", "No changes detected", "error");
        return;
      }
      let room_id = this.payload.room_id;

      let foundRoom = this.filteredRooms.find((e) => e.id == room_id);

      let payload = {
        id: this.payload.id,
        booking_id: this.payload.booking_id,
        room_id: foundRoom.id,
        check_in: this.payload.check_in + " 12:00",
        check_out: this.payload.check_out + " 11:00",
        total_price: this.payload.total_price,
        company_id: this.payload.company_id,
        user_id: this.payload.user_id,
        remaining_price: this.payload.remaining_price,
        total_days: this.payload.total_days,
        total_tax: this.payload.total_tax,
        room_tax: this.payload.room_tax,
        room_price: this.payload.room_price,
        room_no: foundRoom.room_no,
        room_type: foundRoom.room_type.name,
        extra_bed_qty: this.payload.extra_bed_qty,
        bed_amount: this.payload.bed_amount,
        food_plan_id: this.payload.food_plan_id,
        early_check_in: this.payload.early_check_in,
        late_check_out: this.payload.late_check_out,
        booked_room_id: this.BookedRoomId,
        room_discount: this.payload.room_discount || 0,
        booking_remaining_price: this.payload.booking_remaining_price,
        booking_total_price: this.payload.booking_total_price,
        food_plan_price: this.payload.food_plan_price,
        no_of_adult: this.bookingResponse.no_of_adult,
        no_of_child: this.bookingResponse.no_of_child,

        breakfast: this.bookingResponse.breakfast,
        lunch: this.bookingResponse.lunch,
        dinner: this.bookingResponse.dinner,

        dinner: this.bookingResponse.dinner,
        customer_id: this.bookingResponse?.customer_id,

        room_orders: this.room_orders,
        old: this.old,
      };

      console.log(payload);
      // return;

      this.loading = true;
      this.$axios
        .post(`/modify_booking`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.dialog = false;
            this.loading = false;

            this.alert("Sucess!", "Booking has been modified", "success");
            this.$emit("close-calender-room");
          }
        })
        .catch((e) => {
          this.loading = false;
          console.log(e);
        });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>