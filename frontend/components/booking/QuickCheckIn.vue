<template>
  <div>
    <v-dialog v-model="dialog">
      <template v-slot:activator="{ on, attrs }">
        <div style="text-align: center">
          <v-btn
            dense
            x-small
            v-bind="attrs"
            v-on="on"
            class="text-center"
            title="Quick"
            color="#34444c"
            style="width: 37px; height: 26px"
          >
            <v-icon color="white">mdi-clock-fast</v-icon>
          </v-btn>
          <div style="font-size: 10px; text-align: center">Quick</div>
        </div>
      </template>
      <!-- <template v-slot:activator="{ on, attrs }">
        <div style="text-align: center">
          <v-btn
            dense
            x-small
            v-bind="attrs"
            v-on="on"
            class="text-center"
            title="Quick Checkin"
            color="#34444c"
            style="width: 37px; height: 26px"
          >
            <v-icon color="white">mdi-google-classroom</v-icon>
            <span v-if="!onlyButton">Quick Checkin</span>
          </v-btn>
          <div v-if="onlyButton" style="font-size: 10px; text-align: center">
            Quick Checkin
          </div>
        </div>
      </template> -->

      <v-dialog v-model="imgView">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>ID</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="imgView = false"
              >mdi mdi-close-box</v-icon
            >
          </v-toolbar>
          <v-container>
            <ImagesImagePreview :docObj="documentObj" />
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog v-model="documentDialog" max-width="30%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Add ID</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="documentDialog = false"
              >mdi mdi-close-box</v-icon
            >
          </v-toolbar>
          <v-container class="pa-5">
            <v-row>
              <v-col md="12" sm="12" cols="12" dense>
                <v-select
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
                ></v-select>
              </v-col>
              <v-col md="12" cols="12" sm="12">
                <v-text-field
                  dense
                  label="ID Card"
                  outlined
                  type="text"
                  v-model="checkIn.id_card_no"
                  :hide-details="errors && !errors.id_card_no"
                  :error="errors && errors.id_card_no"
                  :error-messages="
                    errors && errors.id_card_no ? errors.id_card_no[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col md="12" cols="12" sm="12">
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
                    v-model="checkIn.exp"
                    @input="checkIn.exp_menu = false"
                  ></v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="12">
                <v-file-input
                  v-model="checkIn.checkIn_document"
                  color="primary"
                  counter
                  placeholder="Select your files"
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
                      +{{ checkIn.checkIn_document.length - 2 }} File(s)
                    </span>
                  </template>
                </v-file-input>
              </v-col>
              <v-divider></v-divider>
              <v-col md="12">
                <!-- @click="documentDialog = false" -->

                <v-btn
                  small
                  dark
                  class="primary pt-4 pb-4"
                  @click="store_document(BookingData.id)"
                >
                  Submit
                  <v-icon right dark>mdi-file</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-card>
        <v-container fluid>
          <v-row class="m-0 p-0 mt-1">
            <v-col md="8">
              <v-tabs
                v-model="activeTab"
                :vertical="vertical"
                background-color="primary"
                dark
                show-arrows
              >
                <div class="py-3" style="background-color: #1259a7">
                  <span class="mx-2">Check In</span>
                </div>
                <v-spacer></v-spacer>

                <v-tab active-class="active-link">
                  <v-icon> mdi mdi-account-tie </v-icon>
                </v-tab>
                <v-tab active-class="active-link" v-if="customer.id > 0">
                  <v-icon> mdi mdi-clipboard-text-clock </v-icon>
                </v-tab>
                <v-tabs-slider color="#1259a7"></v-tabs-slider>
                <v-tab-item>
                  <v-card flat>
                    <v-card-text>
                      <v-row>
                        <v-col md="3" cols="12" class="text-center">
                          <!-- style="
                    width: 150px;
                    height: 150px;
                    margin: 0 auto;
                    border-radius: 50%;
                    " -->
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
                            v-if="errors && errors.image"
                            class="red--text mt-2"
                          >
                            {{ errors.image[0] }}</span
                          >
                          <div class="mt-2 ml-4" v-if="BookingData.document">
                            <v-btn
                              small
                              dark
                              class="primary ipad-preview lg-pt-4 lg-pb-4 doc-btn"
                              @click="preview(BookingData.document)"
                            >
                              ID
                              <v-icon right dark>mdi-file</v-icon>
                            </v-btn>
                          </div>
                          <div class="mt-2" v-else>
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
                        <v-col md="9" cols="12">
                          <v-row>
                            <v-col md="6" cols="12" sm="12">
                              <v-autocomplete
                                :items="groupList"
                                label="Group Name *"
                                dense
                                item-text="group_name"
                                item-value="id"
                                outlined
                                hide-details
                                @change="getBookedRoomList"
                                return-object
                              ></v-autocomplete>
                            </v-col>
                            <v-col md="6" cols="12" sm="12">
                              <v-autocomplete
                                multiple
                                v-model="BookingData.room_nos"
                                :items="rooms"
                                label="Rooms *"
                                dense
                                item-text="room_no"
                                item-value="room_no"
                                outlined
                                hide-details
                              ></v-autocomplete>
                            </v-col>

                            <v-col md="6" cols="12" sm="12">
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
                            <v-col md="6" cols="12" sm="12">
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
                            <v-col md="5" cols="12" sm="12">
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
                          </v-row>
                        </v-col>
                      </v-row>
                      <v-row>
                        <v-col md="4" cols="12" sm="12">
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
                        <v-col md="4">
                          <v-select
                            label="Purpose"
                            v-model="room.purpose"
                            :items="purposes"
                            dense
                            :hide-details="true"
                            outlined
                          ></v-select>
                        </v-col>
                      </v-row>

                      <v-row>
                        <v-col md="6" cols="12" sm="12">
                          <v-textarea
                            rows="3"
                            label="Address"
                            v-model="customer.address"
                            outlined
                            :hide-details="true"
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
                      </v-row>
                      <v-row>
                        <v-col cols="12" class="text-right">
                          <v-btn small @click="nextTab" color="primary"
                            >Next</v-btn
                          >
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </v-tab-item>
                <v-tab-item>
                  <v-card flat>
                    <v-card-text>
                      <CustomerHistory :customerId="customer.id" />
                    </v-card-text>
                  </v-card>
                </v-tab-item>
              </v-tabs>
            </v-col>

            <v-col md="4">
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
                          v-if="BookingData.room_nos.length"
                          type="text"
                          class="form-control"
                          aria-label="Sizing example input"
                          aria-describedby="inputGroup-sizing-sm"
                          disabled
                        >
                          {{ BookingData.room_nos.join(",") || "---" }}
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
                          {{ BookingData.advance_price }}
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
                          <strong>{{ BookingData.remaining_price }}</strong>
                        </div>
                      </div>
                      <v-card-actions class="pl-0 pr-2">
                        <v-btn
                          style="background-color: #4390fc; margin-right: 5px"
                          width="50%"
                          height="40"
                          @click="advanceDialog = true"
                          dark
                        >
                          Pay
                        </v-btn>
                        <v-btn
                          style="background-color: #5fafa3"
                          width="50%"
                          height="40"
                          @click="store_check_in"
                          :loading="loading"
                          dark
                          >CheckIn</v-btn
                        >
                      </v-card-actions>
                    </section>
                  </v-card>
                </v-tab-item>
                <!-- end room summary -->

                <v-tab-item v-for="(item, index) in selectedRooms" :key="index">
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
            </v-col>
          </v-row>
        </v-container>
      </v-card>

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
              mdi mdi-close-box
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
    </v-dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      BookingData: {
        id: 1,
        document: null,
        reservation_no: 1,
        customer_type: "Company",
        title: 1,
        contact_no: 1,
        check_in_date: "",
        check_out_date: "",
        total_days: 1,
        room_nos: [],
        total_price: 0,
        advance_price: 0,
        remaining_price: 0,
      },
      // ----------------------
      dialog: false,
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

      groupList: [],
      rooms: [],
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
    this.get_id_cards();
    this.getGroupList();
    this.preloader = false;
  },
  computed: {
    showImage() {
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
    getGroupList() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`booking`, config).then(({ data }) => {
        this.groupList = data.data;
      });
    },

    async getBookedRoomList(booking) {
      let url = `get-booked-room-list?booking_id=${booking.id}`;
      let { data } = await this.$axios.get(url);
      this.rooms = data;
      this.BookingData = {
        ...booking,
        room_nos: [],
      };
    },
    nextTab() {
      this.activeTab += 1;
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

    store_check_in() {
      this.loading = true;

      let payload = new FormData();
      payload.append("customer_id", this.BookingData.customer_id);
      payload.append("booking_id", this.BookingData.id);
      this.BookingData.room_nos.forEach((roomId, index) => {
        payload.append(`room_nos[${index}]`, roomId);
      });

      payload.append("remaining_price", this.BookingData.remaining_price);

      payload.append("new_payment", this.new_payment || 0);
      payload.append("payment_mode_id", this.room.payment_mode_id);
      payload.append("company_id", this.$auth.user.company.id);
      payload.append("id_card_type_id", this.checkIn.id_card_type_id);
      payload.append("id_card_no", this.checkIn.id_card_no);
      payload.append("expired", this.checkIn.exp);

      if (this.reference_number) {
        payload.append("reference_number", this.reference_number);
      }
      if (this.checkIn.checkIn_document) {
        payload.append("document", this.checkIn.checkIn_document);
      }

      payload.append("user_id", this.$auth.user.id);

      const fields = this.getCustomerFields();

      fields.forEach((field) => {
        if (this.guest[field]) {
          payload.append(field, this.customer[field]);
        }
      });

      this.$axios
        .post("/quick_check_in_room", payload)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
            this.loading = false;
          } else {
            this.$emit("success");
            this.closeDialog(data);
            this.alert("Success!", "success check in", "success");
            // this.redirect_to_invoice(bookingId);
            this.loading = false;
            this.dialog = false;
          }
        })
        .catch((e) => {
          this.loading = false;
          this.closeDialog(e.response.data);
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

    redirect_to_invoice(id) {
      console.log(id);
      let element = document.createElement("a");
      element.setAttribute("target", "_blank");
      element.setAttribute("href", `${process.env.BACKEND_URL}grc/${id}`);
      document.body.appendChild(element);
      element.click();
    },

    closeDialog(data) {
      this.$emit("close-dialog", data);
    },

    get_customer() {
      this.errors = [];
      this.errorsForSubCustomer = [];
      this.checkLoader = true;
      let customer_id = this.BookingData.customer_id;
      // if (contact_no == undefined || contact_no == "") {
      //   // alert("Enter contact number");
      //   this.checkLoader = false;
      //   return;
      // }
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

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
    },

    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store_document(id) {
      let payload = new FormData();
      // payload.append("document", this.room.document);
      payload.append("document", this.checkIn.checkIn_document);
      payload.append("image", this.customer.image);
      payload.append("booking_id", id);

      this.$axios
        .post("/store_document", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.documentDialog = false;
          }
        })
        .catch((e) => console.log(e));
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
