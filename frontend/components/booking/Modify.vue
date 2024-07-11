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
          <v-container>
            <table>
              <v-progress-linear
                v-if="false"
                :active="loading"
                :indeterminate="loading"
                absolute
                color="primary"
              ></v-progress-linear>
              <tr>
                <th style="width:200px;">Customer Name</th>
                <td style="width: 300px">
                  {{ BookingData && BookingData.title }}
                </td>
              </tr>

              <tr>
                <th>Group Name</th>
                <td>---</td>
              </tr>

              <tr>
                <th>Agent Name</th>
                <td>
                  {{ BookingData.agent_name || "---" }}
                </td>
              </tr>
              <tr>
                <th>Contact</th>
                <td>
                  {{ BookingData.contact_no || "---" }}
                </td>
              </tr>
              <tr>
                <th>Room No</th>
                <td>
                  <v-autocomplete
                    v-model="payload.room_id"
                    :items="rooms"
                    item-text="name"
                    item-value="id"
                    dense
                    outlined
                    :hide-details="true"
                    :height="1"
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
                        v-model="payload.check_in"
                        persistent-hint
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="payload.check_in"
                      no-title
                      @input="checkin_menu = false"
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
                        v-model="payload.check_out"
                        persistent-hint
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="payload.check_out"
                      no-title
                      @input="checkout_menu = false"
                    ></v-date-picker>
                  </v-menu>
                </td>
              </tr>
              <tr>
                <th>Days</th>
                <td>2</td>
              </tr>
              <!-- <tr>
                <th>
                  Payment Mode
                  <span class="error--text">*</span>
                </th>
                <td>
                  <v-select
                    v-model="BookingData.payment_mode_id"
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
                    dense
                    outlined
                    :hide-details="true"
                    :height="1"
                  ></v-select>
                </td>
              </tr> -->

              <tr>
                <th>Total</th>
                <td class="text-right">
                    {{payload.total_price}}
                </td>
              </tr>
              <tr style="background-color: white">
                <th>Advance Payment</th>
                <td>
                  <v-text-field
                    dense
                    outlined
                    type="number"
                    v-model="payload.new_advance"
                    :hide-details="true"
                  ></v-text-field>
                </td>
              </tr>
              <tr>
                <th>Balance Amount</th>
                <td class="text-right">
                    {{payload.grand_remaining_price}}
                </td>
              </tr>

              <tr></tr>
            </table>
            <div class="text-right">
              <v-btn
                class="grey white--text mt-2"
                small
                @click="dialog = false"
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
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  props: ["BookingData"],
  data() {
    return {
      checkin_menu: false,
      checkout_menu: false,
      rooms: [],
      payload: {
        room_id: 1,
        extra_bed: "",
        check_in: "",
        check_out: "",
        total_price: "",
        new_advance: "",
        grand_remaining_price: "",
      },
      dialog: false,
      isDiscount: false,
      snackbar: false,
      response: "",
      preloader: false,
      loading: false,
      new_advance: 0,
      reference: "",
      errors: [],
      checkOutDialog: false,
    };
  },

  async created() {
    this.preloader = false;

    this.payload = {
      booking_id: this.BookingData.id,
      room_id: this.BookingData.room_id,
      extra_bed: 0,
      check_in: this.BookingData.check_in,
      check_out: this.BookingData.check_out,
      total_price: this.BookingData.total_price,
      new_advance: this.BookingData.new_advance,
      grand_remaining_price: this.BookingData.grand_remaining_price,
      company_id: this.$auth.user.company_id,
      user_id: this.$auth.user.id,
    };

    let payload = {
      params: {
        company_id: this.$auth.user.company.id,
      },
    };

    let { data: rooms } = await this.$axios.get(
      `get_available_rooms_for_modify`,
      payload
    );

    this.rooms = rooms.map((e) => ({
      id: e.id,
      room_no: e.room_no,
      price: e.price,
      name: `${e.room_no} ${e.room_type.name}`,
      room_type_price: e.room_type.price,
    }));
  },

  mounted() {},

  computed: {},

  methods: {
    submit() {
      this.loading = true;
      this.$axios
        .post("/modify_booking", this.payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.new_advance = 0;
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
