<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-row class="mt-5">
        <v-col cols="6">
          <h3>{{ Model }}</h3>
          <div>Dashboard / {{ Model }}</div>
        </v-col>
        <v-col cols="6">
          <div class="text-left">
            <!-- <v-btn
              small
              class="primary--text pt-4 pb-4"
              to="/employees/employee_list"
            >
              <v-icon class="pa-0">mdi-menu</v-icon>
            </v-btn>
            <v-btn x-small class="primary pt-4 pb-4" to="/employees">
              <v-icon class="pa-0">mdi-grid</v-icon>
            </v-btn> -->
          </div>
          <div class="text-right mt-6">
            <v-btn
              small
              class="primary--text pt-4 pb-4"
              to="/employees/employee_list"
            >
              <v-icon class="pa-0">mdi-menu</v-icon>
            </v-btn>
            <v-btn x-small class="primary pt-4 pb-4" to="/employees">
              <v-icon class="pa-0">mdi-grid</v-icon>
            </v-btn>
            <v-btn
              v-if="can('employee_import_access')"
              small
              dark
              class="primary pt-4 pb-4"
              @click="dialog = true"
            >
              Import <v-icon right dark>mdi-cloud-upload</v-icon>
            </v-btn>

            <v-btn
              v-if="can('employee_export_access')"
              small
              dark
              class="primary pt-4 pb-4"
              @click="export_submit"
            >
              Export <v-icon right dark>mdi-cloud-download</v-icon>
            </v-btn>

            <v-dialog v-model="dialog" max-width="500px">
              <v-card>
                <v-card-text>
                  <v-container>
                    <v-row>
                      <v-col cols="12" class="mb-2">
                        <span class="headline">Import Employee</span>
                      </v-col>
                      <v-col cols="12">
                        <v-file-input
                          accept="text/csv"
                          v-model="files"
                          placeholder="Upload your file"
                          label="File"
                          prepend-icon="mdi-paperclip"
                        >
                          <template v-slot:selection="{ text }">
                            <v-chip v-if="text" small label color="primary">
                              {{ text }}
                            </v-chip>
                          </template>
                        </v-file-input>
                        <br />
                        <a href="/employees.csv" download> Download Sample</a>
                        <br />
                        <span
                          v-if="errors && errors.length > 0"
                          class="error--text"
                          >{{ errors[0] }}</span
                        >
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn class="error" small @click="close"> Cancel </v-btn>

                  <v-btn
                    class="primary"
                    :loading="btnLoader"
                    small
                    @click="save"
                    >Save</v-btn
                  >
                </v-card-actions>
              </v-card>
            </v-dialog>

            <v-btn
              v-if="can('employee_create')"
              @click="createEmployee"
              small
              dark
              class="primary  pt-4 pb-4"
              >{{ Model }} +
            </v-btn>
          </div>
        </v-col>
      </v-row>

      <v-row class="mt-15">
        <v-col md="7">
          <v-row class="d-flex justify-space-between ">
            <v-col xs="12" sm="12" md="3" cols="12">
              <v-select
                @change="getDataFromApi(`employee`)"
                outlined
                v-model="per_page"
                :items="['Default', 10, 25, 50, 100]"
                dense
                placeholder="Per Page Records"
              ></v-select>
            </v-col>
            <v-col xs="12" sm="12" md="3" cols="12">
              <div color="pt-2" class="text-center">
                <v-btn
                  @click="getDataFromApi(prev_page_url)"
                  :disabled="prev_page_url ? false : true"
                  color="primary"
                  small
                  elevation="11"
                >
                  <v-icon dark>mdi-chevron-double-left </v-icon>
                </v-btn>
                <v-btn
                  @click="getDataFromApi(next_page_url)"
                  :disabled="next_page_url ? false : true"
                  color="primary"
                  small
                  elevation="11"
                >
                  <v-icon dark>mdi-chevron-double-right </v-icon>
                </v-btn>
              </div>
            </v-col>
            <v-col xs="12" sm="12" md="3" cols="12">
              <v-text-field
                outlined
                @input="searchIt"
                v-model="search"
                dense
                placeholder="Search..."
              ></v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col
              xs="12"
              sm="6"
              md="3"
              v-for="(item, index) in data"
              :key="index"
              class="pa-xs-0  ma-xs-0"
            >
              <div
                class="card mx-0 my-0 pa-0"
                @click="res(item.id)"
                style="cursor:pointer"
              >
                <div class="banner">
                  <v-img
                    class="gg"
                    viewBox="0 0 100 100"
                    style="border-radius: 50%;  height: 80px; max-width: 80px !important"
                    :src="item.profile_picture || '/no-profile-image.jpg'"
                  ></v-img>
                </div>
                <div class="menu">
                  <div class="opener"></div>
                </div>
                <h2 class="name" style="font-size:15px">
                  {{ limitName(item.display_name) }}
                </h2>
                <div class="title" style="font-size:12px !important">
                  EID: {{ item.employee_id }}
                </div>
                <div class="title" style="font-size:12px !important">
                  {{
                    (item.designation && item.designation.name) ||
                      "Software Developer"
                  }}
                </div>
                <div class="actions">
                  <div class="follow-info">
                    <h2>
                      <v-divider class="pa-0 ma-0"></v-divider>
                      <a href="#"
                        ><span>{{ item && item.department.name }} </span>
                        <p style="font-size:12px;color:#A09FA0">Department</p>
                      </a>
                    </h2>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <div color="pt-2" class="text-center">
                <v-btn
                  @click="getDataFromApi(prev_page_url)"
                  :disabled="prev_page_url ? false : true"
                  color="primary"
                  small
                  elevation="11"
                >
                  <v-icon dark>mdi-chevron-double-left </v-icon>
                </v-btn>
                <v-btn
                  @click="getDataFromApi(next_page_url)"
                  :disabled="next_page_url ? false : true"
                  color="primary"
                  small
                  elevation="11"
                >
                  <v-icon dark>mdi-chevron-double-right </v-icon>
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-col>
        <v-col md="5">
          <v-row>
            <v-col md="12">
              <v-skeleton-loader
                class="view-card"
                v-if="boilerplate"
                type="list-item-avatar-three-line, image, article,article,article"
              ></v-skeleton-loader>

              <v-card v-else height="700px" class="view-card mx-0 my-0 pa-0">
                <v-toolbar color="primary " dark flat fixed max-height="55">
                  <v-toolbar-title>{{
                    selectedItem == 0 ? "Work Info" : caps(ListName)
                  }}</v-toolbar-title>
                </v-toolbar>

                <v-container grid-list-xs>
                  <div class="" style=" margin:  4px  auto; width: 91px;">
                    <div class="banner">
                      <v-img
                        class="gg"
                        viewBox="0 0 100 100"
                        style="border-radius: 50%;  height: 100px; min-width: 100px !important"
                        :src="work.profile_picture || '/no-profile-image.jpg'"
                      ></v-img>
                    </div>
                  </div>
                  <div style="margin: 0 auto; width: 100%;">
                    <p style=" text-align: center;">
                      <b>{{ work && caps(work.display_name) }}</b> <br />
                      <b style="color:#A09FA0;font-size: 15px;">
                        EID: {{ work && work.employee_id }} </b
                      ><br />
                      <b style="color:#A09FA0">
                        {{ work && caps(work.designation.name) }}</b
                      >
                    </p>
                  </div>
                  <v-divider style="width:94%;"></v-divider>
                  <!-- work info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col md="12" v-show="selectedItem == 0" color="primary">
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 0">
                            <table>
                              <tr>
                                <th>Role</th>
                                <td>
                                  {{
                                    (work && work.role && work.role.name) ||
                                      "---"
                                  }}
                                </td>
                              </tr>
                              <tr>
                                <th>EID</th>
                                <td>
                                  {{ (work && work.employee_id) || "----" }}
                                </td>
                              </tr>
                              <tr>
                                <th>First Name</th>
                                <td>
                                  {{ caps(work && work.first_name) || "---" }}
                                </td>
                              </tr>
                              <tr>
                                <th>Last Name</th>
                                <td>
                                  {{ caps(work && work.last_name) || "---" }}
                                </td>
                              </tr>
                              <tr>
                                <th>Department</th>
                                <td>
                                  {{ (work && work.department.name) || "----" }}
                                </td>
                              </tr>

                              <tr>
                                <th>Sub Department</th>
                                <td>
                                  {{
                                    (work &&
                                      work.sub_department &&
                                      work.sub_department.name) ||
                                      "----"
                                  }}
                                </td>
                              </tr>

                              <tr>
                                <th>Email</th>
                                <td>
                                  {{ (work && work.user.email) || "----" }}
                                </td>
                              </tr>
                              <tr>
                                <th>Whatsapp Number</th>
                                <td>
                                  {{ (work && work.whatsapp_number) || "----" }}
                                </td>
                              </tr>
                              <tr>
                                <th>Joining Date</th>
                                <td>
                                  {{ (work && work.joining_date) || "----" }}
                                </td>
                              </tr>
                            </table>
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- personal info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col md="12" v-show="selectedItem == 1" color="primary">
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 1">
                            <Personal :personalItem="personalItem" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- contact info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-3"
                        v-show="selectedItem == 2"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 2">
                            <Contact :contactItem="contactItem" /></section
                        ></v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Passport info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-6"
                        v-show="selectedItem == 3"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 3">
                            <Passport :passport_list="passport_list" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Emirates info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 4"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 4">
                            <Emirates :emirateItems="emirateItems" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Visa info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 5"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 5">
                            <Visa :visaItem="visaItem" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Bank info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 6"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 6">
                            <Bank :BankInfo="BankInfo" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Document info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 7"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 7">
                            <Bank :BankInfo="BankInfo" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Qualification info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 8"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 8">
                            <Qualification
                              :qualification_list="qualification_list"
                            />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                  <!-- Setting info -->
                  <section style="width:94%">
                    <v-row>
                      <v-col
                        md="12"
                        class="mt-10"
                        v-show="selectedItem == 9"
                        color="primary"
                      >
                        <v-expand-x-transition>
                          <section class="pridmary" v-show="selectedItem == 9">
                            <Setting :setting="setting" />
                          </section>
                        </v-expand-x-transition>
                      </v-col>
                    </v-row>
                  </section>
                </v-container>
                <v-navigation-drawer
                  permanent
                  exdpand-on-hover
                  :mini-variant="drawer"
                  v-model="rightDrawer"
                  :clipped="true"
                  :right="right"
                  absolute
                >
                  <v-list class="primary" dark height="55">
                    <v-list-item class="px-2">
                      <v-list-item-avatar>
                        <v-app-bar-nav-icon
                          @click.stop="drawer = !drawer"
                        ></v-app-bar-nav-icon>
                      </v-list-item-avatar>
                    </v-list-item>
                  </v-list>
                  <!-- <v-divider></v-divider> -->
                  <v-list dense class="mt-0 pt-0">
                    <v-list-item-group v-model="selectedItem" color="primary">
                      <v-list-item
                        @click="getListName(item.text)"
                        class="py-2"
                        v-for="(item, i) in items"
                        :key="i"
                      >
                        <v-list-item-icon>
                          <v-icon v-text="item.icon"></v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                          <v-list-item-title
                            v-text="item.text"
                          ></v-list-item-title>
                        </v-list-item-content>
                      </v-list-item>
                    </v-list-item-group>
                  </v-list>
                </v-navigation-drawer>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
import Personal from "../../components/employee/Personal.vue";
import Contact from "../../components/employee/Contact.vue";
import Passport from "../../components/employee/Passport.vue";
import Emirates from "../../components/employee/Emirates.vue";
import Visa from "../../components/employee/Visa.vue";
import Bank from "../../components/employee/Bank.vue";
import Qualification from "../../components/employee/Qualification.vue";
import Setting from "../../components/employee/Setting.vue";
export default {
  data: () => ({
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    drawer: true,
    selectedItem: 0,
    tab: null,
    items: [
      {
        text: "Work info",
        icon: "mdi-briefcase ",
        permission: "employee_personal_access"
      },
      {
        text: "Personal info",
        icon: "mdi-account-circle ",
        permission: "employee_personal_access"
      },
      {
        text: "Contact info",
        icon: "mdi-account-box ",
        permission: "employee_contact_access"
      },
      {
        text: "Passport info",
        icon: "mdi-file-powerpoint-outline ",
        permission: "employee_passport_access"
      },
      {
        text: "Emirates info",
        icon: "mdi-city-variant",
        permission: "employee_emirate_access"
      },
      {
        text: "Visa info",
        icon: "mdi-file-document-multiple ",
        permission: "employee_visa_access"
      },
      {
        text: "Bank info",
        icon: "mdi-bank",
        permission: "employee_bank_access"
      },
      {
        text: "Documents",
        icon: "mdi-file",
        permission: "employee_document_access"
      },
      {
        text: "Qualification",
        icon: "mdi-file-sign",
        permission: "employee_qualification_access"
      },
      {
        text: "Setting",
        icon: "mdi-wrench",
        permission: "employee_setting_access"
      },
      {
        text: "Assign Reporter",
        icon: "mdi-account",
        permission: "employee_setting_access"
      }
    ],
    color: "primary",
    files: "",
    dialog: false,
    Model: "Employee",
    endpoint: "employee",
    search: "",
    loading: false,
    data: [],
    errors: [],
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
    response: "",
    ListName: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    payload: {},
    work: {
      display_name: "",
      first_name: "",
      last_name: "",
      department: "",
      sub_department: "",
      designation: "",
      role: "",
      employee_id: "",
      system_user_id: "",
      user: "",
      profile_picture: "",
      phone_number: "",
      whatsapp_number: "",
      joining_date: ""
    },
    personalItem: {
      passport_no: "",
      passport_expiry: "",
      tel: "",
      nationality: "",
      religion: "",
      marital_status: "",
      no_of_spouse: "",
      no_of_children: "",
      father_name: "",
      mother_name: "",
      gender: "",
      date_of_birth: "",
      company_id: "",
      employee_id: ""
    },
    contactItem: {
      local_address: "",
      phone_number: "",
      whatsapp_number: "",
      phone_relative_number: "",
      whatsapp_relative_number: "",
      local_fax: "",
      local_email: "",
      local_country: "",
      local_city: "",
      local_residence_no: "",
      relation: "",
      home_address: "",
      home_tel: "",
      home_tel: "",
      home_mobile: "",
      home_fax: "",
      home_city: "",
      home_state: "",
      home_country: "",
      home_email: "",
      other_value: "",
      other_text: "",
      company_id: "",
      employee_id: ""
    },
    emirateItems: {
      emirate_id: "",
      name: "",
      gender: "",
      date_of_birth: "",
      nationality: "",
      issue: "",
      expiry: "",
      company_id: "",
      employee_id: ""
    },
    visaItem: {
      visa_no: "",
      place_of_issues: "",
      country: "",
      issue_date: "",
      expiry_date: "",
      security_amount: "",
      labour_no: "",
      personal_no: "",
      labour_issue_date: "",
      labour_expiry_date: "",
      note: "",
      company_id: "",
      employee_id: ""
    },
    BankInfo: {
      bank_name: "",
      account_no: "",
      account_title: "",
      iban: "",
      address: "",
      company_id: "",
      employee_id: ""
    },
    setting: {
      status: "",
      overtime: "",
      mobile_application: "",
      employee_id: ""
    },
    passport_list: {},
    qualification_list: {}
  }),
  async created() {
    this.loading = false;
    this.boilerplate = true;
    this.getDataFromApi();
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  methods: {
    getListName(val) {
      this.ListName = val;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, c => c.toUpperCase());
      }
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    limitName(value) {
      if (!value) {
        return "---";
      }
      var string = value;
      var length = 14;
      return string.substring(0, length);
    },
    json_to_csv(json) {
      let data = json.map(e => ({
        first_name: e.first_name,
        last_name: e.last_name,
        display_name: e.display_name,
        email: e.user.email,
        phone_number: e.phone_number,
        whatsapp_number: e.whatsapp_number,
        phone_relative_number: e.phone_relative_number,
        whatsapp_relative_number: e.whatsapp_relative_number,
        employee_id: e.employee_id,
        joining_date: e.show_joining_date,
        department_code: e.department_id,
        designation_code: e.designation_id,
        department: e.department.name,
        designation: e.designation.name
      }));
      let header = Object.keys(data[0]).join(",") + "\n";
      let rows = "";
      data.forEach(e => {
        rows +=
          Object.values(e)
            .join(",")
            .trim() + "\n";
      });
      return header + rows;
    },
    res(id) {
      window.scrollTo(0, 0);
      this.boilerplate = true;
      this.$axios.get(`employee/${id}`).then(({ data }) => {
        this.contactItem = {
          ...data
        };
        this.work = {
          ...data
        };
        this.setting = {
          ...data
        };
        this.getAllData(data.employee_id);
        this.boilerplate = false;
      });
    },

    getAllData(id) {
      this.getPersonalInfo(id);
      this.getPassportInfo(id);
      this.getEmirateInfo(id);
      this.getVisaInfo(id);
      this.getBankInfo(id);
      this.getQualificationInfo(id);
    },

    getPersonalInfo(id) {
      this.$axios.get(`personalinfo/${id}`).then(({ data }) => {
        this.personalItem = {
          ...data
        };
      });
    },
    getPassportInfo(id) {
      this.$axios.get(`passport/${id}`).then(({ data }) => {
        this.passport_list = {
          ...data
        };
      });
    },
    getEmirateInfo(id) {
      this.$axios.get(`emirate/${id}`).then(({ data }) => {
        this.emirateItems = {
          ...data
        };
      });
    },
    getVisaInfo(id) {
      this.$axios.get(`visa/${id}`).then(({ data }) => {
        this.visaItem = {
          ...data
        };
      });
    },
    getBankInfo(id) {
      this.$axios.get(`bankinfo/${id}`).then(({ data }) => {
        this.BankInfo = {
          ...data
        };
      });
    },
    getQualificationInfo(id) {
      this.$axios.get(`qualification/${id}`).then(({ data }) => {
        this.qualification_list = {
          ...data
        };
      });
    },
    export_submit() {
      if (this.data.length == 0) {
        this.snackbar = true;
        this.response = "No record to download";
        return;
      }
      let csvData = this.json_to_csv(this.data);
      let element = document.createElement("a");
      element.setAttribute(
        "href",
        "data:text/csv;charset=utf-8, " + encodeURIComponent(csvData)
      );
      element.setAttribute("download", "download.csv");
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
    },
    save() {
      let payload = new FormData();
      payload.append("employees", this.files);
      payload.append("company_id", this.$auth?.user?.company?.id);
      let options = {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      };
      this.btnLoader = true;
      this.$axios
        .post("/employee/import", payload, options)
        .then(({ data }) => {
          this.btnLoader = false;
          if (!data.status) {
            this.errors = data.errors;
            payload.delete("employees");
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees imported successfully";
            this.getDataFromApi();
            this.close();
          }
        })
        .catch(e => {
          if (e.toString().includes("Error: Network Error")) {
            this.errors = [
              "File is modified.Please cancel the current file and try again"
            ];
          }
        });
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },
    createEmployee() {
      if (this.total >= this.max_employee) {
        alert(`You cannot add more than ${this.max_employee} employees`);
        return;
      }
      this.$router.push(`/employees/create`);
    },
    goDetails(id) {
      this.$router.push(`/employees/details/${id}`);
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },
    getDataFromApi(url = this.endpoint) {
      let options = {
        params: {
          per_page: this.per_page === "Default" ? 8 : this.per_page,
          company_id: this.$auth?.user?.company?.id
        }
      };
      this.$axios.get(`${url}`, options).then(({ data }) => {
        this.data = data.data;

        this.contactItem = {
          ...this.data[0]
        };
        this.work = {
          ...this.data[0]
        };
        this.setting = {
          ...this.data[0]
        };

        this.getAllData(this.data[0].employee_id);

        this.max_employee = this.$auth.user.company.max_employee;
        this.next_page_url = data.next_page_url;
        this.prev_page_url = data.prev_page_url;
        this.current_page = data.current_page;
        this.loading = false;
        this.boilerplate = false;
      });
    },
    editItem(item) {
      this.$router.push(`/employees/${item.id}`);
    },
    deleteItem(item) {
      confirm("Are you sure you want to delete this item?") &&
        this.$axios.delete(this.endpoint + "/" + item.id).then(res => {
          const index = this.data.indexOf(item);
          this.data.splice(index, 1);
          this.getDataFromApi();
        });
    }
  },
  components: {
    Personal,
    Contact,
    Passport,
    Emirates,
    Visa,
    Bank,
    Qualification,
    Setting
  }
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #fbfdff;
}
body {
  font-size: 16px;
  color: #404040;
  font-family: Montserrat, sans-serif;
  background-image: linear-gradient(
    to bottom right,
    #ff9eaa 0% 65%,
    #e860ff 95% 100%
  );
  background-position: center;
  background-attachment: fixed;
  margin: 0;
  padding: 2rem 0;
  display: grid;
  place-items: center;
  box-sizing: border-box;
}
.card {
  height: 285px !important;
  background-color: #fff !important;
  max-width: 260px !important;
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
  border-radius: 2rem !important;
}
.view-card {
  /* height: 285px !important; */
  background-color: #fff !important;
  /* max-width: 260px !important; */
  display: flex !important;
  flex-direction: column !important;
  overflow: hidden !important;
  border-radius: 1rem !important;
}

.card .banner {
  background-color: #5fafa3;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  height: 11rem;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  box-sizing: border-box;
}

.card .banner .gg {
  background-color: #fff;
  width: 8rem;
  height: 8rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  transform: translateY(50%);
  transition: transform 200ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
.card .banner svg:hover {
  transform: translateY(50%) scale(1.3);
}
.card .menu {
  width: 100%;
  height: 3.5rem;
  padding: 1rem;
  display: flex;
  align-items: flex-start;
  justify-content: flex-end;
  position: relative;
  box-sizing: border-box;
}
.card .menu .opener {
  width: 2.5rem;
  height: 2.5rem;
  position: relative;
  border-radius: 50%;
  transition: background-color 100ms ease-in-out;
}
.card .menu .opener:hover {
  background-color: #f2f2f2;
}
.card .menu .opener span {
  background-color: #404040;
  width: 0.4rem;
  height: 0.4rem;
  position: absolute;
  top: 0;
  left: calc(50% - 0.2rem);
  border-radius: 50%;
}
.card .menu .opener span:nth-child(1) {
  top: 0.45rem;
}
.card .menu .opener span:nth-child(2) {
  top: 1.05rem;
}
.card .menu .opener span:nth-child(3) {
  top: 1.65rem;
}
.card h2.name {
  text-align: center;
  padding: 0 2rem 0.5rem;
  margin: 0;
}
.card .title {
  color: #a0a0a0;
  font-size: 0.85rem;
  text-align: center;
  /* padding: 0 0rem 1.2rem; */
}
.card .actions {
  padding: 0 2rem 1.2rem;
  display: flex;
  flex-direction: column;
  order: 99;
}
.card .actions .follow-info {
  padding: 0 0 1rem;
  /* display: flex; */
}
.card .actions .follow-info h2 {
  text-align: center;
  width: 100%;
  margin: 0;
  box-sizing: border-box;
}
.card .actions .follow-info h2 a {
  text-decoration: none;
  /* padding: 0.8rem; */
  /* display: flex; */
  /* flex-direction: column; */
  border-radius: 0.8rem;
  transition: background-color 100ms ease-in-out;
}
.card .actions .follow-info h2 a span {
  color: #1c9eff;
  font-weight: bold;
  transform-origin: bottom;
  transform: scaleY(1.3);
  transition: color 100ms ease-in-out;
  font-size: 15px;
}
.card .actions .follow-info h2 a small {
  color: #afafaf;
  font-size: 0.85rem;
  font-weight: normal;
}

.card .actions .follow-info h2 a:hover span {
  color: #007ad6;
}
.card .actions .follow-btn button {
  color: inherit;
  font: inherit;
  font-weight: bold;
  background-color: #ffd01a;
  width: 100%;
  border: none;
  padding: 1rem;
  outline: none;
  box-sizing: border-box;
  border-radius: 1.5rem/50%;
  transition: background-color 100ms ease-in-out,
    transform 200ms cubic-bezier(0.18, 0.89, 0.32, 1.28);
}
.card .actions .follow-btn button:hover {
  background-color: #efb10a;
  transform: scale(1.1);
}
.card .actions .follow-btn button:active {
  background-color: #e8a200;
  transform: scale(1);
}
.card .desc {
  text-align: justify;
  padding: 0 2rem 2.5rem;
  order: 100;
}
@media screen and (min-width: 1280px) and (max-width: 1440px) {
  .card {
    height: 303px !important;
  }
  /* Styles for Desktops */
}
</style>
