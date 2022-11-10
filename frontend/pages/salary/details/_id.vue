<template>
  <div v-if="can('salary_details_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-row class="mt-10 mb-10">
        <v-col cols="10">
          <h3>{{ Model }}</h3>
          <div>Dashboard / {{ Model }} / Details</div>
        </v-col>
      </v-row>

      <v-card elevation="2">
        <v-row>
          <v-col cols="6" style="border-right: 1px dashed #808080">
            <v-list-item>
              <v-list-item-avatar tile size="120">
                <v-avatar size="100">
                  <img
                    :src="payload.employee.profile_picture || '/no-image.PNG'"
                  />
                </v-avatar>
              </v-list-item-avatar>
              <v-list-item-content>
                <div class="text-overline mb-1">
                  Employee Id : {{ payload.employee.id }}
                </div>
                <v-list-item-title class="text-h5 mb-1">
                  {{ payload.amount }} AED
                </v-list-item-title>
                <v-list-item-subtitle>{{
                  payload.salary_type.name
                }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-row class="mt-2">
                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      First Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ payload.employee.first_name }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-h7 mb-1">
                      Last Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ payload.employee.last_name }}
                  </v-col>
                </v-row>
              </v-list-item-content>
            </v-list-item>
          </v-col>
          <v-col cols="6">
            <v-row>
              <v-col cols="6">
                <v-list-item-title class="text-h7 mb-1">
                  Phone Number
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.employee.phone_number }}
              </v-col>

              <!-- <v-col cols="3" class="text-right" style="margin:-8px;">
                <v-icon
                  v-if="can(`employee_edit`)"
                  @click="editItem(`/employees/${$route.params.id}`)"
                  small
                  class="grey"
                  style="border-radius:50%; padding:5px;"
                  color="secondary"
                  >mdi-pencil</v-icon
                >
              </v-col> -->

              <v-col cols="6">
                <v-list-item-title class="text-h7 mb-1">
                  Phone Number (Relative)
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.employee.phone_relative_number }}
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-h7 mb-1">
                  Whatsapp Number
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.employee.whatsapp_number }}
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-h7 mb-1">
                  Whatsapp Number (Relative)
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.employee.whatsapp_relative_number }}
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-h7 mb-1">
                  Joining Date
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.employee.show_joining_date }}
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card>
      <v-card>
        <v-tabs class="mt-5 mb-5">
          <v-tab v-if="can(`salary_deductions_access`)">
            <v-icon> mdi-cash </v-icon>
            Deductions
          </v-tab>

          <v-tab v-if="can(`salary_overtime_access`)">
            <v-icon left> mdi-clock </v-icon>
            Overtime
          </v-tab>

          <v-tab v-if="can(`salary_allowance_access`)">
            <v-icon left> mdi-cash </v-icon>
            Allowance
          </v-tab>

          <v-tab v-if="can(`salary_commission_access`)">
            <v-icon left> mdi-sale </v-icon>
            Commission
          </v-tab>

          <v-tab-item v-if="can(`salary_deductions_access`)">
            <Deduction :employee_id="employee_id" />
          </v-tab-item>
          <v-tab-item v-if="can(`salary_overtime_access`)">
            <Overtime :employee_id="employee_id" />
          </v-tab-item>
          <v-tab-item v-if="can(`salary_allowance_access`)">
            <Allowance :employee_id="employee_id" />
          </v-tab-item>
          <v-tab-item v-if="can(`salary_commission_access`)">
            <Commision :employee_id="employee_id" />
          </v-tab-item>
        </v-tabs>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import Deduction from "../../../components/Deduction.vue";
import Preloader from "../../../components/Preloader.vue";
import NoAccess from "../../../components/NoAccess.vue";
import Overtime from "../../../components/Overtime.vue";
import Allowance from "../../../components/Allowance.vue";
import Commision from "../../../components/Commision.vue";

export default {
  data: () => ({
    Model: "Salary",
    add_other_personal_info: false,
    add_other_bank_info: false,
    loading: true,
    payload: {},
    deductions_popup: false,
    deductions_popup_edit: false,
    e1: 1,
    errors: [],
    data: [],
    devices: [],
    snackbar: false,
    response: "",

    editedItem_Deduction: {},
    Deduction: {
      items: [{ title: "", amount: "" }],
    },
    deduction_list: [],

    employee_id: "",
  }),
  async created() {
    this.getSalaryDetails();
  },

  methods: {
    caps(str) {
      return str.replace(/\b\w/g, (c) => c.toUpperCase());
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },
    getSalaryDetails() {
      this.$axios.get(`salary/${this.$route.params.id}`).then(({ data }) => {
        this.payload = data.record;
        this.employee_id = data.record.employee_id;
        this.loading = false;
      });
    },

    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },
  },
  components: {
    NoAccess,
    Preloader,
    Deduction,
    Overtime,
    Allowance,
    Commision,
  },
};
</script>
