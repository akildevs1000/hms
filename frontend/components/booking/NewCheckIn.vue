<template>
  <div>
    <v-row class="mt-3">
      <v-dialog v-model="imgView" max-width="80%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Preview</span>
            <v-spacer></v-spacer>
            <v-icon dark class="pa-0" @click="imgView = false">
              mdi mdi-close-box
            </v-icon>
          </v-toolbar>
          <v-container>
            <ImagePreview :docObj="documentObj"></ImagePreview>
          </v-container>
          <v-card-actions> </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="documentDialog" max-width="30%">
        <v-card>
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <span>Add Document</span>
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
                  v-model="customer.id_card_no"
                  :hide-details="errors && !errors.id_card_no"
                  :error="errors && errors.id_card_no"
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
                  class="primary pt-4 pb-4"
                  @click="store_document_new()"
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

      <v-col md="8">
        <v-tabs
          v-model="activeTab"
          :vertical="vertical"
          background-color="primary"
          dark
          show-arrows
        >
          <v-tab active-class="active-link">
            <v-icon> mdi mdi-account-tie </v-icon>
          </v-tab>
          <v-tab active-class="active-link">
            <v-icon> mdi mdi-bed </v-icon>
          </v-tab>
          <v-tab active-class="active-link" v-if="customer.id > 0">
            <v-icon> mdi mdi-clipboard-text-clock </v-icon>
          </v-tab>
          <v-tabs-slider color="#1259a7"></v-tabs-slider>
          <v-tab-item>
            <v-card flat>
              <v-card-text>
                <v-row>
                  <!-- <v-col md="2" cols="12">
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
                    <span v-if="errors && errors.image" class="red--text mt-2">
                      {{ errors.image[0] }}</span
                    >
                    <div class="mt-2 ml-4" v-if="customer.document">
                      <v-btn
                        small
                        dark
                        class="primary pt-4 pb-4"
                        @click="preview(customer.document)"
                      >
                        Preview
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                    </div>
                  </v-col> -->

                  <v-col md="2" cols="12">
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
                    <span v-if="errors && errors.image" class="red--text mt-2">
                      {{ errors.image[0] }}</span
                    >
                    <div class="mt-2 ml-4" v-if="getDocType(customer.document)">
                      <v-btn
                        small
                        dark
                        class="pridmary lg-pt-4 lg-pb-4 doc-btn"
                        @click="preview(customer.document)"
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
                        @click="documentDialog = true"
                      >
                        <small>Document</small>
                        <v-icon right dark>mdi-plus</v-icon>
                      </v-btn>
                    </div>
                  </v-col>

                  <v-col md="10" cols="12">
                    <v-row>
                      <v-col md="2" class="mt-0">
                        <v-btn color="primary" @click="searchDialog = true">
                          Search
                          <v-icon right dark>mdi mdi-magnify</v-icon>
                        </v-btn>
                      </v-col>
                      <v-col md="5" dense> </v-col>
                      <v-col md="5" dense>
                        <v-select
                          label="Type"
                          v-model="customer.customer_type"
                          :items="['Company', 'Regular', 'Corporate']"
                          dense
                          item-text="name"
                          item-value="id"
                          outlined
                          :hide-details="true"
                        ></v-select>
                      </v-col>
                      <v-col md="2" cols="12" sm="12">
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
                      <v-col md="5" cols="12" sm="12">
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
                            errors && errors.whatsapp ? errors.whatsapp[0] : ''
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
                  <v-col md="4" sm="12" cols="12" dense>
                    <v-menu
                      v-model="check_out_date_menu"
                      :close-on-content-click="false"
                      :nudge-right="40"
                      transition="scale-transition"
                      offset-y
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          v-model="check_out_date"
                          readonly
                          v-on="on"
                          label="Checkout Date"
                          v-bind="attrs"
                          :hide-details="true"
                          dense
                          outlined
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="check_out_date"
                        @input="check_out_date_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                  <v-col md="4" cols="12" sm="12">
                    <v-text-field
                      dense
                      outlined
                      label="GST"
                      type="text"
                      v-model="customer.gst_number"
                      :hide-details="errors && !errors.gst_number"
                      :error="errors && errors.gst_number"
                      :error-messages="
                        errors && errors.gst_number ? errors.gst_number[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col md="4" cols="12" sm="12">
                    <v-text-field
                      dense
                      label="Car Number"
                      outlined
                      :hide-details="true"
                      type="text"
                      v-model="customer.car_no"
                    ></v-text-field>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col md="12">
                    <v-textarea
                      rows="3"
                      label="Customer Request"
                      v-model="room.request"
                      :hide-details="true"
                      outlined
                    ></v-textarea>
                  </v-col>
                  <v-col md="12" cols="12" sm="12">
                    <v-textarea
                      rows="3"
                      label="Address"
                      v-model="customer.address"
                      outlined
                      :hide-details="true"
                    ></v-textarea>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" class="text-right">
                    <v-btn x-small @click="nextTab" color="primary">Next</v-btn>
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
                    <v-alert
                      border="left"
                      colored-border
                      color="deep-purple accent-4"
                      elevation="2"
                    >
                      <div class="d-flex justify-space-between">
                        <h4 class="px-2 mt-3">
                          {{ temp.room_no || "---" }} -
                          {{ temp.room_type || "---" }}
                        </h4>
                        <div>
                          <v-btn
                            color="primary"
                            @click="get_available_rooms"
                            small
                          >
                            <v-icon color="white" small>mdi-plus</v-icon>
                            Add Room
                          </v-btn>
                          <v-btn icon>
                            <v-icon>mdi-dots-vertical</v-icon>
                          </v-btn>
                        </div>
                      </div>
                      <v-divider class="p-0 m-0" dense></v-divider>
                      <div class="mt-3">
                        <v-row>
                          <v-col md="4" sm="12" cols="12" dense>
                            <label class="col-form-label"
                              >Adult <span class="text-danger">*</span>
                            </label>

                            <div class="wrapper">
                              <span
                                class="minus"
                                @mouseup="
                                  get_food_price_cal(
                                    'adult',
                                    temp.no_of_adult == 0
                                      ? 0
                                      : --temp.no_of_adult
                                  )
                                "
                                @click="
                                  temp.no_of_adult < 1 || temp.no_of_adult
                                "
                                >-</span
                              >
                              <span class="num">{{ temp.no_of_adult }}</span>
                              <span
                                class="plus"
                                @mouseup="
                                  get_food_price_cal(
                                    'adult',
                                    temp.no_of_adult < 4
                                      ? ++temp.no_of_adult
                                      : 4
                                  )
                                "
                                @click="
                                  temp.no_of_adult > 3 || temp.no_of_adult
                                "
                                >+</span
                              >
                            </div>
                          </v-col>
                          <v-col md="4" sm="12" cols="12" dense>
                            <label class="col-form-label">Child </label>
                            <div class="wrapper">
                              <span
                                class="minus"
                                @mouseup="
                                  get_food_price_cal(
                                    'child',
                                    temp.no_of_child == 0
                                      ? 0
                                      : --temp.no_of_child
                                  )
                                "
                                @click="
                                  temp.no_of_child < 1 || temp.no_of_child
                                "
                                >-</span
                              >
                              <span class="num">{{ temp.no_of_child }}</span>
                              <span
                                class="plus"
                                @mouseup="
                                  get_food_price_cal(
                                    'child',
                                    temp.no_of_child < 3
                                      ? ++temp.no_of_child
                                      : 3
                                  )
                                "
                                @click="
                                  temp.no_of_child > 1 || temp.no_of_child
                                "
                                >+</span
                              >
                            </div>
                          </v-col>
                          <v-col md="4" sm="12" cols="12" dense>
                            <label class="col-form-label">Baby </label>
                            <div class="wrapper">
                              <span
                                class="minus"
                                @mouseup="
                                  get_food_price_cal(
                                    'baby',
                                    temp.no_of_baby == 0 ? 0 : --temp.no_of_baby
                                  )
                                "
                                @click="temp.no_of_baby < 1 || temp.no_of_baby"
                                >-</span
                              >
                              <span class="num">{{ temp.no_of_baby }}</span>
                              <span
                                class="plus"
                                @mouseup="
                                  get_food_price_cal(
                                    'baby',
                                    temp.no_of_baby < 1 ? ++temp.no_of_baby : 1
                                  )
                                "
                                @click="temp.no_of_baby > 1 || temp.no_of_baby"
                                >+</span
                              >
                            </div>
                          </v-col>
                          <v-col md="6" sm="12" cols="12" dense>
                            <label class="col-form-label"> Meals </label>
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
                          <v-col md="6" cols="12">
                            <table class="food-table">
                              <tr
                                class="food-table"
                                style="background-color: #4390fc; color: white"
                              >
                                <th class="food-table" style="width: 100px">
                                  Type
                                </th>
                                <td class="food-table" style="width: 100px">
                                  Breakfast
                                </td>
                                <td class="food-table" style="width: 100px">
                                  Lunch
                                </td>
                                <td class="food-table" style="width: 100px">
                                  Dinner
                                </td>
                              </tr>
                              <tr class="food-table">
                                <th class="food-table">Adult</th>
                                <td class="food-table">
                                  {{ tempAdult.tot_ab }}
                                </td>
                                <td class="food-table">
                                  {{ tempAdult.tot_al }}
                                </td>
                                <td class="food-table">
                                  {{ tempAdult.tot_ad }}
                                </td>
                              </tr>
                              <tr class="food-table">
                                <th class="food-table">Child</th>
                                <td class="food-table">
                                  {{ tempChild.tot_cb }}
                                </td>
                                <td class="food-table">
                                  {{ tempChild.tot_cl }}
                                </td>
                                <td class="food-table">
                                  {{ tempChild.tot_cd }}
                                </td>
                              </tr>
                              <tr>
                                <th colspan="2" style="text-align: left">
                                  Room Price
                                </th>
                                <td colspan="2" style="text-align: right">
                                  {{ temp.price }}
                                </td>
                              </tr>
                            </table>
                          </v-col>
                          <v-col md="6" cols="12">
                            <table class="food-table">
                              <tr
                                class="food-table"
                                style="background-color: #4390fc; color: white"
                              >
                                <th class="food-table" style="width: 100px">
                                  Date
                                </th>
                                <td class="food-table" style="width: 100px">
                                  Day
                                </td>
                                <td class="food-table" style="width: 100px">
                                  Type
                                </td>
                                <td class="food-table" style="width: 100px">
                                  Tax
                                </td>
                                <td class="food-table" style="width: 100px">
                                  Amount
                                </td>
                              </tr>
                              <tr
                                class="food-table"
                                v-for="(item, index) in temp.priceList"
                                :key="index"
                              >
                                <td class="food-table">
                                  {{ item.date }}
                                </td>
                                <td class="food-table">
                                  {{ item.day }}
                                </td>
                                <td class="food-table">
                                  {{ item.day_type }}
                                </td>
                                <td class="food-table">
                                  {{ convert_decimal(item.tax) }}
                                </td>
                                <td class="food-table">
                                  {{ convert_decimal(item.price) }}
                                </td>
                              </tr>
                              <tr>
                                <td colspan="5"><hr /></td>
                              </tr>
                              <tr>
                                <td>Total</td>
                                <td></td>
                                <td></td>
                                <td class="food-table">
                                  {{ convert_decimal(temp.room_tax) }}
                                </td>
                                <td class="food-table">
                                  {{ convert_decimal(temp.price) }}
                                </td>
                              </tr>
                            </table>
                          </v-col>
                          <v-col md="6"> </v-col>
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
                          <v-col
                            md="4"
                            sm="12"
                            cols="12"
                            dense
                            v-if="isDiscount"
                          >
                            <label class="col-form-label"> Amount </label>
                            <v-text-field
                              dense
                              outlined
                              type="number"
                              v-model="temp.room_discount"
                              :hide-details="true"
                            ></v-text-field>
                          </v-col>
                          <v-col
                            md="4"
                            sm="12"
                            cols="12"
                            dense
                            v-if="isDiscount"
                          >
                            <label class="col-form-label"> Reason </label>
                            <v-text-field
                              dense
                              outlined
                              type="text"
                              v-model="temp.discount_reason"
                              :hide-details="true"
                            ></v-text-field>
                          </v-col>
                        </v-row>

                        <v-row>
                          <v-col md="12">
                            <v-btn
                              @click="add_room"
                              class="float-right"
                              color="primary"
                            >
                              <v-icon color="white" small>mdi-plus</v-icon>
                              Confirm Room
                            </v-btn>
                          </v-col>
                        </v-row>
                      </div>
                    </v-alert>
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" class="text-right">
                    <v-btn x-small @click="prevTab" dark color="background">
                      Back
                    </v-btn>
                    <!-- <v-btn
                      x-small
                      @click="store"
                      :loading="subLoad"
                      color="primary"
                      >Submit</v-btn
                    > -->
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

      <v-col md="4">
        <v-tabs
          color="primary"
          v-model="activeSummaryTab"
          :vertical="vertical"
          background-color="primary"
          dark
          show-arrows
        >
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
          <v-tab-item>
            <v-card flat>
              <v-divider class="px-5 py-0"></v-divider>
              <section>
                <div class="input-group input-group-sm px-5 py-0">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Name
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ customer.first_name || "---" }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Contact
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ customer.contact_no || "---" }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Check In
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ temp.check_in || "---" }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Check Out
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ temp.check_out || "---" }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Days
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ getDays() }}
                  </div>
                </div>
                <div class="input-group input-group-sm mb-2 px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    No. Rooms
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ selectedRooms.length || 0 }}
                  </div>
                </div>
              </section>
              <!-- <p class="px-5 py-0" style="font-size: 16px; color: #aaaaaa">
                Payment
              </p> -->
              <v-divider class="px-5 py-0"></v-divider>
              <section class="payment-section pt-0">
                <v-row class="px-5 mt-0">
                  <div class="input-group input-group-sm px-3">
                    <span class="input-group-text" id="inputGroup-sizing-sm">
                      <!-- <v-select
                      elevation="0"
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
                        dense
                        solo
                        :disabled="room.paid_by == '2' ? true : false"
                        @change="getType(room.type)"
                        :hide-details="errors && !errors.payment_mode_id"
                        :error="errors && errors.payment_mode_id"
                        :error-messages="
                          errors && errors.payment_mode_id
                            ? errors.payment_mode_id[0]
                            : ''
                        "
                      ></v-select> -->

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
                        solo-inverted
                        background-color="#E9ECEF"
                      ></v-autocomplete>
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
                    <span class="input-group-text" id="inputGroup-sizing-sm">
                      Reference No
                    </span>
                    <input
                      type="text"
                      class="form-control"
                      aria-label="Sizing example input"
                      aria-describedby="inputGroup-sizing-sm"
                      style="height: 39px"
                    />
                    <!-- v-model="room.reference_no" -->
                  </div>

                  <!-- <v-col md="12" cols="12" sm="12">
                    <v-text-field
                      label="Advance Amount"
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
                    <v-select
                      label="Payment Mode"
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
                  </v-col> -->
                  <v-col md="12">
                    <v-divider></v-divider>
                  </v-col>
                </v-row>

                <!-- <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Room Price
                  </span>
                  <div type="text" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm" disabled>
                    {{ convert_decimal(room.all_room_Total_amount) }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Sub Total ({{ getDays() }} x
                    {{ convert_decimal(room.all_room_Total_amount) }})
                  </span>
                  <div type="text" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm" disabled>
                    {{ convert_decimal(room.sub_total) }}
                  </div>
                </div> -->
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Total
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ convert_decimal(room.total_price) }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    Advance Payment
                  </span>
                  <div
                    type="text"
                    class="form-control"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    {{ room.advance_price }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5 mb-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
                    <strong>Balance Amount</strong>
                  </span>
                  <div
                    type="text"
                    class="form-control red--text"
                    aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-sm"
                    disabled
                  >
                    <strong>{{ convert_decimal(room.remaining_price) }}</strong>
                  </div>
                </div>
                <div class="input-group input-group-sm px-3 mb-5">
                  <v-btn
                    style="background-color: #5fafa3"
                    width="100%"
                    height="60"
                    @click="store"
                    :loading="subLoad"
                    dark
                    >Book</v-btn
                  >
                </div>
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
                <div class="input-group input-group-sm px-5 pt-2 text-center">
                  <v-card class="pa-2" style="width: 25%" outlined tile>
                    <div class="d-flex justify-space-between">
                      <span class="pa-0 m-0" style="width: 33.33%" outlined>
                        {{ item.no_of_adult }}|
                      </span>
                      <span class="pa-0 m-0" style="width: 33.33%" outlined>
                        {{ item.no_of_child }} |
                      </span>
                      <span class="pa-0 m-0" style="width: 33.33%" outlined>
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

                <!-- <div class="input-group input-group-sm px-5 pt-2 text-center">
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ item.no_of_adult }}
                  </v-card>
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ item.no_of_child }}
                  </v-card>
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ item.no_of_baby }}
                  </v-card>
                </div>

                <div class="input-group input-group-sm px-5 pt-2 text-center">
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ getMealSeparate(item.meal)[0] }}
                  </v-card>
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ getMealSeparate(item.meal)[1] }}
                  </v-card>
                  <v-card class="pa-2" style="width:33.33%" outlined tile>
                    {{ getMealSeparate(item.meal)[2] }}
                  </v-card>
                </div> -->

                <div class="input-group input-group-sm px-5 pt-2">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                      convert_decimal(item.tot_adult_food + item.tot_child_food)
                    }}
                  </div>
                </div>
                <div class="input-group input-group-sm px-5">
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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
                  <span class="input-group-text" id="inputGroup-sizing-sm">
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

    <!---------------------------------------------------------------->

    <v-dialog v-model="searchDialog" width="500">
      <v-card>
        <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="12" cols="12" sm="12">
              <label class="col-form-label"
                >Search By Mobile Number
                <span class="text-danger">*</span>
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
    <v-navigation-drawer
      v-model="RoomDrawer"
      temporary
      right
      :fixed="true"
      :clipped="true"
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
  </div>
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";

export default {
  props: ["reservation"],
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      // -------customer history---------------
      customer: "",
      bookings: "",
      revenue: "",
      city_ledger: "",
      payments: "",
      bookedRooms: "",
      loading: false,
      documentDialog: false,
      check_out_date_menu: false,
      check_out_date: null,

      checkIn: {
        id_card_type_id: "",
        id_card_no: "",
        exp: "",
        checkIn_document: null,
      },

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
        mobile: "",
      },
      availableRooms: [],
      selectedRooms: [],
      rooms: [],
      sources: [],

      agentList: [],

      idCards: [],
      imgView: false,
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
        dinner: "",
        tot_adult_food: 0,
        tot_child_food: 0,
        discount_reason: "",
      },

      check_in_menu: false,
      check_out_menu: false,
      upload: {
        name: "",
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
        type: "Walking",
        source: "walking",
        agent_name: "",
        check_in: null,
        check_out: null,
        discount: 0,
        advance_price: 0,
        payment_mode_id: 1,
        total_days: 0,
        sub_total: 0,
        booking_status: 2,
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
      // reservation: {},
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

      meals: [
        { name: "Room only", slug: "room_only_price" },
        { name: "Breakfast", slug: "Break_fast_price" },
        { name: "Breakfast and Dinner", slug: "Break_fast_with_dinner_price" },
        { name: "Breakfast and Lunch", slug: "Break_fast_with_lunch_price" },
        { name: "Full Board", slug: "full_board_price" },
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
        passport_expiration_menu: false,
        no_of_child: 0,
        no_of_baby: 0,
        address: "",
        passport_expiration: null,
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
    };
  },

  watch: {
    reservation() {
      this.get_next_day();
    },
    check_out_date() {
      this.selectedRooms = [];
      this.get_reservation();
      this.get_total_amounts();
      this.get_all_room_Total_amount();
      this.runAllFunctions();
    },
  },

  created() {
    this.get_next_day();
    this.get_food_price();
    this.get_room_types();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
    this.preloader = false;
  },

  mounted() {
    this.get_next_day();
  },

  computed: {
    showImage() {
      if (!this.customer.image && !this.previewImage) {
        return "/no-profile-image.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.customer.image;
    },

    currentDate() {
      return new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10);
    },
  },
  methods: {
    nextTab() {
      this.activeTab += 1;
    },

    prevTab() {
      if (this.activeTab > 0) {
        this.activeTab -= 1;
      }
    },

    getDocType(doc) {
      return typeof doc == "string" ? true : false;
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
      const fileExtension = file.split(".").pop().toLowerCase();
      fileExtension == "pdf" ? (this.isPdf = true) : (this.isImg = true);
      this.documentObj = {
        fileExtension: fileExtension,
        file: file,
      };
      this.imgView = true;
    },

    getMealSeparate(meal) {
      const mealsString = this.capsTitle(meal);
      const mealsArray = mealsString.split("|");
      return mealsArray;
    },

    runAllFunctions() {
      this.getDays();
      this.subTotal();
      // this.afterDiscount();
      // this.getAmountAfterSalesTax();
      this.getTotal();
      this.getRemainingAmount();

      // this.convert_decimal(this.room.advance_price);
    },

    get_each_date_price() {
      //       {
      //     "id": 37,
      //     "room_type_id": 6,
      //     "room_no": "102",
      //     "status": "0",
      //     "deleteStatus": 0,
      //     "company_id": 2,
      //     "created_at": null,
      //     "background": "#f48665",
      //     "price": "2800.00",
      //     "room_type": {
      //         "id": 6,
      //         "name": "queen",
      //         "price": "2800.00"
      //     }
      // }
      this.reservation;
      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: this.reservation.room_type.name,
          room_no: this.reservation.obj.room_no,
          checkin: this.reservation.check_in,
          checkout: this.reservation.check_out,
        },
      };
      console.log(payload);
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          this.reservation.room_id = data.room.id;
          this.reservation.price = data.total_price;
          this.reservation.priceList = data.data;
          let commitObj = {
            ...this.reservation,
          };
          console.log(commitObj);
          this.$store.commit("reservation", commitObj);
          this.$router.push(`/hotel/new2`);
        });
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

    get_next_day() {
      const today = new Date();
      const tomorrow = new Date(today);
      tomorrow.setDate(tomorrow.getDate() + 1);
      this.check_out_date = tomorrow.toISOString().substr(0, 10);
      console.log("get_day" + this.check_out_date);

      this.get_reservation();
    },

    store_document_new() {
      this.documentDialog = false;
      return;

      let payload = new FormData();
      // payload.append("document", this.room.document);
      payload.append("document", this.checkIn.checkIn_document);
      payload.append("image", this.customer.image);
      // payload.append("booking_id", id);

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

    get_cs_gst(amount) {
      let gst = parseFloat(amount) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
    },

    get_reservation() {
      this.temp.room_id = this.reservation.id;
      this.temp.room_no = this.reservation.room_no;
      this.temp.room_type = this.reservation.room_type.name;
      this.temp.check_in = this.currentDate;
      this.temp.check_out = this.check_out_date;
      this.room.check_in = this.currentDate;
      this.room.check_out = this.check_out_date;
      this.get_cs_gst(this.temp.room_tax);

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: this.reservation.room_type.name,
          room_no: this.reservation.room_no,
          checkin: this.currentDate,
          checkout: this.check_out_date,
        },
      };
      console.log(payload);
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          this.temp.price = data.total_price;
          this.temp.priceList = data.data;
          this.temp.room_tax = this.get_room_tax(this.temp.price);
        });
    },

    get_reservation1() {
      // this.reservation = this.$store.state.reservation;
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
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`get_food_prices`, payload).then(({ data }) => {
        this.foodPriceList = data;
        this.get_food_price_cal("adult", 1);
      });
    },

    // preview(file) {
    //   let element = document.createElement("a");
    //   element.setAttribute("target", "_blank");
    //   element.setAttribute("href", file);
    //   document.body.appendChild(element);
    //   element.click();
    //   // document.body.removeChild(element);
    // },

    redirect() {
      this.$router.push("/");
    },

    get_food_price_cal(person_type, person_qty) {
      if (this.foodPriceList.length == 0) {
        return;
      }
      let person = this.foodPriceList.find((e) => e.type == person_type);

      person.qty = person_qty;

      let index = this.person_type_arr.findIndex((e) => e.type == person_type);

      if (index == -1) {
        this.person_type_arr.push(person);
      } else {
        this.person_type_arr.splice(index, 1, person);
      }
    },

    meal_cal(meal_type) {
      this.person_type_arr.find((e) => {
        if (e.type == "adult") {
          this.get_adult_cal(e);
        }
        if (e.type == "child") {
          this.get_child_cal(e);
        }
        if (e.type == "baby") {
          this.get_baby_cal(e);
        }
      });
    },

    get_adult_cal(e) {
      let tab, tax_tab, tal, tax_tal, tad, tax_tad;
      if (this.temp.breakfast) {
        tab = parseFloat(e.breakfast) * parseFloat(e.qty);
        this.breakfast.adult = e.qty;
        tax_tab = this.get_amount_with_tax(tab);
      } else {
        this.breakfast.adult = 0;
      }
      if (this.temp.lunch) {
        tal = parseFloat(e.lunch) * parseFloat(e.qty);
        tax_tal = this.get_amount_with_tax(tal);
        this.lunch.adult = e.qty;
      } else {
        this.lunch.adult = 0;
      }
      if (this.temp.dinner) {
        tad = parseFloat(e.dinner) * parseFloat(e.qty);
        tax_tad = this.get_amount_with_tax(tad);
        this.dinner.adult = e.qty;
      } else {
        this.dinner.adult = 0;
      }

      this.tempAdult = {
        tot_ab: tax_tab + tab || 0,
        tot_al: tax_tal + tal || 0,
        tot_ad: tax_tad + tad || 0,
      };
    },

    get_child_cal(e) {
      let tcb, tax_tcb, tcl, tax_tcl, tcd, tax_tcd;
      if (this.temp.breakfast) {
        tcb = parseFloat(e.breakfast) * parseFloat(e.qty);
        tax_tcb = this.get_amount_with_tax(tcb);
        this.breakfast.child = e.qty;
      } else {
        this.breakfast.child = 0;
      }

      if (this.temp.lunch) {
        tcl = parseFloat(e.lunch) * parseFloat(e.qty);
        tax_tcl = this.get_amount_with_tax(tcl);
        this.lunch.child = e.qty;
      } else {
        this.lunch.child = 0;
      }

      if (this.temp.dinner) {
        tcd = parseFloat(e.dinner) * parseFloat(e.qty);
        tax_tcd = this.get_amount_with_tax(tcd);
        this.dinner.child = e.qty;
      } else {
        this.dinner.child = 0;
      }

      this.tempChild = {
        tot_cb: tax_tcb + tcb || 0,
        tot_cl: tax_tcl + tcl || 0,
        tot_cd: tax_tcd + tcd || 0,
      };
    },

    get_baby_cal(e) {
      if (this.temp.breakfast) {
        this.breakfast.baby = e.qty;
      } else {
        this.breakfast.baby = 0;
      }

      if (this.temp.lunch) {
        this.lunch.baby = e.qty;
      } else {
        this.lunch.baby = 0;
      }

      if (this.temp.dinner) {
        this.dinner.baby = e.qty;
      } else {
        this.dinner.baby = 0;
      }
    },

    get_amount_with_tax(amount) {
      let per = 5;
      let tax = this.getPercentage(amount, per);
      return tax;
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
      return (this.room.sub_total = this.room.all_room_Total_amount);
      // parseFloat(this.room.all_room_Total_amount) *
      // parseFloat(this.getDays()));
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
          company_id: this.$auth.user.company.id,
        },
      };
      this.$axios.get(`room_type`, payload).then(({ data }) => {
        this.roomTypes = data;
      });
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
      let res = arr.filter((e) => e.room_no == val);
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
      this.selectedRooms.map((e) => (sum += parseFloat(e.total_with_tax)));
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

    // selectRoom(item) {
    //   let isSelect = this.selectedRooms.find((e) => e.room_no == item.room_no);
    //   if (isSelect) {
    //     this.alert(
    //       "oops",
    //       "Already selected please choose another room",
    //       "error"
    //     );
    //     return;
    //   }

    //   this.RoomDrawer = false;
    //   this.temp.company_id = this.$auth.user.company.id;
    //   this.temp.room_no = item.room_no;
    //   this.temp.room_id = item.id;
    //   this.temp.room_type = item.room_type.name;
    //   this.temp.price = item.price;
    // },

    selectRoom(item) {
      this.selectRoomLoading = true;
      let isSelect = this.selectedRooms.find((e) => e.room_no == item.room_no);
      if (isSelect) {
        this.selectRoomLoading = false;
        this.alert(
          "oops",
          "Already selected please choose another room",
          "error"
        );
        return;
      }

      let payload = {
        params: {
          company_id: this.$auth.user.company.id,
          roomType: item.room_type.name,
          room_no: item.room_no,
          checkin: this.currentDate,
          checkout: this.check_out_date,
        },
      };
      console.log(payload);
      this.$axios
        .get(`get_data_by_select_with_tax`, payload)
        .then(({ data }) => {
          this.selectRoomLoading = false;
          this.RoomDrawer = false;
          this.temp.company_id = this.$auth.user.company.id;
          this.temp.room_no = item.room_no;
          this.temp.room_id = item.id;
          this.temp.room_type = item.room_type.name;
          this.temp.price = data.total_price;
          this.temp.priceList = data.data;
        });
    },

    capsTitle(val) {
      if (!val) return "---";
      let res = val;
      let upper = res.toUpperCase();
      // let title = upper.replace(/[^A-Z]/g, " ");
      return upper;
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
          company_id: this.$auth.user.company.id,
        },
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

      let adult_f_tot = Object.values(this.tempAdult).reduce(
        (a, b) => a + b,
        0
      );
      let child_f_tot = Object.values(this.tempChild).reduce(
        (a, b) => a + b,
        0
      );

      this.temp.tot_adult_food = adult_f_tot * this.getDays();
      this.temp.tot_child_food = child_f_tot * this.getDays();

      this.temp.total =
        parseFloat(this.temp.total_with_tax) +
        parseFloat(this.temp.tot_adult_food) +
        parseFloat(this.temp.tot_child_food);

      this.temp.grand_total =
        parseFloat(this.temp.days) * parseFloat(this.temp.total);

      this.room.check_in = this.temp.check_in;
      this.room.check_out = this.temp.check_out;

      this.temp.room_discount =
        this.temp.room_discount == "" ? 0 : this.temp.room_discount;

      this.temp.meal = `${this.temp.breakfast || "---"} | ${
        this.temp.lunch || "---"
      } | ${this.temp.dinner || "---"}`;

      delete this.temp.check_in_menu;
      delete this.temp.check_out_menu;
      delete this.temp.breakfast;
      delete this.temp.lunch;
      delete this.temp.dinner;
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
        (e) =>
          (tot_bed_amount += e.bed_amount == "" ? 0 : parseFloat(e.bed_amount))
      );

      this.room.total_extra = tot_bed_amount;

      this.selectedRooms.map(
        (e) => (tot_total += e.total == "" ? 0 : parseFloat(e.total))
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
        discount_reason: "",
        no_of_adult: 1,
        no_of_child: 0,
        no_of_baby: 0,
      };

      this.tempAdult = {
        tot_ab: 0,
        tot_al: 0,
        tot_ad: 0,
      };
      this.tempChild = {
        tot_cb: 0,
        tot_cl: 0,
        tot_cd: 0,
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
          company_id: this.$auth.user.company.id,
        },
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
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        u.is_master
      );
    },

    store() {
      console.log(this.customer.document);
      if (this.room.advance_price == "") {
        this.room.advance_price = 0;
      }
      if (
        this.customer.document == null ||
        this.customer.document == undefined
      ) {
        this.alert("Missing!", "Select document", "error");
        this.subLoad = false;
        return;
      }
      this.subLoad = true;
      if (this.selectedRooms.length == 0) {
        this.alert("Missing!", "Atleast select one room", "error");
        this.subLoad = false;
        return;
      }

      let rooms = this.selectedRooms.map((e) => e.room_no);
      this.room.rooms = rooms.toString();
      let payload = {
        ...this.room,
        ...this.customer,
      };
      this.$axios
        .post("/booking_validate1", payload)
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
            this.store_booking();
          }
        })
        .catch((e) => console.log(e));
    },

    store_booking() {
      let payload = {
        ...this.room,
        customer_type: this.customer.customer_type,
        qty_breakfast: this.breakfast,
        qty_lunch: this.lunch,
        qty_dinner: this.dinner,
        selectedRooms: this.selectedRooms,
        ...this.customer,
      };
      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.store_document(data.data);
            this.$router.push(`/customer/details/${data.data}`);
            // this.$router.push(`/customer/details/${260}`);
            this.alert("Success!", "Successfully room added", "success");
          }
        })
        .catch((e) => console.log(e));
    },

    store_document(id) {
      console.log(this.customer.document);
      let payload = new FormData();
      payload.append("document", this.customer.document);
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

/* .food-table table {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
  padding: 100px;
  text-align: right;
}

.food-table th,
td {
  border: 1px solid #a0a0a0 !important;
  padding: 8px 50px;
  text-align: right;
} */

/* .food-table {
  border: 1px solid #a0a0a0 !important;
  border-collapse: collapse;
  width: 250px;
  text-align: right;
  padding: 0 5px;
} */

.input-group {
  display: flex;
  align-items: center;
  margin: 2px 0px !important;
  padding: 0px 0px !important;
}

.input-group-sm {
  width: 100%;
}

.input-group-text {
  background-color: #e9ecef;
  border-color: #e9ecef;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  line-height: 1.9;
  /* border-radius: 0.25rem; */
  text-align: left;
  width: 150px;
}

.form-control {
  height: calc(1.5em + 0.75rem + 2px);
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  width: 100%;
}

.mb-3 {
  margin-bottom: 1rem;
}

.payment-section .input-group-text {
  background-color: #e9ecef;
  border-color: #e9ecef;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
  line-height: 1.9;
  /* border-radius: 0.25rem; */
  text-align: left;
  width: 350px;
}

.payment-section .form-control {
  height: calc(1.5em + 0.75rem + 2px);
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  /* border-radius: 0.25rem; */
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  width: 100%;
  text-align: right;
}

.active-link {
  background-color: #1259a7;
}

input[type="number"]:focus.form-control {
  /* border: 2px solid #5fafa3 !important; */
  border-color: #4390fc;
  outline: none;
}
</style>
