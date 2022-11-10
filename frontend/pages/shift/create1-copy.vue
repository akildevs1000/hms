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
    <v-stepper v-model="e1">
      <v-stepper-header>
        <v-stepper-step :complete="e1 > 1" step="1"> Shift </v-stepper-step>

        <v-divider></v-divider>

        <v-stepper-step :complete="e1 > 2" step="2"> Time Slot </v-stepper-step>
      </v-stepper-header>

      <v-stepper-items>
        <v-stepper-content step="1">
          <v-row>
            <v-col cols="12" md="6">
              Shift Name <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.name"
                :error-messages="errors.name && errors.name[0]"
                class="mt-1"
                outlined
                dense
                v-model="shift_payload.name"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
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
                @change="checkIfShiftIsManual"
                v-model="shift_payload.shift_type_id"
                x-small
                :items="shift_types"
                item-value="id"
                item-text="name"
              >
              </v-autocomplete>
            </v-col>

            <!-- <v-col cols="12" md="4">
              Beginning Date <span class="error--text">*</span>
              <v-menu
                ref="menu"
                v-model="menu"
                :close-on-content-click="false"
                :return-value.sync="date"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    :hide-details="!errors.beginning_date"
                    :error-messages="
                      errors.beginning_date && errors.beginning_date[0]
                    "
                    class="mt-1"
                    outlined
                    dense
                    v-model="shift_payload.beginning_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="shift_payload.beginning_date"
                  no-title
                  scrollable
                >
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(shift_payload.beginning_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col> -->

            

            <v-col cols="12" md="6">
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
                v-model="shift_payload.working_hours"
                x-small
                type="number"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="6">
              Overtime start after duty hours (Minutes)
              <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.overtime"
                :error-messages="errors.overtime && errors.overtime[0]"
                class="mt-1"
                outlined
                dense
                v-model="shift_payload.overtime"
                label=""
                type="number"
              ></v-text-field>
            </v-col>

            <!-- <v-col cols="12" md="4">
              Cycle Number
              <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.cycle_number"
                :error-messages="errors.cycle_number && errors.cycle_number[0]"
                class="mt-1"
                outlined
                dense
                v-model="shift_payload.cycle_number"
                type="number"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
              Cycle Unit
              <v-text-field
                :hide-details="!errors.cycle_unit"
                class="mt-1"
                outlined
                dense
                readonly
                v-model="shift_payload.cycle_unit"
              ></v-text-field>
            </v-col> -->
            

            <!-- <v-col cols="12" md="6" v-if="manual_shift.slug == `manual_shift`">
              Select TimeTable
              <v-autocomplete
                :hide-details="!errors.time_table_id"
                :error-messages="
                  errors.time_table_id && errors.time_table_id[0]
                "
                class="mt-1"
                outlined
                dense
                v-model="shift_payload.time_table_id"
                :items="time_tables"
                item-text="name"
                item-value="id"
              >
              </v-autocomplete>
            </v-col> -->

            <v-col md="12">
              <b>Holidays</b>
              <br />
              <v-checkbox
                style="float: left"
                class="mr-5"
                v-for="(week_day, index) in week_days"
                :key="index"
                v-model="shift_payload.days"
                :label="week_day.label"
                :value="week_day.value"
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
                  :loading="loading"
                  color="primary"
                  @click="store_shift"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-stepper-content>

        <v-stepper-content step="2">
          <v-row>
            <v-col cols="12" md="3">
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
                    v-model="payload.on_duty_time"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
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
            <v-col cols="12" md="3">
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
                    readonly
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

            <v-col cols="12" md="3">
              Late Time (Minutes)
              <v-text-field
                v-model="payload.late_time"
                label=""
                type="number"
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

            <v-col cols="12" md="3">
              Early Time (Minutes)
              <v-text-field
                v-model="payload.early_time"
                type="number"
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

            <v-col cols="12" md="3">
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
                    readonly
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

            <v-col cols="12" md="3">
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
                    readonly
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

            <v-col cols="12" md="3">
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
                    readonly
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

            <v-col cols="12" md="3">
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
                    label=""
                    readonly
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

            <v-col cols="12" md="3">
              Minutes for Absent In
              <v-text-field
                v-model="payload.absent_min_in"
                type="number"
                dense
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.absent_min_in"
                class="text-danger mt-2"
                >{{ errors.absent_min_in[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3">
              Minutes for Absent Out
              <v-text-field
                v-model="payload.absent_min_out"
                type="number"
                dense
                outlined
                class="mt-2"
              ></v-text-field>
              <span
                v-if="errors && errors.absent_min_out"
                class="text-danger mt-2"
                >{{ errors.absent_min_out[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3">
              <v-menu
                ref="break_time_start_menu_ref"
                v-model="break_time_start_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.break_time_start"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Break Start
                  <v-text-field
                    v-model="payload.break_time_start"
                    label=""
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="break_time_start_menu"
                  v-model="payload.break_time_start"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="break_time_start_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="
                      $refs.break_time_start_menu_ref.save(
                        payload.break_time_start
                      )
                    "
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.break_time_start"
                class="text-danger mt-2"
                >{{ errors.break_time_start[0] }}</span
              >
            </v-col>

            <v-col cols="12" md="3">
              <v-menu
                ref="break_time_end_menu_ref"
                v-model="break_time_end_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="payload.break_time_end"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  Break End
                  <v-text-field
                    v-model="payload.break_time_end"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    dense
                    outlined
                    class="mt-2"
                  ></v-text-field>
                </template>
                <v-time-picker
                  v-if="break_time_end_menu"
                  v-model="payload.break_time_end"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn
                    x-small
                    color="primary"
                    @click="break_time_end_menu = false"
                  >
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="
                      $refs.break_time_end_menu_ref.save(payload.break_time_end)
                    "
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span
                v-if="errors && errors.break_time_end"
                class="text-danger mt-2"
                >{{ errors.break_time_end[0] }}</span
              >
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12">
              <div class="text-right">
                <v-btn small color="secondary" @click="e1 = 1"> Back </v-btn>
                <v-btn
                  v-if="can(`shift_create`)"
                  small
                  :loading="loading"
                  color="primary"
                  @click="store_time_slot"
                >
                  Submit
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
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

    week_days: [
      { label: "Sun", value: "Sun" },
      { label: "Mon", value: "Mon" },
      { label: "Tue", value: "Tue" },
      { label: "Wed", value: "Wed" },
      { label: "Thu", value: "Thu" },
      { label: "Fri", value: "Fri" },
      { label: "Sat", value: "Sat" },
    ],

    shift_payload: {
      name: null,
      beginning_date: null,
      cycle_number: 1,
      cycle_unit: "Week",
      overtime: 0,
      shift_type_id: "",
      days: [],
      time_table_id: null,
      working_hours: 0,
      company_id: 0,
    },

    errors: [],
    data: [],
    response: "",
    snackbar: false,

    Model: "Schedule",
    e1: 1,
    ticksLabels: [],

    loading: false,
    time_in_menu: false,
    time_out_menu: false,
    grace_time_in_menu: false,
    grace_time_out_menu: false,

    beginning_in_menu: false,
    ending_in_menu: false,

    beginning_out_menu: false,
    ending_out_menu: false,

    break_time_start_menu: false,
    break_time_end_menu: false,

    payload: {
      count_as_workday: 0,
      break_time_start_menu: false,
      break_time_end_menu: false,
      on_duty_time: null,
      off_duty_time: null,
      late_time: null,
      early_time: null,
      beginning_in: null,
      ending_in: null,
      beginning_out: null,
      ending_out: null,
      count_as_minute: "0",
      absent_min_in: null,
      absent_min_out: null,
    },

    errors: [],
    data: [],
    response: "",
    snackbar: false,
  }),
  async created() {
    for (let i = 1; i <= 24; i++) {
      this.ticksLabels.push(`${i}`);
    }

    let options = {
      per_page: 1000,
      company_id: this.$auth.user.company.id,
    };

    this.$axios.get("time_table", { params: options }).then(({ data }) => {
      this.time_tables = data.data;
    });

    this.$axios.get("shift_type", { params: options }).then(({ data }) => {
      this.shift_types = data;
      return;

      let manual_shift = data.filter((e) =>
        e.slug == "manual_shift" ? e : ""
      )[0];

      this.shift_types = [].concat(
        [{ id: "", name: "Select Shift Type" }, manual_shift],
        data.filter((e) => (e.shift ? "" : e))
      );
    });
  },
  methods: {
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    checkIfShiftIsManual() {
      this.manual_shift = this.shift_types.find(
        (e) => e.id == this.shift_payload.shift_type_id
      );
    },

    store_time_slot() {
      let payload = this.payload;
      payload.company_id = this.$auth.user.company.id;
      payload.shift_id = this.shift_last_id;
      this.loading = true;

      this.$axios
        .post(`/time_table`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "TimeTable added successfully";
            this.payload = {};
            this.$router.push(`/shift`);
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    store_shift() {
      let payload = this.shift_payload;
      payload.company_id = this.$auth.user.company.id;
      this.loading = true;
      this.$axios
        .post(`/shift`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = true;
            this.response = "Shift added successfully";
            if (this.manual_shift.slug == "manual_shift") {
              this.e1 = 2;
              this.shift_last_id = data.record.id;
            } else {
              this.$router.push(`/shift`);
            }
          }
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
  },
};
</script>
