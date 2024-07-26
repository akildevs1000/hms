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
        <v-container class="pa-5">
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
                class="float-right primary pt-4 pb-4"
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
    <v-dialog v-model="dialog" width="85%">
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" class="primary"> Group Booking </v-btn>
      </template>
      <v-card>
        <v-row no-gutters>
          <v-col cols="8">
            <v-tabs
              v-model="activeTab"
              :vertical="vertical"
              background-color="primary"
              dark
              show-arrows
            >
              <div class="py-3" style="background-color: #1259a7">
                <span class="mx-2">New Reservation</span>
              </div>
              <v-spacer></v-spacer>
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

              <v-tab-item class="pl-2 pt-2 pb-2">
                <v-row no-gutters>
                  <v-col cols="2">
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
                    <div
                      class="mt-2 ml-4"
                      v-if="
                        customer.document &&
                        this.reservation.booking_status == 1
                      "
                    >
                      <v-btn
                        small
                        dark
                        class="primary pt-4 pb-4"
                        @click="preview(customer.document)"
                      >
                        ID
                        <v-icon right dark>mdi-file</v-icon>
                      </v-btn>
                    </div>
                    <div
                      class="mt-2 ml-2"
                      v-if="this.reservation.booking_status == 2"
                    >
                      <v-btn
                        small
                        dark
                        class="primary pt-4 pb-4"
                        @click="documentDialog = true"
                      >
                        <small>ID</small>
                        <v-icon right dark>mdi-plus</v-icon>
                      </v-btn>
                    </div>
                  </v-col>
                  <v-col cols="10">
                    <v-row class="mt-3">
                      <v-col>
                        <v-btn color="primary" @click="searchDialog = true">
                          Search
                          <v-icon right dark>mdi-magnify</v-icon>
                        </v-btn>
                      </v-col>
                      <v-col cols="4">
                        <v-text-field
                          dense
                          label="Group Name"
                          outlined
                          :hide-details="true"
                          type="text"
                          v-model="customer.group_name"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="5">
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
                      <v-col cols="4">
                        <v-select
                          v-model="customer.title"
                          :items="titleItems"
                          label="Title *"
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
                      <v-col cols="4">
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
                      <v-col cols="4">
                        <v-text-field
                          label="Last Name"
                          dense
                          :hide-details="true"
                          outlined
                          type="text"
                          v-model="customer.last_name"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="4">
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
                      <v-col cols="4">
                        <v-text-field
                          dense
                          label="Contact No *"
                          outlined
                          max="1111111111111"
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
                      <v-col cols="4">
                        <v-text-field
                          dense
                          label="Whatsapp No"
                          outlined
                          max="1111111111111"
                          type="number"
                          v-model="customer.whatsapp"
                          :hide-details="errors && !errors.whatsapp"
                          :error="errors && errors.whatsapp"
                          :error-messages="
                            errors && errors.whatsapp ? errors.whatsapp[0] : ''
                          "
                        ></v-text-field>
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
                      :error="errors && errors.id_card_type_id"
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
                      :error="errors && errors.id_card_no"
                      :error-messages="
                        errors && errors.id_card_no ? errors.id_card_no[0] : ''
                      "
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
                      :error="errors && errors.gst_number"
                      :error-messages="
                        errors && errors.gst_number ? errors.gst_number[0] : ''
                      "
                    ></v-text-field>
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
                  <v-col md="3" sm="12" cols="12" dense>
                    <v-select
                      v-model="room.type"
                      label="Source Type *"
                      :items="types"
                      dense
                      outlined
                      @change="getType(room.type)"
                      :hide-details="errors && !errors.type"
                      :error="errors && errors.type"
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
                      :error="errors && errors.source"
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
                      :error="errors && errors.source"
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
                      :error="errors && errors.source"
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
                      :error="errors && errors.reference_no"
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
                      :error="errors && errors.paid_by"
                      :error-messages="
                        errors && errors.paid_by ? errors.paid_by[0] : ''
                      "
                    ></v-select>
                  </v-col>
                  <v-col cols="6"></v-col>
                  <v-col cols="3" class="text-right">
                    <v-btn
                      small
                      @click="dialog = false"
                      color="grey white--text"
                      >Close</v-btn
                    >

                    <v-btn small @click="nextTab" color="primary"
                      >Next</v-btn
                    ></v-col
                  >
                </v-row>
              </v-tab-item>

              <v-tab-item>
                <!-- <v-card-title>Group Booking</v-card-title> -->
                <v-container>
                  <v-row>
                    <v-col md="12" cols="12">
                      <table>
                        <tr class="primary white--text">
                          <td class="text-center"><small>Date</small></td>
                          <td class="text-center"><small>Day</small></td>
                          <td class="text-center"><small>Type</small></td>
                          <td class="text-center"><small>Room Type</small></td>
                          <td class="text-center"><small>Tariff</small></td>
                          <td class="text-center"><small>adult</small></td>
                          <td class="text-center"><small>child</small></td>
                          <td class="text-center"><small>meal</small></td>
                          <td class="text-center">
                            <small>No of rooms</small>
                          </td>
                          <td class="text-center"><small>price</small></td>
                          <td class="text-center"><small>Tax</small></td>
                          <td class="text-center"><small>total</small></td>
                        </tr>

                        <tr v-for="(room, index) in roomPrices" :key="index">
                          <td class="text-center">
                            <small> {{ room.date }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.day }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.day_type }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.room_type }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ convert_decimal(room.tariff) }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.adult_per_room }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.child_per_room }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.foodplan }}</small>
                          </td>
                          <td class="text-center">
                            <small> {{ room.no_of_room }}</small>
                          </td>
                          <td class="text-center">
                            <small>
                              {{ convert_decimal(room.room_price) }}</small
                            >
                          </td>
                          <td class="text-center">
                            <small>{{ convert_decimal(room.total_tax) }}</small>
                          </td>
                          <td class="text-center">
                            <small>
                              {{
                                convert_decimal(room.room_group_total)
                              }}</small
                            >
                          </td>
                        </tr>
                        <tr>
                          <td colspan="10"></td>
                          <td class="text-left"><small>Sub Total:</small></td>
                          <td class="text-center">
                            <small>{{ convert_decimal(temp_sub_total) }}</small>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="10"></td>
                          <td class="text-left">
                            <small>Early Check in </small>
                          </td>
                          <td class="text-center">
                            <small>{{
                              convert_decimal(temp_early_check_in)
                            }}</small>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="10"></td>
                          <td class="text-left">
                            <small>Late check out</small>
                          </td>
                          <td class="text-center">
                            <small>{{
                              convert_decimal(temp_late_check_out)
                            }}</small>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="10"></td>
                          <td class="text-left"><small>Discount/add</small></td>
                          <td class="text-center">
                            <small>{{
                              convert_decimal(temp.room_discount)
                            }}</small>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="10"></td>
                          <td class="text-left"><small>Total</small></td>
                          <td class="text-center">
                            <small>{{ convert_decimal(superTotal) }}</small>
                          </td>
                        </tr>
                      </table>
                    </v-col>
                    <v-col md="6" sm="12" cols="12" dense>
                      <v-select
                        label="Discount/Extra"
                        v-model="extraPayType"
                        :items="['Discount', 'ExtraAmount']"
                        dense
                        :hide-details="true"
                        outlined
                      ></v-select>
                    </v-col>
                    <v-col md="6" class="text-right">
                      <BookingGroupRoomPopup
                        @group_booking_rooms="processCalculation"
                      />

                      <v-btn
                        small
                        @click="confirm_rooms"
                        style="background-color: #5fafa3"
                        color="white--text"
                      >
                        Confirm Room
                      </v-btn>
                    </v-col>
                  </v-row>
                  <v-row v-if="extraPayType == 'Discount'">
                    <v-col md="3" sm="12" cols="12">
                      <v-text-field
                        label=" Discount Amount"
                        dense
                        outlined
                        type="number"
                        v-model="temp.room_discount"
                        :hide-details="true"
                        @blur="updateTotal"
                      ></v-text-field>
                    </v-col>
                    <v-col md="3" sm="12" cols="12">
                      <v-text-field
                        label="Reason"
                        dense
                        outlined
                        type="text"
                        v-model="temp.discount_reason"
                        :hide-details="true"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-row v-if="extraPayType == 'ExtraAmount'">
                    <v-col md="3" sm="12" cols="12">
                      <v-text-field
                        label="Extra Amount"
                        dense
                        outlined
                        type="number"
                        v-model="temp.room_extra_amount"
                        :hide-details="true"
                      ></v-text-field>
                    </v-col>
                    <v-col md="3" sm="12" cols="12">
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
                </v-container>
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
          <v-col cols="4">
            <div class="pl-3">
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
                  <v-container>
                    <table style="width: 100%" class="my-3">
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">Name</td>
                        <td cols="8">
                          {{ customer.first_name || "---" }}
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          Contact
                        </td>
                        <td cols="8">
                          {{ customer.contact_no || "---" }}
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          Check In
                        </td>
                        <td cols="8">
                          <span v-if="tempDates.length > 0">{{
                            tempDates[0].check_in_display
                          }}</span>
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          Check Out
                        </td>
                        <td cols="8">
                          <span v-if="tempDates.length > 0">{{
                            tempDates[tempDates.length - 1].check_out_display
                          }}</span>
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">Days</td>
                        <td cols="8">
                          {{ getDays() }}
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          No. Rooms
                        </td>
                        <td cols="8">
                          {{ totalRooms || 0 }}
                        </td>
                      </tr>
                    </table>
                    <table style="width: 100%" class="my-3">
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">Total</td>
                        <td cols="8">
                          {{ convert_decimal(room.total_price) }}
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          Advance Payment
                        </td>
                        <td cols="8">
                          {{ room.advance_price }}
                        </td>
                      </tr>
                      <tr class="my-1">
                        <td style="width: 150px; background: #e9ecef">
                          Balance Amount
                        </td>
                        <td cols="8">
                          {{ convert_decimal(room.remaining_price) }}
                        </td>
                      </tr>
                    </table>
                    <v-row>
                      <v-col>
                        <v-btn
                          class="primary"
                          block
                          small
                          @click="advanceDialog = true"
                          dark
                        >
                          Pay
                        </v-btn>
                      </v-col>
                      <v-col>
                        <v-btn
                          style="background-color: #5fafa3"
                          block
                          small
                          @click="store"
                          :loading="subLoad"
                          dark
                        >
                          Book
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
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
                          {{ convert_decimal(item.price) }}
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
                          Extra Amount
                        </span>
                        <div
                          type="text"
                          class="form-control"
                          aria-label="Sizing example input"
                          aria-describedby="inputGroup-sizing-sm"
                          disabled
                        >
                          {{ convert_decimal(item.room_extra_amount) }}
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

                      <div class="input-group input-group-sm px-5">
                        <span
                          class="input-group-text"
                          id="inputGroup-sizing-sm"
                        >
                          Amount Reason
                        </span>
                        <div
                          type="text"
                          class="form-control"
                          aria-label="Sizing example input"
                          aria-describedby="inputGroup-sizing-sm"
                          disabled
                        >
                          {{ item.extra_amount_reason || "---" }}
                        </div>
                      </div>
                    </section>
                  </v-card>
                </v-tab-item>
              </v-tabs>
            </div>
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
  </div>
  <NoAccess v-else />
</template>
<script>
import History from "../../components/customer/History.vue";
import ImagePreview from "../../components/images/ImagePreview.vue";

export default {
  components: {
    History,
    ImagePreview,
  },
  data() {
    return {
      roomPrices: [],
      group_booking_rooms_date: null,
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
      temp: {
        room_no: "",
        room_type: "",
        room_id: "",
        price: 0,
        days: 0,
        sgst: 0,
        cgst: 0,
        check_in: "",
        check_out: "",
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
        discount_reason: "",
      },
      merge_food_in_room_price: "",
      gst_calculation: {
        recal_basePrice: 0,
        recal_gst_percentage: 0,
        recal_gst_total: 0,
        recal_final: 0,
      },
      upload: {
        name: "",
      },
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
        type: "",
        source: "",
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

      meals: [
        { name: "Room only", slug: "room_only_price" },
        { name: "Breakfast", slug: "Break_fast_price" },
        { name: "Breakfast and Dinner", slug: "Break_fast_with_dinner_price" },
        { name: "Breakfast and Lunch", slug: "Break_fast_with_lunch_price" },
        { name: "Full Board", slug: "full_board_price" },
        // { name: 5, slug: "lunch_with_dinner_price" },
      ],
      dialog: false,
      temp_sub_total: 0,
      temp_early_check_in: 0,
      temp_late_check_out: 0,
      superTotal: 0,
      totalRooms: 0,
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
        //  new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        //   .toISOString()
        //   .substr(0, 10)
      },
      id_card_type_id: 0,
      errors: [],

      imgPath: "",
      image: "",

      upload: {
        name: "",
      },

      previewImage: null,
      extraPayType: "",
      allFood: [],

      documentObj: {
        fileExtension: null,
        file: null,
      },

      tempDates: [],
    };
  },
  created() {
    this.get_reservation();
    this.get_room_types();
    this.get_id_cards();
    this.runAllFunctions();
    this.get_countries();
    this.get_agents();
    this.get_online();
    this.get_Corporate();
    // this.getImage();
    this.preloader = false;
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
  },
  methods: {
    processCalculation(e) {
      this.tempDates.push({
        check_in_display: e.check_in_display,
        check_out_display: e.check_out_display,
      });
      this.roomPrices.push(e);

      this.temp_sub_total = 0;
      this.totalRooms = 0;
      this.temp_early_check_in = 0;
      this.temp_late_check_out = 0;

      this.roomPrices.forEach((e) => {
        this.temp_sub_total += e.room_group_total;
        this.totalRooms += e.no_of_room;

        if (e.is_early_check_in) {
          this.temp_early_check_in += e.early_check_in;
        }
        if (e.is_late_check_out) {
          this.temp_late_check_out += e.late_check_out;
        }
      });

      this.updateTotal();
    },
    updateTotal() {
      if (!this.temp.room_discount) {
        this.temp.room_discount = 0;
      }
      let total =
        this.temp_sub_total +
        this.temp_early_check_in +
        this.temp_late_check_out;
      this.superTotal = total - this.temp.room_discount || 0;
      // let finalDisplayPrice =
      //   parseFloat(temp.price) + parseFloat(-temp.room_discount);
      // this.$axios
      //   .get("get_re_calculate_price/" + finalDisplayPrice, null)
      //   .then(({ data }) => {
      //     this.gst_calculation.recal_basePrice = data.basePrice;
      //     this.gst_calculation.recal_final = data.basePrice + data.gstAmount;
      //     this.gst_calculation.recal_gst_total = data.gstAmount;
      //     this.gst_calculation.recal_gst_percentage = data.tax;
      //   });
    },
    nextTab() {
      // if (this.activeTab) {

      if (this.reservation.booking_status == 2) {
        if (
          this.customer.document == null ||
          this.customer.document == undefined
        ) {
          this.alert("Missing!", "Select document", "error");
          this.subLoad = false;
          return;
        }
      }

      if (this.room.type == "") {
        this.alert("oops", "Select Source Type", "error");

        return false;
      }
      this.activeTab += 1;
      // }
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

    getDays() {
      let tempDates = this.tempDates;
      if (!tempDates.length) {
        return;
      }
      let ci = new Date(tempDates[0].check_in_display);
      let co = new Date(tempDates[tempDates.length - 1].check_out_display);
      let Difference_In_Time = co.getTime() - ci.getTime();
      let days = Difference_In_Time / (1000 * 3600 * 24);
      if (days > 0) {
        return Math.ceil((this.room.total_days = days));
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
      this.temp.room_tax = this.reservation.total_tax;
      this.room.check_in = this.reservation.check_in;
      this.room.check_out = this.reservation.check_out;
      this.temp.priceList = this.reservation.priceList;
      this.get_cs_gst(this.temp.room_tax);
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

    getTotal() {
      return (this.room.total_price = this.subTotal());
      // parseInt(this.getAmountAfterSalesTax()) +
      // this.subTotal() - this.room.discount);
    },

    getRemainingAmount() {
      return (this.room.remaining_price =
        this.getTotal() - this.room.advance_price);
    },

    subTotal() {
      return (this.room.sub_total = this.room.all_room_Total_amount);
      // --------------old---------------
      // return (this.room.sub_total =
      // parseFloat(this.room.all_room_Total_amount) *
      // parseFloat(this.getDays()));
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

    get_cs_gst(amount) {
      let gst = parseFloat(amount) / 2;
      this.temp.cgst = gst;
      this.temp.sgst = gst;
    },

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
          checkin: this.reservation.check_in,
          checkout: this.reservation.check_out,
        },
      };
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
          this.temp.room_tax = this.reservation.total_tax;
          this.get_cs_gst(this.temp.room_tax);
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

    confirm_rooms() {
      this.temp.after_discount = this.superTotal;
      this.temp.grand_total = this.superTotal;
      this.room.total_price = this.superTotal;

      // this.selectedRooms.push(this.temp);

      // this.runAllFunctions();

      this.alert("Success!", "success selected room", "success");
      this.isSelectRoom = false;
      return;
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
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },

    store() {
      this.alert("Success!", "Successfully room added", "success");
      return;
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
        allFoods: this.allFood,
        selectedRooms: this.selectedRooms,
        ...this.customer,
        user_id: this.$auth.user.id,
      };

      this.subLoad = false;

      this.$axios
        .post("/booking", payload)
        .then(({ data }) => {
          this.loading = false;
          if (!data.status) {
            this.errors = data.errors;
            this.subLoad = false;
          } else {
            this.store_document(data.data);
            this.alert("Success!", "Successfully room added", "success");

            if (this.reservation.booking_status == 2) {
              this.$router.push("/reservation/in_house");
              return "";
            }
            this.$router.push("/");
          }
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

<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>
