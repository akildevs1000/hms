<template>
  <div v-if="can('lost_and_found_access') && can('lost_and_found_view')">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackbarColor"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <div>
      <v-dialog v-model="newItem" max-width="900">
        <template>
          <v-card>
            <v-tabs dense small v-model="tab" background-color="grey lighten-3">
              <div style="width: 100%">
                <v-row>
                  <v-col cols="1" class="pt-4"
                    ><SearchReservation @reservation="handleReservation"
                  /></v-col>
                  <v-col>
                    <div
                      style="font-size: 18px; color: #1402f7"
                      class="text-center mt-2"
                    >
                      Reservation # {{ bookingData.reservation_no }}
                    </div>
                  </v-col>
                </v-row>
              </div>
              <v-spacer></v-spacer>
              <v-tab href="#tab-1">
                <small>Guest</small>
                <v-icon small right>mdi-human-male-female-child</v-icon>
              </v-tab>
              <v-tab href="#tab-2" :disabled="disableNextProcess">
                <small>Lost</small>
                <v-icon small right>mdi-magnify-minus-outline</v-icon>
              </v-tab>
              <v-tab href="#tab-3" :disabled="editedIndex == -1">
                <small>Found</small>
                <v-icon small right>mdi-magnify-plus-outline</v-icon>
              </v-tab>

              <v-tab href="#tab-4" :disabled="editedIndex == -1">
                <small>Return</small>
                <v-icon small right>mdi-emoticon-happy-outline</v-icon>
              </v-tab>
              <v-tab @click="newItem = false">
                <v-icon dark class="pa-0">mdi-close</v-icon>
              </v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
              <v-tab-item value="tab-1">
                <v-card-text>
                  <v-container class="py-5">
                    <v-row no-gutter>
                      <v-col md="3" cols="12" class="text-center">
                        <v-avatar tile size="150">
                          <v-card>
                            <img
                              class="zoom-on-hover"
                              style="z-index: 1; width: 100%"
                              :src="
                                bookingData?.customer?.captured_photo ||
                                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRudDbHeW2OobhX8E9fAY-ctpUAHeTNWfaqJA&usqp=CAU'
                              "
                            />
                          </v-card>
                        </v-avatar>
                      </v-col>
                      <v-col md="9" cols="12">
                        <v-row>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              label="First Name *"
                              dense
                              outlined
                              type="text"
                              v-model="bookingData.customer.title"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col md="9" cols="12" sm="12">
                            <v-row>
                              <v-col>
                                <v-text-field
                                  label="First Name *"
                                  dense
                                  outlined
                                  type="text"
                                  v-model="bookingData.customer.first_name"
                                  hide-details
                                ></v-text-field>
                              </v-col>
                              <v-col>
                                <v-text-field
                                  label="Last Name"
                                  dense
                                  hide-details
                                  outlined
                                  type="text"
                                  v-model="bookingData.customer.last_name"
                                ></v-text-field>
                              </v-col>
                            </v-row>
                          </v-col>

                          <v-col cols="6">
                            <v-text-field
                              dense
                              label="Email *"
                              outlined
                              type="email"
                              v-model="bookingData.customer.email"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              v-model="bookingData.customer.dob"
                              readonly
                              label="DOB"
                              hide-details
                              dense
                              outlined
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              dense
                              label="Contact No *"
                              outlined
                              max="1111111111111"
                              type="number"
                              v-model="bookingData.customer.contact_no"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col cols="6">
                            <v-text-field
                              dense
                              label="Whatsapp No"
                              outlined
                              max="1111111111111"
                              type="number"
                              v-model="bookingData.customer.whatsapp"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="country"
                              outlined
                              v-model="bookingData.customer.country"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="state"
                              outlined
                              v-model="bookingData.customer.state"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              dense
                              label="city"
                              outlined
                              v-model="bookingData.customer.city"
                              hide-details
                            ></v-text-field>
                          </v-col>
                          <v-col md="3" cols="12" sm="12">
                            <v-text-field
                              label="Zip Code"
                              v-model="bookingData.customer.zip_code"
                              outlined
                              hide-details
                              dense
                            ></v-text-field>
                          </v-col>

                          <v-col md="6" cols="12" sm="12">
                            <v-text-field
                              label="Check In"
                              v-model="bookingData.check_in_date"
                              outlined
                              hide-details
                              dense
                            ></v-text-field>
                          </v-col>

                          <v-col md="6" cols="12" sm="12">
                            <v-text-field
                              label="Check Out"
                              v-model="bookingData.check_out_date"
                              outlined
                              hide-details
                              dense
                            ></v-text-field>
                          </v-col>
                          <v-col
                            cols="12"
                            style="position: relative"
                            class="text-center"
                            v-if="!viewMode"
                          >
                            <AssetsButtonCancel @close="newItem = false" />
                            &nbsp;
                            <AssetsButtonSubmit
                              v-if="bookingId"
                              @click="tab = `tab-2`"
                            />
                          </v-col>
                          <!-- <v-col v-if="editedIndex == -1">
                          <v-row class="mb-2 mt=4" >
                            <v-col md="6" cols="12">
                              <span
                                style="color: red; text-align: left"
                                class="mr-5"
                              >
                                {{ response }}
                              </span>

                              <span
                                v-if="
                                  bookingData &&
                                  bookingData.id &&
                                  editedItem &&
                                  editedItem.id
                                "
                                style="color: red; text-align: left"
                                class="mr-5"
                              >
                                Lost Item name(s):
                                {{
                                  editedItem && editedItem.item_name
                                    ? editedItem.item_name
                                    : ""
                                }}
                              </span>
                            </v-col>
                          </v-row>
                         </v-col> -->
                        </v-row>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
              </v-tab-item>
              <v-tab-item value="tab-2">
                <v-card>
                  <!-- <v-card-title>Lost Item Details</v-card-title> -->
                  <v-card-text class="pt-2">
                    <v-container>
                      <v-row>
                        <v-col md="4" cols="12">
                          <strong></strong>
                          <v-text-field
                            label="Lost Item name(s)*"
                            hide-details
                            v-model="editedItem.item_name"
                            small
                            dense
                            outlined
                          ></v-text-field>
                          <span
                            dense
                            v-if="errors && errors.item_name"
                            class="error--text"
                            >{{ errors.item_name[0] }}</span
                          >
                        </v-col>
                        <v-col md="4" cols="12">
                          <v-menu
                            v-model="menu1"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            transition="scale-transition"
                            offset-y
                            min-width="auto"
                          >
                            <template v-slot:activator="{ on, attrs }">
                              <v-text-field
                                label="Missing Date*"
                                hide-details
                                dense
                                small
                                outlined
                                v-model="missing_date"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                              ></v-text-field>
                            </template>
                            <v-date-picker
                              no-title
                              v-model="missing_date"
                              @input="menu1 = false"
                            ></v-date-picker>
                          </v-menu>
                          <span
                            dense
                            v-if="errors && errors.missing_datetime"
                            class="error--text"
                            >{{ errors.missing_datetime[0] }}</span
                          >
                        </v-col>
                        <v-col md="4" cols="12">
                          <v-menu
                            ref="menu"
                            v-model="menu2"
                            :close-on-content-click="false"
                            :nudge-right="40"
                            :return-value.sync="missing_time"
                            transition="scale-transition"
                            offset-y
                            max-width="290px"
                            min-width="290px"
                          >
                            <template v-slot:activator="{ on, attrs }">
                              <v-text-field
                                label="Missing Time"
                                hide-details
                                dense
                                small
                                outlined
                                v-model="missing_time"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                              ></v-text-field>
                            </template>
                            <v-time-picker
                              no-title
                              v-if="menu2"
                              v-model="missing_time"
                              full-width
                              @click:minute="$refs.menu.save(missing_time)"
                            ></v-time-picker>
                          </v-menu>
                          <span
                            dense
                            v-if="errors && errors.missing_datetime"
                            class="error--text"
                            >{{ errors.missing_datetime[0] }}</span
                          >
                        </v-col>
                        <v-col md="12" cols="12">
                          <v-text-field
                            label="Remarks"
                            hide-details
                            v-model="editedItem.missing_remarks"
                            small
                            dense
                            outlined
                          ></v-text-field>
                        </v-col>
                        <v-col md="12" cols="12">
                          <v-textarea
                            rows="3"
                            v-model="editedItem.missing_notes"
                            outlined
                            name="input-7-4"
                            label="Notes"
                            value=" "
                            hide-details
                          ></v-textarea>
                        </v-col>
                        <v-col class="text-center" v-if="!viewMode">
                          <AssetsButtonCancel @close="newItem = false" />
                          &nbsp;
                          <AssetsButtonSubmit @click="saveMissingItems()" />
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item value="tab-3">
                <v-card flat>
                  <!-- <v-card-title>Found Item Details</v-card-title> -->
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col cols="3">
                          <v-card outlined>
                            <v-img
                              style="
                                height: 100%;

                                margin: 0 auto;
                                border-radius: 10%;
                              "
                              :src="showFoundImage"
                            ></v-img>
                            <v-btn
                              v-if="!viewMode"
                              block
                              small
                              class="primary--text"
                              text
                              @click="$refs.attachment_input.click()"
                              >Upload Image</v-btn
                            >
                          </v-card>
                          <a
                            v-if="viewMode"
                            download
                            target="_blank"
                            :href="showFoundImage"
                          >
                            <v-icon color="primary" class="mt-2" small
                              >mdi-eye</v-icon
                            ></a
                          >
                          <input
                            required
                            type="file"
                            @change="attachment"
                            style="display: none"
                            accept="image/*"
                            ref="attachment_input"
                          />
                        </v-col>
                        <v-col cols="9">
                          <v-row>
                            <v-col cols="4">
                              <v-menu
                                v-model="menu3"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    label="Found Date"
                                    hide-details
                                    dense
                                    small
                                    outlined
                                    v-model="found_date"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                  ></v-text-field>
                                </template>

                                <v-date-picker
                                  no-title
                                  v-model="found_date"
                                  @input="menu3 = false"
                                ></v-date-picker>
                              </v-menu>
                            </v-col>
                            <v-col cols="4">
                              <v-menu
                                ref="menu4"
                                v-model="menu4"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                :return-value.sync="found_time"
                                transition="scale-transition"
                                offset-y
                                max-width="290px"
                                min-width="290px"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    label="Found Time"
                                    hide-details
                                    dense
                                    small
                                    outlined
                                    v-model="found_time"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                  ></v-text-field>
                                </template>
                                <v-time-picker
                                  no-title
                                  v-if="menu4"
                                  v-model="found_time"
                                  full-width
                                  @click:minute="$refs.menu4.save(found_time)"
                                ></v-time-picker>
                              </v-menu>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                label="Found By"
                                hide-details
                                v-model="editedItem.found_person_name"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col>
                              <v-text-field
                                label="Found Location"
                                hide-details
                                v-model="editedItem.found_location"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col>
                              <v-text-field
                                label="Remarks"
                                hide-details
                                v-model="editedItem.found_remarks"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                              <v-textarea
                                rows="2"
                                v-model="editedItem.found_notes"
                                outlined
                                label="Notes"
                                hide-details
                              ></v-textarea>
                            </v-col>
                            <v-col
                              cols="12"
                              class="text-center"
                              v-if="!viewMode"
                            >
                              <AssetsButtonCancel @close="newItem = false" />
                              &nbsp;
                              <AssetsButtonSubmit @click="saveFoundDetails" />
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>
                </v-card>
              </v-tab-item>
              <v-tab-item value="tab-4">
                <v-card flat>
                  <!-- <v-card-title>Return Item Details</v-card-title> -->
                  <v-card-text>
                    <v-container>
                      <v-row>
                        <v-col cols="3">
                          <v-card outlined>
                            <v-img
                              style="
                                height: 100%;
                                margin: 0 auto;
                                border-radius: 10%;
                              "
                              :src="showRetunedImage"
                            ></v-img>
                            <v-btn
                              v-if="!viewMode"
                              block
                              small
                              class="primary--text"
                              text
                              @click="$refs.returned_attachment_input.click()"
                              >Upload Image</v-btn
                            >
                          </v-card>
                          <a
                            v-if="viewMode"
                            download
                            target="_blank"
                            :href="showRetunedImage"
                          >
                            <v-icon color="primary" class="mt-2" small
                              >mdi-eye</v-icon
                            ></a
                          >
                          <input
                            v-nodel="input_returned_image"
                            required
                            type="file"
                            @change="return_attachment"
                            style="display: none"
                            accept="image/*"
                            ref="returned_attachment_input"
                          />
                        </v-col>
                        <v-col cols="9">
                          <v-row>
                            <v-col cols="4">
                              <v-menu
                                v-model="menu5"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    label="Retuned Date"
                                    hide-details
                                    dense
                                    small
                                    outlined
                                    v-model="returned_date"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                  ></v-text-field>
                                </template>
                                <v-date-picker
                                  no-title
                                  v-model="returned_date"
                                  @input="menu5 = false"
                                ></v-date-picker>
                              </v-menu>
                            </v-col>
                            <v-col cols="4">
                              <v-menu
                                ref="menu6"
                                v-model="menu6"
                                :close-on-content-click="false"
                                :nudge-right="40"
                                :return-value.sync="returned_time"
                                transition="scale-transition"
                                offset-y
                                max-width="290px"
                                min-width="290px"
                              >
                                <template v-slot:activator="{ on, attrs }">
                                  <v-text-field
                                    label="Retuned Time"
                                    hide-details
                                    dense
                                    small
                                    outlined
                                    v-model="returned_time"
                                    readonly
                                    v-bind="attrs"
                                    v-on="on"
                                  ></v-text-field>
                                </template>
                                <v-time-picker
                                  no-title
                                  v-if="menu6"
                                  v-model="returned_time"
                                  full-width
                                  @click:minute="
                                    $refs.menu6.save(returned_time)
                                  "
                                ></v-time-picker>
                              </v-menu>
                            </v-col>
                            <v-col cols="4">
                              <v-text-field
                                label="Returned By"
                                hide-details
                                v-model="editedItem.returned_person_name"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col>
                              <v-text-field
                                label="Approved By"
                                hide-details
                                v-model="editedItem.approved_person_name"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col>
                              <v-text-field
                                label="Remarks"
                                hide-details
                                v-model="editedItem.returned_remarks"
                                small
                                dense
                                outlined
                              ></v-text-field>
                            </v-col>
                            <v-col cols="12">
                              <v-textarea
                                label="Notes"
                                hide-details
                                rows="2"
                                v-model="editedItem.returned_notes"
                                outlined
                              ></v-textarea>
                            </v-col>
                            <v-col
                              cols="12"
                              class="text-center"
                              v-if="!viewMode"
                            >
                              <AssetsButtonCancel @close="newItem = false" />
                              &nbsp;
                              <AssetsButtonSubmit
                                @click="saveReturnedDetails"
                              />
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card-text>
                </v-card>
              </v-tab-item>
            </v-tabs-items>
          </v-card>
        </template>
      </v-dialog>

      <v-dialog v-model="viewDialog" max-width="80%">
        <v-card>
          <v-card-title dense class="primary white--text background">
            <span>View Lost Item Details </span>
            <v-spacer></v-spacer>
            <v-icon @click="viewDialog = false" outlined dark color="white">
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-container>
              <ItemLost :lost_item_id="lost_item_id" />
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>
      <v-card>
        <v-container fluid>
          <v-row>
            <v-col v-for="(stat, index) in stats" :key="index">
              <AssetsCard :options="stat" />
            </v-col>
            <v-col cols="12">
              <v-data-table
                dense
                :headers="headers_table"
                :items="data"
                :loading="loading"
                :options.sync="options"
                :footer-props="{
                  itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
                }"
                class="elevation-0"
                :server-items-length="totalRowsCount"
                @page-change="updateIndex"
              >
                <template v-slot:top>
                  <v-row no-gutter>
                    <v-col cols="2">
                      <FilterDateRange @filter-attr="filterAttr" />
                    </v-col>
                    <v-col class="text-right">
                      <v-btn
                        class="py-3"
                        color="primary"
                        x-small
                        @click="openNewRecord()"
                      >
                        <v-icon color="white" small class="py-5"
                          >mdi-plus</v-icon
                        >
                        New
                      </v-btn>
                    </v-col>
                  </v-row>
                </template>
                <template v-slot:item.sno="{ item, index }">
                  {{
                    currentPage
                      ? (currentPage - 1) * perPage +
                        (cumulativeIndex + itemIndex(item))
                      : ""
                  }}
                </template>
                <template v-slot:item.booking.reservation_no="{ item }">
                  {{ item.booking.reservation_no }}
                </template>
                <template v-slot:item.bookings.rooms="{ item }">
                  {{ item.booking.rooms }}
                </template>

                <template v-slot:item.customer.name="{ item }">
                  {{ item.booking.customer.title }}
                  {{ item.booking.customer.first_name }}
                  {{ item.booking.customer.last_name }}
                </template>
                <template v-slot:item.missing_datetime="{ item }">
                  {{ formatDateTime(item.missing_datetime) }}
                </template>
                <template v-slot:item.found_datetime="{ item }">
                  {{ item.found_datetime }}
                </template>
                <template v-slot:item.returned_datetime="{ item }">
                  {{ item.returned_datetime }}
                </template>
                <template v-slot:item.returned_remarks="{ item }">
                  {{ item.returned_remarks }}
                </template>
                <template v-slot:item.created_at="{ item }">
                  {{ formatDateTime(item.created_at) }}
                </template>

                <template v-slot:item.status="{ item }">
                  <span> {{ getStatus(item.status) }}</span>
                  <!-- <v-icon title="Item Not Found yet" v-if="item.status == 0" color="red" dark
                                    white>mdi-minus-circle</v-icon>
                                <v-icon title="Item Found and Not Returned yet" v-if="item.status == 1" color="yellow" dark
                                    white>mdi-plus-circle</v-icon>
                                <v-icon title="Item Found and Returned to Guest" v-if="item.status == 2" color="green" dark
                                    white>mdi-emoticon-happy</v-icon> -->
                </template>

                <template v-slot:item.options="{ item }">
                  <v-menu
                    bottom
                    left
                    v-if="
                      can('lost_and_found_create') ||
                      can('lost_and_found_edit') ||
                      can('lost_and_found_delete')
                    "
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn dark-2 icon v-bind="attrs" v-on="on">
                        <v-icon>mdi-dots-vertical</v-icon>
                      </v-btn>
                    </template>
                    <v-list width="120" dense>
                      <v-list-item
                        v-if="can('lost_and_found_edit')"
                        @click="editItem(item, true)"
                      >
                        <v-list-item-title style="cursor: pointer">
                          <v-icon color="primary" small> mdi-eye </v-icon>
                          View
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="can('lost_and_found_edit')"
                        @click="editItem(item, false)"
                      >
                        <v-list-item-title style="cursor: pointer">
                          <v-icon color="secondary" small> mdi-pencil </v-icon>
                          Edit
                        </v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="can('lost_and_found_delete')"
                        @click="deleteItem(item)"
                      >
                        <v-list-item-title style="cursor: pointer">
                          <v-icon color="error" small> mdi-delete </v-icon>
                          Delete
                        </v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </div>
  </div>
  <NoAccess v-else />
</template>
<script>
import ItemLost from "/components/ItemLost.vue";
export default {
  components: {
    ItemLost,
  },
  data: () => ({
    viewDialog: false,
    snackbarColor: "black",

    input_found_image: "",
    input_returned_image: "",

    data_table: [],
    cumulativeIndex: 1,
    perPage: 20,
    currentPage: 1,
    filterLoader: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    options: {},
    headers_table: [
      {
        text: "S.No",
        align: "left",
        sortable: false,
        key: "",
        filterable: false,
        value: "sno",
      },
      {
        text: "Rev.No",
        align: "left",
        sortable: true,
        key: "reservation_no",
        filterable: true,
        value: "booking.reservation_no",
      },
      {
        text: "Room",
        align: "left",
        sortable: true,
        key: "bookings_rooms",
        filterable: true,
        value: "bookings.rooms",
      },
      {
        text: "Guest Name",
        align: "left",
        sortable: false,
        key: "customer_name",
        filterable: true,
        value: "customer.name",
      },
      {
        text: "Items",
        align: "left",
        sortable: true,
        key: "item_name",
        filterable: true,
        value: "item_name",
      },
      {
        text: "Missing From",
        align: "left",
        sortable: true,
        key: "missing_datetime",
        filterable: true,
        value: "missing_datetime",
        filterSpecial: true,
      },
      {
        text: "Date Found",
        align: "left",
        sortable: true,
        key: "date_found",
        filterable: true,
        value: "found_datetime",
        filterSpecial: true,
      },
      {
        text: "Returned Date ",
        align: "left",
        sortable: true,
        key: "date_returned",
        filterable: true,
        value: "returned_datetime",
        filterSpecial: true,
      },
      {
        text: "Retuned Remarks",
        align: "left",
        sortable: true,
        key: "returned_remarks",
        filterable: true,
        value: "returned_remarks",
      },
      {
        text: "Created",
        align: "left",
        sortable: true,
        key: "created",
        filterable: true,
        value: "created_at",
        filterSpecial: true,
      },
      {
        text: "Status",
        align: "left",
        sortable: true,
        key: "status",
        filterable: true,
        value: "status",
        filterSpecial: true,
      },

      {
        text: "Options",
        align: "left",
        sortable: false,

        filterable: false,
        value: "options",
      },
    ],

    filter_returned_datetime: "",
    filter_found_datetime: "",
    from_menu_filter: "",
    from_date_filter: "",
    to_date_filter: "",
    to_menu_filter: "",
    to_menu_filter1: "",
    missing: {},
    found: {},
    missing_date: "",
    missing_time: "",
    found_date: "",
    found_time: "",
    returned_date: "",
    returned_time: "",
    menu1: false,
    menu2: false,
    menu3: false,
    menu4: false,
    menu5: false,
    menu6: false,
    date: "",
    disableNextProcess: true,
    bookingId: "",
    reservation_no: "",
    newItem: false,

    tab: null,

    pagination: {
      current: 1,
      total: 0,
      per_page: 50,
    },
    Model: "Room Category",
    options: {},
    endpoint: "lost_and_found_items",
    search: "",
    snackbar: false,
    dialog: false,
    roomTypeDialog: false,
    ids: [],
    loading: false,
    total: 0,
    bookingData: { customer: {} },
    editedIndex: -1,

    editedItem: {
      item_name: "",
      missing_datetime: "",
      missing_remarks: "",
      missing_notes: "",
      found_datetime: "",
      found_person_name: "",
      found_location: "",
      found_remarks: "",
      found_notes: "",
      returned_datetime: "",
      returned_person_name: "",
      returned_remarks: "",
      returned_notes: "",
      approved_person_name: "",
      status: "",
    },

    defaultItem: {
      item_name: "",
      missing_datetime: "",
      missing_remarks: "",
      missing_notes: "",
      found_datetime: "",
      found_person_name: "",
      found_location: "",
      found_remarks: "",
      found_notes: "",
      returned_datetime: "",
      returned_person_name: "",
      returned_remarks: "",
      returned_notes: "",
      approved_person_name: "",
      status: "",
    },

    response: "",
    data: [],
    previewImage: null,
    previewRetunedImage: null,
    errors: [],
    viewMode: false,
    lost_item_id: "",
    stats: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
    showFoundImage() {
      if (!this.editedItem.found_image && !this.previewImage) {
        return "https://thumbs.dreamstime.com/b/upload-photo-simple-icon-image-thumbnail-sign-vector-upload-photo-simple-icon-image-thumbnail-sign-picture-placeholder-symbol-219679524.jpg";
      } else if (this.previewImage) {
        return this.previewImage;
      }

      return this.editedItem.found_image;
    },
    showRetunedImage() {
      if (!this.editedItem.recovered_image && !this.previewRetunedImage) {
        return "https://thumbs.dreamstime.com/b/upload-photo-simple-icon-image-thumbnail-sign-vector-upload-photo-simple-icon-image-thumbnail-sign-picture-placeholder-symbol-219679524.jpg";
      } else if (this.previewRetunedImage) {
        return this.previewRetunedImage;
      }

      return this.editedItem.recovered_image;
    },
  },

  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },

    roomTypeDialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    // missing(val) {
    //     this.editedItem.missing_datetime = this.missing.date + ' ' + this.missing.time + ':00';

    // },
    missing_date(val) {
      this.editedItem.missing_datetime =
        this.missing_date + " " + this.missing_time; //+ ':00';
    },
    missing_time(val) {
      this.editedItem.missing_datetime =
        this.missing_date + " " + this.missing_time; //+ ':00';
    },
    found_date(val) {
      this.editedItem.found_datetime = this.found_date + " " + this.found_time; //+ ':00';
    },
    found_time(val) {
      this.editedItem.found_datetime = this.found_date + " " + this.found_time; // + ':00';
    },
    returned_date(val) {
      this.editedItem.returned_datetime =
        this.returned_date + " " + this.returned_time; // + ':00';
    },
    returned_time(val) {
      this.editedItem.returned_datetime =
        this.returned_date + " " + this.returned_time; // + ':00';
    },
  },

  created() {
    this.month = new Date().getMonth();
    this.year = new Date().getFullYear();
    this.from_date = this.formatDate(new Date(this.year, this.month, 1));
    this.to_date = this.formatDate(new Date(this.year, this.month + 1, 0));

    this.loading = true;
    //this.getDataFromApi();
  },

  methods: {
    handleReservation(booking) {
      this.disableNextProcess = false;
      this.bookingData = booking;
      this.bookingId = booking.id;
    },
    formatDate(date) {
      var day = date.getDate();
      var month = date.getMonth() + 1; // Months are zero-based
      var year = date.getFullYear();
      return (
        year +
        "-" +
        (month < 10 ? "0" : "") +
        month +
        "-" +
        (day < 10 ? "0" : "") +
        day
      );
    },
    handleDatesFilter(dates) {
      this.from_date = dates[0];
      this.to_date = dates[1];
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      // this.filterType = data.type;
      //this.search = data.search;
      if (this.from_date && this.to_date) this.getDataFromApi();
    },
    getPriceFormat(price) {
      return parseFloat(price).toLocaleString("en-IN", {
        maximumFractionDigits: 2,
      });
    },
    formatDateTime(dateString) {
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      const hours = String(date.getHours()).padStart(2, "0");
      const minutes = String(date.getMinutes()).padStart(2, "0");
      const seconds = String(date.getSeconds()).padStart(2, "0");

      return `${year}-${month}-${day} ${hours}:${minutes}`;
    },
    attachment(e) {
      this.editedItem.found_image = e.target.files[0] || "";

      this.input_found_image = e.target.files[0];

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
    return_attachment(e) {
      this.input_returned_image = e.target.files[0];
      this.editedItem.recovered_image = e.target.files[0] || "";
      let input = this.$refs.returned_attachment_input;
      let file = input.files;
      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          this.previewRetunedImage = e.target.result;
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);
      }
    },
    viewItem(id) {
      this.lost_item_id = id;
      this.response = "";
      this.viewDialog = true;

      // this.$axios.get(`${this.endpoint}/${item.id}`, options).then(({ data }) => {
      //     this.loading = false;
      //     this.disableNextProcess = false;
      //     this.loadLostItemDetails(data);
      // });
    },
    editItem(item, viewMode = false) {
      this.clearForm();
      this.viewMode = viewMode;

      this.response = "";
      this.newItem = true;
      this.tab = 1;
      let lostItemId = item.id;
      this.loading = true;
      this.bookingData = { customer: {} };
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          lostItemId: lostItemId,
        },
      };

      this.$axios
        .get(`${this.endpoint}/${lostItemId}`, options)
        .then(({ data }) => {
          this.loading = false;
          this.disableNextProcess = false;
          if (data == "") {
            this.clearForm();
            return false;
          } else {
            this.bookingData = data.booking;
            this.editedIndex = data.id;
            this.bookingId = data.booking.id;
            // if (this.bookingData.booking_status > 1)
            //     this.disableNextProcess = false;
            // else
            //     this.disableNextProcess = true;
            this.loadLostItemDetails(data);
          }
        });
    },
    gotoViewPage() {
      this.tab = 1;
      this.clearForm();
      this.newItem = true;
    },
    openNewRecord() {
      this.tab = 1;
      this.clearForm();
      this.newItem = true;
    },
    clearForm() {
      this.response = "";
      //this.bookingId = '';
      this.editedIndex = -1;
      this.bookingData = { customer: {} };
      this.editedItem = this.defaultItem;

      this.missing_date = "";
      this.missing_time = "";
      this.found_time = "";
      this.found_date = "";

      this.returned_time = "";
      this.returned_date = "";
    },
    getStatus(status) {
      if (status == 0) return "Not Found";
      else if (status == 1) return "Item Found";
      else if (status == 2) return "Returned";
    },

    loadLostItemDetails(missingItemData) {
      if (missingItemData) {
        this.editedItem = missingItemData;
        this.editedIndex = missingItemData.id;

        this.missing_date = this.editedItem.missing_datetime
          ? this.editedItem.missing_datetime.split(" ")[0]
          : "";
        this.missing_time = this.editedItem.missing_datetime
          ? this.editedItem.missing_datetime.split(" ")[1]
          : "";
        this.found_date = this.editedItem.found_datetime
          ? this.editedItem.found_datetime.split(" ")[0]
          : "";
        this.found_time = this.editedItem.found_datetime
          ? this.editedItem.found_datetime.split(" ")[1]
          : "";
        this.returned_date = this.editedItem.returned_datetime
          ? this.editedItem.returned_datetime.split(" ")[0]
          : "";
        this.returned_time = this.editedItem.returned_datetime
          ? this.editedItem.returned_datetime.split(" ")[1]
          : "";
      } else {
        this.editedIndex = -1;
        this.editedItem = this.defaultItem;
      }
    },
    can(per) {
      let u = this.$auth.user;
      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
    saveMissingItems() {
      let url = this.endpoint;
      this.loading = true;

      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          booking_id: this.bookingId,
          update_type: "missing",
          user_id: this.$auth.user.id,
          ...this.editedItem,
        },
      };

      if (this.editedIndex > -1) {
        this.$axios
          .put(url + "/" + this.editedIndex, options.params)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;

              return false;
            } else {
              this.getDataFromApi();
              this.newItem = false;
            }
            this.snackbarColor = "primary";
            this.snackbar = true;
            this.response = "Detais are Updated Successfully";
            this.newItem = false;
          });
      } else {
        //New
        this.$axios
          .post(`${url}`, options.params)
          .then(({ data }) => {
            this.loading = false;
            if (data.errors) {
              this.errors = data.errors;

              return false;
            } else {
              this.getDataFromApi();
              this.newItem = false;
            }
            this.snackbarColor = "primary";
            this.snackbar = true;
            this.response = "Detais are saved Successfully";
            this.newItem = false;
          })
          .catch((res) => console.log(res));
      }
    },
    saveFoundDetails() {
      let url = this.endpoint;
      this.loading = true;
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          booking_id: this.bookingId,
          update_type: "found",
          ...this.editedItem,
        },
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(url + "/" + this.editedIndex, options.params)
          .then(({ data }) => {
            this.uploadFoundFile(this.editedIndex);
            this.loading = false;
            this.getDataFromApi();

            this.snackbarColor = "primary";
            this.snackbar = true;
            this.response = "Detais are saved Successfully";
            this.newItem = false;
          });
      } else {
        return false;
      }
    },
    uploadFoundFile(editedIndex) {
      if (!this.input_found_image) {
        return false;
      }
      let payload = new FormData();
      payload.append("image", this.editedItem.found_image);
      payload.append("booking_id", this.bookingId);
      this.$axios
        .post(this.endpoint + "/file_found/" + editedIndex, payload)
        .then(({ data }) => {});
    },
    saveReturnedDetails() {
      let url = this.endpoint;
      this.loading = true;

      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          booking_id: this.bookingId,
          update_type: "return",
          ...this.editedItem,
        },
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(url + "/" + this.editedIndex, options.params)
          .then(({ data }) => {
            this.uploadReturnedFile(this.editedIndex);
            this.loading = false;
            this.getDataFromApi();
            // this.newItem = false;

            this.snackbarColor = "primary";
            this.snackbar = true;
            this.response = "Detais are saved Successfully";
            this.newItem = false;
          });
      } else {
        return false;
      }
    },
    uploadReturnedFile(editedIndex) {
      if (!this.input_returned_image) {
        return false;
      }
      let payload = new FormData();
      payload.append("image", this.editedItem.recovered_image);
      payload.append("booking_id", this.bookingId);

      this.$axios
        .post(this.endpoint + "/file_returned/" + editedIndex, payload)
        .then(({ data }) => {});
    },
    // caps(str) {
    //     if (str == "" || str == null) {
    //         return "---";
    //     } else {
    //         let res = str.toString();
    //         return res.replace(/\b\w/g, (c) => c.toUpperCase());
    //     }
    // },
    // onPageChange() {
    //     this.getDataFromApi();
    // },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    applyFilters() {
      this.getDataFromApi();
      this.from_menu_filter = false;
      this.to_menu_filter = false;
      this.to_menu_filter1 = false;
    },
    updateIndex(page) {
      this.currentPage = page;
      this.cumulativeIndex = (page - 1) * this.perPage;
    },
    itemIndex(item) {
      return this.data.indexOf(item);
    },
    reload() {
      this.isFilter = false;
      this.filters = {};
      this.$set(this.options, "page", 1);
      this.getDataFromApi(this.endpoint, 1);
    },
    getDataFromApi(url = this.endpoint, customPage = 0) {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      if (customPage == 1) page = 1;
      this.currentPage = page;

      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company.id,
          search: this.search,
          from: this.from_date,
          to: this.to_date,
          ...this.filters,
        },
      };

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalRowsCount = data.total;

        this.getStatissticsApi();
      });
    },
    getStatissticsApi(url = this.endpoint, customPage = 0) {
      let options = {
        params: {
          company_id: this.$auth.user.company.id,
          from: this.from_date,
          to: this.to_date,
        },
      };

      this.$axios
        .get(`lost_and_found_items_statistics`, options)
        .then(({ data }) => {
          this.stats = [
            {
              color: "red",
              icon: "mdi-alert-circle-outline", // Lost icon
              label: "Lost",
              value: data.missing,
            },
            {
              color: "green",
              icon: "mdi-shield-check-outline", // Recovered icon
              label: "Recovered",
              value: data.recovered,
            },
            {
              color: "blue",
              icon: "mdi-package-variant-closed", // Handovered icon
              label: "Handovered",
              value: data.returned,
            },
            {
              color: "orange",
              icon: "mdi-timer-sand", // Pending icon
              label: "Pending",
              value: data.missing - data.returned,
            },
          ];
        });
    },

    deleteItem(item) {
      confirm("Are you sure you wish to delete?") &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },
  },
};
</script>
