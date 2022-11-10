<template>
  <div v-if="can(`user_access`)">
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
          <v-btn v-if="can(`user_create`)" small to="/shift" color="primary">
            <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
          </v-btn>
        </div>
      </v-col>
    </v-row>
    <v-card>
      <v-toolbar dark flat class="primary">
        <h3>Create {{ Model }}</h3>
      </v-toolbar>

      <v-card flat>
        <v-card-text>
          <v-row>
            <v-col cols="12" md="4">
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

            <v-col cols="12" md="4">
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
                    v-model="payload.beginning_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="payload.beginning_date"
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
                    @click="$refs.menu.save(payload.beginning_date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>

            <v-col cols="12" md="4">
              Overtime start after duty hours (Minutes)
              <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.overtime"
                :error-messages="errors.overtime && errors.overtime[0]"
                class="mt-1"
                outlined
                dense
                v-model="payload.overtime"
                label=""
                type="number"
              ></v-text-field>
            </v-col>

            <v-col cols="12" md="4">
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

            <v-col cols="12" md="4">
              Cycle Number
              <span class="error--text">*</span>
              <v-text-field
                :hide-details="!errors.cycle_number"
                :error-messages="errors.cycle_number && errors.cycle_number[0]"
                class="mt-1"
                outlined
                dense
                v-model="payload.cycle_number"
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
                v-model="payload.cycle_unit"
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
                v-model="payload.shift_type_id"
                x-small
                :items="shift_types"
                item-value="id"
                item-text="name"
              >
              </v-autocomplete>
            </v-col>

            <v-col cols="12" md="6" v-if="manual_shift.slug == `manual_shift`">
              Select Time Slot
              <v-autocomplete
                :hide-details="!errors.time_table_id"
                :error-messages="
                  errors.time_table_id && errors.time_table_id[0]
                "
                class="mt-1"
                outlined
                dense
                v-model="payload.time_table_id"
                :items="time_tables"
                item-text="name"
                item-value="id"
              >
              </v-autocomplete>
            </v-col>

            <v-col md="12">
              <b>Holidays</b>
              <br />
              <v-checkbox
                style="float: left"
                class="mr-5"
                v-for="(week_day, index) in week_days"
                :key="index"
                v-model="payload.days"
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
                  v-if="can(`user_create`)"
                  small
                  :loading="loading"
                  color="primary"
                  @click="store_schedule"
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
    Model: "Shift",
    manual_shift: {},

    date: null,
    menu: false,

    hours: 1,
    ticksLabels: [],
    time_tables: [],
    shift_types: [],

    week_days: [
      { label: "Sun", value: "Sun" },
      { label: "Mon", value: "Mon" },
      { label: "Tue", value: "Tue" },
      { label: "Wed", value: "Wed" },
      { label: "Thu", value: "Thu" },
      { label: "Fri", value: "Fri" },
      { label: "Sat", value: "Sat" },
    ],
    loading: false,

    payload: {
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
  }),
  async created() {
    let options = {
      per_page: 1000,
      company_id: this.$auth.user.company.id,
    };
    this.$axios.get("time_table", { params: options }).then(({ data }) => {
      this.time_tables = data.data.filter((e) => (!e.shift ? e : null));
    });

    this.$axios.get("shift_type", { params: options }).then(({ data }) => {
      this.shift_types = [].concat(
        [{ id: "", name: "Select Shift Type" }],
        data
      );
    });
  },
  methods: {
    checkIfShiftIsManual() {
      this.manual_shift = this.shift_types.find(
        (e) => e.id == this.payload.shift_type_id
      );
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store_schedule() {
      let payload = this.payload;
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
