<template>
  <div>
    <v-dialog v-model="imgView">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>ID</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-container>
          <ImagePreview :docObj="documentObj"></ImagePreview>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row class="m-0 p-0 mt-1">
      <v-col md="12">
        <v-tabs right v-model="activeTab" background-color="primary" dark>
          <!-- <div class="py-3" style="background-color: #1259a7">
            <span class="mx-2">Check In</span>
          </div> -->
          <!-- <v-spacer></v-spacer> -->
          <v-tab v-if="BookingData.group_name">
            <v-icon> mdi mdi-account </v-icon>
          </v-tab>
          <v-tab>
            <v-icon> mdi mdi-account-tie </v-icon>
          </v-tab>
          <v-tab v-if="customer.id > 0">
            <v-icon>mdi-bed </v-icon>
          </v-tab>
          <v-tab v-if="customer.id > 0">
            <v-icon> mdi mdi-clipboard-text-clock </v-icon>
          </v-tab>
          <v-tabs-slider color="#1259a7"></v-tabs-slider>
          <v-tab-item v-if="BookingData.group_name">
            <v-card flat>
              <v-card-text>
                <v-row>
                  <v-col md="4" cols="12">
                    <v-row>
                      <v-col md="12" class="text-center">
                        <v-img
                          @click="onpick_attachment"
                          style="
                            max-width: 150px;
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
                          v-if="
                            errorsForSubCustomer && errorsForSubCustomer.image
                          "
                          class="red--text mt-2"
                        >
                          {{ errors.image[0] }}</span
                        >
                        <div class="mt-2" v-if="BookingData.document">
                          <v-btn
                            small
                            dark
                            class="primary ipad-preview lg-pt-4 lg-pb-4 doc-btn mx-auto"
                            @click="preview(BookingData.document)"
                          >
                            ID
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
                    </v-row>
                  </v-col>
                  <v-col md="8" cols="12">
                    <v-row>
                      <v-col md="4" dense>
                        <v-text-field
                          label="Reservation Number"
                          v-model="BookingData.reservation_no"
                          :items="['Company', 'Regular', 'Corporate']"
                          dense
                          readonly
                          item-text="name"
                          item-value="id"
                          outlined
                          :hide-details="true"
                        ></v-text-field>
                      </v-col>
                      <v-col md="4" dense>
                        <v-text-field
                          v-if="BookingData.group_name"
                          label="Group Name"
                          v-model="BookingData.group_name"
                          dense
                          readonly
                          outlined
                          :hide-details="true"
                        ></v-text-field>
                      </v-col>
                      <v-col md="4" dense>
                        <v-text-field
                          label="Type"
                          v-model="BookingData.customer_type"
                          dense
                          outlined
                          readonly
                          :hide-details="true"
                        ></v-text-field>
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <v-select
                          v-model="guest.title"
                          :items="titleItems"
                          label="Tittle *"
                          dense
                          item-text="name"
                          item-value="name"
                          :hide-details="
                            errorsForSubCustomer && !errorsForSubCustomer.title
                          "
                          :error-messages="
                            errorsForSubCustomer && errorsForSubCustomer.title
                              ? errorsForSubCustomer.title[0]
                              : ''
                          "
                          outlined
                        ></v-select>
                      </v-col>
                      <v-col md="5" cols="12" sm="12">
                        <v-text-field
                          label="First Name *"
                          dense
                          outlined
                          type="text"
                          v-model="guest.first_name"
                          :hide-details="
                            errorsForSubCustomer &&
                            !errorsForSubCustomer.first_name
                          "
                          :error-messages="
                            errorsForSubCustomer &&
                            errorsForSubCustomer.first_name
                              ? errorsForSubCustomer.first_name[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col md="4" cols="12" sm="12">
                        <v-text-field
                          label="Last Name"
                          dense
                          outlined
                          type="text"
                          v-model="guest.last_name"
                          :hide-details="
                            errorsForSubCustomer &&
                            !errorsForSubCustomer.last_name
                          "
                          :error-messages="
                            errorsForSubCustomer &&
                            errorsForSubCustomer.last_name
                              ? errorsForSubCustomer.last_name[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col md="4" cols="12" sm="12">
                        <v-text-field
                          dense
                          label="Contact No *"
                          outlined
                          type="number"
                          v-model="guest.contact_no"
                          :hide-details="
                            errorsForSubCustomer &&
                            !errorsForSubCustomer.contact_no
                          "
                          :error-messages="
                            errorsForSubCustomer &&
                            errorsForSubCustomer.contact_no
                              ? errorsForSubCustomer.contact_no[0]
                              : ''
                          "
                          @keyup="mergeContact"
                        ></v-text-field>
                      </v-col>
                      <v-col md="4" cols="12" sm="12">
                        <v-text-field
                          dense
                          label="Whatsapp No"
                          outlined
                          type="number"
                          v-model="guest.whatsapp"
                          :hide-details="
                            errorsForSubCustomer &&
                            !errorsForSubCustomer.whatsapp
                          "
                          :error-messages="
                            errorsForSubCustomer &&
                            errorsForSubCustomer.whatsapp
                              ? errorsForSubCustomer.whatsapp[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>

                      <v-col md="4" cols="12" sm="12">
                        <v-text-field
                          dense
                          label="Email *"
                          outlined
                          type="email"
                          v-model="guest.email"
                          :hide-details="
                            errorsForSubCustomer && !errorsForSubCustomer.email
                          "
                          :error-messages="
                            errorsForSubCustomer && errorsForSubCustomer.email
                              ? errorsForSubCustomer.email[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col md="4" cols="12" sm="12">
                    <v-select
                      v-model="guest.nationality"
                      :items="countryList"
                      label="Nationality"
                      item-text="name"
                      item-value="name"
                      :hide-details="
                        errorsForSubCustomer &&
                        !errorsForSubCustomer.nationality
                      "
                      :error-messages="
                        errorsForSubCustomer && errorsForSubCustomer.nationality
                          ? errorsForSubCustomer.nationality[0]
                          : ''
                      "
                      dense
                      outlined
                    ></v-select>
                  </v-col>
                  <v-col md="4" cols="12" sm="12">
                    <v-menu
                      v-model="guest.dob_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="guest.dob"
                          readonly
                          label="DOB  "
                          v-on="on"
                          v-bind="attrs"
                          :hide-details="
                            errorsForSubCustomer && !errorsForSubCustomer.dob
                          "
                          :error-messages="
                            errorsForSubCustomer && errorsForSubCustomer.dob
                              ? errorsForSubCustomer.dob[0]
                              : ''
                          "
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        no-title
                        v-model="guest.dob"
                        @input="guest.dob_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>

                  <v-col md="6" cols="12" sm="12">
                    <v-textarea
                      rows="3"
                      label="Address"
                      v-model="guest.address"
                      outlined
                      :hide-details="
                        errorsForSubCustomer && !errorsForSubCustomer.address
                      "
                      :error-messages="
                        errorsForSubCustomer && errorsForSubCustomer.address
                          ? errorsForSubCustomer.address[0]
                          : ''
                      "
                    ></v-textarea>
                  </v-col>
                  <v-col md="6">
                    <v-textarea
                      rows="3"
                      label="Customer Request"
                      v-model="room.request"
                      :hide-details="true"
                      outlined
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" class="text-right">
                    <v-btn small @click="nextTab" color="primary">Next</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-dialog
              v-model="GRCDialog"
              persistent
              :width="900"
              class="checkin-models"
            >
              <v-card>
                <v-toolbar
                  class="rounded-md"
                  color="background"
                  dense
                  flat
                  dark
                >
                  <span>{{ "GRC" }}</span>
                  <v-spacer></v-spacer>
                  <v-icon dark class="pa-0" @click="GRCDialog = false"
                    >mdi mdi-close</v-icon
                  >
                </v-toolbar>
                <v-card-text>
                  <BookingGRC :bookingId="BookingData.id"> </BookingGRC>
                </v-card-text>
                <v-container></v-container>
                <v-card-actions> </v-card-actions>
              </v-card>
            </v-dialog>
            <v-card flat>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col md="3" cols="12" class="text-center">
                      <v-row>
                        <v-col cols="12">
                          <v-img
                            class="my-2"
                            style="max-height: 175px"
                            :src="
                              (customerDocs && customerDocs.captured_photo) ||
                              `https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png`
                            "
                          ></v-img>
                          <v-img
                            class="my-2"
                            :src="
                              (customerDocs && customerDocs.id_frontend_side) ||
                              `https://th.bing.com/th/id/OIP.ACtFyuYiJkcGt3vHXzZbvgHaE9?w=290&h=194&c=7&r=0&o=5&pid=1.7`
                            "
                          ></v-img>
                          <v-img
                            class="my-2"
                            :src="
                              (customerDocs && customerDocs.id_backend_side) ||
                              `https://th.bing.com/th/id/OIP.ACtFyuYiJkcGt3vHXzZbvgHaE9?w=290&h=194&c=7&r=0&o=5&pid=1.7`
                            "
                          ></v-img>
                        </v-col>

                        <v-col cols="6" class="pa-0 ma-0">
                          <v-btn
                            small
                            color="primary"
                            dark
                            @click="GRCDialog = true"
                          >
                            <v-icon>mdi-printer-outline</v-icon> GRC
                          </v-btn>
                        </v-col>
                        <v-col cols="6" class="pa-0 ma-0">
                          <v-btn
                            small
                            color="primary"
                            dark
                            @click="downloadCustomerAttachments"
                          >
                            <v-icon>mdi-download-outline</v-icon>
                            <v-icon>mdi-account-tie</v-icon>
                          </v-btn>
                        </v-col>
                        <v-col cols="12">
                          <BookingIDPreview
                            :BookingId="BookingData.id"
                            @getCustomerDocs="(e) => (customerDocs = e)"
                          />
                        </v-col>
                      </v-row>
                    </v-col>
                    <v-col md="9" cols="12">
                      <v-row>
                        <v-col md="4" cols="12" sm="12">
                          <v-select
                            v-model="customer.title"
                            :items="titleItems"
                            label="Tittle *"
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
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            label="First Name *"
                            dense
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
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            label="Last Name"
                            dense
                            :hide-details="true"
                            outlined
                            type="text"
                            v-model="customer.last_name"
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Contact No *"
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
                            @keyup="mergeContact"
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Whatsapp No"
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
                            label="Email *"
                            outlined
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
                          <!-- <pre> {{countries}}</pre> -->
                          <v-autocomplete
                            :items="countries"
                            item-text="name"
                            item-value="name"
                            label="Country"
                            v-model="customer.country"
                            outlined
                            :hide-details="true"
                            dense
                            @change="getStates(customer.country)"
                          ></v-autocomplete>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-autocomplete
                            :items="states"
                            item-text="name"
                            item-value="name"
                            label="State"
                            v-model="customer.state"
                            outlined
                            :hide-details="true"
                            dense
                            @change="getCities"
                          ></v-autocomplete>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-autocomplete
                            :items="cities"
                            label="City"
                            v-model="customer.city"
                            outlined
                            :hide-details="true"
                            dense
                          ></v-autocomplete>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-text-field
                            label="Zip Code"
                            v-model="customer.zip_code"
                            outlined
                            :hide-details="true"
                            dense
                          ></v-text-field>
                        </v-col>
                        <!-- <v-col md="5" dense>
                        <v-text-field
                          label="Reservation Number"
                          v-model="BookingData.reservation_no"
                          :items="['Company', 'Regular', 'Corporate']"
                          dense
                          readonly
                          item-text="name"
                          item-value="id"
                          outlined
                          :hide-details="true"
                        ></v-text-field>
                      </v-col>
                      <v-col md="2" dense> </v-col>
                      <v-col md="5" dense>
                        <v-text-field
                          label="Type"
                          v-model="BookingData.customer_type"
                          dense
                          outlined
                          disabled
                          :hide-details="true"
                        ></v-text-field>
                      </v-col> -->

                        <v-col md="3" cols="12" sm="12">
                          <v-select
                            v-model="customer.nationality"
                            :items="countryList"
                            label="Nationality"
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
                        <v-col md="3" cols="12" sm="12">
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
                                v-model="customer.dob"
                                readonly
                                label="DOB"
                                v-on="on"
                                v-bind="attrs"
                                :hide-details="true"
                                dense
                                outlined
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              no-title
                              v-model="customer.dob"
                              @input="customer.dob_menu = false"
                            ></v-date-picker>
                          </v-menu>
                        </v-col>
                        <v-col md="3">
                          <v-select
                            label="Purpose"
                            v-model="room.purpose"
                            :items="purposes"
                            dense
                            :hide-details="true"
                            outlined
                          ></v-select>
                        </v-col>
                        <v-col md="3" cols="12" sm="12">
                          <v-text-field
                            label="Car Number *"
                            dense
                            outlined
                            type="text"
                            v-model="customer.car_no"
                            :hide-details="errors && !errors.car_no"
                            :error="errors && errors.car_no"
                            :error-messages="
                              errors && errors.car_no ? errors.car_no[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" sm="12" cols="12" dense>
                          <v-autocomplete
                            v-model="checkIn.id_card_type_id"
                            :items="idCards"
                            dense
                            label="ID Card Type"
                            outlined
                            item-text="name"
                            item-value="id"
                            :hide-details="errors && !errors.id_card_type_id"
                            :error="errors && errors.id_card_type_id"
                            :error-messages="
                              errors && errors.id_card_type_id
                                ? errors.id_card_type_id[0]
                                : ''
                            "
                          ></v-autocomplete>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="ID Card"
                            outlined
                            type="text"
                            v-model="checkIn.id_card_no"
                            :hide-details="errors && !errors.id_card_no"
                            :error="errors && errors.id_card_no"
                            :error-messages="
                              errors && errors.id_card_no
                                ? errors.id_card_no[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12" sm="12">
                          <v-menu
                            v-model="checkIn.exp_menu"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                          >
                            <template v-slot:activator="{ on, attrs }">
                              <v-text-field
                                v-model="checkIn.exp"
                                readonly
                                label="Expired"
                                v-on="on"
                                v-bind="attrs"
                                :hide-details="true"
                                dense
                                outlined
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              no-title
                              v-model="checkIn.exp"
                              @input="checkIn.exp_menu = false"
                            ></v-date-picker>
                          </v-menu>
                        </v-col>

                        <v-col md="3" cols="12" sm="12">
                          <v-text-field
                            dense
                            label="Business Source"
                            outlined
                            type="text"
                            v-model="customer.customer_type"
                            :hide-details="errors && !errors.customer_type"
                            :error="errors && errors.customer_type"
                            :error-messages="
                              errors && errors.customer_type
                                ? errors.customer_type[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="3" sm="12" cols="12">
                          <v-text-field
                            readonly
                            dense
                            label="Business Type"
                            outlined
                            type="text"
                            v-model="BookingData.type"
                            :hide-details="errors && !errors.type"
                            :error="errors && errors.type"
                            :error-messages="
                              errors && errors.type ? errors.type[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="3" sm="12" cols="12">
                          <v-text-field
                            readonly
                            dense
                            label="Reference Number"
                            outlined
                            type="text"
                            v-model="BookingData.reference_no"
                            :hide-details="errors && !errors.reference_no"
                            :error="errors && errors.reference_no"
                            :error-messages="
                              errors && errors.reference_no
                                ? errors.reference_no[0]
                                : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="3" sm="12" cols="12">
                          <v-text-field
                            readonly
                            dense
                            label="Paid By"
                            outlined
                            type="text"
                            v-model="BookingData.paid_by"
                            :hide-details="errors && !errors.paid_by"
                            :error="errors && errors.paid_by"
                            :error-messages="
                              errors && errors.paid_by ? errors.paid_by[0] : ''
                            "
                          ></v-text-field>
                        </v-col>
                        <v-col md="12">
                          <v-textarea
                            rows="3"
                            label="Notes"
                            v-model="room.request"
                            :hide-details="true"
                            outlined
                          ></v-textarea>
                        </v-col>
                        <v-col cols="12" class="text-right">
                          <v-btn
                            :disabled="!customerDocs"
                            small
                            @click="nextTab"
                            color="primary"
                            >Next</v-btn
                          >
                        </v-col>
                      </v-row>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <v-tabs
                  color="primary"
                  v-model="activeSummaryTab"
                  :vertical="vertical"
                  background-color="primary"
                  dark
                  show-arrows
                >
                  <v-spacer></v-spacer>
                  <v-tab active-class="active-link">
                    <v-icon> mdi mdi-list-box-outline </v-icon>
                  </v-tab>
                  <v-tab
                    class="p-0 m-0"
                    active-class="active-link"
                    style="min-width: 10px !important"
                    v-for="(item, index) in selectedRooms"
                    :key="index"
                  >
                    <small>
                      {{ item && item.room_no }}
                    </small>
                  </v-tab>
                  <v-tabs-slider color="#1259a7"></v-tabs-slider>
                  <!-- room summary -->
                  <v-tab-item>
                    <v-card flat>
                      <v-divider class="px-5 py-0"></v-divider>
                      <section>
                        <div class="input-group input-group-sm px-0 py-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Name
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.title || "---" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Contact
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.contact_no || "---" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Check In
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.check_in_date || "---" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Check Out
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.check_out_date || "---" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Days
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.total_days || "---" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm mb-2 px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm "
                          >
                            No. Rooms
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.rooms || "---" }}
                          </div>
                        </div>
                      </section>

                      <!-- <v-divider class="px-5 py-0"></v-divider> -->
                      <section class="payment-section pt-0">
                        <div class="input-group input-group-sm px-0 mt-3">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Room Price
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.total_price || "0" }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Total
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ BookingData.total_price }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Advance Payment
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ new_payment }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-0 mb-0">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            <strong>Balance Amount</strong>
                          </span>
                          <div
                            type="text"
                            class="form-control red--text"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            <strong>{{ BookingData.total_price - new_payment }}</strong>
                          </div>
                        </div>
                        <v-card-actions class="pl-0 pr-2">
                          <v-row>
                            <v-col class="text-rights">
                              <v-btn
                              :disabled="!customerDocs"
                                @click="advanceDialog = true"
                                color="primary"
                                >Pay</v-btn
                              >

                              <v-btn
                              :disabled="!customerDocs"
                                class="white--text"
                                style="background-color: #5fafa3"
                                @click="storeSubCustomer"
                                :loading="loading"
                                >CheckIn</v-btn
                              >
                            </v-col>
                          </v-row>
                        </v-card-actions>
                      </section>
                    </v-card>
                  </v-tab-item>
                  <!-- end room summary -->

                  <v-tab-item
                    v-for="(item, index) in selectedRooms"
                    :key="index"
                  >
                    <v-card flat>
                      <div
                        class="px-5 pt-2 d-flex justify-space-between"
                        style="font-size: 16px; color: #aaaaaa"
                      >
                        <span> Room - {{ item.room_no }}</span>
                        <span> {{ item.room_type }}</span>
                      </div>
                      <v-divider></v-divider>
                      <section class="payment-section">
                        <div
                          class="input-group input-group-sm px-5 pt-2 text-center"
                        >
                          <v-card class="pa-2" style="width: 25%" outlined tile>
                            <div class="d-flex justify-space-between">
                              <span
                                class="pa-0 m-0"
                                style="width: 33.33%"
                                outlined
                              >
                                {{ item.no_of_adult }}|
                              </span>
                              <span
                                class="pa-0 m-0"
                                style="width: 33.33%"
                                outlined
                              >
                                {{ item.no_of_child }} |
                              </span>
                              <span
                                class="pa-0 m-0"
                                style="width: 33.33%"
                                outlined
                              >
                                {{ item.no_of_baby }}
                              </span>
                            </div>
                          </v-card>
                          <v-card class="pa-2" style="width: 25%" outlined tile>
                            {{ getMealSeparate(item.meal)[0] }}
                          </v-card>
                          <v-card class="pa-2" style="width: 25%" outlined tile>
                            {{ getMealSeparate(item.meal)[1] }}
                          </v-card>
                          <v-card class="pa-2" style="width: 25%" outlined tile>
                            {{ getMealSeparate(item.meal)[2] }}
                          </v-card>
                        </div>
                        <div class="input-group input-group-sm px-5 pt-2">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Amount
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ item.price }}
                          </div>
                        </div>

                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Discount
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.room_discount) }}
                          </div>
                        </div>

                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            After Dis.
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.after_discount) }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            GST
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.room_tax) }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            T.R Rent
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.total_with_tax) }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Adult Food
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.tot_adult_food) }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Child Food
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.tot_child_food) }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Total Food
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{
                              convert_decimal(
                                item.tot_adult_food + item.tot_child_food
                              )
                            }}
                          </div>
                        </div>
                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Grand Total
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ convert_decimal(item.total) }}
                          </div>
                        </div>

                        <div class="input-group input-group-sm px-5">
                          <span
                            class="input-group-text"
                            id="inputGroup-sizing-sm"
                          >
                            Discount Reason
                          </span>
                          <div
                            type="text"
                            class="form-control"
                            aria-label="Sizing example input"
                            aria-describedby="inputGroup-sizing-sm"
                            disabled
                          >
                            {{ item.discount_reason || "---" }}
                          </div>
                        </div>
                      </section>
                    </v-card>
                  </v-tab-item>
                </v-tabs>
              </v-card-text>
            </v-card>
          </v-tab-item>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <History :customerId="customer.id"></History>
              </v-card-text>
            </v-card>
          </v-tab-item>
        </v-tabs>
      </v-col>
    </v-row>

    <!---------------------------------------------------------------->

    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="error--text">*</span></label
              >
              <v-text-field
                dense
                outlined
                type="text"
                v-model="search.mobile"
                :hide-details="true"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="get_customer" :loading="checkLoader">
            Search
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="advanceDialog" width="600">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Payment</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="advanceDialog = false">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-card-text>
          <v-row class="px-5 mt-2">
            <div class="input-group input-group-sm px-1">
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="height: 44px; width: 215px"
              >
                <v-select
                  v-model="room.payment_mode_id"
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
                  :outlined="false"
                  cache-items
                  dense
                  flat
                  hide-no-data
                  solo
                  elevation="0"
                  background-color="#E9ECEF"
                  :disabled="room.paid_by == '2' ? true : false"
                  :hide-details="errors && !errors.payment_mode_id"
                  :error="errors && errors.payment_mode_id"
                  :error-messages="
                    errors && errors.payment_mode_id
                      ? errors.payment_mode_id[0]
                      : ''
                  "
                  style="font-size: 13px"
                ></v-select>
              </span>
              <input
                type="number"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 44px"
                v-model="new_payment"
              />
            </div>
            <div
              class="input-group input-group-sm px-1"
              v-if="room.payment_mode_id != 1"
            >
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="height: 44px; width: 215px"
              >
                Reference No
              </span>
              <input
                v-model="reference_number"
                type="text"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="
                  height: 44px;
                  text-align: left !important;
                  text-transform: lowercase !important ;
                "
              />
            </div>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="advanceDialog = false">
            Pay
            <!-- <v-icon right dark>mdi mdi-magnify</v-icon> -->
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
export default {
  props: ["BookingData"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      countries: require("@/json/countries.json"),
      states: [],
      cities: [],
      customerDocs: null,
      GRCDialog: false,
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      documentDialog: false,
      // ------------------

      checkIn: {
        id_card_type_id: "",
        id_card_no: "",
        exp: "",
        checkIn_document: null,
      },

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
      ],

      selectMeal: [],
      wantNewDoc: false,
      row: null,
      calIn: {},
      calOut: {},
      searchDialog: false,
      RoomDrawer: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" },
      ],
      val: 1,
      Model: "Reservation",
      // ---------booked data from parent-------------
      document: "",
      new_payment: 0,
      remaining_price:0,
      reference_number: 0,
      imgView: 0,
      isImg: false,
      isPdf: false,
      // ----------------------
      documentObj: {
        fileExtension: null,
        file: null,
      },
      isSelectRoom: true,
      isBed: false,
      subLoad: false,
      isDiscount: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      roomTypes: [],
      types: ["Online", "Walking", "Travel Agency", "Complimentary"],
      search: {
        mobile: "0752388923",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],
      agentList: [],
      idCards: [],
      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      advanceDialog: false,
      isAgent: false,
      isDiff: false,
      search_available_room: "",
      room: {
        customer_type: "",
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: "",
        source: "",
        agent_name: "",
        check_in: null,
        check_out: null,
        discount: 0,
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        sub_total: 0,
        after_discount: 0,
        sales_tax: 0,
        total_price: 0,
        remaining_price: 0,
        request: "",
        company_id: this.$auth.user.company.id,
        remark: "",
        rooms: "",
        reference_no: "",
        paid_by: "",
        purpose: "Visiting",
      },
      reservation: {},
      countryList: [],
      foodPriceList: [],
      person_type_arr: [],

      titleItems: [
        { id: 1, name: "Mr" },
        { id: 2, name: "Mrs" },
        { id: 3, name: "Miss" },
        { id: 4, name: "Ms" },
        { id: 5, name: "Dr" },
      ],

      guest: {
        title: "",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        exp_menu: false,
        exp: null,
      },

      customer: {
        title: "",
        whatsapp: "",
        nationality: "India",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        image: "",
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: null,
        exp_menu: false,
        exp: null,
      },
      id_card_type_id: 0,
      errors: [],
      errorsForSubCustomer: [],
      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
    };
  },

  created() {
    this.get_countries();
    this.get_customer();
    this.get_id_cards();
    this.preloader = false;
  },
  computed: {
    showImage() {
      if (this.BookingData.group_name) {
        if (!this.guest.image && !this.previewImage) {
          // return "/no-image.PNG";
          return "/no-profile-image.jpg";
        } else if (this.previewImage) {
          return this.previewImage;
        }
        return this.guest.image;
      }
      if (!this.customer.image && !this.previewImage) {
        // return "/no-image.PNG";
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },
  },
  methods: {
    downloadCustomerAttachments() {
      let id = this.BookingData.id;
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute(
        "href",
        `${process.env.BACKEND_URL}download_customer_attachments/${id}`
      );
      document.body.appendChild(element);
      element.click();
    },
    getStates(country) {
      // Find the country object from the countries array
      const countryObj = this.countries.find((e) => e.name === country);

      // Check if the country object exists
      if (countryObj) {
        // Set the states array from the found country object
        this.states = countryObj.states || [];
      } else {
        // If country not found, clear the states array and handle error
        this.states = [];
        console.warn(`Country with slug "${country}" not found.`);
      }
    },

    getCities(state) {
      // Find the state object from the states array
      const stateObj = this.states.find((e) => e.name === state);

      // Check if the state object exists
      if (stateObj) {
        // Set the cities array from the found state object
        this.cities = stateObj.cities || [];
      } else {
        // If state not found, clear the cities array and handle error
        this.cities = [];
        console.warn(`State "${state}" not found.`);
      }
    },
    nextTab() {
      if (this.customerDocs) {
        this.activeTab += 1;
      }
    },

    prevTab() {
      if (this.activeTab > 0) {
        this.activeTab -= 1;
      }
    },

    onpick_attachment() {
      this.$refs.attachment_input.click();
    },

    attachment(e) {
      this.customer.image = e.target.files[0] || "";

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

    add_document() {
      this.documentDialog = true;
      // this.alert("hello world");
    },

    store_check_in(data) {
      this.loading = true;

      let payload = new FormData();
      payload.append("customer_id", data.customer_id);
      payload.append("new_payment", this.new_payment || 0);
      payload.append("booking_id", data.id);
      payload.append("room_id", data.room_id);
      payload.append("remaining_price", data.total_price - this.new_payment);
      payload.append("payment_mode_id", this.room.payment_mode_id);
      payload.append("company_id", this.$auth.user.company.id);
      payload.append("id_card_type_id", this.checkIn.id_card_type_id);
      payload.append("id_card_no", this.checkIn.id_card_no);
      payload.append("expired", this.checkIn.exp);
      if (this.reference_number) {
        payload.append("reference_number", this.reference_number);
      }

      payload.append("user_id", this.$auth.user.id);
      payload.append("image", this.customer.image);
      payload.append("title", this.customer.title);
      payload.append("contact_no", this.customer.contact_no);
      payload.append("whatsapp", this.customer.whatsapp);
      payload.append("nationality", this.customer.nationality);
      payload.append("dob", this.customer.dob);
      payload.append("first_name", this.customer.first_name);
      payload.append("last_name", this.customer.last_name);
      payload.append("address", this.customer.address);
      payload.append("email", this.customer.email);

      const fields = this.getCustomerFields();

      fields.forEach((field) => {
        if (this.guest[field]) {
          payload.append(field, this.customer[field]);
        }
      });

      this.$axios
        .post("/check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.$emit("close-dialog");

            this.loading = false;
            // if ($nuxt.$route.name == "hotel-calendar1") {
            //   this.$router.push(`/`);
            // }
          }
        })
        .catch((e) => {
          this.loading = false;
        });
    },

    getCustomerFields() {
      return [
        "image",
        "title",
        "contact_no",
        "whatsapp",
        "nationality",
        "dob",
        "first_name",
        "last_name",
        "address",
        "email",
      ];
    },

    storeSubCustomer() {
      if (!this.BookingData.group_name) {
        this.store_check_in(this.BookingData);
        return;
      }

      let payload = new FormData();

      const fields = this.getSubCustomerFields();
      payload.append("customer_id", this.BookingData.customer_id);

      fields.forEach((field) => {
        if (this.guest[field]) {
          payload.append(field, this.guest[field]);
        }
      });

      this.$axios
        .post("/sub_customer", payload)
        .then(({ data }) => {
          this.store_check_in(this.BookingData);
        })
        .catch((e) => {
          this.loading = false;
          this.errorsForSubCustomer = e.response?.data?.errors;
        });
    },

    getSubCustomerFields() {
      return [
        "image",
        "title",
        "contact_no",
        "whatsapp",
        "nationality",
        "dob",
        "first_name",
        "last_name",
        "address",
        "email",
      ];
    },

    get_customer() {
      this.errors = [];
      this.errorsForSubCustomer = [];
      this.checkLoader = true;
      let customer_id = this.BookingData.customer_id;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer_by_id/${customer_id}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            alert("Customer not found");
            return;
          }
          this.room.purpose = this.BookingData.purpose;

          this.customer = {
            ...data.data,
            customer_id: data.data.id,
          };

          this.getStates(data.data.country);
          this.getCities(data.data.state);
          this.customer.id_card_type_id = parseInt(
            this.customer.id_card_type_id
          );

          this.searchDialog = false;
          this.checkLoader = false;
        });
    },

    preview(file) {
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      }
    },

    newWhatsapp() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
      } else {
        this.customer.whatsapp = "";
      }
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
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
  },
};
</script>
