<template>
  <div class="text-center">
    <v-dialog v-model="dialog" width="700">
      <template v-slot:activator="{ on, attrs }">
        <span v-bind="attrs" v-on="on"> Modify Booking </span>
      </template>

      <v-card>
        <v-toolbar flat class="blue white--text" dense>
          Modify Booking <v-spacer></v-spacer
          ><v-icon @click="dialog = false" color="white"
            >mdi-close</v-icon
          ></v-toolbar
        >

        <v-card-text class="py-5">
          <v-container v-if="payload && payload.booking_id">
            <table>
              <v-progress-linear
                v-if="false"
                :active="loading"
                :indeterminate="loading"
                absolute
                color="primary"
              ></v-progress-linear>
              <tr>
                <th style="width: 200px">Customer Name</th>
                <td style="width: 300px">
                  {{ bookingResponse?.customer?.full_name }}
                </td>
              </tr>
              <tr>
                <th>Group Name</th>
                <td>{{ bookingResponse?.booking?.group_name || "---" }}</td>
              </tr>

              <tr>
                <th>Agent Name</th>
                <td>
                  {{ bookingResponse?.booking?.agent_name || "---" }}
                </td>
              </tr>
              <tr>
                <th>Contact</th>
                <td>
                  {{ bookingResponse?.customer?.contact_no || "---" }}
                </td>
              </tr>

              <tr>
                <th>Room Type</th>
                <td>
                  <!-- <pre>{{bookingResponse}}</pre> -->
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
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
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
                </td>
              </tr>

              <tr>
                <th>Check In</th>
                <td>
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
                </td>
              </tr>
              <tr>
                <th>Check Out</th>
                <td>
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
                </td>
              </tr>

              <tr>
                <th>Meal</th>
                <td>
                  <v-autocomplete
                    label="Food Plan"
                    outlined
                    dense
                    hide-details
                    item-value="id"
                    item-text="title"
                    v-model="payload.food_plan_id"
                    :items="foodplans"
                    @change="adjust_food_charges(payload.food_plan_id)"
                  ></v-autocomplete>
                </td>
              </tr>
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
              <tr>
                <th>Extra Bed</th>
                <td>
                  <v-text-field
                    min="0"
                    dense
                    outlined
                    type="number"
                    v-model="payload.extra_bed_qty"
                    :hide-details="true"
                    @keyup="adjust_bed_charges"
                  ></v-text-field>
                </td>
              </tr>
                <tr>
                <th>Days</th>
                <td class="text-right">{{ payload.total_days }}</td>
              </tr>
              <tr>
                <th>Advance Payment</th>
                <td class="text-right">
                  {{ convert_decimal(payload.advance_price) }}
                </td>
              </tr>
              <!-- <tr>
                <th>Balance Amount</th>
                <td class="text-right">
                  {{ convert_decimal(payload.booking_remaining_price) }}
                </td>
              </tr> -->
              <tr>
                <th>Old Grand Total</th>
                <td class="text-right">
                  {{ convert_decimal(old.booking_total_price) }}
                </td>
              </tr>
              <tr>
                <th>Grand Total</th>
                <td class="text-rights">
                  <!-- <pre>{{ payload }}</pre> -->
                  {{ convert_decimal(payload.booking_total_price) }}
                </td>
              </tr>
            </table>
            <div class="text-right">
              <v-btn class="grey white--text mt-2" small @click="dialog = false"
                >Close</v-btn
              >
              <v-btn
                class="primary mt-2"
                small
                @click="submit"
                :loading="loading"
                >Submit</v-btn
              >
            </div>
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

    await this.get_booking();

    await this.get_additional_charges();

    await this.get_room_types();

    await this.get_food_plans();

    await this.adjust_bed_charges();
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

  methods: {
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },
    adjust_food_charges(id) {
      let { unit_price } = this.foodplans.find((e) => e.id == id);

      let { booking_total_price, food_plan_price } = this.old;

      let deductedOldPrice = booking_total_price - food_plan_price;

      this.payload.food_plan_price = unit_price;

      this.payload.booking_total_price = deductedOldPrice + unit_price;
    },

    async adjust_bed_charges() {
      // this.temp.early_check_in = this.is_early_check_in
      //   ? this.additional_charges.early_check_in || 0
      //   : 0;
      // this.temp.late_check_out = this.is_late_check_out
      //   ? this.additional_charges.late_check_out || 0
      //   : 0;

      let newPrice = this.payload.extra_bed_qty
        ? this.payload.extra_bed_qty * (this.additional_charges.extra_bed || 0)
        : 0;

      let { booking_total_price, bed_amount } = this.old;

      let deductedOldPrice = booking_total_price - bed_amount;

      this.payload.bed_amount = newPrice;

      this.payload.booking_total_price = deductedOldPrice + newPrice;

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
          console.log("🚀 ~ .then ~ data:", data);
          if (!data.status) {
            this.alert("Failure!", data.data, "error");
            return;
          }

          let restOfPayload = {
            room_tax: data.room_tax,
            room_price: data.total_price / data.data.length || 0,
            room_no: found.room_no,
            room_type_id: data.room.room_type_id,
            room_id: data.room.id,
            remaining_price: data.total_price - this.payload.advance_price,
            total_price: data.total_price,
            total_discount: data.total_discount,
            total_days: data.data.length || 0,
            total_tax: data.total_tax,
            room_tax: data.total_tax / data.data.length || 0,
            booking_total_price: this.getGrandTotal(data.total_price),
            // booking_remaining_price: this.payload.booking_remaining_price,
          };
          console.log(
            "🚀 ~ .then ~ restOfPayload.total_price:",
            restOfPayload.room_price
          );

          this.payload = {
            ...this.payload,
            ...restOfPayload,
          };

          console.log(
            "🚀 ~ .then ~ payload.total_price:",
            this.payload.room_price
          );
        });
    },
    getGrandTotal(new_price) {
      return this.old.booking_total_price - this.old.room_price + new_price;
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
          booking_total_price: data.booking.total_price,
          food_plan_price: data.food_plan_price,
          extra_bed_qty: data.extra_bed_qty,
          bed_amount: data.bed_amount,
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
            this.$emit("close");
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

<style scoped src="@/assets/css/tableStyles.css"></style>
