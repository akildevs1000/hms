<template>
  <div v-if="can(`attendance_report_access`)">
    <v-row justify="center">
      <v-dialog v-model="time_table_dialog" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5">Time Slots</span>
          </v-toolbar>
          <v-card-text>
            <ol class="pa-3">
              <li v-for="(shift, index) in shifts" :key="index">
                {{ shift.name }}
                {{
                  shift.on_duty_time
                    ? `(${shift.on_duty_time} - ${shift.off_duty_time})`
                    : ""
                }}
              </li>
            </ol>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-row justify="center">
      <v-dialog v-model="dialog" max-width="700px">
        <v-card>
          <v-card-title class="primary darken-2">
            <span class="headline white--text"> Update Log </span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-col md="12">
                    <v-menu
                      ref="time_menu_ref"
                      v-model="time_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      :return-value.sync="payload.time"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="290px"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="editItems.time"
                          label="Time"
                          readonly
                          v-bind="attrs"
                          :rules="timeRules"
                          v-on="on"
                        ></v-text-field>
                      </template>
                      <v-time-picker
                        v-if="time_menu"
                        v-model="editItems.time"
                        full-width
                        format="24hr"
                      >
                        <v-spacer></v-spacer>
                        <v-btn
                          x-small
                          color="primary"
                          @click="time_menu = false"
                        >
                          Cancel
                        </v-btn>
                        <v-btn
                          x-small
                          color="primary"
                          @click="$refs.time_menu_ref.save(editItems.time)"
                        >
                          OK
                        </v-btn>
                      </v-time-picker>
                    </v-menu>
                    <span
                      v-if="errors && errors.time"
                      class="text-danger mt-2"
                      >{{ errors.time[0] }}</span
                    >
                  </v-col>
                  <!-- <v-col md="12">
                  <v-text-field
                    v-model="editItems.device_id"
                    label="Device Id"
                    readonly
                  ></v-text-field>
                  <span
                    v-if="errors && errors.device_id"
                    class="text-danger mt-2"
                    >{{ errors.device_id[0] }}</span
                  >
                </v-col> -->

                  <v-col md="12">
                    <v-autocomplete
                      label="Select Device"
                      v-model="editItems.device_id"
                      :items="devices"
                      item-text="name"
                      item-value="id"
                      :rules="deviceRules"
                    >
                    </v-autocomplete>
                    <span
                      v-if="errors && errors.device_id"
                      class="text-danger mt-2"
                      >{{ errors.device_id[0] }}</span
                    >
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      filled
                      label="Reason"
                      v-model="editItems.reason"
                      auto-grow
                      :rules="nameRules"
                      required
                    ></v-textarea>
                    <span v-if="errors && errors.reason" class="error--text">
                      {{ errors.reason[0] }}
                    </span>
                  </v-col>
                </v-form>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close"> Cancel </v-btn>
            <v-btn class="primary" small @click="update">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>

    <v-row class="mt-5 mb-5">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col cols="12">
        <v-card elevation="2" class="pa-5">
          <v-row>
            <v-col md="12">
              <h5>Filters</h5>
            </v-col>
            <v-col md="3">
              Report Type
              <v-select
                @change="fetch_logs"
                class="mt-2"
                outlined
                dense
                v-model="payload.status"
                x-small
                :items="[
                  `Select All`,
                  `Summary`,
                  `Present`,
                  `Absent`,
                  `Missing`,
                  `Manual Entry`
                ]"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-select>
            </v-col>
            <v-col md="3" v-if="isCompany">
              Departments
              <v-autocomplete
                @change="getEmployeesByDepartment"
                class="mt-2"
                outlined
                dense
                v-model="payload.department_id"
                x-small
                :items="departments"
                item-value="id"
                item-text="name"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col md="3">
              Employee ID
              <v-autocomplete
                @change="fetch_logs"
                class="mt-2"
                outlined
                dense
                v-model="payload.employee_id"
                x-small
                :items="scheduled_employees"
                item-value="system_user_id"
                item-text="name_with_user_id"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col md="3" v-if="isCompany"></v-col>
            <v-col md="3">
              <div>Frequency</div>
              <v-autocomplete
                class="mt-2"
                @change="changeReportType(payload.report_type)"
                outlined
                dense
                v-model="payload.report_type"
                x-small
                :items="['Daily', 'Weekly', 'Monthly']"
                item-text="['Daily', 'Weekly']"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col md="3" v-if="payload.report_type == 'Daily'">
              <div>Date</div>
              <div class="text-left mt-2">
                <v-menu
                  class="mt-2"
                  ref="daily_menu"
                  v-model="daily_menu"
                  :close-on-content-click="false"
                  :return-value.sync="daily_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="payload.daily_date"
                      outlined
                      dense
                      v-model="payload.daily_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.daily_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="daily_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="
                        set_date_save($refs.daily_menu, payload.daily_date)
                      "
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col v-if="payload.report_type !== 'Daily'" md="3">
              <div class="text-left">
                <v-menu
                  ref="from_menu"
                  v-model="from_menu"
                  :close-on-content-click="false"
                  :return-value.sync="from_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <div class="mb-2">From Date</div>
                    <v-text-field
                      :hide-details="payload.from_date"
                      outlined
                      dense
                      v-model="payload.from_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.from_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="set_date_save($refs.from_menu, payload.from_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
            <v-col v-if="payload.report_type !== 'Daily'" md="3">
              <div class="mb-2">To Date</div>

              <div class="text-left">
                <v-menu
                  ref="to_menu"
                  v-model="to_menu"
                  :close-on-content-click="false"
                  :return-value.sync="to_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :hide-details="payload.to_date"
                      outlined
                      dense
                      v-model="payload.to_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="payload.to_date"
                    :max="max_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="to_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="set_date_save($refs.to_menu, payload.to_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </div>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
    <v-dialog v-model="add_manual_log" width="700">
      <v-card>
        <v-card-title class="text-h5 primary white--text darken-2" dark>
          Manual Log
        </v-card-title>

        <v-card-text class="pa-3">
          <v-row>
            <v-col md="12">
              <v-text-field
                v-model="log_payload.user_id"
                label="User Id"
              ></v-text-field>
              <span v-if="errors && errors.user_id" class="text-danger mt-2">{{
                errors.user_id[0]
              }}</span>
            </v-col>
            <v-col md="12">
              <v-autocomplete
                label="Select Device"
                v-model="log_payload.device_id"
                :items="devices"
                item-text="name"
                item-value="id"
                :rules="deviceRules"
              >
              </v-autocomplete>
              <span
                v-if="errors && errors.device_id"
                class="text-danger mt-2"
                >{{ errors.device_id[0] }}</span
              >
            </v-col>
            <v-col md="12">
              <v-autocomplete
                label="In/Out"
                v-model="log_payload.log_type"
                :items="['In', 'Out']"
                :rules="deviceRules"
              >
                {{ log_payload.log_type }}
              </v-autocomplete>
              <span v-if="errors && errors.log_type" class="text-danger mt-2">{{
                errors.log_type[0]
              }}</span>
            </v-col>
            <v-col cols="12" md="6">
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
                    v-model="log_payload.date"
                    label="Date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  >
                  </v-text-field>
                </template>
                <v-date-picker v-model="log_payload.date" no-title scrollable>
                  <v-spacer></v-spacer>
                  <v-btn text color="primary" @click="menu = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    text
                    color="primary"
                    @click="$refs.menu.save(log_payload.date)"
                  >
                    OK
                  </v-btn>
                </v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12" md="6">
              <v-menu
                ref="manual_time_menu_ref"
                v-model="manual_time_menu"
                :close-on-content-click="false"
                :nudge-right="40"
                :return-value.sync="log_payload.time"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="log_payload.time"
                    label="Time"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  >
                  </v-text-field>
                </template>
                <v-time-picker
                  v-if="manual_time_menu"
                  v-model="log_payload.time"
                  full-width
                  format="24hr"
                >
                  <v-spacer></v-spacer>
                  <v-btn x-small color="primary" @click="manual_ = false">
                    Cancel
                  </v-btn>
                  <v-btn
                    x-small
                    color="primary"
                    @click="$refs.manual_time_menu_ref.save(log_payload.time)"
                  >
                    OK
                  </v-btn>
                </v-time-picker>
              </v-menu>
              <span v-if="errors && errors.time" class="text-danger mt-2">{{
                errors.time[0]
              }}</span>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            small
            :loading="loading"
            color="primary"
            @click="store_schedule"
          >
            Submit
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-toolbar
      class="background"
      dark
      flat
      v-if="payload.report_type == 'Daily'"
    >
      <v-spacer></v-spacer>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            class="ma-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('daily')"
          >
            <v-icon class="">mdi-printer-outline</v-icon>
          </v-btn>
        </template>
        <span>PRINT</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('daily_download_pdf')"
          >
            <v-icon class="">mdi-download-outline</v-icon>
          </v-btn>
        </template>
        <span>DOWNLOAD</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('daily_download_csv')"
          >
            <v-icon class="">mdi-file-outline</v-icon>
          </v-btn>
        </template>
        <span>CSV</span>
      </v-tooltip>
    </v-toolbar>

    <v-toolbar
      class="background"
      dark
      flat
      v-if="payload.report_type == 'Weekly'"
    >
      <v-spacer></v-spacer>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            class="ma-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('weekly')"
          >
            <v-icon class="">mdi-printer-outline</v-icon>
          </v-btn>
        </template>
        <span>PRINT</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('weekly_download_pdf')"
          >
            <v-icon class="">mdi-download-outline</v-icon>
          </v-btn>
        </template>
        <span>DOWNLOAD</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('weekly_download_csv')"
          >
            <v-icon class="">mdi-file-outline</v-icon>
          </v-btn>
        </template>
        <span>CSV</span>
      </v-tooltip>
    </v-toolbar>

    <v-toolbar
      class="background"
      dark
      flat
      v-if="payload.report_type == 'Monthly'"
    >
      <v-spacer></v-spacer>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            class="ma-0"
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('monthly')"
          >
            <v-icon class="">mdi-printer-outline</v-icon>
          </v-btn>
        </template>
        <span>PRINT</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('monthly_download_pdf')"
          >
            <v-icon class="">mdi-download-outline</v-icon>
          </v-btn>
        </template>
        <span>DOWNLOAD</span>
      </v-tooltip>

      <v-tooltip top color="primary">
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            x-small
            :ripple="false"
            text
            v-bind="attrs"
            v-on="on"
            @click="process_file('monthly_download_csv')"
          >
            <v-icon class="">mdi-file-outline</v-icon>
          </v-btn>
        </template>
        <span>CSV</span>
      </v-tooltip>
    </v-toolbar>
    <v-data-table
      v-if="can(`attendance_report_view`)"
      :headers="headers"
      :items="data"
      :server-items-length="total"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [50, 100, 500, 1000]
      }"
      class="elevation-1"
    >
      <template v-slot:item.employee_id="{ item }">
        <!-- <NuxtLink :to="`/employees/details/${item.employee.id}`"
          >{{ item.employee_id
          }}<v-icon small color="black">mdi-open-in-new</v-icon></NuxtLink
        > -->
        {{ item.employee_id }}
      </template>
      <template v-slot:item.status="{ item }">
        <v-icon v-if="item.status == 'A'" color="error">mdi-close</v-icon>

        <v-icon v-else-if="item.status == 'P'" color="success darken-1"
          >mdi-check</v-icon
        >
        <v-icon v-else-if="item.status == 'H'" color="grey darken-1"
          >mdi-check</v-icon
        >
        <span v-else>{{ item.status }}</span>
      </template>

      <template v-slot:item.schedule="{ item }">
        <v-tooltip v-if="item && item.schedule" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ (item.schedule.shift && item.schedule.shift.name) || "---" }}
            </div>
          </template>
          <div v-for="(iterable, index) in item.schedule.shift" :key="index">
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>

      <template v-slot:item.device_in="{ item }">
        <v-tooltip v-if="item && item.device_in" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ (item.device_in && item.device_in.short_name) || "---" }}
            </div>
          </template>
          <div v-for="(iterable, index) in item.device_in" :key="index">
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>

      <template v-slot:item.device_out="{ item }">
        <v-tooltip v-if="item && item.device_out" top color="primary">
          <template v-slot:activator="{ on, attrs }">
            <div class="primary--text" v-bind="attrs" v-on="on">
              {{ (item.device_out && item.device_out.short_name) || "---" }}
            </div>
          </template>
          <div v-for="(iterable, index) in item.device_out" :key="index">
            <span v-if="index !== 'id'">
              {{ caps(index) }}: {{ iterable || "---" }}</span
            >
          </div>
        </v-tooltip>
        <span v-else>---</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon @click="editItem(item)" x-small color="primary" class="mr-2">
          mdi-pencil
        </v-icon>
        <v-icon @click="viewItem(item)" x-small color="primary" class="mr-2">
          mdi-eye
        </v-icon>
      </template>
    </v-data-table>
    <NoAccess v-else />

    <v-row justify="center">
      <v-dialog v-model="log_details" max-width="600px">
        <v-card class="darken-1">
          <v-toolbar class="primary" dense dark flat>
            <span class="text-h5 pa-2">Log Details</span>
          </v-toolbar>
          <v-card-text>
            <div class="pt-5">
              <span v-for="(log, index) in log_list" :key="index">
                {{ log.time }}
                <hr />
              </span>
            </div>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
  </div>
  <NoAccess v-else />
</template>
<script>
export default {
  // layout({ $auth }) {
  //   let { is_master } = $auth.user;
  //   return is_master ? "default" : "employee";
  // },
  data: () => ({
    isCompany: true,
    time_table_dialog: false,
    log_details: false,
    overtime: false,
    options: {},
    date: null,
    menu: false,
    loading: false,
    time_menu: false,
    manual_time_menu: false,
    Model: "Reports",
    endpoint: "report",
    search: "",
    snackbar: false,
    add_manual_log: false,
    dialog: false,
    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,
    ids: [],
    departments: [],
    scheduled_employees: [],
    DateRange: true,
    devices: [],
    valid: true,
    nameRules: [v => !!v || "reason is required"],
    timeRules: [v => !!v || "time is required"],
    deviceRules: [v => !!v || "device is required"],

    daily_menu: false,
    daily_date: null,
    dailyDate: false,
    editItems: {
      attendance_logs_id: "",
      UserID: "",
      device_id: "",
      user_id: "",
      reason: "",
      date: "",
      time: null
    },
    loading: false,
    total: 0,
    headers: [
      { text: "Date", align: "left", sortable: false, value: "date" },
      { text: "E.ID", align: "left", sortable: false, value: "employee_id" },
      {
        text: "Name",
        align: "left",
        sortable: false,
        value: "employee.first_name"
      },
      {
        text: "Dept",
        align: "left",
        sortable: false,
        value: "employee.department.name"
      },
      {
        text: "Shift Type",
        align: "left",
        sortable: false,
        value: "schedule.shift_type.name"
      },
      {
        text: "Shift",
        align: "left",
        sortable: false,
        value: "schedule"
      },
      { text: "Status", align: "left", sortable: false, value: "status" },
      { text: "In", align: "left", sortable: false, value: "in" },
      { text: "Out", align: "left", sortable: false, value: "out" },
      {
        text: "Total Hrs",
        align: "left",
        sortable: false,
        value: "total_hrs"
      },
      { text: "OT", align: "left", sortable: false, value: "ot" },
      {
        text: "Late coming",
        align: "left",
        sortable: false,
        value: "late_coming"
      },
      {
        text: "Early Going",
        align: "left",
        sortable: false,
        value: "early_going"
      },
      {
        text: "D.In",
        align: "left",
        sortable: false,
        value: "device_in"
      },
      {
        text: "D.Out",
        align: "left",
        sortable: false,
        value: "device_out"
      },

      { text: "Actions", value: "actions", sortable: false }
    ],
    payload: {
      from_date: null,
      to_date: null,
      daily_date: null,
      employee_id: "",
      report_type: "Daily",
      department_id: -1,
      status: "Select All",
      late_early: "Select All"
    },
    log_payload: {
      user_id: null,
      device_id: "OX-8862021010011",
      date: null,
      time: null
    },
    log_list: [],
    snackbar: false,
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    shifts: [],
    errors: [],
    custom_options: {},
    max_date: null
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    }
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  created() {
    this.loading = true;

    // this.setMonthlyDateRange();
    this.payload.daily_date = new Date().toJSON().slice(0, 10);
    this.custom_options = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company.id
      }
    };
    this.getDepartments(this.custom_options);
    this.getEmployeesByDepartment();
    this.getDeviceList();

    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;
    let d = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.payload.from_date = `${y}-${m}-01`;
    this.payload.to_date = `${y}-${m}-${d.getDate()}`;
  },

  methods: {
    setSevenDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 6);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    setThirtyDays(selected_date) {
      const date = new Date(selected_date);

      date.setDate(date.getDate() + 29);

      let datetime = new Date(date);

      let d = datetime.getDate();
      d = d < "10" ? "0" + d : d;
      let m = datetime.getMonth() + 1;
      m = m < 10 ? "0" + m : m;
      let y = datetime.getFullYear();

      this.max_date = `${y}-${m}-${d}`;
      this.payload.to_date = `${y}-${m}-${d}`;
    },

    set_date_save(from_menu, field) {
      from_menu.save(field);

      if (this.payload.report_type == "Weekly") {
        this.setSevenDays(this.payload.from_date);
      } else if (this.payload.report_type == "Monthly") {
        this.setThirtyDays(this.payload.from_date);
      }

      this.fetch_logs();
    },
    changeReportType(report_type) {
      let dt = new Date();
      let y = dt.getFullYear();
      let m = dt.getMonth() + 1;
      let d = new Date(dt.getFullYear(), m, 0);

      m = m < 10 ? "0" + m : m;

      if (this.payload.from_date == null) {
        this.payload.from_date = `${y}-${m}-01`;
      }

      if (report_type == "Daily") {
        this.setDailyDate();
      }
      else if (report_type == "Weekly") {
        this.setSevenDays(this.payload.from_date);
      } else {
        this.setThirtyDays(this.payload.from_date);
      }
      this.fetch_logs();
    },

    getDeviceList() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`/device_list`, payload).then(({ data }) => {
        this.devices = data;
      });
    },

    setDailyDate() {
      this.payload.daily_date = new Date().toJSON().slice(0, 10);
      delete this.payload.from_date;
      delete this.payload.to_date;
    },

    store_schedule() {
      let { user_id, date, time, device_id } = this.log_payload;
      let log_payload = {
        UserID: user_id,
        LogTime: date + " " + time + ":00",
        DeviceID: device_id,
        company_id: this.$auth.user.company.id
      };
      this.loading = true;

      this.$axios
        .post(`/generate_log`, log_payload)
        .then(({ data }) => {
          this.fetch_logs();
          this.add_manual_log = false;
          this.loading = false;
        })
        .catch(({ message }) => {
          this.snackbar = true;
          this.response = message;
        });
    },
    setEmployeeId(id) {
      this.$store.commit("employee_id", id);
    },
    get_time_slots() {
      this.getShift(this.custom_options);
    },
    getShift(options) {
      this.$axios.get(`/shift`, options).then(({ data }) => {
        this.shifts = data.data.map(e => ({
          name: e.name,
          on_duty_time: (e.time_table && e.time_table.on_duty_time) || "",
          off_duty_time: (e.time_table && e.time_table.off_duty_time) || ""
        }));
        this.time_table_dialog = true;
      });
    },

    getScheduledEmployees() {
      this.$axios.get(`/scheduled_employees_with_type`).then(({ data }) => {
        this.scheduled_employees = data;
      });
    },
    // getDevices(options) {
    //   this.$axios.get(`/device`, options).then(({ data }) => {
    //     this.devices = data.data;
    //   });
    // },
    getDepartments(options) {
      let u = this.$auth.user;
      if (u.user_type == "employee") {
        this.departments = [u.employee.department];
        this.isCompany = false;
        return;
      }
      this.$axios
        .get("departments", options)
        .then(({ data }) => {
          this.departments = [{ id: -1, name: "Select All" }].concat(data.data);
        })
        .catch(err => console.log(err));
    },

    getEmployeesByDepartment() {
      this.fetch_logs();
      let u = this.$auth.user;
      let department_id = "";
      if (u.user_type == "employee") {
        department_id = u.employee.department_id;
      } else {
        department_id = this.payload.department_id;
      }

      this.$axios
        .get(`/employees_by_departments/${department_id}`, this.custom_options)
        .then(({ data }) => {
          this.scheduled_employees = data;
          if (this.scheduled_employees.length > 0) {
            this.scheduled_employees.unshift({
              system_user_id: "",
              name_with_user_id: "Select All"
            });
          }
          this.loading = false;
        });
    },

    caps(str) {
      return str.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    fetch_logs() {
      this.getDataFromApi();
    },

    getDataFromApi(url = this.endpoint) {
      this.loading = true;

      
      let late_early = this.payload.late_early;

      switch (late_early) {
        case "Select All":
          late_early = "SA";
          break;

        default:
          late_early = late_early.charAt(0);
          break;
      }

      const { page, itemsPerPage } = this.options;

      let u = this.$auth.user;
      if (u.user_type == "employee") {
        this.payload.department_id = u.employee.department_id;
      }
      let options = {
        params: {
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company.id,
          ...this.payload,
          status: this.getStatus(this.payload.status),
          late_early,
          ot: this.overtime ? 1 : 0
        }
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.data = data.data;
        this.total = data.total;
        this.loading = false;
      });
    },

    editItem(item) {
      this.dialog = true;
      this.editItems.UserID = item.employee_id;
      this.editItems.date = item.edit_date;
    },

    update() {
      if (this.$refs.form.validate()) {
        let payload = {
          UserID: this.editItems.UserID,
          LogTime: this.editItems.date + " " + this.editItems.time + ":00",
          DeviceID: this.editItems.device_id,
          user_id: this.editItems.UserID,
          company_id: this.$auth.user.company.id,
          reason: this.editItems.reason
        };

        this.$axios
          .post("/generate_manual_log", payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
              // this.msg = data.message;
            } else {
              this.snackbar = true;
              this.response = data.message;
              this.editItems = [];
              this.getDataFromApi();
              this.close();
            }
          })
          .catch(e => console.log(e));
      }
    },

    viewItem(item) {
      this.log_list = [];
      let options = {
        params: {
          per_page: 500,
          UserID: item.employee_id,
          LogTime: item.edit_date,
          company_id: this.$auth.user.company.id
        }
      };
      this.log_details = true;

      this.$axios.get("attendance_single_list", options).then(({ data }) => {
        this.log_list = data.data;
      });

      // this.editedIndex = this.data.indexOf(item);
      // this.editedItem = Object.assign({}, item);
      // this.dialog = true;
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    pdfDownload() {
      let path = process.env.BACKEND_URL + "/pdf";
      let pdf = document.createElement("a");
      pdf.setAttribute("href", path);
      pdf.setAttribute("target", "_blank");
      pdf.click();
    },

    process_file(type) {
      let data = this.payload;

      if(data.department_id == -1) {
          alert("Department must be selected.");
          return false;
      }
      let status = this.getStatus(this.payload.status);

      let company_id = this.$auth.user.company.id;
      let path = process.env.BACKEND_URL + "/" + type;

      let qs = `${path}?company_id=${company_id}&status=${status}&department_id=${data.department_id}&employee_id=${data.employee_id}&report_type=${data.report_type}`;

      qs +=
        data.report_type == "Daily"
          ? `&daily_date=${data.daily_date}`
          : `&from_date=${data.from_date}&to_date=${data.to_date}`;

      let report = document.createElement("a");
      report.setAttribute("href", qs);
      report.setAttribute("target", "_blank");
      report.click();

      this.fetch_logs();
      return;
    },

    getStatus(status) {
      switch (status) {
        case "Select All":
          return "SA";
        case "Missing":
          return "---";
        case "Manual Entry":
          return "ME";
        default:
          return status.charAt(0);
      }
    }
  }
};
</script>
