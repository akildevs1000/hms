<template>
  <div v-if="can('calendar_create')">
    <v-dialog v-model="documentDialog" max-width="30%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Add ID</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="documentDialog = false"
            >mdi-close</v-icon
          >
        </v-toolbar>
        <v-row>
          <v-col md="12" sm="12" cols="12" dense>
            <v-select
              v-model="customer.id_card_type_id"
              :items="idCards"
              dense
              label="ID Card Type"
              outlined
              item-text="name"
              item-value="id"
              :hide-details="errors && !errors.id_card_type_id"
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
              v-model="customer.id_card_no"
              :hide-details="errors && !errors.id_card_no"
              :error-messages="
                errors && errors.id_card_no ? errors.id_card_no[0] : ''
              "
            ></v-text-field>
          </v-col>
          <v-col md="12" cols="12" sm="12">
            <v-menu
              v-model="customer.passport_expiration_menu"
              :close-on-content-click="false"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="customer.passport_expiration"
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
                v-model="customer.passport_expiration"
                @input="customer.passport_expiration_menu = false"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col md="12">
            <v-file-input
              v-model="customer.document"
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
                  +{{ customer.document.length - 2 }} File(s)
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
              class="float-right primary pt-4 pb-4"
              @click="store_document_new()"
            >
              Submit
              <v-icon right dark>mdi-file</v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
    <v-dialog v-model="imgView" max-width="80%">
      <v-card>
        <v-toolbar class="rounded-md" color="background" dense flat dark>
          <span>Preview</span>
          <v-spacer></v-spacer>
          <v-icon dark class="pa-0" @click="imgView = false">
            mdi-close
          </v-icon>
        </v-toolbar>
        <v-container>
          <ImagePreview :docObj="documentObj"></ImagePreview>
        </v-container>
        <v-card-actions> </v-card-actions>
      </v-card>
    </v-dialog>
    <v-card>
      <v-container>
        <v-row>
          <v-col md="12">
            <v-tabs v-model="activeTab" background-color="primary" dark>
              <div class="py-3">
                <span class="mx-2">New Reservation</span>
              </div>
              <v-spacer></v-spacer>
              <v-tab>
                <v-icon> mdi mdi-account-tie </v-icon>
              </v-tab>
              <v-tab>
                <v-icon> mdi mdi-bed </v-icon>
              </v-tab>
              <v-tab v-if="customer.id > 0">
                <v-icon> mdi mdi-clipboard-text-clock </v-icon>
              </v-tab>
              <v-icon class="pa-2" @click="$emit(`close-dialog`)">
                mdi-close
              </v-icon>
              <v-tabs-slider color="#1259a7"></v-tabs-slider>
              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col md="2" cols="12">
                        <v-img
                          style="
                            width: 150px;
                            height: 150px;
                            margin: 0 auto;
                            border-radius: 50%;
                          "
                          :src="
                            customer.captured_photo || '/no-profile-image.jpg'
                          "
                        ></v-img>
                      </v-col>
                      <v-col md="10" cols="12">
                        <v-row>
                          <v-col md="2" class="mt-0">
                            <v-btn color="primary" @click="searchDialog = true">
                              Search
                              <v-icon right dark>mdi-magnify</v-icon>
                            </v-btn>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-select
                              v-model="customer.title"
                              :items="titleItems"
                              label="Title *"
                              dense
                              item-text="name"
                              item-value="name"
                              :hide-details="errors && !errors.title"
                              :error-messages="
                                errors && errors.title ? errors.title[0] : ''
                              "
                              outlined
                            ></v-select>
                          </v-col>

                          <v-col md="3" dense>
                            <v-autocomplete
                              label="Business Source"
                              v-model="customer.customer_type"
                              :items="business_sources"
                              dense
                              item-text="name"
                              item-value="name"
                              outlined
                              :hide-details="true"
                            ></v-autocomplete>
                          </v-col>
                          <v-col md="2"></v-col>

                          <v-col md="4" cols="12" sm="12">
                            <v-text-field
                              label="First Name *"
                              dense
                              outlined
                              type="text"
                              v-model="customer.first_name"
                              :hide-details="errors && !errors.first_name"
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
                              label="Email *"
                              outlined
                              type="email"
                              v-model="customer.email"
                              :hide-details="errors && !errors.email"
                              :error-messages="
                                errors && errors.email ? errors.email[0] : ''
                              "
                            ></v-text-field>
                          </v-col>
                          <v-col md="4" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="Contact No *"
                              outlined
                              max="1111111111111"
                              type="number"
                              v-model="customer.contact_no"
                              :hide-details="errors && !errors.contact_no"
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
                              max="1111111111111"
                              type="number"
                              v-model="customer.whatsapp"
                              :hide-details="errors && !errors.whatsapp"
                              :error-messages="
                                errors && errors.whatsapp
                                  ? errors.whatsapp[0]
                                  : ''
                              "
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
                                v-model="customer.dob"
                                @input="customer.dob_menu = false"
                              ></v-date-picker>
                            </v-menu>
                          </v-col>
                        </v-row>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col md="3" cols="12" sm="12">
                        <v-select
                          v-model="customer.nationality"
                          :items="countryList"
                          label="Nationality"
                          item-text="name"
                          item-value="name"
                          :hide-details="errors && !errors.nationality"
                          :error-messages="
                            errors && errors.nationality
                              ? errors.nationality[0]
                              : ''
                          "
                          dense
                          outlined
                        ></v-select>
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
                          dense
                          label="Car Number"
                          outlined
                          :hide-details="true"
                          type="text"
                          v-model="customer.car_no"
                        ></v-text-field>
                      </v-col>
                      <v-col md="3" cols="12" sm="12">
                        <v-text-field
                          dense
                          outlined
                          label="GST"
                          type="text"
                          v-model="customer.gst_number"
                          :hide-details="errors && !errors.gst_number"
                          :error-messages="
                            errors && errors.gst_number
                              ? errors.gst_number[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                    </v-row>

                    <v-row>
                      <v-col md="3" sm="12" cols="12" dense>
                        <v-select
                          v-model="customer.id_card_type_id"
                          :items="idCards"
                          dense
                          label="ID Card Type"
                          outlined
                          item-text="name"
                          item-value="id"
                          :hide-details="errors && !errors.id_card_type_id"
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
                          label="ID Card"
                          outlined
                          type="text"
                          v-model="customer.id_card_no"
                          :hide-details="errors && !errors.id_card_no"
                          :error-messages="
                            errors && errors.id_card_no
                              ? errors.id_card_no[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <FullAddress @location="handleFullAddress" />
                    <v-row>
                      <!-- <v-col md="6" cols="12" sm="12">
                          <v-textarea
                            rows="3"
                            label="Address"
                            v-model="customer.address"
                            outlined
                            :hide-details="true"
                          ></v-textarea>
                        </v-col> -->
                      <v-col md="12">
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
                      <v-col md="3" sm="12" cols="12" dense>
                        <v-select
                          v-model="room.type"
                          label="Source Type *"
                          :items="types"
                          dense
                          outlined
                          @change="getType(room.type)"
                          :hide-details="errors && !errors.type"
                          :error-messages="
                            errors && errors.type ? errors.type[0] : ''
                          "
                        ></v-select>
                      </v-col>
                      <v-col md="3" cols="12" sm="12" v-if="isAgent">
                        <v-select
                          dense
                          label="Agent Name"
                          outlined
                          :items="agentList"
                          type="text"
                          @change="get_gst(room.source, 'agent')"
                          item-value="name"
                          item-text="name"
                          v-model="room.source"
                          :hide-details="errors && !errors.source"
                          :error-messages="
                            errors && errors.source ? errors.source[0] : ''
                          "
                        ></v-select>
                      </v-col>
                      <v-col md="3" sm="12" cols="12" dense v-if="isOnline">
                        <v-select
                          v-model="room.source"
                          label="Source"
                          :items="sources"
                          dense
                          @change="get_gst(room.source, 'online')"
                          outlined
                          item-value="name"
                          item-text="name"
                          :hide-details="errors && !errors.source"
                          :error-messages="
                            errors && errors.source ? errors.source[0] : ''
                          "
                        ></v-select>
                      </v-col>
                      <v-col md="3" sm="12" cols="12" dense v-if="isCorporate">
                        <v-select
                          v-model="room.source"
                          label="Corporate"
                          :items="CorporateList"
                          dense
                          outlined
                          @change="get_gst(room.source, 'corporate')"
                          item-value="name"
                          item-text="name"
                          :hide-details="errors && !errors.source"
                          :error-messages="
                            errors && errors.source ? errors.source[0] : ''
                          "
                        ></v-select>
                      </v-col>
                      <v-col
                        md="3"
                        cols="12"
                        sm="12"
                        v-if="isAgent || isOnline || isCorporate"
                      >
                        <v-text-field
                          label="Reference Number"
                          dense
                          outlined
                          type="text"
                          v-model="room.reference_no"
                          :hide-details="errors && !errors.reference_no"
                          :error-messages="
                            errors && errors.reference_no
                              ? errors.reference_no[0]
                              : ''
                          "
                        ></v-text-field>
                      </v-col>
                      <v-col
                        md="3"
                        sm="12"
                        cols="12"
                        dense
                        v-if="isAgent || isOnline || isCorporate"
                      >
                        <v-select
                          v-model="room.paid_by"
                          label="Paid Type"
                          :items="[
                            { name: 'Paid at Hotel', value: '1' },
                            { name: 'Paid by Agents', value: '2' },
                          ]"
                          dense
                          outlined
                          item-value="value"
                          item-text="name"
                          :hide-details="errors && !errors.paid_by"
                          :error-messages="
                            errors && errors.paid_by ? errors.paid_by[0] : ''
                          "
                        ></v-select>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" class="text-right">
                        <v-btn small @click="store" color="primary">Next</v-btn>
                      </v-col>
                    </v-row>
                  </v-card-text>
                </v-card>
              </v-tab-item>

              <v-tab-item>
                <v-card flat>
                  <v-card-text>
                    <v-row>
                      <v-col md="12" cols="12">
                        <v-alert colored-border elevation="0">
                          <div class="mt-3">
                            <!-- <pre>{{ priceListTableView }}</pre> -->
                            <v-row>
                              <v-col
                                md="12"
                                cols="12"
                                class="d-flex py-0 my-0 justify-center"
                              >
                                <table
                                  class="styled-table py-0 my-0"
                                  style="width: 100%"
                                >
                                  <thead>
                                    <tr>
                                      <td><small>Date</small></td>
                                      <td><small>Day</small></td>
                                      <td><small>Room Type</small></td>
                                      <td><small>Type</small></td>
                                      <td><small>Adult</small></td>
                                      <td><small>Child</small></td>
                                      <td><small>Meal</small></td>
                                      <td><small>Tariff</small></td>
                                      <!-- <td><small>Early Checkin</small></td>
                                      <td><small>Late Checkout</small></td>
                                      <td><small>Extra Bed</small></td> -->
                                      <td><small>Total</small></td>
                                      <td><small>Action</small></td>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr
                                      v-for="(
                                        item, index
                                      ) in priceListTableView"
                                      :key="index"
                                    >
                                      <td>
                                        {{ item.date }}
                                      </td>
                                      <td>
                                        {{ item.day }}
                                      </td>
                                      <td>
                                        {{ item.room_type }}
                                      </td>
                                      <td>
                                        {{ item.day_type }}
                                      </td>
                                      <td>{{ item.no_of_adult }}</td>
                                      <td>{{ item.no_of_child }}</td>
                                      <td>{{ item.meal_name }}</td>
                                      <td>
                                        {{ convert_decimal(item.price) }}
                                      </td>
                                      <!-- <td>
                                        {{
                                          convert_decimal(item.early_check_in)
                                        }}
                                      </td>
                                      <td>
                                        {{
                                          convert_decimal(item.late_check_out)
                                        }}
                                      </td>
                                      <td>
                                        {{ convert_decimal(item.bed_amount) }}
                                      </td> -->
                                      <td>
                                        {{ convert_decimal(item.total_price) }}
                                      </td>
                                      <td class="text-center">
                                        <v-row no-gutters>
                                          <v-col>
                                            <RoomDetails
                                              :selectedRooms="selectedRooms"
                                            />
                                          </v-col>
                                          <v-col>
                                            <v-icon
                                              small
                                              color="orange"
                                              @click="RoomDrawer = true"
                                              >mdi-pencil</v-icon
                                            >
                                          </v-col>
                                          <v-col>
                                            <v-icon
                                              small
                                              color="red"
                                              @click="deleteItem(index)"
                                              >mdi-close</v-icon
                                            >
                                          </v-col>
                                        </v-row>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </v-col>
                              <v-col
                                md="12"
                                style="padding-top: 0px; font-weight: bold"
                              >
                                <div
                                  class="d-flex justify-space-around py-3 styled-table"
                                  style="margin-top: 5px"
                                >
                                  <v-col cols="10" class="text-right">
                                    <div>Sub Total:</div>
                                    <div>Add :</div>
                                    <div>Discount :</div>
                                    <v-divider color="#4390FC"></v-divider>
                                    <div>Total :</div>
                                    <div>Advance :</div>
                                    <div>Balance :</div>
                                  </v-col>
                                  <v-col cols="2" class="text-right">
                                    <div>
                                      {{ convert_decimal(subTotal()) }}
                                    </div>

                                    <div>
                                      {{
                                        convert_decimal(temp.room_extra_amount)
                                      }}
                                    </div>
                                    <div style="color: red">
                                      -{{ convert_decimal(temp.room_discount) }}
                                    </div>

                                    <v-divider color="#4390FC"></v-divider>
                                    <div>
                                      {{
                                        convert_decimal(processCalculation())
                                      }}
                                    </div>
                                    <div>
                                      {{ convert_decimal(room.advance_price) }}
                                    </div>
                                    <div>
                                      {{ convert_decimal(room.remaining_price) }}
                                    </div>
                                    
                                  </v-col>
                                </div>
                                <v-divider color="#4390FC"></v-divider>
                              </v-col>
                              <v-col md="3" sm="12" cols="12" dense>
                                <v-select
                                  label="Discount/Extra"
                                  v-model="extraPayType"
                                  :items="['Discount', 'ExtraAmount']"
                                  dense
                                  :hide-details="true"
                                  outlined
                                ></v-select>
                              </v-col>
                              <v-col
                                md="4"
                                sm="12"
                                cols="12"
                                dense
                                v-if="extraPayType == 'Discount'"
                              >
                                <v-text-field
                                  label="Discount Amount"
                                  dense
                                  outlined
                                  type="number"
                                  v-model="temp.room_discount"
                                  :hide-details="true"
                                  @keyup="processCalculation"
                                ></v-text-field>
                              </v-col>
                              <v-col
                                md="4"
                                sm="12"
                                cols="12"
                                dense
                                v-if="extraPayType == 'Discount'"
                              >
                                <v-text-field
                                  label="Reason"
                                  dense
                                  outlined
                                  type="text"
                                  v-model="temp.discount_reason"
                                  :hide-details="true"
                                ></v-text-field>
                              </v-col>
                              <v-col
                                md="4"
                                sm="12"
                                cols="12"
                                dense
                                v-if="extraPayType == 'ExtraAmount'"
                              >
                                <v-text-field
                                  label="Extra Amount"
                                  dense
                                  outlined
                                  type="number"
                                  v-model="temp.room_extra_amount"
                                  @keyup="processCalculation"
                                  :hide-details="true"
                                ></v-text-field>
                              </v-col>
                              <v-col
                                md="4"
                                sm="12"
                                cols="12"
                                dense
                                v-if="extraPayType == 'ExtraAmount'"
                              >
                                <v-text-field
                                  label="Reason"
                                  dense
                                  outlined
                                  type="text"
                                  v-model="temp.extra_amount_reason"
                                  :hide-details="true"
                                ></v-text-field>
                              </v-col>
                            </v-row>

                            <v-row>
                              <v-col md="12" class="text-right">
                                <v-btn
                                  small
                                  class="primary"
                                  @click="advanceDialog = true"
                                >
                                  <v-icon small>mdi-wallet</v-icon> Pay
                                </v-btn>
                                <IDPreviewForDirectCheckIn
                                  @response="$emit(`close-dialog`)"
                                  ref="id_preview"
                                  :RoomId="temp.room_id"
                                  :BookingId="BookingId"
                                />
                                <v-btn
                                  small
                                  style="background-color: #5fafa3"
                                  @click="store_booking"
                                  :loading="subLoad"
                                  dark
                                  ><v-icon small>mdi-floppy</v-icon>
                                  Submit</v-btn
                                >
                                <!-- <v-btn
                                  color="primary"
                                  @click="RoomDrawer = true"
                                  small
                                >
                                  <v-icon color="white" small>mdi-pencil</v-icon>
                                  Edit Room
                                </v-btn> -->
                              </v-col>
                            </v-row>
                          </div>
                        </v-alert>
                      </v-col>
                    </v-row>
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
                <span class="error--text">*</span>
              </label>
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
            <v-icon right dark>mdi mdi-magnify</v-icon>
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
            <div class="input-group input-group-sm px-3">
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="width: 220px !important; color: black !important"
              >
                <v-autocomplete
                  v-model="room.payment_mode_id"
                  :items="[
                    { id: 1, name: 'Cash' },
                    { id: 2, name: 'Card' },
                    { id: 3, name: 'Online' },
                    { id: 4, name: 'Bank' },
                    { id: 5, name: 'UPI' },
                    { id: 6, name: 'Cheque' },
                  ]"
                  cache-items
                  item-text="name"
                  item-value="id"
                  class="ma-0 pa-0"
                  dense
                  flat
                  hide-no-data
                  hide-details
                  solo
                  elevation="0"
                  background-color="#E9ECEF"
                  style="color: black !important"
                >
                </v-autocomplete>
              </span>
              <input
                type="number"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 48px"
                @keyup="runAllFunctions"
                :disabled="room.paid_by == '2' ? true : false"
                v-model="room.advance_price"
              />
            </div>

            <div
              class="input-group input-group-sm px-3"
              v-if="room.payment_mode_id != 1"
            >
              <span
                class="input-group-text"
                id="inputGroup-sizing-sm"
                style="width: 220px !important"
              >
                Reference No
              </span>
              <input
                v-model="room.reference_number"
                type="text"
                class="form-control"
                aria-label="Sizing example input"
                aria-describedby="inputGroup-sizing-sm"
                style="height: 39px"
              />
            </div>
          </v-row>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            @click="
              (e) => {
                selectedRooms.length > 0
                  ? (advanceDialog = false)
                  : alert('oops', 'Select room');
              }
            "
          >
            Pay
            <!-- <v-icon right dark>mdi mdi-magnify</v-icon> -->
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="RoomDrawer" max-width="400">
      <v-card>
        <v-toolbar flat class="primary white--text" dense>
          Individual Booking <v-spacer></v-spacer
          ><v-icon @click="RoomDrawer = false" color="white"
            >mdi-close</v-icon
          ></v-toolbar
        >
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-menu
                v-model="checkin_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check In"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="formattedCheckinDate"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  :min="new Date().toISOString().substr(0, 10)"
                  v-model="temp.check_in"
                  no-title
                  @input="checkin_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-menu
                v-model="checkout_menu"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    label="Check Out"
                    append-icon="mdi-calendar"
                    outlined
                    dense
                    hide-details
                    v-model="formattedCheckOutDate"
                    persistent-hint
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="temp.check_out"
                  no-title
                  @input="checkout_menu = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                readonly
                label="Room Type"
                outlined
                dense
                hide-details
                item-value="id"
                item-text="name"
                v-model="room_type_id"
                :items="roomTypes"
                return-object
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                readonly
                v-model="multipleRoomId"
                hide-details
                :items="availableRooms"
                item-value="id"
                item-text="room_no"
                label="Select Room"
                dense
                outlined
                return-object
              >
              </v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Adult Per Room"
                :items="[0, 1, 2, 3]"
                dense
                outlined
                v-model="temp.no_of_adult"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="6">
              <v-autocomplete
                label="Child Per Room"
                :items="[0, 1, 2, 3]"
                dense
                outlined
                v-model="temp.no_of_child"
                :hide-details="true"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                label="Food Plan"
                outlined
                dense
                hide-details
                item-value="id"
                item-text="title"
                v-model="temp.food_plan_id"
                :items="foodplans"
                @change="
                  selectRoom({ name: temp.room_type, room_no: temp.room_no })
                "
              ></v-autocomplete>
            </v-col>
            <!-- <v-col cols="12">
              <v-row>
                <v-col cols="4">
                  <v-checkbox
                    v-model="temp.breakfast"
                    readonly
                    outlined
                    dense
                    hide-details
                    label="Breakfast"
                  ></v-checkbox>
                </v-col>
                <v-col cols="4">
                  <v-checkbox
                    v-model="temp.lunch"
                    readonly
                    outlined
                    dense
                    hide-details
                    label="Lunch"
                  ></v-checkbox>
                </v-col>
                <v-col cols="4">
                  <v-checkbox
                    v-model="temp.dinner"
                    readonly
                    outlined
                    dense
                    hide-details
                    label="Dinner"
                  ></v-checkbox>
                </v-col>
              </v-row>
            </v-col> -->
            <v-col cols="6">
              <v-checkbox
                v-model="is_early_check_in"
                label="Early Check In"
                :hide-details="true"
                dense
                @change="set_additional_charges"
              >
              </v-checkbox>
            </v-col>
            <v-col cols="6"
              ><v-checkbox
                v-model="is_late_check_out"
                label="Late Check Out"
                :hide-details="true"
                dense
                @change="set_additional_charges"
              >
              </v-checkbox>
            </v-col>
            <v-col cols="12">
              <v-text-field
                label="Extra Bed"
                min="0"
                dense
                outlined
                type="number"
                v-model="temp.extra_bed_qty"
                :hide-details="true"
                @keyup="set_additional_charges"
              ></v-text-field>
            </v-col>
            <v-col cols="12">
              <v-btn block @click="add_room(temp)" color="primary" small>
                Confirm Room
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
  <NoAccess v-else />
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["reservation"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      BookingId: 0,
      customerDocs: null,
      additional_charges: {},
      is_early_check_in: false,
      is_late_check_out: false,
      dialog: false,
      foodplans: [],
      multipleRoomObjects: [],
      multipleRoomId: null,
      checkin_menu: false,
      checkout_menu: false,
      room_type_id: 1,
      documentDialog: false,
      // -------customer history---------------
      customer: "",
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,
      advanceDialog: false,
      selectRoomLoading: false,
      roomTab: null,
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
      // ----------------------
      vertical: false,
      activeTab: 0,
      activeSummaryTab: 0,
      // ------------------

      purposes: [
        "Tour",
        "Business",
        "Hospital",
        "Holiday",
        "Party/Functions",
        "Friend Visit",
        "Marriage",
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
      isSelectRoom: true,
      isBed: false,
      subLoad: false,
      isDiscount: false,
      isExtra: false,
      snackbar: false,
      checkLoader: false,
      response: "",
      preloader: false,
      loading: false,
      show_password: false,
      show_password_confirm: false,
      roomTypes: [],
      types: [
        "Online",
        "Walking",
        "Travel Agency",
        "Complimentary",
        "Corporate",
      ],

      search: {
        mobile: "",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],

      agentList: [],
      CorporateList: [],
      // room_extra_amount: 0,
      idCards: [],
      imgView: false,
      priceListTableView: [],

      temp: {
        food_plan_price: 1,
        extra_bed_qty: 0,
        food_plan_id: 1,
        early_check_in: 0,
        late_check_out: 0,
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: today.toISOString().split("T")[0], // format as YYYY-MM-DD
        check_out: tomorrow.toISOString().split("T")[0], // format as YYYY-MM-DD
        // meal: [],
        bed_amount: 0,
        room_extra_amount: 0,
        extra_amount_reason: "",
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
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
        priceList: [],
      },
      merge_food_in_room_price: "",
      gst_calculation: {
        recal_basePrice: 0,
        recal_gst_percentage: 0,
        recal_gst_total: 0,
        recal_final: 0,
      },
      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
      },
      member_numbers: [1, 2, 3, 4],
      isOnline: false,
      isCorporate: false,
      isAgent: false,
      isDiff: false,
      search_available_room: "",
      room: {
        customer_type: "",
        customer_status: "",
        all_room_Total_amount: 0, // sum of temp.totals
        total_extra: 0,
        type: "Walking",
        source: "walking",
        agent_name: "",
        booking_status: 1,
        check_in: null,
        check_out: null,
        discount: 0,
        reference_number: "",
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
        purpose: "Tour",
        // priceList: [],
      },

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

      customer: {
        customer_type: "Walking",
        title: "Mr",
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
        //  new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        //   .toISOString()
        //   .substr(0, 10)
      },
      id_card_type_id: 0,
      errors: [],
      tempAdult: {
        tot_ab: 0,
        tot_al: 0,
        tot_ad: 0,
      },
      tempChild: {
        tot_cb: 0,
        tot_cl: 0,
        tot_cd: 0,
      },

      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
      extraPayType: "",
      allFood: [],

      breakfast: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      lunch: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      dinner: {
        adult: 0,
        child: 0,
        baby: 0,
      },

      documentObj: {
        fileExtension: null,
        file: null,
      },
      business_sources: [],
    };
  },
  async created() {
    await this.get_food_plans();

    this.get_reservation();
    this.get_room_types();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
    this.get_Corporate();
    this.get_available_rooms({ id: this.room_type_id });
    this.preloader = false;

    await this.get_additional_charges();

    await this.get_business_sources();
  },
  computed: {
    formattedCheckinDate() {
      if (!this.temp.check_in) return "";

      const date = new Date(this.temp.check_in);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 12:00`;
    },
    formattedCheckOutDate() {
      if (!this.temp.check_out) return "";

      const date = new Date(this.temp.check_out);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day} 11:00`;
    },
    showImage() {
      if (!this.customer.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },
  },
  methods: {
    async get_business_sources() {
      let config = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      let { data } = await this.$axios.get("business-source-list", config);
      this.business_sources = data;
    },
    handleFullAddress(e) {
      this.customer = {
        ...this.customer,
        ...e,
      };
    },
    deleteItem(index) {
      this.priceListTableView.splice(index, 1);
      this.selectedRooms.splice(index, 1);
    },
    set_additional_charges() {
      this.temp.early_check_in = this.is_early_check_in
        ? this.additional_charges.early_check_in || 0
        : 0;
      this.temp.late_check_out = this.is_late_check_out
        ? this.additional_charges.late_check_out || 0
        : 0;

      this.temp.bed_amount = this.temp.extra_bed_qty
        ? this.temp.extra_bed_qty * (this.additional_charges.extra_bed || 0)
        : 0;
    },
    async get_additional_charges() {
      let { data } = await this.$axios.get(`additional_charges`, {
        params: {
          company_id: this.$auth.user.company_id,
        },
      });

      this.additional_charges = data;
    },
    async get_food_plans() {
      let { data: foodplans } = await this.$axios.get(`foodplan-list`);

      this.foodplans = foodplans;
    },
    addOneDay(originalDate) {
      this.get_available_rooms({ id: this.room_type_id });
      this.selectRoom({
        name: this.temp.room_type,
        room_no: this.temp.room_no,
      });

      if (!originalDate) {
        return new Date().toISOString().substr(0, 10);
      }
      const date = new Date(originalDate);

      date.setDate(date.getDate() + 1);

      return date.toISOString().split("T")[0];
    },

    nextTab() {
      this.add_room(this.temp);
    },
    store_document_new() {
      this.documentDialog = false;
      return;
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

    preview(file) {
      if (file.name) {
        file = file.name;
      }
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    runAllFunctions() {
      this.getDays();
      this.subTotal();
      this.processCalculation();
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
      this.room_type_id = this.reservation.room_type_id;

      this.multipleRoomId = {
        id: this.reservation.id,
        room_no: this.reservation.room_no,
      };
      this.temp.room_id = this.reservation.id;

      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type.name;
      this.room.check_in = this.temp.check_in;
      this.room.check_out = this.temp.check_out;
      this.temp.priceList = this.reservation.priceList;
    },

    redirect() {
      this.$router.push("/");
    },

    mergeContact() {
      if (!this.isDiff) {
        this.customer.whatsapp = this.customer.contact_no;
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

    processCalculation() {
      let discount = parseFloat(this.temp.room_discount) || 0;
      let room_extra_amount = parseFloat(this.temp.room_extra_amount) || 0;
      let sub_total = parseFloat(this.room.sub_total) || 0;

      let advance_price = parseFloat(this.room.advance_price) || 0;

      let afterExtraAmount = sub_total + room_extra_amount;
      let afterDiscount = afterExtraAmount - discount;

      this.room.remaining_price = afterDiscount - advance_price;

      return (this.room.total_price = afterDiscount);
    },

    subTotal() {
      return (this.room.sub_total = this.priceListTableView.reduce(
        (total, num) => total + num.total_price,
        0
      ));
    },

    getType(val) {
      if (val == "Online") {
        this.isOnline = true;
        this.isCorporate = false;
        this.isAgent = false;
        return;
      }
      if (val == "Travel Agency") {
        this.isCorporate = false;
        this.isOnline = false;
        this.isAgent = true;
        return;
      }
      if (val == "Corporate") {
        this.isOnline = false;
        this.isAgent = false;
        this.isCorporate = true;
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
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
    },

    get_gst(item, type) {
      // agent
      // online
      // corporate

      switch (type) {
        case "agent":
          this.customer.gst_number = this.agentList.find(
            (e) => e.name == item
          ).gst;
          break;
        case "online":
          this.customer.gst_number = this.sources.find(
            (e) => e.name == item
          ).gst;
          break;
        case "corporate":
          this.customer.gst_number = this.CorporateList.find(
            (e) => e.name == item
          ).gst;
          break;
        default:
          break;
      }
    },

    get_agents() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_agent`, payload).then(({ data }) => {
        this.agentList = data;
      });
    },
    get_Corporate() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_corporate`, payload).then(({ data }) => {
        this.CorporateList = data;
      });
    },

    get_online() {
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_online`, payload).then(({ data }) => {
        this.sources = data;
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
    searchAvailableRoom(val) {
      let arr = this.availableRooms;
      let res = arr.filter((e) => e.room_no == val);
      if (val.length == 0) {
        this.get_available_rooms();
        return;
      }
      if (res.length > 0) {
        this.availableRooms = res;
      }
    },

    get_cs_gst(amount) {
      let gst = parseFloat(amount) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
    },
    selectRoom(item) {
      this.selectRoomLoading = true;

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: item.name,
          room_no: item.room_no,
          checkin: this.temp.check_in,
          checkout: this.temp.check_out,
        },
      };

      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          this.selectRoomLoading = false;
          this.temp.room_type = item.name;
          this.temp.company_id = this.$auth.user.company.id;
          this.temp.price = data.total_price;
          this.temp.priceList = data.data;
          this.temp.room_tax = data.total_tax;

          this.get_cs_gst(data.total_tax);
          this.add_room(this.temp);
        });
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
    },

    getFoodCalculation({ no_of_adult, no_of_child, food_plan_id }) {
      let selectedFP = this.foodplans.find((e) => e.id == food_plan_id);

      if (!selectedFP) {
        return null;
      }

      let total_members = no_of_adult + no_of_child;

      let { title, unit_price } = selectedFP;

      let food_plan_price =
        unit_price * no_of_adult + (unit_price * no_of_child) / 2;

      return {
        meal: "------",
        meal_name: title,
        food_plan_price: food_plan_price,
        breakfast: selectedFP.breakfast ? total_members : 0,
        lunch: selectedFP.lunch ? total_members : 0,
        dinner: selectedFP.dinner ? total_members : 0,
      };
    },

    add_room({
      room_type,
      price,
      early_check_in,
      late_check_out,
      room_discount,
      room_extra_amount,
      bed_amount,
      priceList,
      no_of_adult,
      no_of_child,
    }) {
      if (this.room.type == "") {
        this.alert("oops", "Select Source Type", "error");
        return;
      }
      if (!this.room_type_id || !this.multipleRoomId) {
        this.alert("Error!", "Room type or Room not selected", "error");
        return;
      }

      let selected_food_plan = this.getFoodCalculation(this.temp);

      if (!selected_food_plan) {
        this.alert("Error!", "Food plan not found!", "error");
        return;
      }

      let extras =
        early_check_in +
        late_check_out +
        bed_amount +
        selected_food_plan.food_plan_price;

      let sub_total =
        extras +
        price +
        parseFloat(room_extra_amount == "" ? 0 : room_extra_amount);

      let after_discount =
        sub_total - (room_discount == "" ? 0 : room_discount);

      let room_no = this.multipleRoomId.room_no;

      this.room.check_in = this.temp.check_in;
      this.room.check_out = this.temp.check_out;

      this.runAllFunctions();
      // this.alert("Success!", "success selected room", "success");
      this.isSelectRoom = false;
      this.RoomDrawer = false;

      let arrToMerge = priceList.map((e) => ({
        ...e,
        ...selected_food_plan,
        room_type,
        no_of_adult,
        no_of_child,
        early_check_in,
        late_check_out,
        bed_amount,
        total_price:
          e.price +
          early_check_in +
          late_check_out +
          bed_amount +
          selected_food_plan.food_plan_price,
      }));

      this.priceListTableView = arrToMerge;

      // console.log(after_discount);return;
      let meal_price =
        selected_food_plan.food_plan_price * this.priceListTableView.length;

      let payload = {
        ...this.temp,
        ...selected_food_plan,
        food_plan_price: meal_price,
        days: this.getDays(),
        room_discount: room_discount == "" ? 0 : room_discount,
        after_discount:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        total_price:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        grand_total:
          price + early_check_in + late_check_out + bed_amount + meal_price,
        room_no: this.multipleRoomId.room_no,
        room_id: this.multipleRoomId.id,
      };

      this.selectedRooms = [payload];
      this.activeTab += 1;
    },

    get_available_rooms(item) {
      // if (this.temp.check_in == undefined || this.temp.check_out == undefined) {
      //   alert("Please select date");
      //   this.RoomDrawer = false;
      //   return;
      // }

      this.$axios
        .get(`get_available_rooms_by_date_and_room_type`, {
          params: {
            check_in: this.temp.check_in,
            check_out: this.temp.check_out,
            room_type_id: item.id,
            company_id: this.$auth.user.company.id,
          },
        })
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
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
        },
      };

      this.$axios
        .get(`get_customer/${contact_no}`, payload)
        .then(({ data }) => {
          if (!data.status) {
            this.checkLoader = false;
            // this.customer = {};
            this.customer.contact_no = contact_no;
            this.customer.whatsapp = contact_no;
            alert("Customer not found");
            return;
          }

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

    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    store() {
      this.$axios
        .post("/customer-validate", this.customer)
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
            this.selectRoom({
              name: this.temp.room_type,
              room_no: this.temp.room_no,
            });
          }
        })
        .catch((e) => console.log(e));
    },

    store_booking() {
      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }

      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.alert("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
      }

      if (this.reservation.booking_status == 2) {
        this.room.booking_status = 2;
      }

      let rooms = this.selectedRooms.map((e) => e.room_no);
      this.room.rooms = rooms.toString();
      let payload = {
        ...this.room,
        customer_type: this.customer.customer_type,
        selectedRooms: this.selectedRooms,
        ...this.customer,
        user_id: this.$auth.user.id,
      };

      this.subLoad = false;

      this.$axios
        .post("/group-booking", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
            return;
          }

          this.BookingId = data.data;
          this.$refs["id_preview"].idPreviewPopup = true;
        })
        .catch((e) => console.log(e));
    },

    store_document(id) {
      let payload = new FormData();
      payload.append("document", this.room.document);
      payload.append("image", this.customer.image);
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
        .catch((e) => console.log(e));
    },

    alert(title = "Success!", message = "hello", type = "error") {
      this.$swal(title, message, type);
    },
  },
};
</script>
