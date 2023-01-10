<template>
  <div>
    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Returning Customer
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="text-danger">*</span></label
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
    <v-navigation-drawer
      v-model="RoomDrawer"
      temporary
      right
      :fixed="true"
      :clipped="true"
      style="z-index: 1000"
    >
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title>Available Rooms</v-list-item-title>
          <v-list-item-title>
            <v-text-field
              label="Search..."
              outlined
              dense
              class="mt-3"
              v-model="search_available_room"
              @keyup="searchAvailableRoom(search_available_room)"
            ></v-text-field>
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <!-- :color="item.background" -->
      <v-alert
        style="border-bottom: 1px solid rgba(0, 0, 0, 0.2)"
        :style="index == 1 ? 'border-top:1px solid rgba(0, 0, 0, 0.2);' : ''"
        border="left"
        colored-border
        :color="item.background"
        elevation="0"
        rounded="0"
        dense
        class="my-0 py-0"
        v-for="(item, index) in availableRooms"
        :key="index"
      >
        <v-row dense>
          <v-col md="6" dense>
            <b class="pt-3">{{ item.room_no }}</b>
            <p>{{ item && item.room_type && item.room_type.name }}</p>
          </v-col>
          <v-col md="6" dense>
            <v-btn class="mt-3" @click="selectRoom(item)" small color="primary"
              >Select</v-btn
            >
          </v-col>
        </v-row>
      </v-alert>
    </v-navigation-drawer>
    <!-- color="#ECF0F4" -->
    <v-app-bar
      elevation="0"
      style="position: fixed; z-index: 1; left: 280px; width: 75%; top: 64px"
      width="1000"
      scroll-threshold
      tile
      color="#ECF0F4"
      flat
    >
      <v-container grid-list-xs>
        <v-row>
          <v-col md="6">
            <h4>New Reservation</h4>
          </v-col>
          <v-col md="6">
            <div class="text-center">
              <v-btn
                class="mx-2"
                dark
                small
                color="primary"
                @click="store"
                :loading="subLoad"
              >
                Submit
              </v-btn>
              <!-- <v-btn class="mx-2" fab dark small color="background">
                <v-icon dark>mdi-arrow-left </v-icon>
              </v-btn> -->
            </div>
          </v-col>
        </v-row>
      </v-container>
    </v-app-bar>
    <v-row max-height="600" class="mt-15">
      <v-col md="7" class="ml-5">
        <v-card class="mb-8">
          <h6 class="p-3">Details</h6>
          <v-divider class="p-0 m-0" dense></v-divider>
          <v-container style="background-color: #f9fafd">
            <v-row>
              <div class="d-flex mt-4 primary--text">
                <v-icon color="primary" large>mdi-account</v-icon>
                <span style="font-size: 25px" class="ml-2 mt-1">
                  Customer
                </span>
                <v-divider
                  class="ml-3 mt-6 mr-3"
                  style="padding-top: 1px"
                ></v-divider>
                <v-icon @click="searchDialog = true" color="primary" medium
                  >mdi-account-search-outline
                </v-icon>
              </div>
            </v-row>
            <v-row class="px-10">
              <v-col md="3" dense>
                <label class="col-form-label">
                  Type
                </label>
                <v-select
                  v-model="customer.customer_type"
                  :items="['Company', 'Regular', 'Corporate']"
                  dense
                  item-text="name"
                  item-value="id"
                  outlined
                  :hide-details="true"
                ></v-select>
              </v-col>
              <v-col md="9" dense> </v-col>
              <!-- <v-col md="3" dense>
                <label class="col-form-label">Status </label>
                <v-select
                  :items="['Waiting', 'Confirmed']"
                  dense
                  item-text="name"
                  item-value="id"
                  :hide-details="true"
                  outlined
                  v-model="room.customer_status"
                ></v-select>
              </v-col> -->
              <v-col md="12" class="b-0 mt-2" style="padding-bottom: 0px" dense>
                <h6><b>Personal Details</b></h6>
              </v-col>
              <v-col md="2" cols="12" sm="12">
                <label class="col-form-label"
                  >Tittle <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="customer.title"
                  :items="titleItems"
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
              <v-col md="5" cols="12" sm="12">
                <label class="col-form-label"
                  >First Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.first_name"
                  :hide-details="errors && !errors.first_name"
                  :error="errors && errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="5" cols="12" sm="12">
                <label class="col-form-label">Last Name</label>
                <v-text-field
                  dense
                  :hide-details="true"
                  outlined
                  type="text"
                  v-model="customer.last_name"
                ></v-text-field>
              </v-col>
              <v-col md="3" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >ID Card Type <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="customer.id_card_type_id"
                  :items="idCards"
                  dense
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
                ></v-select>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label"
                  >ID Card <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.id_card_no"
                  :hide-details="errors && !errors.id_card_no"
                  :error="errors && errors.id_card_no"
                  :error-messages="
                    errors && errors.id_card_no ? errors.id_card_no[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label">GST</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.gst_number"
                  :hide-details="errors && !errors.gst_number"
                  :error="errors && errors.gst_number"
                  :error-messages="
                    errors && errors.gst_number ? errors.gst_number[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="3" cols="12" sm="12">
                <label class="col-form-label">Car Number</label>
                <v-text-field
                  dense
                  outlined
                  :hide-details="true"
                  type="text"
                  v-model="customer.car_no"
                ></v-text-field>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Document</label>
                <!-- <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="customer.document"
                  :hide-details="true"
                ></v-text-field> -->
                <v-file-input
                  v-model="room.document"
                  color="primary"
                  counter
                  placeholder="Select your files"
                  prepend-icon="mdi-paperclip"
                  outlined
                  :show-size="1000"
                >
                  <template v-slot:selection="{ index, text }">
                    <v-chip v-if="index < 2" color="primary" dark label small>
                      {{ text }}
                    </v-chip>

                    <span
                      v-else-if="index === 2"
                      class="text-overline grey--text text--darken-3 mx-2"
                    >
                      +{{ room.document.length - 2 }} File(s)
                    </span>
                  </template>
                </v-file-input>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Photo</label>
                <v-file-input
                  v-model="room.image"
                  color="primary"
                  counter
                  placeholder="Select your files"
                  prepend-icon="mdi-paperclip"
                  outlined
                  :show-size="1000"
                >
                  <template v-slot:selection="{ index, text }">
                    <v-chip v-if="index < 2" color="primary" dark label small>
                      {{ text }}
                    </v-chip>

                    <span
                      v-else-if="index === 2"
                      class="text-overline grey--text text--darken-3 mx-2"
                    >
                      +{{ room.image.length - 2 }} File(s)
                    </span>
                  </template>
                </v-file-input>
              </v-col>
              <v-col md="12" class="b-0 mt-2" style="padding-bottom: 0px" dense>
                <h6><b>Contact Number</b></h6>
              </v-col>
              <v-col md="6" cols="12" sm="12">
                <label class="col-form-label"
                  >Contact No <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="customer.contact_no"
                  :hide-details="errors && !errors.contact_no"
                  :error="errors && errors.contact_no"
                  :error-messages="
                    errors && errors.contact_no ? errors.contact_no[0] : ''
                  "
                  @keyup="mergeContact"
                ></v-text-field>
              </v-col>
              <v-col md="5" cols="12" sm="12">
                <label class="col-form-label"
                  >Whatsapp No <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="customer.whatsapp"
                  :hide-details="errors && !errors.whatsapp"
                  :error="errors && errors.whatsapp"
                  :error-messages="
                    errors && errors.whatsapp ? errors.whatsapp[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="1" sm="12" cols="12" dense>
                <label class="col-form-label"> Different </label>
                <v-checkbox
                  v-model="isDiff"
                  :hide-details="true"
                  class="pt-0 py-1 chk-align"
                  @click="newWhatsapp"
                >
                </v-checkbox>
              </v-col>
              <v-col md="6" cols="12" sm="12">
                <label class="col-form-label"
                  >Nationality <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="customer.nationality"
                  :items="countryList"
                  item-text="name"
                  item-value="name"
                  :hide-details="errors && !errors.nationality"
                  :error="errors && errors.nationality"
                  :error-messages="
                    errors && errors.nationality ? errors.nationality[0] : ''
                  "
                  dense
                  outlined
                ></v-select>
              </v-col>
              <v-col md="6" cols="12" sm="12">
                <label class="col-form-label">DOB</label>
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
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label"
                  >Email <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="email"
                  v-model="customer.email"
                  :hide-details="errors && !errors.email"
                  :error="errors && errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
                <label class="col-form-label mt-4">Address </label>
                <v-textarea
                  rows="3"
                  v-model="customer.address"
                  outlined
                  :hide-details="true"
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <div class="d-flex mt-4 primary--text">
                <v-icon color="primary" large>mdi-account-details </v-icon>
                <span style="font-size: 25px" class="ml-2 mt-1">
                  More Details
                </span>
                <v-divider
                  class="ml-3 mt-6"
                  style="padding-top: 1px"
                ></v-divider>
              </div>
            </v-row>
            <v-row class="px-10">
              <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label"
                  >Type <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="room.type"
                  :items="types"
                  dense
                  outlined
                  @change="getType(room.type)"
                  :hide-details="errors && !errors.type"
                  :error="errors && errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                ></v-select>
              </v-col>
              <v-col md="6" cols="12" sm="12" v-if="isAgent">
                <label class="col-form-label">Agent Name</label>
                <v-select
                  dense
                  outlined
                  :items="agentList"
                  type="text"
                  item-value="name"
                  item-text="name"
                  v-model="room.source"
                  :hide-details="errors && !errors.source"
                  :error="errors && errors.source"
                  :error-messages="
                    errors && errors.source ? errors.source[0] : ''
                  "
                ></v-select>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense v-if="isOnline">
                <label class="col-form-label">Source </label>
                <v-select
                  v-model="room.source"
                  :items="sources"
                  dense
                  outlined
                  item-value="name"
                  item-text="name"
                  :hide-details="errors && !errors.source"
                  :error="errors && errors.source"
                  :error-messages="
                    errors && errors.source ? errors.source[0] : ''
                  "
                ></v-select>
              </v-col>
              <v-col md="6" cols="12" sm="12" v-if="isAgent || isOnline">
                <label class="col-form-label">Reference Number</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="room.reference_no"
                  :hide-details="errors && !errors.reference_no"
                  :error="errors && errors.reference_no"
                  :error-messages="
                    errors && errors.reference_no ? errors.reference_no[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col
                md="6"
                cols="12"
                sm="12"
                v-if="isAgent || isOnline"
                class="mt-2"
              >
                <v-container fluid>
                  <v-radio-group
                    v-model="room.paid_by"
                    row
                    :hide-details="errors && !errors.paid_by"
                    :error="errors && errors.paid_by"
                    :error-messages="
                      errors && errors.paid_by ? errors.paid_by[0] : ''
                    "
                  >
                    <v-radio label="Paid at Hotel" value="1"></v-radio>
                    <v-radio label="Paid by Agents" value="2"></v-radio>
                  </v-radio-group>
                </v-container>
              </v-col>

              <v-col
                md="6"
                sm="12"
                cols="12"
                dense
                v-if="!isOnline && !isAgent"
              ></v-col>
              <v-col md="12">
                <label class="col-form-label">Customer Request </label>
                <v-textarea
                  rows="3"
                  v-model="room.request"
                  :hide-details="true"
                  outlined
                ></v-textarea>
              </v-col>
              <v-col md="12">
                <label class="col-form-label">Purpose</label>
                <v-textarea
                  rows="3"
                  v-model="room.purpose"
                  :hide-details="true"
                  outlined
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <div class="d-flex mt-4 primary--text">
                <v-icon color="primary" large>mdi-bed</v-icon>
                <span style="font-size: 25px" class="ml-2 mt-1"> Rooms </span>
                <v-divider
                  class="ml-3 mt-6"
                  style="padding-top: 1px"
                ></v-divider>
              </div>
            </v-row>
            <v-row class="px-5">
              <v-col md="12" class="mb-2">
                <v-alert
                  border="left"
                  colored-border
                  color="deep-purple accent-4"
                  elevation="2"
                  v-for="(item, index) in selectedRooms"
                  :key="index"
                >
                  <div class="d-flex justify-space-between">
                    <h6 class="px-2 mt-3">
                      {{ item.room_no || "---" }} —
                      {{ item.room_type }}
                    </h6>
                    <div>
                      <v-btn
                        x-small
                        elevation="0"
                        color="white"
                        @click="remove_select_room(index)"
                      >
                        <v-icon small class="red--text">mdi-delete</v-icon>
                      </v-btn>
                      <v-btn icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </div>
                  </div>
                  <v-divider class="p-0 m-0" dense></v-divider>
                  <div class="mt-3">
                    <v-row>
                      <v-col cols="12" sm="6" md="6">
                        <label class="col-form-label">Check In </label>
                        <v-menu
                          v-model="calIn[index]"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ attrs }">
                            <v-text-field
                              v-model="item.check_in"
                              v-bind="attrs"
                              :hide-details="true"
                              dense
                              outlined
                              readonly
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="item.check_in"
                            @input="calIn[index] = false"
                            @change="getDays"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <label class="col-form-label">Check Out </label>
                        <v-menu
                          v-model="calOut[index]"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ attrs }">
                            <v-text-field
                              v-model="item.check_out"
                              v-bind="attrs"
                              :hide-details="true"
                              dense
                              outlined
                              readonly
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="item.check_out"
                            @input="calOut[index] = false"
                            @change="runAllFunctions"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">First Name </label>
                        <v-text-field
                          dense
                          outlined
                          readonly
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
                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">Last Name</label>
                        <v-text-field
                          dense
                          :hide-details="true"
                          readonly
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Amount : </label>
                        {{ item.price }}
                      </v-col>

                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Meal : </label>
                        {{ capsTitle(item.meal) }}
                      </v-col>
                      <v-col md="2" cols="12" sm="12">
                        <label class="col-form-label">Tax : </label>
                        {{ convert_decimal(item.room_tax) }}
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">After Discount : </label>
                        {{ convert_decimal(item.after_discount) }}
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Tax with Amount : </label>
                        {{ convert_decimal(item.total_with_tax) }}
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Adult : </label>
                        {{ item.no_of_adult }}
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Child : </label>
                        {{ item.no_of_child }}
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <label class="col-form-label">Baby : </label>
                        {{ item.no_of_baby }}
                      </v-col>
                    </v-row>
                  </div>
                </v-alert>
                <!-- ///////////////////////////////// -->

                <v-row v-if="selectedRooms.length > 0">
                  <div class="d-flex mt-4 mb-10 primary--text">
                    <v-icon color="primary" large>mdi-bed</v-icon>
                    <span style="font-size: 25px" class="ml-2 mt-1">
                      Add Room
                    </span>
                    <v-divider
                      class="ml-3 mt-6 mr-3"
                      style="padding-top: 1px"
                    ></v-divider>
                    <v-icon
                      @click="isSelectRoom = !isSelectRoom"
                      color="primary"
                      medium
                    >
                      {{ isSelectRoom == false ? "mdi-plus" : "mdi-minus" }}
                    </v-icon>
                  </div>
                </v-row>

                <v-alert
                  border="left"
                  colored-border
                  color="deep-purple accent-4"
                  elevation="2"
                  v-if="isSelectRoom"
                >
                  <div class="d-flex justify-space-between">
                    <h6 class="px-2 mt-3">
                      {{ temp.room_no || "---" }} -
                      {{ temp.room_type || "---" }}
                    </h6>
                    <div>
                      <v-btn color="primary" @click="get_available_rooms" small
                        >Select Room
                      </v-btn>
                      <!-- @click.stop="RoomDrawer = !RoomDrawer" -->
                      <v-btn icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </div>
                  </div>
                  <v-divider class="p-0 m-0" dense></v-divider>
                  <div class="mt-3">
                    <v-row>
                      <v-col cols="12" sm="6" md="6">
                        <label class="col-form-label">Check In </label>
                        <v-menu
                          v-model="temp.check_in_menu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="temp.check_in"
                              readonly
                              v-on="selectedRooms.length == 0 ? on : ''"
                              v-bind="attrs"
                              :hide-details="true"
                              dense
                              outlined
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="temp.check_in"
                            @input="temp.check_in_menu = false"
                            @change="getDays"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <label class="col-form-label">Check Out </label>
                        <v-menu
                          v-model="temp.check_out_menu"
                          :close-on-content-click="false"
                          :nudge-right="40"
                          transition="scale-transition"
                          offset-y
                          min-width="auto"
                        >
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="temp.check_out"
                              readonly
                              v-on="selectedRooms.length == 0 ? on : ''"
                              v-bind="attrs"
                              :hide-details="true"
                              dense
                              outlined
                            ></v-text-field>
                          </template>
                          <v-date-picker
                            v-model="temp.check_out"
                            @input="temp.check_out_menu = false"
                            @change="runAllFunctions"
                          ></v-date-picker>
                        </v-menu>
                      </v-col>
                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">First Name </label>
                        <v-text-field
                          dense
                          readonly
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
                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">Last Name</label>
                        <v-text-field
                          dense
                          :hide-details="true"
                          readonly
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>
                      <v-col md="3" sm="12" cols="12" dense>
                        <label class="col-form-label"
                          >Adult <span class="text-danger">*</span>
                        </label>

                        {{ temp.no_of_adult }}
                        <div class="wrapper">
                          <span
                            class="minus"
                            @mouseup="
                              get_food_price_cal('adult', --temp.no_of_adult)
                            "
                            @click="temp.no_of_adult < 1 || temp.no_of_adult"
                            >-</span
                          >
                          <span class="num">{{ temp.no_of_adult }}</span>
                          <span
                            class="plus"
                            @mouseup="
                              get_food_price_cal('adult', ++temp.no_of_adult)
                            "
                            @click="temp.no_of_adult > 3 || temp.no_of_adult"
                            >+</span
                          >
                        </div>
                      </v-col>
                      <v-col md="3" sm="12" cols="12" dense>
                        <label class="col-form-label">Child </label>
                        <div class="wrapper">
                          <span
                            class="minus"
                            @mouseup="
                              get_food_price_cal('child', --temp.no_of_child)
                            "
                            @click="temp.no_of_child < 1 || temp.no_of_child"
                            >-</span
                          >
                          <span class="num">{{ temp.no_of_child }}</span>
                          <span
                            class="plus"
                            @mouseup="
                              get_food_price_cal('child', ++temp.no_of_child)
                            "
                            @click="temp.no_of_child > 1 || temp.no_of_child"
                            >+</span
                          >
                        </div>
                      </v-col>
                      <v-col md="3" sm="12" cols="12" dense>
                        <label class="col-form-label">Baby </label>
                        <div class="wrapper">
                          <span
                            class="minus"
                            @mouseup="
                              get_food_price_cal('baby', --temp.no_of_baby)
                            "
                            @click="temp.no_of_baby < 1 || temp.no_of_baby"
                            >-</span
                          >
                          <span class="num">{{ temp.no_of_baby }}</span>
                          <span
                            class="plus"
                            @mouseup="
                              get_food_price_cal('baby', ++temp.no_of_baby)
                            "
                            @click="temp.no_of_baby > 1 || temp.no_of_baby"
                            >+</span
                          >
                        </div>
                      </v-col>

                      <!-- <v-col md="6" sm="12" cols="12" dense>
                        <label class="col-form-label"> Meals </label>
                        <v-select
                          v-model="temp.meal"
                          :items="meals"
                          dense
                          item-text="name"
                          item-value="slug"
                          outlined
                          :hide-details="errors && !errors.meal"
                          :error="errors && errors.meal"
                          :error-messages="
                            errors && errors.meal ? errors.meal[0] : ''
                          "
                          @change="changeMealPlan(temp.meal)"
                        ></v-select>
                      </v-col> -->

                      <v-col md="8" sm="12" cols="12" dense>
                        <label class="col-form-label"> Meals </label>
                        {{ temp.meal }}
                        <v-radio-group row dense>
                          <v-checkbox
                            v-model="temp.breakfast"
                            label="Breakfast"
                            value="breakfast"
                            class="px-3"
                            @change="meal_cal(temp.breakfast)"
                          >
                          </v-checkbox>
                          <v-checkbox
                            v-model="temp.lunch"
                            label="Lunch"
                            value="lunch"
                            class="px-3"
                            @change="meal_cal(temp.lunch)"
                          >
                          </v-checkbox>
                          <v-checkbox
                            v-model="temp.dinner"
                            label="Dinner"
                            value="dinner"
                            class="px-3"
                            @change="meal_cal(temp.dinner)"
                          >
                          </v-checkbox>
                        </v-radio-group>
                      </v-col>
                      <v-col md="4">
                        <table class="food-table">
                          <tr class="food-table">
                            <th class="food-table">Type</th>
                            <td class="food-table">Breakfast</td>
                            <td class="food-table">Lunch</td>
                            <td class="food-table">Dinner</td>
                          </tr>
                          <tr class="food-table">
                            <th class="food-table">Adult</th>
                            <td class="food-table">{{ tempAdult.tot_ab }}</td>
                            <td class="food-table">{{ tempAdult.tot_al }}</td>
                            <td class="food-table">{{ tempAdult.tot_ad }}</td>
                          </tr>
                          <tr class="food-table">
                            <th class="food-table">Child</th>
                            <td class="food-table">{{ tempChild.tot_cb }}</td>
                            <td class="food-table">{{ tempChild.tot_cl }}</td>
                            <td class="food-table">{{ tempChild.tot_cd }}</td>
                          </tr>
                        </table>
                      </v-col>
                      <v-col md="2" sm="12" cols="12" dense>
                        <label class="col-form-label"> Discount </label>
                        <v-checkbox
                          value="1"
                          v-model="isDiscount"
                          :hide-details="true"
                          class="pt-0 py-1 chk-align"
                        >
                        </v-checkbox>
                      </v-col>
                      <v-col md="4" sm="12" cols="12" dense v-if="isDiscount">
                        <label class="col-form-label"> Amount </label>
                        <v-text-field
                          dense
                          outlined
                          type="number"
                          v-model="temp.room_discount"
                          :hide-details="true"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </div>
                </v-alert>
              </v-col>
              <v-col md="12">
                <v-btn
                  @click="add_room"
                  v-if="isSelectRoom"
                  color="primary"
                  class="w-100 py-5"
                >
                  <v-icon color="white" large>mdi-plus</v-icon>
                  Select Room
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
      <v-col md="3">
        <v-card>
          <h6 class="p-3">Summary</h6>
          <v-divider class="p-0 m-0" dense></v-divider>
          <v-container
            class="px-10"
            grid-list-xs
            style="background-color: #f9fafd"
          >
            <v-row style="font-size: 15px" dense>
              <v-col md="6"><b class="#F9FAFD--text">Customer</b></v-col>
              <v-col md="6" class="text-right">{{ customer.first_name }}</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Check In</b></v-col>
              <v-col md="6" class="text-right">{{ temp.check_in }}</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Check Out</b></v-col>
              <v-col md="6" class="text-right">{{ temp.check_out }}</v-col>
              <br /><br />
              <v-col md="6"><b class="#F9FAFD--text">Days</b></v-col>
              <v-col md="6" class="text-right">{{ getDays() }} </v-col>
              <v-col md="12" class="text-right"><v-divider></v-divider></v-col>
            </v-row>
            <v-row>
              <v-col md="12">
                <b>Room</b>
                <v-alert
                  border="left"
                  colored-border
                  color="deep-purple accent-4"
                  elevation="2"
                  v-for="(item, index) in selectedRooms"
                  :key="index"
                >
                  <v-row>
                    <v-col md="12">
                      {{ item.room_no }} — {{ item.room_type }}
                    </v-col>
                  </v-row>
                </v-alert>
              </v-col>
              <hr />
            </v-row>
            <v-row>
              <b>Remarks</b>
              <v-col md="12">
                <label class="col-form-label">Print Remark </label>
                <v-textarea
                  rows="3"
                  v-model="room.remark"
                  :hide-details="true"
                  outlined
                ></v-textarea>
              </v-col>
              <hr class="my-6" />
            </v-row>
          </v-container>
        </v-card>
        <v-card class="mt-5">
          <h6 class="p-3">Payment</h6>
          <v-divider class="p-0 m-0" dense></v-divider>
          <v-container
            class="px-10"
            grid-list-xs
            style="background-color: #f9fafd"
          >
            <v-row>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Advance Amount</label>
                <v-text-field
                  @keyup="runAllFunctions"
                  dense
                  :hide-details="true"
                  :disabled="room.paid_by == '2' ? true : false"
                  outlined
                  type="number"
                  v-model="room.advance_price"
                ></v-text-field>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Payment Mode</label>
                <v-select
                  v-model="room.payment_mode_id"
                  :items="[
                    { id: 1, name: 'Cash' },
                    { id: 2, name: 'Card' },
                    { id: 3, name: 'Online' },
                    { id: 4, name: 'Bank' },
                    { id: 5, name: 'UPI' },
                    { id: 6, name: 'Cheque' }
                  ]"
                  item-text="name"
                  item-value="id"
                  dense
                  :disabled="room.paid_by == '2' ? true : false"
                  outlined
                  @change="getType(room.type)"
                  :hide-details="errors && !errors.payment_mode_id"
                  :error="errors && errors.payment_mode_id"
                  :error-messages="
                    errors && errors.typpayment_mode_ide
                      ? errors.payment_mode_id[0]
                      : ''
                  "
                ></v-select>
              </v-col>
              <v-col md="12">
                <v-divider></v-divider>
              </v-col>
            </v-row>
            <v-row style="font-size: 15px" dense>
              <v-col cols="12" class="payment-table">
                <table>
                  <tr colspan="2">
                    <th>Rooms Payment Summary</th>
                  </tr>
                  <tr v-for="(item, index) in selectedRooms" :key="index">
                    <td>{{ item.room_no }}</td>
                    <td>
                      <div align="right">{{ convert_decimal(item.total) }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <hr class="p-0 m-0" />
                    </td>
                  </tr>
                  <!-- <tr>
                    <td>Total Extra</td>
                    <td>
                      <div align="right">{{ room.total_extra }}</div>
                    </td>
                  </tr> -->
                  <tr>
                    <td>Total</td>
                    <td>
                      <div align="right">
                        {{ convert_decimal(room.all_room_Total_amount) }}
                      </div>
                    </td>
                  </tr>
                </table>
              </v-col>
              <v-col md="12" class="text-right"><v-divider></v-divider></v-col>
            </v-row>
            <v-row style="font-size: 15px" dense>
              <v-col cols="12" class="payment-table">
                <table>
                  <tr>
                    <td style="width: 250px">Total Days</td>
                    <td style="width: 250px">
                      <div align="right">{{ getDays() }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Room Price</td>
                    <td>
                      <div align="right">
                        {{ convert_decimal(room.all_room_Total_amount) }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <!-- Sub Total (Total Days x Room Price) ({{ getDays() }} x -->
                      Sub Total ({{ getDays() }} x
                      {{ convert_decimal(room.all_room_Total_amount) }})
                    </td>
                    <td>
                      <div align="right">
                        {{ convert_decimal(room.sub_total) }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Total</th>
                    <td>
                      <div align="right">
                        {{ convert_decimal(room.total_price) }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Advance Payment</td>
                    <td>
                      <div align="right">
                        {{ room.advance_price }}
                        <!-- {{ convert_decimal(room.advance_price) }} -->
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>Remaining Amount</td>
                    <td>
                      <div align="right">
                        {{ convert_decimal(room.remaining_price) }}
                      </div>
                    </td>
                  </tr>
                </table>
              </v-col>
              <v-col md="12" class="text-right"><v-divider></v-divider></v-col>
            </v-row>
          </v-container>
        </v-card>
        <pre>
          <!-- {{ selectedRooms }} -->
        </pre>
      </v-col>
      <v-col md="1" style="width: 300px"> </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data() {
    return {
      selectMeal: [],
      row: null,
      calIn: {},
      calOut: {},
      searchDialog: false,
      RoomDrawer: null,
      items: [
        { title: "Home", icon: "mdi-view-dashboard" },
        { title: "About", icon: "mdi-forum" }
      ],
      val: 1,
      Model: "Reservation",
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
        mobile: "0752388923"
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],

      agentList: [],

      idCards: [],

      temp: {
        check_in_menu: false,
        check_out_menu: false,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
        // meal: [],
        bed_amount: 0,
        room_discount: 0,
        after_discount: 0, //(price - room_discount)
        room_tax: 0,
        total_with_tax: 0, //(after_discount * room_tax)
        total: 0, //(total_with_tax * bed_amount)
        grand_total: 0, //(total * days)
        company_id: this.$auth.user.company.id,

        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
        breakfast: "",
        lunch: "",
        dinner: ""
      },

      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: ""
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
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
        paid_by: ""
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
        { id: 5, name: "Dr" }
      ],

      meals: [
        { name: "Room only", slug: "room_only_price" },
        { name: "Breakfast", slug: "Break_fast_price" },
        { name: "Breakfast and Dinner", slug: "Break_fast_with_dinner_price" },
        { name: "Breakfast and Lunch", slug: "Break_fast_with_lunch_price" },
        { name: "Full Board", slug: "full_board_price" }
        // { name: 5, slug: "lunch_with_dinner_price" },
      ],

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
        company_id: this.$auth.user.company.id,
        dob_menu: false,
        dob: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10)
      },
      id_card_type_id: 0,
      errors: [],
      tempAdult: {},
      tempChild: {}
    };
  },
  created() {
    this.get_food_price();
    this.get_reservation();
    this.preloader = false;
    this.get_room_types();
    this.get_id_cards();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
  },
  methods: {
    runAllFunctions() {
      this.getDays();
      this.subTotal();
      // this.afterDiscount();
      // this.getAmountAfterSalesTax();
      this.getTotal();
      this.getRemainingAmount();

      // this.convert_decimal(this.room.advance_price);
    },
    getDays() {
      let ci = new Date(this.temp.check_in);
      let co = new Date(this.temp.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      if (days > 0) {
        return (this.room.total_days = days);
      }
    },

    get_reservation() {
      this.reservation = this.$store.state.reservation;
      this.temp.room_id = this.reservation.room_id;
      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type;
      this.temp.price = this.reservation.price;
      this.temp.check_in = this.reservation.check_in;
      this.temp.check_out = this.reservation.check_out;
      this.temp.room_tax = this.get_room_tax(this.reservation.price);
      this.room.check_in = this.reservation.check_in;
      this.room.check_out = this.reservation.check_out;
    },

    get_food_price() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_food_prices`, payload).then(({ data }) => {
        this.foodPriceList = data;
        console.log(this.foodPriceList);
        this.get_food_price_cal("adult", 1);
      });
    },

    get_food_price_cal(person_type, person_qty) {
      if (this.foodPriceList.length == 0) {
        return;
      }
      let person = this.foodPriceList.find(e => e.type == person_type);

      person.qty = person_qty;

      let index = this.person_type_arr.findIndex(e => e.type == person_type);

      if (index == -1) {
        this.person_type_arr.push(person);
      } else {
        this.person_type_arr.splice(index, 1, person);
      }
      console.log(this.person_type_arr);
    },

    meal_cal(meal_type) {
      // console.log(this.person_type_arr);
      // return;
      let res = this.person_type_arr.find(e => {
        if (e.type == "adult") {
          this.get_adult_cal(e);
        }
        if (e.type == "child") {
          this.get_child_cal(e);
        }
        // if (e.type == "baby") {
        //   this.get_baby_cal(e);
        // }
      });
    },

    get_adult_cal(e) {
      let tab, tax_tab, tal, tax_tal, tad, tax_tad;
      if (this.temp.breakfast) {
        tab = parseFloat(e.breakfast) * parseFloat(e.qty);
        tax_tab = this.get_amount_with_tax(tab);
      }
      if (this.temp.lunch) {
        tal = parseFloat(e.lunch) * parseFloat(e.qty);
        tax_tal = this.get_amount_with_tax(tal);
      }
      if (this.temp.dinner) {
        tad = parseFloat(e.dinner) * parseFloat(e.qty);
        tax_tad = this.get_amount_with_tax(tad);
      }

      this.tempAdult = {
        tot_ab: tax_tab + tab,
        tot_al: tax_tal + tad,
        tot_ad: tax_tad + tad
      };
      console.log(this.tempAdult);
    },

    get_child_cal(e) {
      let tcb, tax_tcb, tcl, tax_tcl, tcd, tax_tcd;
      if (this.temp.breakfast) {
        tcb = parseFloat(e.breakfast) * parseFloat(e.qty);
        tax_tcb = this.get_amount_with_tax(tcb);
      }
      if (this.temp.lunch) {
        tcl = parseFloat(e.lunch) * parseFloat(e.qty);
        tax_tcl = this.get_amount_with_tax(tcl);
      }
      if (this.temp.dinner) {
        tcd = parseFloat(e.dinner) * parseFloat(e.qty);
        tax_tcd = this.get_amount_with_tax(tcd);
      }

      this.tempChild = {
        tot_cb: tax_tcb + tcb,
        tot_cl: tax_tcl + tcd,
        tot_cd: tax_tcd + tcd
      };
      console.log(this.tempChild);
    },

    get_amount_with_tax(amount) {
      let per = 5;
      let tax = this.getPercentage(amount, per);
      return tax;
      // console.log(amount);
    },

    getPercentage(amount, clause) {
      let res = (amount / 100) * clause;
      return res;
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

    get_countries() {
      this.$axios.get(`get_countries`).then(({ data }) => {
        this.countryList = data;
      });
    },

    convert_decimal(n) {
      if (n === +n && n !== (n | 0)) {
        return n.toFixed(2);
      } else {
        return n + ".00";
      }
    },

    getTotal() {
      return (this.room.total_price = this.subTotal());
      // parseInt(this.getAmountAfterSalesTax()) +
      // this.subTotal() - this.room.discount);
    },

    getRemainingAmount() {
      return (this.room.remaining_price =
        this.getTotal() - this.room.advance_price);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    subTotal() {
      return (this.room.sub_total =
        parseFloat(this.room.all_room_Total_amount) *
        parseFloat(this.getDays()));
    },

    getType(val) {
      if (val == "Online") {
        this.isOnline = true;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isOnline = false;
        this.isAgent = true;
        return;
      }

      if (val == "Walking") {
        this.room.source = "walking";
      }

      if (val == "Complimentary") {
        this.room.source = "complimentary";
      }

      this.isOnline = false;
      this.isAgent = false;
    },

    get_room_types() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },

    get_agents() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },

    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
      });
    },

    get_id_cards() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios.get(`get_id_cards`, payload).then(({ data }) => {
        this.idCards = data;
      });
    },

    remove_select_room(index) {
      this.selectedRooms.splice(index, 1);
      this.get_total_amounts();
      this.get_all_room_Total_amount();
      this.runAllFunctions();
      this.isSelectRoom = true;
      if (this.selectedRooms.length == 0) {
      }
    },

    searchAvailableRoom(val) {
      let arr = this.availableRooms;
      let res = arr.filter(e => e.room_no == val);
      if (val.length == 0) {
        this.get_available_rooms();
        return;
      }
      if (res.length > 0) {
        this.availableRooms = res;
      }
    },

    get_all_room_Total_amount() {
      let sum = 0;
      let res = 0;
      this.selectedRooms.map(e => (sum += parseFloat(e.total_with_tax)));
      res = parseFloat(sum) + parseFloat(this.room.total_extra);
      this.room.all_room_Total_amount = res;
    },

    get_room_tax(amount) {
      let per = amount < 3000 ? 12 : 18;
      let tax = (amount / 100) * per;
      this.temp.room_tax = tax;
      this.temp.total_with_tax =
        parseFloat(this.temp.after_discount) + parseFloat(tax);
      let gst = parseFloat(tax) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
      return tax;
    },

    selectRoom(item) {
      this.RoomDrawer = false;
      this.temp.company_id = this.$auth.user.company.id;
      this.temp.room_no = item.room_no;
      this.temp.room_id = item.id;
      this.temp.room_type = item.room_type.name;
      this.temp.price = item.price;
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      let title = upper.replace(/[^A-Z]/g, " ");
      return title;
    },

    changeMealPlan(mealType) {
      if (!this.temp.room_type) {
        alert("Select room");
        return;
      }

      let payload = {
        params: {
          room_type: this.temp.room_type,
          slug: mealType,
          company_id: this.$auth.user.company.id
        }
      };
      this.$axios
        .get(`get_room_price_by_meal_plan`, payload)
        .then(({ data }) => {
          this.temp.price = data;
        });
    },

    add_room() {
      if (this.temp.room_no == "") {
        this.alert("Missing!", "Select room", "error");
        return;
      }

      this.temp.after_discount =
        parseFloat(this.temp.price) -
        parseFloat(this.temp.room_discount == "" ? 0 : this.temp.room_discount);
      this.temp.days = this.getDays();
      this.get_room_tax(this.temp.after_discount);

      this.temp.total = parseFloat(this.temp.total_with_tax);

      this.temp.grand_total =
        parseFloat(this.temp.days) * parseFloat(this.temp.total);

      this.room.check_in = this.temp.check_in;
      this.room.check_out = this.temp.check_out;

      this.temp.room_discount =
        this.temp.room_discount == "" ? 0 : this.temp.room_discount;

      delete this.temp.check_in_menu;
      delete this.temp.check_out_menu;

      this.selectedRooms.push(this.temp);

      this.get_total_amounts();
      this.runAllFunctions();

      this.clear_add_room();
      this.alert("Success!", "success selected room", "success");
      this.isSelectRoom = false;
      return;
    },

    // get_bed_with_tax_amount() {
    //   let sum = this.temp.bed_amount || 0;
    //   let tax = (sum / 100) * 12;
    //   this.temp.bed_amount = parseInt(tax) + parseInt(sum);
    // },

    get_total_amounts() {
      let tot_bed_amount = 0;

      let tot_total = 0;
      this.selectedRooms.map(
        e =>
          (tot_bed_amount += e.bed_amount == "" ? 0 : parseFloat(e.bed_amount))
      );

      this.room.total_extra = tot_bed_amount;

      this.selectedRooms.map(
        e => (tot_total += e.total == "" ? 0 : parseFloat(e.total))
      );
      this.room.all_room_Total_amount = tot_total;
    },

    get_room_discount(val) {
      this.temp.price = parseFloat(this.temp.price) - parseFloat(val);
    },

    clear_add_room() {
      let check_in_old = this.temp.check_in;
      let check_out_old = this.temp.check_out;

      this.temp = {
        check_in_menu: false,
        check_out_menu: false,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: check_in_old,
        check_out: check_out_old,
        meal: "room_only_price",
        bed_amount: 0,
        room_discount: 0,
        after_discount: 0, //(price - room_discount)
        room_tax: 0,
        total_with_tax: 0, //(after_discount * room_tax)
        total: 0, //(total_with_tax * bed_amount)
        grand_total: 0, //(total * days)
        company_id: this.$auth.user.company.id,

        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0
      };

      return;

      let check_in = this.temp.check_in;
      let check_out = this.temp.check_out;

      this.temp = {};

      this.temp.check_in = check_in;
      // this.temp.bed_amount = 0;
      this.temp.room_discount = 0;
      this.temp.check_out = check_out;
      // this.temp.meal = "Room only";

      this.temp.no_of_adult = 1;
      this.temp.no_of_child = 0;
      this.temp.no_of_baby = 0;
    },

    get_available_rooms() {
      if (this.temp.check_in == undefined || this.temp.check_out == undefined) {
        alert("Please select date");
        this.RoomDrawer = false;
        return;
      }
      let payload = {
        params: {
          check_in: this.temp.check_in,
          check_out: this.temp.check_out,
          company_id: this.$auth.user.company.id
        }
      };
      this.RoomDrawer = true;
      this.$axios
        .get(`get_available_rooms_by_date`, payload)
        .then(({ data }) => {
          this.availableRooms = data;
        });
      this.runAllFunctions();
    },

    get_customer() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.search.mobile;
      if (contact_no == undefined || contact_no == "") {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }
      this.$axios.get(`get_customer/${contact_no}`).then(({ data }) => {
        if (!data.status) {
          this.checkLoader = false;
          // this.customer = {};
          this.customer.contact_no = contact_no;
          alert("Customer not found");
          return;
        }

        this.customer = {
          ...data.data,
          customer_id: data.data.id
        };

        this.customer.id_card_type_id = parseInt(this.customer.id_card_type_id);
        this.searchDialog = false;
        this.checkLoader = false;
      });
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store() {
      // this.alert("Waiting..!", "Loading...", "info");
      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }
      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.alert("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
      }

      let rooms = this.selectedRooms.map(e => e.room_no);
      this.room.rooms = rooms.toString();
      let payload = {
        ...this.room,
        ...this.customer
      };
      this.$axios
        .post("/booking_validate", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert(
              "No reservation created!",
              "Some fields are missing or invalid",
              "error"
            );
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.errors = [];
            this.store_customer();
          }
        })
        .catch(e => console.log(e));
    },

    store_customer() {
      let payload = {
        ...this.customer,
        company_id: this.$auth.user.company.id
      };
      this.$axios
        .post("/customer", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert(
              "No reservation created!",
              "Some fields are missing or invalid",
              "error"
            );
            this.subLoad = false;
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.store_booking(data.record);
          }
        })
        .catch(e => console.log(e));
    },

    store_booking(id) {
      let payload = {
        ...this.room,
        customer_id: id,
        type: this.customer.customer_type
      };
      this.$axios
        .post("/booking1", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.store_booked_rooms(data.record.id, id);
            this.store_document(data.record.id);
          }
        })
        .catch(e => console.log(e));
    },

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.room.document);
      payload.append("image", this.room.image);
      payload.append("booking_id", id);
      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          }
        })
        .catch(e => console.log(e));
    },

    store_booked_rooms(id, customer_id) {
      this.selectedRooms.forEach(key => {
        key.booking_id = id;
        key.customer_id = customer_id;
      });

      let payload = {
        ...this.selectedRooms
      };
      this.$axios
        .post("/store_booked_rooms", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.alert(
              "No reservation created!",
              "Some fields are missing or invalid",
              "error"
            );
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.errors = [];
            this.alert("Success!", "Successfully room added", "success");
            this.subLoad = false;
            this.$router.push(`/`);
          }
        })
        .catch(e => console.log(e));
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    }
  }
};
</script>
<style>
.wrapper {
  height: 40px;
  width: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border-radius: 5px;
  border: 1px solid rgb(145, 140, 140);
}
.wrapper span {
  width: 100%;
  text-align: center;
  font-size: 25px;
  font-weight: normal;
  cursor: pointer;
  user-select: none;
}
.wrapper span.num {
  font-size: 20px;
  border-right: 2px solid rgba(0, 0, 0, 0.2);
  border-left: 2px solid rgba(0, 0, 0, 0.2);
  pointer-events: none;
}
fieldset {
  background-color: white !important;
}
.payment-table table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}
.payment-table td,
th {
  text-align: left;
  padding: 7px;
}

.food-table table {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
}

/* .food-table
th,
td {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
} */

.food-table {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
  width: 250px;
  text-align: right;
  padding: 0 5px;
}
</style>
