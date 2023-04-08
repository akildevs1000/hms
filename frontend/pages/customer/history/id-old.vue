<template>
  <div>
    <v-row>
      <v-col md="3">
        <div class="profile-view">
          <div class="cover-image" style="background-color: #5fafa3">
            {{ customer.image }}
          </div>
          <div class="profile-view-header">
            <div class="avatar-container">
              <img
                alt="Avatar"
                class="avatar"
                :src="customer.image || previewImage"
              />
            </div>
            <h1 class="name">
              {{ customer.title || "---" }} {{ customer.full_name || "---" }}
            </h1>
            <div class=""></div>
          </div>

          <div class="profile-view-body">
            <v-tabs v-model="tab" align-with-title>
              <v-tabs-slider color="yellow"></v-tabs-slider>
              <v-tab v-for="item in items" :key="item">
                {{ item }}
              </v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <div class="contact-info-container">
                      <ul class="contact-info">
                        <li>
                          <span class="label">Email:</span>
                          <a href="mailto:john.doe@example.com">
                            {{ customer.email || "---" }}
                          </a>
                        </li>
                        <li>
                          <span class="label">Phone:</span>
                          {{ customer.contact_no || "---" }}
                        </li>
                        <li>
                          <span class="label">Whatsapp:</span>
                          {{ customer.whatsapp || "---" }}
                        </li>
                        <li>
                          <span class="label">Address:</span>
                          {{ customer.address || "---" }}
                        </li>
                      </ul>
                    </div>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <div class="contact-info-container">
                      <ul class="contact-info">
                        <li>
                          <span class="label">Nationality:</span>
                          {{ customer.nationality || "---" }}
                        </li>

                        <li>
                          <span class="label">Car No:</span>
                          {{ customer.car_no || "---" }}
                        </li>

                        <li>
                          <span class="label">Date of Birth:</span>
                          {{ customer.dob || "---" }}
                        </li>

                        <li v-if="customer.document">
                          <span class="label">
                            {{
                              (customer &&
                                customer.id_card_type &&
                                customer.id_card_type.name) ||
                              "---"
                            }}
                          </span>
                          <v-btn
                            x-small
                            dark
                            class="primary pt-4 pb-4"
                            @click="preview(customer.document)"
                          >
                            Preview
                            <v-icon right dark>mdi-file</v-icon>
                          </v-btn>
                        </li>
                      </ul>
                    </div>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </div>
        </div>
      </v-col>

      <v-col md="9">
        <v-card elevation="0">
          <v-toolbar color="cyan" dark flat>
            <v-app-bar-nav-icon></v-app-bar-nav-icon>
            <v-toolbar-title>Customer Details</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon>
              <v-icon>mdi-magnify</v-icon>
            </v-btn>
            <v-btn icon>
              <v-icon>mdi-dots-vertical</v-icon>
            </v-btn>
            <template v-slot:extension>
              <v-tabs v-model="tab1" align-with-title>
                <v-tabs-slider color="yellow"></v-tabs-slider>
                <v-tab v-for="item in itemsCustomer" :key="item">
                  {{ item }}
                </v-tab>
              </v-tabs>
            </template>
          </v-toolbar>
          <v-tabs-items v-model="tab1">
            <v-tab-item class="px-3">
              <v-row>
                <div class="col-xl-3 col-lg-6 text-uppercase">
                  <div class="card px-2 available">
                    <div class="card-statistic-3">
                      <div class="card-icon card-icon-large">
                        <i class="fas fa-door-open"></i>
                      </div>
                      <div class="card-content">
                        <h4 class="card-title text-capitalize">
                          Total Revenue
                        </h4>
                        <span class="data-1"> Rs {{ revenue }} </span>
                        <p class="mb-0 text-sm">
                          <span class="mr-2">
                            <v-icon dark small>mdi-arrow-right</v-icon>
                          </span>
                          <a class="text-nowrap text-white" target="_blank">
                            <span class="text-nowrap">View Report</span>
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-lg-6 text-uppercase">
                  <div class="card px-2 available">
                    <div class="card-statistic-3">
                      <div class="card-icon card-icon-large">
                        <i class="fas fa-door-open"></i>
                      </div>
                      <div class="card-content">
                        <h4 class="card-title text-capitalize">
                          Total City Ledger
                        </h4>
                        <span class="data-1"> Rs {{ city_ledger }} </span>
                        <p class="mb-0 text-sm">
                          <span class="mr-2">
                            <v-icon dark small>mdi-arrow-right</v-icon>
                          </span>
                          <a class="text-nowrap text-white" target="_blank">
                            <span class="text-nowrap">View Report</span>
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-lg-6 text-uppercase">
                  <div class="card px-2 available">
                    <div class="card-statistic-3">
                      <div class="card-icon card-icon-large">
                        <i class="fas fa-door-open"></i>
                      </div>
                      <div class="card-content">
                        <h4 class="card-title text-capitalize">
                          Number of Visit
                        </h4>
                        <span class="data-1">
                          {{ bookings.length || "---" }}
                        </span>
                        <p class="mb-0 text-sm">
                          <span class="mr-2">
                            <v-icon dark small>mdi-arrow-right</v-icon>
                          </span>
                          <a class="text-nowrap text-white" target="_blank">
                            <span class="text-nowrap">View Report</span>
                          </a>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </v-row>

              <table class="responsive-table">
                <thead>
                  <tr>
                    <th
                      scope="col"
                      v-for="(item, index) in headers"
                      :key="index"
                    >
                      {{ item.text }}
                    </th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-for="(item, index) in bookings" :key="index">
                    <th scope="row">
                      <b>{{ ++index }}</b>
                    </th>
                    <td scope="row">{{ item.type || "---" }}</td>
                    <td data-title="Released">{{ item.source || "---" }}</td>
                    <td data-title="Studio">{{ item.rooms || "---" }}</td>
                    <td data-title="Worldwide Gross" data-type="currency">
                      {{ item.booking_date || "---" }}
                    </td>
                    <td data-title="Domestic Gross" data-type="currency">
                      {{ item.check_in || "---" }}
                    </td>
                    <td data-title="International Gross" data-type="currency">
                      {{ item.check_out || "---" }}
                    </td>
                    <td data-title="Budget" data-type="currency">
                      {{ item.total_price || "---" }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </v-tab-item>
            <v-tab-item class="px-3">
              <v-card flat>
                <v-row>
                  <v-col cols="12">
                    <v-divider></v-divider>

                    <v-row>
                      <v-col md="3">
                        <v-col md="12" cols="12">
                          <v-img
                            @click="onpick_attachment"
                            style="
                              width: 150px;
                              height: 150px;
                              margin: 0 auto;
                              border-radius: 50%;
                            "
                            :src="showImage"
                          ></v-img>
                          <input
                            required
                            type="file"
                            @change="attachment"
                            style="display: none"
                            accept="image/*"
                            ref="attachment_input"
                          />
                          <span
                            v-if="errors && errors.image"
                            class="red--text mt-2"
                          >
                            {{ errors.image[0] }}</span
                          >
                          <div class="mt-2 ml-4" v-if="customer.document">
                            <v-btn
                              small
                              dark
                              class="pridmary lg-pt-4 lg-pb-4 doc-btn"
                              @click="preview(Customer.document)"
                            >
                              Preview
                              <v-icon right dark>mdi-file</v-icon>
                            </v-btn>
                          </div>
                          <div class="mt-2 ml-2" v-else>
                            <v-btn
                              small
                              dark
                              class="primary pt-4 pb-4"
                              @click="add_document()"
                            >
                              <small>Document</small>
                              <v-icon right dark>mdi-plus</v-icon>
                            </v-btn>
                          </div>
                        </v-col>
                      </v-col>
                      <v-col md="9">
                        <v-row>
                          <v-col md="2" cols="12" sm="12">
                            <v-select
                              v-model="customer.title"
                              :items="titleItems"
                              label="Tittle"
                              dense
                              item-text="name"
                              item-value="name"
                              :hide-details="errors && !errors.title"
                              :error="errors && errors.title"
                              :error-messages="
                                errors && errors.title ? errors.title[0] : ''
                              "
                              outlined
                            ></v-select>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="First Name"
                              outlined
                              type="text"
                              v-model="customer.first_name"
                              :hide-details="errors && !errors.first_name"
                              :error="errors && errors.first_name"
                              :error-messages="
                                errors && errors.first_name
                                  ? errors.first_name[0]
                                  : ''
                              "
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              :hide-details="true"
                              outlined
                              label="Last Name"
                              type="text"
                              v-model="customer.last_name"
                            ></v-text-field>
                          </v-col>
                          <v-col md="4" cols="12" sm="12">
                            <v-menu
                              v-model="customer.dob_menu"
                              :close-on-content-click="false"
                              :nudge-right="40"
                              transition="scale-transition"
                              offset-y
                              min-width="auto"
                            >
                              <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                  label="DOB"
                                  v-model="customer.dob"
                                  placeholder="date"
                                  readonly
                                  v-on="on"
                                  v-bind="attrs"
                                  :hide-details="true"
                                  dense
                                  outlined
                                ></v-text-field>
                              </template>
                              <v-date-picker
                                v-model="customer.dob"
                                @input="customer.dob_menu = false"
                              ></v-date-picker>
                            </v-menu>
                          </v-col>
                          <v-col md="4" cols="12" sm="12">
                            <v-text-field
                              label="First Name"
                              dense
                              outlined
                              type="number"
                              v-model="customer.contact_no"
                              :hide-details="errors && !errors.contact_no"
                              :error="errors && errors.contact_no"
                              :error-messages="
                                errors && errors.contact_no
                                  ? errors.contact_no[0]
                                  : ''
                              "
                            ></v-text-field>
                          </v-col>
                          <v-col md="4" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="Whatsapp"
                              outlined
                              type="number"
                              v-model="customer.whatsapp"
                              :hide-details="errors && !errors.whatsapp"
                              :error="errors && errors.whatsapp"
                              :error-messages="
                                errors && errors.whatsapp
                                  ? errors.whatsapp[0]
                                  : ''
                              "
                            ></v-text-field>
                          </v-col>
                          <v-col md="4" cols="12" sm="12">
                            <v-text-field
                              dense
                              outlined
                              label="Email"
                              type="email"
                              v-model="customer.email"
                              :hide-details="errors && !errors.email"
                              :error="errors && errors.email"
                              :error-messages="
                                errors && errors.email ? errors.email[0] : ''
                              "
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-select
                              v-model="customer.nationality"
                              label="Nationality"
                              :items="countryList"
                              item-text="name"
                              item-value="name"
                              :hide-details="errors && !errors.nationality"
                              :error="errors && errors.nationality"
                              :error-messages="
                                errors && errors.nationality
                                  ? errors.nationality[0]
                                  : ''
                              "
                              dense
                              outlined
                            ></v-select>
                          </v-col>
                          <v-col md="3" sm="12" cols="12" dense>
                            <v-select
                              v-model="id_card_type_id"
                              :items="idCards"
                              label="ID Card Type"
                              dense
                              outlined
                              item-text="name"
                              item-value="id"
                              :hide-details="!errors.id_card_type_id"
                              :error="errors.id_card_type_id"
                              :error-messages="
                                errors && errors.id_card_type_id
                                  ? errors.id_card_type_id[0]
                                  : ''
                              "
                            ></v-select>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="Selected ID Card Number"
                              outlined
                              type="text"
                              v-model="customer.id_card_no"
                              :hide-details="errors && !errors.id_card_no"
                              :error="errors && errors.id_card_no"
                              :error-messages="
                                errors && errors.id_card_no
                                  ? errors.id_card_no[0]
                                  : ''
                              "
                            ></v-text-field>
                          </v-col>

                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              label="Car Number"
                              dense
                              outlined
                              :hide-details="true"
                              type="text"
                              v-model="customer.car_no"
                            ></v-text-field>
                          </v-col>

                          <v-col md="6" cols="12" sm="12">
                            <div>
                              <v-file-input
                                v-model="document"
                                label="Document"
                                color="primary"
                                counter
                                placeholder="Select your files"
                                prepend-icon="mdi-paperclip"
                                outlined
                                :show-size="1000"
                                style="margin-top: 150px"
                              >
                                <template v-slot:selection="{ index, text }">
                                  <v-chip
                                    v-if="index < 2"
                                    color="primary"
                                    dark
                                    label
                                    small
                                  >
                                    {{ text }}
                                  </v-chip>
                                  <span
                                    v-else-if="index === 2"
                                    class="text-overline grey--text text--darken-3 mx-2"
                                  >
                                    +{{ document.length - 2 }} File(s)
                                  </span>
                                </template>
                              </v-file-input>
                            </div>
                          </v-col>

                          <v-col cols="12">
                            <label class="col-form-label">Address </label>
                            <v-textarea
                              rows="3"
                              v-model="customer.address"
                              outlined
                              :hide-details="true"
                            ></v-textarea>
                          </v-col>

                          <v-col cols="12">
                            <hr />
                            <div class="text-left">
                              <v-btn
                                dark
                                small
                                :loading="false"
                                color="primary"
                                @click="store_customer"
                              >
                                Submit
                              </v-btn>
                            </div>
                          </v-col>
                        </v-row>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card>
            </v-tab-item>
          </v-tabs-items>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    tab1: null,
    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",

    tab: null,
    items: ["Contact", "Personal"],
    itemsCustomer: ["History", "Customer"],
    headers: [
      {
        text: "#",
      },
      {
        text: "Type",
      },
      {
        text: "Source",
      },
      {
        text: "Rooms",
      },
      {
        text: "Booking Date",
      },
      {
        text: "Check In",
      },
      {
        text: "Check Out",
      },
      {
        text: "Total Price",
      },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Customer",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    document: null,
    response: "",
    customer: [],
    payments: [],
    bookings: [],
    revenue: 0,
    city_ledger: 0,
    errors: [],
    idCards: [],
    // idCards: [
    //   { id: -1, name: "select" },
    //   { id: 1, name: "National Identity Card" },
    //   { id: 2, name: "Voter Id Card" },
    //   { id: 3, name: "Pan Card" },
    //   { id: 4, name: "Driving License" }
    // ],
    id_card_type_id: 2,
    countryList: [],
    customer: {
      nationality: "",
      title: "",
      first_name: "",
      last_name: "",
      contact_no: "",
      email: "",
      id_card_type_id: 2,
      id_card_no: "",
      car_no: "",
      no_of_adult: "",
      no_of_child: "",
      no_of_baby: "",
      address: "",
      company_id: "",

      dob_menu: false,
      dob: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
    },

    titleItems: [
      { id: 1, name: "Mr" },
      { id: 2, name: "Mrs" },
      { id: 3, name: "Miss" },
      { id: 4, name: "Ms" },
      { id: 5, name: "Dr" },
    ],

    imgPath: "",
    image: "",

    upload: {
      name: "",
    },

    previewImage: null,
  }),

  computed: {
    showImage() {
      if (!this.customer.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },
  },
  created() {
    this.loading = true;
    this.id_card_type_id = this.customer.id_card_type_id;
    this.getData();
    this.get_id_cards();
    this.get_countries();
  },
  mounted() {},

  methods: {
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      // this.customer.image = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },

    getDate(dataTime) {
      return dataTime;
      // return new Date(dataTime.toDateString());
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    preview(file) {
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", file);
      document.body.appendChild(element);
      element.click();
      // document.body.removeChild(element);
    },

    getData() {
      let id = this.$route.params.id;
      this.$axios.get(`get_customer_history/${id}`).then(({ data }) => {
        this.customer = data.data;
        this.bookings = data.data.bookings;
        this.revenue = data.revenue;
        this.city_ledger = data.city_ledger;
        this.payments = data.booking.payments;
        this.bookedRooms = data.booking.booked_rooms;
        this.loading = false;
      });
    },

    store_customer() {
      let payload = {
        ...this.customer,
        document: document,
        company_id: this.$auth.user.company.id,
        id_card_type_id: this.id_card_type_id,
      };
      // console.log(payload);
      // return;
      this.$axios
        .post("/customer_update", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert("oops!", "Some fields are missing or invalid", "error");
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.getData();
            this.alert("Success!", "Successfully customer updated", "success");
          }
        })
        .catch((e) => console.log(e));
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },
    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>

<style scoped src="@/assets/custom.css"></style>
