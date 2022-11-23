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
                :hide-details="!errors.mobile"
                :error="errors.mobile"
                :error-messages="
                  errors && errors.mobile ? errors.mobile[0] : ''
                "
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
      style="z-index:1000"
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
        style="border-bottom:1px solid rgba(0, 0, 0, 0.2);"
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
      style="position:fixed;z-index:1;left:315px;width:70%;top:64px"
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
            <div class="text-right">
              <v-btn class="mx-2" dark small color="primary">
                Submit
              </v-btn>
              <v-btn class="mx-2" fab dark small color="background">
                <v-icon dark>mdi-arrow-left </v-icon>
              </v-btn>
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
          <v-container style="background-color:#F9FAFD">
            <v-row>
              <div class="d-flex mt-4 primary--text">
                <v-icon color="primary" large>mdi-account</v-icon>
                <span style="font-size:25px" class="ml-2 mt-1 ">
                  Customer
                </span>
                <v-divider
                  class="ml-3 mt-6 mr-3"
                  style="padding-top:1px"
                ></v-divider>
                <v-icon @click="searchDialog = true" color="primary" medium
                  >mdi-account-search-outline
                </v-icon>
              </div>
            </v-row>
            <v-row class="px-10">
              <v-col md="3" dense>
                <label class="col-form-label"
                  >Type <span class="text-danger">*</span></label
                >
                <v-select
                  v-model="customer.type"
                  :items="[
                    { id: 1, name: 'Company' },
                    { id: 2, name: 'Regular' }
                  ]"
                  dense
                  item-text="name"
                  item-value="id"
                  outlined
                  :hide-details="true"
                ></v-select>
              </v-col>
              <v-col md="6" dense> </v-col>
              <v-col md="3" dense>
                <label class="col-form-label"
                  >Status <span class="text-danger">*</span></label
                >
                <v-select
                  :items="[
                    { id: 1, name: 'Cancel' },
                    { id: 2, name: 'Waiting' },
                    { id: 3, name: 'Confirmed' }
                  ]"
                  dense
                  item-text="name"
                  item-value="id"
                  :hide-details="true"
                  outlined
                ></v-select>
              </v-col>
              <v-col md="12" class="b-0 mt-2" style="padding-bottom:0px" dense>
                <h6><b>Personal Details</b></h6>
              </v-col>
              <v-col md="6" cols="12" sm="12">
                <label class="col-form-label"
                  >First Name <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.first_name"
                  :hide-details="!errors.first_name"
                  :error="errors.first_name"
                  :error-messages="
                    errors && errors.first_name ? errors.first_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" cols="12" sm="12">
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
                  >Adult <span class="text-danger">*</span>
                </label>
                <div class="wrapper">
                  <span
                    class="minus"
                    @click="customer.no_of_adult < 1 || customer.no_of_adult--"
                    >-</span
                  >
                  <span class="num">{{ customer.no_of_adult }}</span>
                  <span
                    class="plus"
                    @click="customer.no_of_adult > 8 || customer.no_of_adult++"
                    >+</span
                  >
                </div>
              </v-col>
              <v-col md="3" sm="12" cols="12" dense>
                <label class="col-form-label">Child </label>

                <div class="wrapper">
                  <span
                    class="minus"
                    @click="customer.no_of_child < 1 || customer.no_of_child--"
                    >-</span
                  >
                  <span class="num">{{ customer.no_of_child }}</span>
                  <span
                    class="plus"
                    @click="customer.no_of_child > 8 || customer.no_of_child++"
                    >+</span
                  >
                </div>
              </v-col>
              <v-col md="3" sm="12" cols="12" dense>
                <label class="col-form-label">Baby </label>

                <div class="wrapper">
                  <span
                    class="minus"
                    @click="customer.no_of_baby < 1 || customer.no_of_baby--"
                    >-</span
                  >
                  <span class="num">{{ customer.no_of_baby }}</span>
                  <span
                    class="plus"
                    @click="customer.no_of_baby > 8 || customer.no_of_baby++"
                    >+</span
                  >
                </div>
              </v-col>
              <v-col md="3"></v-col>
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
                <label class="col-form-label"
                  >Selected ID Card Number
                  <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="customer.id_card_no"
                  :hide-details="!errors.id_card_no"
                  :error="errors.id_card_no"
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
                  :hide-details="!errors.gst_number"
                  :error="errors.gst_number"
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
              <v-col md="12" class="b-0 mt-2" style="padding-bottom:0px" dense>
                <h6><b>Contact Number</b></h6>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label"
                  >Contact No <span class="text-danger">*</span></label
                >
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="customer.contact_no"
                  :hide-details="!errors.contact_no"
                  :error="errors.contact_no"
                  :error-messages="
                    errors && errors.contact_no ? errors.contact_no[0] : ''
                  "
                ></v-text-field>
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
                  :hide-details="!errors.email"
                  :error="errors.email"
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
                <v-icon color="primary" large>mdi-room-service</v-icon>
                <span style="font-size:25px" class="ml-2 mt-1 ">
                  More Details
                </span>
                <v-divider
                  class="ml-3 mt-6"
                  style="padding-top:1px"
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
                  :hide-details="!errors.type"
                  :error="errors.type"
                  :error-messages="errors && errors.type ? errors.type[0] : ''"
                ></v-select>
              </v-col>
              <v-col md="6" cols="12" sm="12" v-if="isAgent">
                <label class="col-form-label">Agent Name</label>
                <v-text-field
                  dense
                  outlined
                  type="text"
                  v-model="room.agent_name"
                  :hide-details="!errors.agent_name"
                  :error="errors.agent_name"
                  :error-messages="
                    errors && errors.agent_name ? errors.agent_name[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense v-if="isOnline">
                <label class="col-form-label">Source </label>
                <v-select
                  v-model="room.source"
                  :items="sources"
                  dense
                  outlined
                  :hide-details="!errors.source"
                  :error="errors.source"
                  :error-messages="
                    errors && errors.source ? errors.source[0] : ''
                  "
                ></v-select>
              </v-col>
              <v-col
                md="6"
                sm="12"
                cols="12"
                dense
                v-if="!isOnline && !isAgent"
              ></v-col>
              <!-- <v-col md="6" sm="12" cols="12" dense>
                <label class="col-form-label">
                  Meals
                  <span class="text-danger">*</span>
                </label>
                <v-select
                  v-model="room.meal"
                  :items="meals"
                  dense
                  outlined
                  :hide-details="!errors.meal"
                  :error="errors.type"
                  :error-messages="errors && errors.meal ? errors.meal[0] : ''"
                ></v-select>
              </v-col>
              <v-col md="6" sm="12" cols="12" dense></v-col>

              <v-col md="2" sm="12" cols="12" dense>
                <label class="col-form-label">
                  Extra Bed
                </label>
                <v-checkbox
                  value="1"
                  v-model="isBed"
                  :hide-details="true"
                  class="pt-0  py-1 chk-align"
                >
                </v-checkbox>
              </v-col>
              <v-col md="4" sm="12" cols="12" dense v-if="isBed">
                <label class="col-form-label">
                  Amount
                  <span class="text-danger">*</span>
                </label>
                <v-text-field
                  dense
                  outlined
                  type="number"
                  v-model="room.bed_amount"
                  :hide-details="!errors.bed_amount"
                  :error="errors.bed_amount"
                  :error-messages="
                    errors && errors.bed_amount ? errors.bed_amount[0] : ''
                  "
                ></v-text-field>
              </v-col>-->
              <v-col md="12">
                <label class="col-form-label">Customer Request </label>
                <v-textarea
                  rows="3"
                  v-model="room.request"
                  :hide-details="true"
                  outlined
                ></v-textarea>
              </v-col>
            </v-row>
            <v-row>
              <div class="d-flex mt-4 primary--text">
                <v-icon color="primary" large>mdi-bed</v-icon>
                <span style="font-size:25px" class="ml-2 mt-1 ">
                  Rooms
                </span>
                <v-divider
                  class="ml-3 mt-6"
                  style="padding-top:1px"
                ></v-divider>
              </div>
            </v-row>
            <v-row class="px-5">
              <v-col md="12" class="mb-2">
                {{ selectedRooms.length }}
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
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="item.check_in"
                              v-bind="attrs"
                              :hide-details="true"
                              v-on="on"
                              dense
                              outlined
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
                          <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                              v-model="item.check_out"
                              v-bind="attrs"
                              :hide-details="true"
                              v-on="on"
                              dense
                              outlined
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
                        <label class="col-form-label"
                          >First Name <span class="text-danger">*</span></label
                        >
                        <v-text-field
                          dense
                          outlined
                          type="text"
                          v-model="customer.first_name"
                          :hide-details="!errors.first_name"
                          :error="errors.first_name"
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
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>

                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">Amount : </label>
                        {{ item.price }}
                      </v-col>

                      <v-col md="6" cols="12" sm="12">
                        <label class="col-form-label">Amount : </label>
                        {{ item.price }}
                      </v-col>
                    </v-row>
                  </div>
                </v-alert>
                <!-- ///////////////////////////////// -->
                <v-alert
                  border="left"
                  colored-border
                  color="deep-purple accent-4"
                  elevation="2"
                >
                  <div class="d-flex justify-space-between">
                    <h6 class="px-2 mt-3">
                      {{ temp.room_no || "---" }} -
                      {{ temp.room_type || "---" }}
                    </h6>
                    <div>
                      <v-btn color="primary" @click="get_available_rooms" small
                        >Select Room</v-btn
                      >
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
                              v-bind="attrs"
                              :hide-details="true"
                              v-on="on"
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
                              v-bind="attrs"
                              :hide-details="true"
                              v-on="on"
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
                        <label class="col-form-label"
                          >First Name <span class="text-danger">*</span></label
                        >
                        <v-text-field
                          dense
                          outlined
                          type="text"
                          v-model="customer.first_name"
                          :hide-details="!errors.first_name"
                          :error="errors.first_name"
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
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>
                      <v-col md="6" sm="12" cols="12" dense>
                        <label class="col-form-label">
                          Meals
                          <span class="text-danger">*</span>
                        </label>
                        <v-select
                          v-model="temp.meal"
                          :items="meals"
                          dense
                          outlined
                          :hide-details="!errors.meal"
                          :error="errors.type"
                          :error-messages="
                            errors && errors.meal ? errors.meal[0] : ''
                          "
                        ></v-select>
                      </v-col>
                      <v-col md="6" sm="12" cols="12" dense></v-col>

                      <v-col md="2" sm="12" cols="12" dense>
                        <label class="col-form-label">
                          Extra Bed
                        </label>
                        <v-checkbox
                          value="1"
                          v-model="isBed"
                          :hide-details="true"
                          class="pt-0  py-1 chk-align"
                        >
                        </v-checkbox>
                      </v-col>
                      <v-col md="4" sm="12" cols="12" dense v-if="isBed">
                        <label class="col-form-label">
                          Amount
                          <span class="text-danger">*</span>
                        </label>
                        <v-text-field
                          dense
                          outlined
                          type="number"
                          v-model="temp.bed_amount"
                          :hide-details="!errors.bed_amount"
                          :error="errors.bed_amount"
                          :error-messages="
                            errors && errors.bed_amount
                              ? errors.bed_amount[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </div>
                </v-alert>
              </v-col>
              <v-col md="12">
                <v-btn @click="add_room" color="primary" class="w-100 py-5">
                  <v-icon color="white" large>mdi-plus</v-icon>
                  Add Room
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
      <v-col md="3">
        <v-card>
          <h6 class="p-3">Summart</h6>
          <v-divider class="p-0 m-0" dense></v-divider>
          <v-container
            class="px-10"
            grid-list-xs
            style="background-color:#F9FAFD"
          >
            <v-row style="font-size:15px" dense>
              <v-col md="6"><b class="#F9FAFD--text">Customer</b></v-col>
              <v-col md="6" class="text-right">{{ customer.first_name }}</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Check In</b></v-col>
              <v-col md="6" class="text-right">{{ room.check_in }}</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Check Out</b></v-col>
              <v-col md="6" class="text-right">{{ room.check_out }}</v-col>
              <br /><br />
              <v-col md="6"><b class="#F9FAFD--text">Days</b></v-col>
              <v-col md="6" class="text-right">{{ getDays() }} </v-col>
              <v-col md="6"><b class="#F9FAFD--text">Bed</b></v-col>
              <v-col md="6" class="text-right">{{ room.bed_amount }}</v-col>
              <v-col md="12">
                <v-divider></v-divider>
              </v-col>
              <v-col md="6"><b class="#F9FAFD--text">Adult</b></v-col>
              <v-col md="6" class="text-right">4</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Child</b></v-col>
              <v-col md="6" class="text-right">2</v-col>
              <v-col md="6"><b class="#F9FAFD--text">Baby</b></v-col>
              <v-col md="6" class="text-right">1</v-col>
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
            style="background-color:#F9FAFD"
          >
            <v-row>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Discount</label>
                <v-text-field
                  @keyup="runAllFunctions"
                  type="number"
                  dense
                  :hide-details="true"
                  outlined
                  v-model="room.discount"
                ></v-text-field>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <label class="col-form-label">Advance Price</label>
                <v-text-field
                  @keyup="runAllFunctions"
                  dense
                  :hide-details="true"
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
                  outlined
                  @change="getType(room.type)"
                  :hide-details="!errors.payment_mode_id"
                  :error="errors.payment_mode_id"
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

            <v-row style="font-size:15px" dense>
              <v-col cols="12" class="payment-table">
                <table>
                  <tr colspan="2">
                    <th>Rooms Payment Summary</th>
                  </tr>
                  <tr v-for="(item, index) in selectedRooms" :key="index">
                    <td>{{ item.room_no }}</td>
                    <td>
                      <div align="right">{{ item.price }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      <hr class="p-0 m-0" />
                    </td>
                  </tr>
                  <tr>
                    <td>Total</td>
                    <td>
                      <div align="right">{{ room.all_room_Total_amount }}</div>
                    </td>
                  </tr>
                </table>
              </v-col>
              <v-col md="12" class="text-right"><v-divider></v-divider></v-col>
            </v-row>

            <v-row style="font-size:15px" dense>
              <v-col cols="12" class="payment-table">
                <table>
                  <tr>
                    <td style="width:250px">Total Days</td>
                    <td style="width:250px">
                      <div align="right">{{ getDays() }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Room Price</td>
                    <td>
                      <div align="right">{{ room.price }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <!-- Sub Total (Total Days x Room Price) ({{ getDays() }} x -->
                      Sub Total ({{ getDays() }} x {{ room.price }})
                    </td>
                    <td>
                      <div align="right">{{ room.sub_total }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Discount</td>
                    <td>
                      <div align="right">{{ room.discount }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>After Discount</td>
                    <td>
                      <div align="right">{{ room.after_discount }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Sales Tax</td>
                    <td>
                      <div align="right">{{ room.sales_tax }}</div>
                    </td>
                  </tr>

                  <tr>
                    <th>Total</th>
                    <td>
                      <div align="right">{{ room.total_price }}</div>
                    </td>
                  </tr>

                  <tr>
                    <td>Advance Payment</td>
                    <td>
                      <div align="right">{{ room.advance_price }}</div>
                    </td>
                  </tr>
                  <tr>
                    <td>Remaining Amount</td>
                    <td>
                      <div align="right">{{ room.remaining_price }}</div>
                    </td>
                  </tr>
                </table>
              </v-col>
              <v-col md="12" class="text-right"><v-divider></v-divider></v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-col>
      <v-col md="1" style="width:300px">
        <pre>
          {{ selectedRooms }}
        </pre>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data() {
    return {
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
      isBed: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      roomTypes: [],
      types: ["Online", "Walking", "Travel Agency", "Complimentary"],
      meals: [
        "Breakfast",
        "Breakfast and Dinner",
        "Breakfast and Lunch",
        "Full Board",
        "Room only"
      ],
      search: {
        mobile: ""
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],

      sources: [
        "MakeMyTrip",
        "OYO Rooms",
        "Airbnb.co.in",
        "Expedia.co.in",
        "Trivago.in",
        "Yatra",
        "Cleartrip",
        "in.hotels.com",
        "Booking.com",
        "TripAdvisor.in"
      ],
      idCards: [],

      temp: {
        //check in calender
        check_in_menu: false,
        //check out calender
        check_out_menu: false
      },

      //check in calender
      check_in_menu: false,
      //check out calender
      check_out_menu: false,

      // check_in: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      //   .toISOString()
      //   .substr(0, 10),
      // check_out: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
      //   .toISOString()
      //   .substr(0, 10),

      upload: {
        name: ""
      },

      member_numbers: [1, 2, 3, 4],

      isOnline: false,
      isAgent: false,
      search_available_room: "",

      room: {
        all_room_Total_amount: 0,
        type: "",
        source: "",
        agent_name: "",
        room_type: "",
        room_id: "",
        check_in: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
        check_out: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
          .toISOString()
          .substr(0, 10),
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

        amount: 0,
        price: 0,

        company_id: 0
      },
      reservation: {},
      customer: {
        remark: "",
        meal: "",
        customer_type: "",
        type: "",
        status: "",
        first_name: "",
        last_name: "",
        contact_no: "",
        email: "",
        id_card_type_id: "",
        id_card_no: "",
        car_no: "",
        no_of_adult: 0,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        company_id: ""
      },

      errors: []
    };
  },
  created() {
    this.get_reservation();
    this.preloader = false;
    this.get_room_types();
    this.get_id_cards();
    this.get_id_cards();
    this.runAllFunctions();
  },

  methods: {
    runAllFunctions() {
      this.getDays();
      this.subTotal();
      this.afterDiscount();
      this.getAmountAfterSalesTax();
      this.getTotal();
      this.getRemainingAmount();
    },

    getDays() {
      let ci = new Date(this.room.check_in);
      let co = new Date(this.room.check_out);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24) + 1;
      if (days > 0) {
        return (this.room.total_days = days);
      }
    },

    getAmountAfterSalesTax() {
      let amount = this.afterDiscount();
      let per = amount < 3000 ? 12 : 18;
      return (this.room.sales_tax = this.getPercentage(amount, per).toFixed(0));
    },

    afterDiscount() {
      this.room.after_discount = this.subTotal() - this.room.discount;
      return this.room.after_discount;
    },

    getTotal() {
      return (this.room.total_price =
        parseInt(this.getAmountAfterSalesTax()) +
        this.subTotal() -
        this.room.discount);
    },

    getRemainingAmount() {
      return (this.room.remaining_price =
        this.getTotal() - this.room.advance_price);
    },

    getPercentage(amount, clause) {
      return (amount / 100) * clause;
    },

    subTotal() {
      return (this.room.sub_total = this.reservation.price * this.getDays());
    },

    get_all_room_Total_amount() {
      let tot = this.selectedRooms.reduce(
        (a, b) => parseInt(a.price) + parseInt(b.price)
      );
      typeof tot == "number"
        ? (this.room.all_room_Total_amount = tot)
        : (this.room.all_room_Total_amount = tot.price);

      console.log(this.room.all_room_Total_amount);
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

    get_room(val) {
      let room_type = this.roomTypes.find(e => e.id == val);
      this.room.price = room_type.price;
      this.$axios.get(`get_room/${val}`).then(({ data }) => {
        this.rooms = data;
      });
      this.runAllFunctions();
    },

    get_reservation() {
      this.reservation = this.$store.state.reservation;

      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type;
      this.temp.price = this.reservation.price;
      this.temp.check_in = this.reservation.check_in;
      this.temp.check_out = this.reservation.check_out;
    },

    remove_select_room(index) {
      this.selectedRooms.splice(index, 1);
    },

    selectRoom(item) {
      this.RoomDrawer = false;
      this.temp.room_no = item.room_no;
      this.temp.room_type = item.room_type.name;
      this.temp.price = item.price;
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

    add_room() {
      this.selectedRooms.push(this.temp);
      this.temp = {};
      this.get_all_room_Total_amount();
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
          check_out: this.temp.check_out
        }
      };
      this.RoomDrawer = true;
      this.$axios
        .get(`get_available_rooms_by_date`, payload)
        .then(({ data }) => {
          // console.log(data);
          this.availableRooms = data;
        });
      this.runAllFunctions();
    },

    get_customer() {
      this.errors = [];
      this.checkLoader = true;
      let contact_no = this.search.mobile;
      console.log(contact_no);
      if (contact_no == undefined || contact_no == "") {
        alert("Enter contact number");
        this.checkLoader = false;
        return;
      }

      this.$axios.get(`get_customer/${contact_no}`).then(({ data }) => {
        if (!data.status) {
          this.checkLoader = false;
          this.customer = {};
          this.customer.contact_no = contact_no;
          alert("Customer not found");
          return;
        }

        this.customer = {
          ...data.data,
          customer_id: data.data.id
        };
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
      let payload = {
        ...this.room,
        company_id: this.$auth.user.company.id
      };
      console.log(payload);
      this.$axios
        .post("/booking_validate", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
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
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.store_booking(data.record.id);
          }
        })
        .catch(e => console.log(e));
    },
    store_booking(id) {
      let payload = {
        ...this.room,
        customer_id: id,
        company_id: this.$auth.user.company.id
      };

      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
          }
        })
        .catch(e => console.log(e));
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
/* .v-text-field__slot input {
  color: red !important;
} */

/* v-input__slot */
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
</style>
