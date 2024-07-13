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
          <v-container v-if="bookingResponse && bookingResponse.id">
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
                <td>---</td>
              </tr>

              <tr>
                <th>Agent Name</th>
                <td>
                  {{ bookingResponse.agent_name || "---" }}
                </td>
              </tr>
              <tr>
                <th>Contact</th>
                <td>
                  {{ bookingResponse?.customer?.contact_no || "---" }}
                </td>
              </tr>
              <tr>
                <th>Room No {{ dialog }}</th>
                <td>
                  <v-autocomplete
                    @change="getPricesByRoomId(payload.room_id)"
                    :items="rooms"
                    item-text="label"
                    item-value="id"
                    dense
                    outlined
                    v-model="payload.room_id"
                    :hide-details="true"
                  ></v-autocomplete>
                </td>
              </tr>
              <tr>
                <th>Extra Bed</th>
                <td>
                  <v-text-field
                    min="0"
                    dense
                    outlined
                    type="number"
                    v-model="payload.extra_bed"
                    :hide-details="true"
                  ></v-text-field>
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
                <th>Days</th>
                <td>{{ bookingResponse.days }}</td>
              </tr>
              <tr>
                <th>Total ( New Price )</th>
                <td class="text-right">
                  {{ payload.total_price }}
                </td>
              </tr>
              <tr>
                <th>Discount</th>
                <td class="text-right">
                  {{ payload.total_discount }}
                </td>
              </tr>
              <tr>
                <th>Tax</th>
                <td class="text-right">
                  {{ payload.total_tax }}
                </td>
              </tr>
              <tr>
                <th>Advance Payment</th>
                <td class="text-right">
                  {{ payload.advance_price }}
                </td>
              </tr>
              <tr>
                <th>Balance Amount</th>
                <td class="text-right">
                  {{ payload.remaining_price }}
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
  props: ["BookingData", "BookedRoomId"],
  data() {
    return {
      checkin_menu: false,
      checkout_menu: false,
      rooms: [],
      payload: {
        extra_bed: 0,
        check_in: "",
        check_out: "",
        total_price: 0,
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
      checkOutDialog: false,
      bookingResponse: null,
      roomPriceResponse: null,
    };
  },

  async created() {
    this.preloader = false;

    await this.get_booking();

    let { data: rooms } = await this.$axios.get(
      `get_available_rooms_for_modify`,
      {
        params: {
          company_id: this.$auth.user.company.id,
        },
      }
    );

    this.rooms = rooms.map((e) => ({
      id: e.id,
      room_no: e.room_no,
      roomType: e.room_type.name,
      label: `${e.room_no} ${e.room_type.name}`,
    }));
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
    getPricesByRoomId(id) {
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
          console.log(data.total_price, data);
          this.payload = {
            ...this.payload,
            total_price: data.total_price,
            total_discount: data.total_discount,
            total_tax: data.total_tax,
            extra_bed: this.extra_bed,
            remaining_price: data.total_price - this.payload.advance_price,
          };

          this.bookingResponse.days = data.data.length || 0;
        });
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
        this.payload = {
          booking_id: data.booking_id,
          room_id: data.room_id,
          check_in: data.checkin_date_only,
          check_out: data.checkout_date_only,
          total_price: data.total_price,
          company_id: this.$auth.user.company_id,
          user_id: this.$auth.user.id,
          extra_bed: this.extra_bed,
          advance_price: data.booking.advance_price || 0,
          remaining_price:
            parseFloat(data.grand_total) -
            parseFloat(data.booking.advance_price),
        };
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
      alert("under process");

      return;

      this.loading = true;
      this.$axios
        .post("/modify_booking", this.payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.closeDialog(data);
            this.loading = false;
          }
        })
        .catch((e) => console.log(e));
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
