<template>
  <div v-if="can(`shift_create`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class="mt-5 mb-10">
      <v-col md="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }} / Create</div>
      </v-col>
      <v-col md="6">
        <div class="text-right">
          <v-btn small to="/shift" color="primary">
            <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-card>
      <v-toolbar flat dark class="primary"> Create {{ Model }} </v-toolbar>
      <v-card flat>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="3">
              Select Shift Type <span class="error--text">*</span>
              <v-autocomplete
                class="mt-1"
                outlined
                dense
                :hide-details="!errors.shift_type_id"
                :error="errors.shift_type_id"
                :error-messages="
                  errors && errors.shift_type_id ? errors.shift_type_id[0] : ''
                "
                v-model="payload.shift_type_id"
                x-small
                :items="shift_types"
                item-value="id"
                item-text="name"
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="12" md="3" v-if="!isAuto">
              Shift Name <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.name"
                :error-messages="errors.name && errors.name[0]"
                class="mt-1"
                outlined
                dense
                v-model="payload.name"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="3" v-if="!isAuto">
              <div class="mb-1">
                Minimum Working Hours<span class="error--text">*</span>
              </div>
              <v-text-field
                :hide-details="!errors.working_hours"
                :error="errors.working_hours"
                :error-messages="
                  errors && errors.working_hours ? errors.working_hours[0] : ''
                "
                outlined
                dense
                v-model="payload.working_hours"
                x-small
                type="number"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="3" v-if="!isAuto">
              Overtime start after duty hours (Minutes)
              <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.overtime_interval"
                :error-messages="
                  errors.overtime_interval && errors.overtime_interval[0]
                "
                class="mt-1"
                outlined
                dense
                v-model="payload.overtime_interval"
                label=""
                type="number"
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="time_in_menu_ref"
                v-model="time_in_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.on_duty_time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  On Duty Time
                  <v-text-field
                    :disabled="isManual"
                    :hide-details="!errors.on_duty_time"
                    v-model="payload.on_duty_time"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                    :class="payload.shift_type_id !== 1 ? '' : 'red lighten-1'"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="time_in_menu"
                  v-model="payload.on_duty_time"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn x-small color="primary" @click="time_in_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.time_in_menu_ref.save(payload.on_duty_time)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.on_duty_time"
                class="text-danger mt-2"
                >{{ errors.on_duty_time[0] }}</span
              >
            </v-col>
            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="time_out_menu_ref"
                v-model="time_out_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.off_duty_time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Off Duty Time
                  <v-text-field
                    v-model="payload.off_duty_time"
                    :disabled="isManual"
                    readonly
                    :hide-details="!errors.off_duty_time"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  format="24hr"
                  v-if="time_out_menu"
                  v-model="payload.off_duty_time"
                  full-width
                >
                  <v-spacer></v-spacer>
                  <v-btn x-small color="primary" @click="time_out_menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.time_out_menu_ref.save(payload.off_duty_time)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.off_duty_time"
                class="text-danger mt-2"
                >{{ errors.off_duty_time[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              Late Time (Minutes)
              <v-text-field
                v-model="payload.late_time"
                :hide-details="!errors.late_time"
                type="number"
                :disabled="isManual"
                dense
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.late_time"
                class="text-danger mt-2"
                >{{ errors.late_time[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              Early Time (Minutes)
              <v-text-field
                v-model="payload.early_time"
                :disabled="isManual"
                type="number"
                :hide-details="!errors.early_time"
                dense
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.early_time"
                class="text-danger mt-2"
                >{{ errors.early_time[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="beginning_in_menu_ref"
                v-model="beginning_in_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.beginning_in"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Beginning In
                  <v-text-field
                    v-model="payload.beginning_in"
                    :disabled="isManual"
                    readonly
                    :hide-details="!errors.beginning_in"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="beginning_in_menu"
                  v-model="payload.beginning_in"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="beginning_in_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="
                      $refs.beginning_in_menu_ref.save(payload.beginning_in)
                    "
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.beginning_in"
                class="text-danger mt-2"
                >{{ errors.beginning_in[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="beginning_out_menu_ref"
                v-model="beginning_out_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.beginning_out"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Beginning Out
                  <v-text-field
                    v-model="payload.beginning_out"
                    :disabled="isManual"
                    readonly
                    :hide-details="!errors.beginning_out"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="beginning_out_menu"
                  v-model="payload.beginning_out"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="beginning_out_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="
                      $refs.beginning_out_menu_ref.save(payload.beginning_out)
                    "
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.beginning_out"
                class="text-danger mt-2"
                >{{ errors.beginning_out[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="ending_in_menu_ref"
                v-model="ending_in_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.ending_in"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Ending In
                  <v-text-field
                    v-model="payload.ending_in"
                    :disabled="isManual"
                    readonly
                    :hide-details="!errors.ending_in"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="ending_in_menu"
                  v-model="payload.ending_in"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="ending_in_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.ending_in_menu_ref.save(payload.ending_in)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.ending_in"
                class="text-danger mt-2"
                >{{ errors.ending_in[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              <v-menu
                ref="ending_out_menu_ref"
                v-model="ending_out_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.ending_out"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Ending Out
                  <v-text-field
                    v-model="payload.ending_out"
                    :hide-details="!errors.ending_out"
                    readonly
                    :disabled="isManual"
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="ending_out_menu"
                  v-model="payload.ending_out"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="ending_out_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.ending_out_menu_ref.save(payload.ending_out)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.ending_out"
                class="text-danger mt-2"
                >{{ errors.ending_out[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              Minutes for Absent In
              <v-text-field
                v-model="payload.absent_min_in"
                type="number"
                :hide-details="!errors.absent_min_in"
                dense
                :disabled="isManual"
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.absent_min_in"
                class="text-danger mt-2"
                >{{ errors.absent_min_in[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3" v-if="!isChange">
              Minutes for Absent Out
              <v-text-field
                v-model="payload.absent_min_out"
                type="number"
                :hide-details="!errors.absent_min_out"
                dense
                :disabled="isManual"
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.absent_min_out"
                class="text-danger mt-2"
                >{{ errors.absent_min_out[0] }}</span
              >
            </v-col>

            <v-col md="12" v-if="!isChange">
              <b>Holidays</b>
              <br />
              <v-checkbox
                style="float: left"
                class="mr-5"
                v-for="(week_day, index) in week_days"
                :disabled="isManual"
                :key="index"
                v-model="payload.days"
                :label="week_day.label"
                :value="week_day.value"
                :error-messages="errors.days && errors.days[0]"
              ></v-checkbox>
            </v-col>

            <v-col md="12" v-if="!shiftList">
              <b>Manual Shifts</b>
              <br />
              <v-checkbox
                style="float: left"
                class="mr-5"
                v-for="(item, index) in shifts"
                :key="index"
                v-model="shift_id"
                :label="item.name"
                :value="item.id"
                :error-messages="errors.days && errors.days[0]"
              ></v-checkbox>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <div class="text-right">
                <v-btn
                  v-if="can(`shift_create`)"
                  small
                  color="primary"
                  @click="store_shift"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
export default {
  data: () => ({
    manual_shift: {},

    date: null,
    menu: false,

    time_tables: [],
    shift_types: [],
    shift_last_id: "",
    shiftList: true,
    isChange: false,
    isAuto: false,

    week_days: [
      { label: "Sun", value: "Sun" },
      { label: "Mon", value: "Mon" },
      { label: "Tue", value: "Tue" },
      { label: "Wed", value: "Wed" },
      { label: "Thu", value: "Thu" },
      { label: "Fri", value: "Fri" },
      { label: "Sat", value: "Sat" }
    ],

    // payload: {
    //   overtime: 0,
    //   shift_type_id: "",
    //   days: [],
    //   working_hours: 0
    // },

    errors: [],
    data: [],
    response: "",
    snackbar: false,

    Model: "Shift",
    e1: 1,

    loading: false,
    time_in_menu: false,
    time_out_menu: false,
    grace_time_in_menu: false,
    grace_time_out_menu: false,

    beginning_in_menu: false,
    ending_in_menu: false,

    beginning_out_menu: false,
    ending_out_menu: false,

    shift_id: [],
    payload: {
      days: []
    },

    errors: [],
    shifts: [],
    data: [],
    response: "",
    snackbar: false
  }),
  async created() {
    let options = {
      per_page: 1000,
      company_id: this.$auth.user.company.id
    };

    this.$axios.get("shift_type", { params: options }).then(({ data }) => {
      this.shift_types = data;
    });
  },
  watch: {
    "payload.shift_type_id"() {
      this.isAuto = false;
      this.isChange = false;
      this.shiftList = true;
      this.changeType;
    }
  },
  computed: {
    changeType() {
      let type = this.payload.shift_type_id;
      if (type == 1) {
        this.isChange = true;
        return;
      }
      if (type == 2) {
        this.getShifts();
        this.isChange = true;
        this.isAuto = true;
        this.shiftList = false;
        return;
      }

      if (type == 3) {
        this.isChange = false;
        return;
      }
    }
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    getShifts() {
      let manual_shift = this.shift_types.find(e => e.slug == "manual_shift");
      let payload = {
        params: {
          shift_type_id: manual_shift.id,
          company_id: this.$auth.user.company.id
        }
      };

      this.$axios
        .get("shift_by_type", payload)
        .then(({ data }) => {
          this.shifts = data;
        })
        .catch(err => console.log(err));
    },

    store_shift() {
      this.payload.company_id = this.$auth.user.company.id;
      this.loading = true;
      this.$axios
        .post(`/shift`, this.payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Shift added successfully";
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    }
  }
};
</script>
