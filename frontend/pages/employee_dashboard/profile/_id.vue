<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-row class="mt-3 mb-4">
        <v-col cols="10">
          <h3>Employee</h3>
          <div>Dashboard / Employee / Details</div>
        </v-col>
      </v-row>
      <v-dialog v-model="personal_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Personal Info </span>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <!-- <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("passport no")
                    }}</label>
                    <input
                      v-model="personalItem.passport_no"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.passport_no"
                      class="text-danger mt-2"
                      >{{ errors.passport_no[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("passport expiry")
                    }}</label>
                    <input
                      v-model="personalItem.passport_expiry"
                      class="form-control"
                      type="date"
                    />
                    <span
                      v-if="errors && errors.passport_expiry"
                      class="text-danger mt-2"
                      >{{ errors.passport_expiry[0] }}</span
                    >
                  </div>
                </v-col> -->

                <!-- <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("tel") }}</label>
                    <input
                      v-model="personalItem.tel"
                      class="form-control"
                      type="tel"
                    />
                    <span
                      v-if="errors && errors.tel"
                      class="text-danger mt-2"
                      >{{ errors.tel[0] }}</span
                    >
                  </div>
                </v-col> -->

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("nationality")
                    }}</label>
                    <input
                      v-model="personalItem.nationality"
                      class="form-control"
                      type=""
                    />
                    <span
                      v-if="errors && errors.nationality"
                      class="text-danger mt-2"
                      >{{ errors.nationality[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("Date of birth")
                    }}</label>
                    <input
                      min="0"
                      v-model="personalItem.date_of_birth"
                      class="form-control"
                      type="date"
                    />
                    <span
                      v-if="errors && errors.date_of_birth"
                      class="text-danger mt-2"
                      >{{ errors.date_of_birth[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("religion") }}</label>
                    <input
                      v-model="personalItem.religion"
                      class="form-control"
                      type=""
                    />
                    <span
                      v-if="errors && errors.religion"
                      class="text-danger mt-2"
                      >{{ errors.religion[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("marital status")
                    }}</label>
                    <select
                      v-model="personalItem.marital_status"
                      class="form-control"
                      aria-label="Default select"
                    >
                      <option value="">select...</option>
                      <option value="1">Married</option>
                      <option value="2">Single</option>
                    </select>
                    <span
                      v-if="errors && errors.marital_status"
                      class="text-danger mt-2"
                      >{{ errors.marital_status[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("no of spouse")
                    }}</label>
                    <input
                      min="0"
                      v-model="personalItem.no_of_spouse"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.no_of_spouse"
                      class="text-danger mt-2"
                      >{{ errors.no_of_spouse[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("no of children")
                    }}</label>
                    <input
                      min="0"
                      v-model="personalItem.no_of_children"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.no_of_children"
                      class="text-danger mt-2"
                      >{{ errors.no_of_children[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("father name")
                    }}</label>
                    <input
                      min="0"
                      v-model="personalItem.father_name"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.father_name"
                      class="text-danger mt-2"
                      >{{ errors.father_name[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("mother name")
                    }}</label>
                    <input
                      min="0"
                      v-model="personalItem.mother_name"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.mother_name"
                      class="text-danger mt-2"
                      >{{ errors.mother_name[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("gender") }}</label>
                    <select
                      v-model="personalItem.gender"
                      class="form-select"
                      aria-label="Default select"
                    >
                      <option value="">Select...</option>
                      <option value="1">Male</option>
                      <option value="2">Female</option>
                    </select>
                    <span
                      v-if="errors && errors.gender"
                      class="text-danger mt-2"
                      >{{ errors.gender[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="12">
                  <a
                    href="javascrip:void(0)"
                    @click="add_other_personal_info = !add_other_personal_info"
                    >{{
                      caps(
                        `${
                          add_other_personal_info ? "hide" : "show"
                        } other field`
                      )
                    }}</a
                  >
                </v-col>

                <v-col cols="6" v-if="add_other_personal_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("other text")
                    }}</label>
                    <input
                      v-model="personalItem.other_text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.other_text"
                      class="text-danger"
                      >{{ errors.other_text[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6" v-if="add_other_personal_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("other value")
                    }}</label>
                    <input
                      v-model="personalItem.other_value"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.other_value"
                      class="text-danger mt-2"
                      >{{ errors.other_value[0] }}</span
                    >
                  </div>
                </v-col>

                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>

              <!-- <v-row v-for="(item, index) in editedItem.items" :key="index">
                <v-col cols="5">
                  <v-text-field v-model="item.key" label="label"></v-text-field>
                </v-col>
                <v-col cols="5">
                  <v-text-field
                    v-model="item.value"
                    label="value"
                  ></v-text-field>
                </v-col>
                <v-col cols="2" class="mt-5">
                  <v-btn dark class="error" fab @click="removeItem(index)" x-small>
                    <v-icon>mdi-delete</v-icon>
                  </v-btn>
                </v-col>
                <span v-if="errors && errors.length" class="error--text">{{
                  errors[0]
                }}</span>
              </v-row> -->
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_personal_info">
              Cancel
            </v-btn>
            <v-btn class="primary" small @click="save_personal_info"
              >Save</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="contact_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Contact Info </span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("local address")
                    }}</label>
                    <input
                      v-model="contactItem.local_address"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.local_address"
                      class="text-danger mt-2"
                      >{{ errors.local_address[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("  email") }}</label>
                    <input
                      v-model="contactItem.local_email"
                      class="form-control"
                      type="email"
                    />
                    <span
                      v-if="errors && errors.local_email"
                      class="text-danger mt-2"
                      >{{ errors.local_email[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("phone number")
                    }}</label>
                    <input
                      v-model="contactItem.phone_number"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.phone_number"
                      class="text-danger mt-2"
                      >{{ errors.phone_number[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("whatsapp number")
                    }}</label>
                    <input
                      v-model="contactItem.whatsapp_number"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.whatsapp_number"
                      class="text-danger mt-2"
                      >{{ errors.whatsapp_number[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("phone relative number")
                    }}</label>
                    <input
                      v-model="contactItem.phone_relative_number"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.phone_relative_number"
                      class="text-danger mt-2"
                      >{{ errors.phone_relative_number[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("relation") }}</label>
                    <input
                      v-model="contactItem.relation"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.relation"
                      class="text-danger mt-2"
                      >{{ errors.relation[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("local city")
                    }}</label>
                    <input
                      v-model="contactItem.local_city"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.local_city"
                      class="text-danger mt-2"
                      >{{ errors.local_city[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("local country")
                    }}</label>
                    <input
                      v-model="contactItem.local_country"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.local_country"
                      class="text-danger mt-2"
                      >{{ errors.local_country[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="12">
                  <a
                    href="javascrip:void(0)"
                    @click="add_other_bank_info = !add_other_bank_info"
                    >{{
                      caps(
                        `${
                          add_other_bank_info ? "hide" : "show"
                        } Home Country Details`
                      )
                    }}</a
                  >
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("  address")
                    }}</label>
                    <input
                      v-model="contactItem.home_address"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.home_address"
                      class="text-danger"
                      >{{ errors.home_address[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("tel") }}</label>
                    <input
                      v-model="contactItem.home_tel"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.home_tel"
                      class="text-danger"
                      >{{ errors.home_tel[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("  mobile") }}</label>
                    <input
                      v-model="contactItem.home_mobile"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.home_mobile"
                      class="text-danger"
                      >{{ errors.home_mobile[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("  fax") }}</label>
                    <input
                      v-model="contactItem.home_fax"
                      class="form-control"
                      type="number"
                    />
                    <span
                      v-if="errors && errors.home_fax"
                      class="text-danger"
                      >{{ errors.home_fax[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("  city") }}</label>
                    <input
                      v-model="contactItem.home_city"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.home_city"
                      class="text-danger"
                      >{{ errors.home_city[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("state") }}</label>
                    <input
                      v-model="contactItem.home_state"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.home_state"
                      class="text-danger"
                      >{{ errors.home_state[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("  country")
                    }}</label>
                    <input
                      v-model="contactItem.home_country"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.home_country"
                      class="text-danger"
                      >{{ errors.home_country[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("  email") }}</label>
                    <input
                      v-model="contactItem.home_email"
                      class="form-control"
                      type="email"
                    />
                    <span
                      v-if="errors && errors.home_email"
                      class="text-danger"
                      >{{ errors.home_email[0] }}</span
                    >
                  </div>
                </v-col>
                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_contact_info">
              Cancel
            </v-btn>
            <v-btn class="primary" small @click="save_contact_info">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="bank_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Bank Info </span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("bank name")
                    }}</label>
                    <input v-model="BankInfo.bank_name" class="form-control" />
                    <span
                      v-if="errors && errors.bank_name"
                      class="text-danger mt-2"
                      >{{ errors.bank_name[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("bank address")
                    }}</label>
                    <input v-model="BankInfo.address" class="form-control" />
                    <span
                      v-if="errors && errors.address"
                      class="text-danger mt-2"
                      >{{ errors.address[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("account no")
                    }}</label>
                    <input v-model="BankInfo.account_no" class="form-control" />
                    <span
                      v-if="errors && errors.account_no"
                      class="text-danger mt-2"
                      >{{ errors.account_no[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("account title")
                    }}</label>
                    <input
                      v-model="BankInfo.account_title"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.account_title"
                      class="text-danger mt-2"
                      >{{ errors.account_title[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("iban") }}</label>
                    <input v-model="BankInfo.iban" class="form-control" />
                    <span
                      v-if="errors && errors.iban"
                      class="text-danger mt-2"
                      >{{ errors.iban[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="12">
                  <a
                    href="javascrip:void(0)"
                    @click="add_other_bank_info = !add_other_bank_info"
                    >{{
                      caps(
                        `${add_other_bank_info ? "hide" : "show"} other field`
                      )
                    }}</a
                  >
                </v-col>

                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("other text")
                    }}</label>
                    <input v-model="BankInfo.other_text" class="form-control" />
                    <span
                      v-if="errors && errors.other_text"
                      class="text-danger"
                      >{{ errors.other_text[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6" v-if="add_other_bank_info">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("other value")
                    }}</label>
                    <input
                      v-model="BankInfo.other_value"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.other_value"
                      class="text-danger mt-2"
                      >{{ errors.other_value[0] }}</span
                    >
                  </div>
                </v-col>

                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_bank_info"> Cancel </v-btn>
            <v-btn class="primary" small @click="save_bank_info">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="passport_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Passport Info </span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("passport no")
                    }}</label>
                    <input
                      v-model="passport_list.passport_no"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.passport_no"
                      class="text-danger mt-2"
                      >{{ errors.passport_no[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("note") }}</label>
                    <input
                      v-model="passport_list.note"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.note"
                      class="text-danger mt-2"
                      >{{ errors.note[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("place of issues")
                    }}</label>
                    <input
                      v-model="passport_list.place_of_issues"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.place_of_issues"
                      class="text-danger mt-2"
                      >{{ errors.place_of_issues[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("country") }}</label>
                    <input
                      v-model="passport_list.country"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.country"
                      class="text-danger mt-2"
                      >{{ errors.country[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("issue date")
                    }}</label>
                    <input
                      v-model="passport_list.issue_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.issue_date"
                      class="text-danger mt-2"
                      >{{ errors.issue_date[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("expiry date")
                    }}</label>
                    <input
                      v-model="passport_list.expiry_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.expiry_date"
                      class="text-danger mt-2"
                      >{{ errors.expiry_date[0] }}</span
                    >
                  </div>
                </v-col>

                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_passport_info">
              Cancel
            </v-btn>
            <v-btn class="primary" small @click="save_passport_info"
              >Save</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="emirate_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Emirate Info </span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("emirate id")
                    }}</label>
                    <input
                      v-model="emirateItems.emirate_id"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.emirate_id"
                      class="text-danger mt-2"
                      >{{ errors.emirate_id[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("name") }}</label>
                    <input
                      v-model="emirateItems.name"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.name"
                      class="text-danger mt-2"
                      >{{ errors.name[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("gender") }}</label>
                    <select
                      v-model="emirateItems.gender"
                      class="form-select"
                      aria-label="Default select"
                    >
                      <option value="">Select...</option>
                      <option value="1">Male</option>
                      <option value="2">Female</option>
                    </select>
                    <span
                      v-if="errors && errors.gender"
                      class="text-danger mt-2"
                      >{{ errors.gender[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("nationality")
                    }}</label>
                    <input
                      v-model="emirateItems.nationality"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.nationality"
                      class="text-danger mt-2"
                      >{{ errors.nationality[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("issue date")
                    }}</label>
                    <input
                      v-model="emirateItems.issue"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.issue"
                      class="text-danger mt-2"
                      >{{ errors.issue[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("expiry date")
                    }}</label>
                    <input
                      v-model="emirateItems.expiry"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.expiry"
                      class="text-danger mt-2"
                      >{{ errors.expiry[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("Date of birth")
                    }}</label>
                    <input
                      v-model="emirateItems.date_of_birth"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.date_of_birth"
                      class="text-danger mt-2"
                      >{{ errors.expiry[0] }}</span
                    >
                  </div>
                </v-col>

                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_emirate_info">
              Cancel
            </v-btn>
            <v-btn class="primary" small @click="save_emirate_info">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="visa_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Visa Info </span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("Visa no") }}</label>
                    <input
                      v-model="visaItem.visa_no"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.visa_no"
                      class="text-danger mt-2"
                      >{{ errors.visa_no[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("note") }}</label>
                    <input
                      v-model="visaItem.note"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.note"
                      class="text-danger mt-2"
                      >{{ errors.note[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("place of issues")
                    }}</label>
                    <input
                      v-model="visaItem.place_of_issues"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.place_of_issues"
                      class="text-danger mt-2"
                      >{{ errors.place_of_issues[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("country") }}</label>
                    <input
                      v-model="visaItem.country"
                      class="form-control"
                      type="text"
                    />
                    <span
                      v-if="errors && errors.country"
                      class="text-danger mt-2"
                      >{{ errors.country[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("issue date")
                    }}</label>
                    <input
                      v-model="visaItem.issue_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.issue_date"
                      class="text-danger mt-2"
                      >{{ errors.issue_date[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("expiry date")
                    }}</label>
                    <input
                      v-model="visaItem.expiry_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.expiry_date"
                      class="text-danger mt-2"
                      >{{ errors.expiry_date[0] }}</span
                    >
                  </div>
                </v-col>

                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("labour no")
                    }}</label>
                    <input
                      v-model="visaItem.labour_no"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.labour_no"
                      class="text-danger mt-2"
                      >{{ errors.labour_no[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("expiry date")
                    }}</label>
                    <input
                      v-model="visaItem.personal_no"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.personal_no"
                      class="text-danger mt-2"
                      >{{ errors.personal_no[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("labour issue date")
                    }}</label>
                    <input
                      v-model="visaItem.labour_issue_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.labour_issue_date"
                      class="text-danger mt-2"
                      >{{ errors.labour_issue_date[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("labour expiry date")
                    }}</label>
                    <input
                      v-model="visaItem.labour_expiry_date"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.labour_expiry_date"
                      class="text-danger mt-2"
                      >{{ errors.labour_expiry_date[0] }}</span
                    >
                  </div>
                </v-col>
                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_visa_info"> Cancel </v-btn>
            <v-btn class="primary" small @click="save_visa_info">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="documents" max-width="500px">
        <v-card>
          <v-card-actions>
            <span class="headline">{{ caps(`documents`) }}</span>
            <v-spacer></v-spacer>
            <v-btn dark class="primary" fab @click="addDocumentInfo" x-small>
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row v-for="(d, index) in Document.items" :key="index">
                <v-col cols="5">
                  <v-text-field v-model="d.title" label="Title"></v-text-field>

                  <span
                    v-if="errors && errors.title"
                    class="text-danger mt-2"
                    >{{ errors.title[0] }}</span
                  >
                </v-col>
                <v-col cols="5">
                  <div class="form-group">
                    <v-file-input
                      v-model="d.file"
                      placeholder="Upload your file"
                      label="Attachment"
                    >
                      <template v-slot:selection="{ text }">
                        <v-chip v-if="text" small label color="primary">
                          {{ text }}
                        </v-chip>
                      </template>
                    </v-file-input>

                    <span
                      v-if="errors && errors.attachment"
                      class="text-danger mt-2"
                      >{{ errors.attachment[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="2">
                  <div class="form-group">
                    <v-btn
                      dark
                      class="error mt-5"
                      fab
                      @click="removeItem(index)"
                      x-small
                    >
                      <v-icon>mdi-delete</v-icon>
                    </v-btn>
                  </div>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_document_info">
              Cancel
            </v-btn>
            <v-btn
              :disabled="!Document.items.length"
              class="primary"
              small
              @click="save_document_info"
              >Save</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="qualification_info" max-width="700px">
        <v-card>
          <v-card-actions>
            <span class="headline">Qualification Info</span>
            <v-spacer></v-spacer>
          </v-card-actions>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("certificate")
                    }}</label>
                    <input
                      v-model="qualification_list.certificate"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.certificate"
                      class="text-danger mt-2"
                      >{{ errors.certificate[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("collage") }}</label>
                    <input
                      v-model="qualification_list.collage"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.collage"
                      class="text-danger mt-2"
                      >{{ errors.collage[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{
                      caps("start date")
                    }}</label>
                    <input
                      v-model="qualification_list.start"
                      class="form-control"
                      type="date"
                    />
                    <span
                      v-if="errors && errors.start"
                      class="text-danger mt-2"
                      >{{ errors.start[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("end date") }}</label>
                    <input
                      v-model="qualification_list.end"
                      type="date"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.end"
                      class="text-danger mt-2"
                      >{{ errors.end[0] }}</span
                    >
                  </div>
                </v-col>
                <v-col cols="6">
                  <div class="form-group">
                    <label class="col-form-label">{{ caps("type") }}</label>
                    <input
                      v-model="qualification_list.type"
                      type="text"
                      class="form-control"
                    />
                    <span
                      v-if="errors && errors.type"
                      class="text-danger mt-2"
                      >{{ errors.type[0] }}</span
                    >
                  </div>
                </v-col>
                <span v-if="errors && errors.length" class="error--text">{{
                  errors
                }}</span>
              </v-row>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="error" small @click="close_qualification_info">
              Cancel
            </v-btn>
            <v-btn class="primary" small @click="save_qualification_info"
              >Save</v-btn
            >
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-card elevation="2">
        <v-row>
          <v-col cols="6" style="border-right: 1px dashed #808080">
            <v-list-item>
              <v-list-item-avatar tile size="120">
                <v-img
                  style="
                    border-radius: 50%;
                    height: 125px;
                    width: 50%;
                    margin: 0 auto;
                  "
                  :src="payload.profile_picture || '/no-image.PNG'"
                >
                </v-img>
              </v-list-item-avatar>
              <v-badge
                class="profile-image-badge"
                :style="payload.profile_picture ? '' : 'top:55px; left:102px;'"
                bordered
                :color="payload.status == 1 ? 'green' : 'red'"
              >
              </v-badge>
              <v-list-item-content>
                <div class="text-overline mb-1">
                  Employee Id : {{ (payload && payload.employee_id) || "---" }}
                </div>
                <div class="text-overline mb-1">
                  Employee Device Id :
                  {{ (payload && payload.system_user_id) || "---" }}
                </div>

                <div class="text-overline mb-1">
                  <div class="d-flex flex-row">
                    Status :
                    <!-- <v-switch
                      color="success"
                      class="mt-0 ml-2"
                      v-model="payload.status"
                    ></v-switch> -->
                    <!-- {{ switch_img }} -->

                    <img
                      lazy-src="https://picsum.photos/id/11/10/6"
                      style="
                        max-height: 36px;
                        max-width: 78px;
                        margin: -3px 4px -15px -7px;
                      "
                      :src="payload.status == true ? '/on.png' : '/off.png'"
                      @click="payload.status = !payload.status"
                    />
                    <v-btn
                      class="primary mt-1"
                      x-small
                      @click="update_setting"
                      >{{ payload.status ? "Active" : " Inactive" }}</v-btn
                    >
                  </div>
                </div>
              </v-list-item-content>
            </v-list-item>

            <v-list-item>
              <v-list-item-content>
                <v-row class="mt-2">
                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      Role
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ (payload.role && payload.role.name) || "---" }}
                  </v-col>
                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      First Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ (payload.first_name && payload.first_name) || "---" }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      Last Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{ (payload.last_name && payload.last_name) || "---" }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      Department
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{
                      (payload.department && payload.department.name) || "---"
                    }}
                  </v-col>

                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      Sub Department
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{
                      (payload.sub_department && payload.sub_department.name) ||
                        "---"
                    }}</v-col
                  >

                  <v-col cols="3">
                    <v-list-item-title class="text-bold mb-1">
                      Designation
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="8">
                    {{
                      (payload.designation && payload.designation.name) || "---"
                    }}
                  </v-col>
                </v-row>
              </v-list-item-content>
            </v-list-item>
          </v-col>
          <v-col cols="6">
            <v-row>
              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  Phone Number
                </v-list-item-title>
              </v-col>
              <v-col cols="3">
                {{ payload.phone_number }}
              </v-col>

              <v-col cols="3" class="text-right" style="margin: -8px">
                <v-icon
                  v-if="can(`employee_edit`)"
                  @click="editItem(`/employees/${$route.params.id}`)"
                  small
                  class="grey"
                  style="border-radius: 50%; padding: 5px"
                  color="secondary"
                  >mdi-pencil</v-icon
                >
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  Phone Number (Relative)
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.phone_relative_number || "----" }}
              </v-col>
              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  Relation
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.relation || "----" }}
              </v-col>
              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  Whatsapp Number
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.whatsapp_number || "----" }}
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  User Email
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.user.email || "----" }}
              </v-col>

              <v-col cols="6">
                <v-list-item-title class="text-bold mb-1">
                  Joining Date
                </v-list-item-title>
              </v-col>
              <v-col cols="6">
                {{ payload.show_joining_date || "----" }}
              </v-col>
            </v-row>
          </v-col>
        </v-row>
      </v-card>
      <v-card>
        <v-tabs md="12" class="mt-5 mb-5">
          <v-tab v-if="can(`employee_access`)">
            <v-icon> mdi-details </v-icon>
            Personal info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon> mdi-details </v-icon>
            Contact info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-bank </v-icon>
            Passport info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-bank </v-icon>
            Emirates info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-bank </v-icon>
            Visa info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-bank </v-icon>
            Bank info
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-file </v-icon>
            Documents
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-file </v-icon>
            Qualification
          </v-tab>

          <v-tab v-if="can(`employee_access`)">
            <v-icon left> mdi-wrench</v-icon>
            Setting
          </v-tab>

          <!-- Personal info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Nationality
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.nationality || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Religion
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.religion || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Marital status
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{
                      personalItem.marital_status
                        ? personalItem.marital_status == 1
                          ? "Married"
                          : "Single"
                        : "---"
                    }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Employment of spouse
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.no_of_spouse || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Date of Birth
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.date_of_birth || "---" }}
                  </v-col>
                </v-row>
              </v-col>

              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      No. of children
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ personalItem.no_of_children || "---" }}
                  </v-col>

                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="personal_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Father Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.father_name || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Mother Name
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.mother_name || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      Gender
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ personalItem.gender == 1 ? "Male" : "Female" || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Contact info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="12" class="text-right py-0">
                <v-col
                  class="text-right"
                  style="margin: -30px -30px -11px -5px"
                >
                  <v-icon
                    v-if="can(`employee_edit`)"
                    @click="contact_info = true"
                    small
                    class="grey"
                    style="border-radius: 50%; padding: 5px"
                    color="secondary"
                    >mdi-pencil</v-icon
                  >
                </v-col>
              </v-col>

              <v-col cols="6" style="border-right: 1px dashed #808080">
                <div
                  class="
                    theme--dark
                    mt-0
                    px-2
                    py-2
                    gradient-custom-2
                    text-white
                  "
                >
                  Local Contact Details
                </div>
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`address`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ payload.local_address || "---" }}
                  </v-col>

                  <v-col cols="3" class="text-right" style="margin: -8px">
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`whatsapp number`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.whatsapp_number || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`phone number`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.phone_number || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`  phone relative number`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.phone_relative_number || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`relation`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.relation || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`city`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.local_city || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`  country`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.local_country || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`  email`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.local_email || "---" }}
                  </v-col>
                </v-row>
              </v-col>

              <v-col cols="6">
                <div
                  class="
                    theme--dark
                    mt-0
                    mr-1
                    px-2
                    py-2
                    gradient-custom-2
                    text-white
                  "
                >
                  Home Country Contact Details
                </div>
                <v-row class="pa-4">
                  <!-- home country contact details -->
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    address`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_address || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    telephone`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_tel || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    mobile`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_mobile || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    city`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_city || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    state`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_state || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    country`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_country || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`    email`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ payload.home_email || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Passport info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`passport no`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.passport_no || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`place of issues`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.place_of_issues || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`country`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.country || "---" }}
                  </v-col>
                </v-row>
              </v-col>

              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`issue date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.issue_date || "---" }}
                  </v-col>
                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="passport_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`expiry date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.expiry_date || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`note`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ passport_list.note || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- emirates info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`emirate id`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ emirateItems.emirate_id || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`name`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.name || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`gender`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.gender || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`date of birth`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.date_of_birth || "---" }}
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`nationality`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.nationality || "---" }}
                  </v-col>
                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="emirate_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`issue`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.issue || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`expiry`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ emirateItems.expiry || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- visa info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`visa no`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ visaItem.visa_no || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`place of issues`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.place_of_issues || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`country`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.country || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`issue date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.issue_date || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`expiry date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.expiry_date || "---" }}
                  </v-col>
                </v-row>
              </v-col>
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`labour no`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.labour_no || "---" }}
                  </v-col>
                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="visa_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`personal no`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.personal_no || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`labour issue date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.labour_issue_date || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`labour expiry date`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.labour_expiry_date || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`note`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ visaItem.note || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Bank info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Bank name`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ BankInfo.bank_name || "---" }}
                  </v-col>
                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="bank_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Bank address`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ BankInfo.address || "---" }}
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Bank account No`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ BankInfo.account_no || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Bank account Title`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ BankInfo.account_title || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`iban`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ BankInfo.iban || "---" }}
                  </v-col>
                  <v-col cols="5" v-if="BankInfo && BankInfo.other_text">
                    <v-list-item-title class="text-bold mb-1">
                      {{ BankInfo.other_text || "---" }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7" v-if="BankInfo && BankInfo.other_value">
                    {{ BankInfo.other_value || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Documents -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row
                  v-for="(d, index) in document_list"
                  :key="index"
                  class="pa-2"
                >
                  <v-col cols="5">
                    <span>{{ d.title }}</span>
                  </v-col>
                  <v-col cols="4">
                    <a :href="d.attachment" target="_blank">
                      <v-btn x-small class="primary"> open file </v-btn>
                    </a>
                    <v-icon color="error" @click="delete_document(d.id)">
                      mdi-delete
                    </v-icon>
                  </v-col>

                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`) && !index"
                      @click="documents = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>
                </v-row>
                <v-col v-if="document_list.length == 0">
                  <v-icon
                    @click="documents = true"
                    small
                    class="grey"
                    style="border-radius: 50%; padding: 5px"
                    color="secondary"
                    >mdi-pencil</v-icon
                  >
                  <!-- <v-btn v-if="can(`employee_edit`)" @click="documents = true" x-small class="primary">{{caps(`add document`)}}</v-btn>                   -->
                </v-col>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Qualification -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`certificate`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="4">
                    {{ qualification_list.certificate || "---" }}
                  </v-col>

                  <v-col cols="3" class="text-right" style="margin: -8px">
                    <v-icon
                      v-if="can(`employee_edit`)"
                      @click="qualification_info = true"
                      small
                      class="grey"
                      style="border-radius: 50%; padding: 5px"
                      color="secondary"
                      >mdi-pencil</v-icon
                    >
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`type`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ qualification_list.type || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`collage`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ qualification_list.collage || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`start`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ qualification_list.start || "---" }}
                  </v-col>
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`end`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    {{ qualification_list.end || "---" }}
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>

          <!-- Other Info -->
          <v-tab-item v-if="can(`employee_access`)">
            <v-row class="pl-1 mt-5 mb-5">
              <v-col cols="6">
                <v-row class="pa-4 ml-2 no-gutters">
                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Status`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    <div class="text-overline mb-1">
                      <v-switch
                        color="success"
                        class="mt-0 ml-2"
                        v-model="setting.status"
                      ></v-switch>
                    </div>
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Over Time `) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    <div class="text-overline mb-1">
                      <v-switch
                        color="success"
                        class="mt-0 ml-2"
                        v-model="setting.overtime"
                      ></v-switch>
                    </div>
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(`Mobile Application Access`) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    <div class="text-overline mb-1">
                      <v-switch
                        color="success"
                        class="mt-0 ml-2"
                        v-model="setting.mobile_application"
                      ></v-switch>
                    </div>
                  </v-col>

                  <v-col cols="5">
                    <v-list-item-title class="text-bold mb-1">
                      {{ caps(``) }}
                    </v-list-item-title>
                  </v-col>
                  <v-col cols="7">
                    <v-btn class="primary mt-1" @click="update_setting()"
                      >Save</v-btn
                    >
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-tab-item>
        </v-tabs>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
</template>

<script>
import Preloader from "../../../components/Preloader.vue";
import NoAccess from "../../../components/NoAccess.vue";

export default {
  // layout: "employee",
  data: () => ({
    add_other_personal_info: false,
    switch_img: "",
    add_other_bank_info: false,
    loading: true,
    payload: {},
    personal_info: false,
    passport_info: false,
    visa_info: false,
    emirate_info: false,
    qualification_info: false,
    bank_info: false,
    contact_info: false,
    documents: false,
    e1: 1,
    errors: [],
    data: [],
    devices: [],
    snackbar: false,
    response: "",

    editedItem: {
      passport_no: "",
      passport_expiry: "",
      tel: "",
      nationality: "",
      religion: "",
      marital_status: "",
      no_of_spouse: "",
      no_of_children: "",
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
      local_tel: "",
      phone_number: "",
      whatsapp_number: "",
      phone_relative_number: "",
      whatsapp_relative_number: "",
      local_fax: "",
      local_email: "",

      local_country: "",
      local_residence_no: "",
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

    setting: {
      status: "",
      overtime: "",
      mobile_application: ""
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

    // passport_list: {
    //   passport_no: "",
    //   note: "",
    //   place_of_issues: "",
    //   country: "",
    //   issue_date: "",
    //   expiry_date: "",

    //   company_id: "",
    //   employee_id: "",
    // },

    Document: {
      items: [{ title: "", file: "" }]
    },
    document_list: [],
    qualification_list: [],
    passport_list: []
  }),
  async created() {
    this.getEmployeeDetails();
    this.getBankInfo();
    this.getDocumentInfo();
    this.getQualificationInfo();
    this.getPassportInfo();
    this.getPersonalInfo();
    this.getVisaInfo();
    this.getEmirateInfo();

    // console.log(this.$auth?.user?.employee.company_id);

    // let dt = new Date();

    // let y = dt.getFullYear();
    // let m = this.addZeroForLessThanTen(dt.getMonth() + 1);
    // let d = this.addZeroForLessThanTen(dt.getDate());

    // let final_date = y + "-" + m + "-" + d;

    // this.editedItem = {
    //   ...this.editedItem,
    //   passport_no : Math.floor((Math.random() * 10009998760) + 1),
    //   passport_expiry : final_date,
    //   tel : Math.floor((Math.random() * 10009998760) + 1)
    // };
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    personal_info(val) {
      val || this.close_personal_info();
    },
    emirate_info(val) {
      val || this.close_emirate_info();
    },
    visa_info(val) {
      val || this.close_visa_info();
    },
    bank_info(val) {
      val || this.close_bank_info();
    },
    documents(val) {
      val || this.close_document_info();
    },

    contact_info(val) {
      val || this.close_contact_info();
    },

    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true
    }
  },
  methods: {
    mapper(obj) {
      let payload = new FormData();

      for (let x in obj) {
        payload.append(x, obj[x]);
      }

      return payload;
    },
    addZeroForLessThanTen(n) {
      return n > 10 ? n : "0" + n;
    },
    caps(str) {
      return str.replace(/\b\w/g, c => c.toUpperCase());
    },
    addDocumentInfo() {
      this.Document.items.push({
        title: "",
        file: ""
      });
    },
    removeItem(index) {
      this.Document.items.splice(index, 1);
    },

    save_personal_info() {
      let payload = {
        ...this.personalItem,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`personalinfo`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_personal_info();
          }
        })
        .catch(e => console.log(e));
    },

    save_contact_info() {
      let payload = {
        ...this.contactItem,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`employee/update/contact`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_contact_info();
          }
        })
        .catch(e => console.log(e));
    },

    save_passport_info() {
      let payload = {
        ...this.passport_list,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`passport`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_passport_info();
          }
        })
        .catch(e => console.log(e));
    },

    save_emirate_info() {
      let payload = {
        ...this.emirateItems,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`emirate`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_emirate_info();
          }
        })
        .catch(e => console.log(e));
    },

    save_visa_info() {
      let payload = {
        ...this.visaItem,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`visa`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_visa_info();
          }
        })
        .catch(e => console.log(e));
    },
    save_qualification_info() {
      let payload = {
        ...this.qualification_list,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`qualification`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_qualification_info();
          }
        })
        .catch(e => console.log(e));
    },

    save_bank_info() {
      let payload = {
        ...this.BankInfo,
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id)
      };

      this.$axios
        .post(`bankinfo`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.close_bank_info();
          }
        })
        .catch(e => console.log(e));
    },

    update_setting() {
      let payload = {
        company_id: this.$auth?.user?.employee?.company_id,
        employee_id: parseInt(this.$route.params.id),
        status: this.payload.status
      };

      this.$axios
        .post(`employee/update/setting`, payload)
        .then(({ data }) => {
          this.loading = false;

          if (data != 1) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "successfully updated ";
          }
        })
        .catch(e => console.log(e));
    },

    // update_setting() {
    //   let payload = {
    //     company_id: this.$auth?.user?.employee?.company_id,
    //     employee_id: parseInt(this.$route.params.id),
    //     status: this.setting.status,
    //     overtime: this.setting.overtime,
    //     mobile_application: this.setting.mobile_application,
    //   };
    //   // console.log(payload);
    //   // return;

    //   this.$axios
    //     .post(`employee/update/setting`, payload)
    //     .then(({ data }) => {
    //       this.loading = false;
    //       console.log(data);

    //       if (data != 1) {
    //         this.errors = data.errors;
    //         console.log("error");
    //       } else {
    //         this.errors = [];
    //         this.snackbar = true;
    //         this.response = "successfully updated ";
    //         console.log("success");
    //       }
    //     })
    //     .catch((e) => console.log(e));
    // },

    save_document_info() {
      let options = {
        headers: {
          "Content-Type": "multipart/form-data"
        }
      };
      let payload = new FormData();

      this.Document.items.forEach(e => {
        payload.append(`items[][title]`, e.title);
        payload.append(`items[][file]`, e.file || {});
      });

      payload.append(`company_id`, this.$auth?.user?.employee?.company_id);
      payload.append(`employee_id`, parseInt(this.$route.params.id));

      this.$axios
        .post(`documentinfo`, payload, options)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.getDocumentInfo();
            this.Document.items = [{ title: "", file: "" }];
            this.close_document_info();
          }
        })
        .catch(e => console.log(e));
    },
    close_personal_info() {
      this.personal_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_passport_info() {
      this.passport_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_visa_info() {
      this.visa_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_emirate_info() {
      this.emirate_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_qualification_info() {
      this.qualification_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_bank_info() {
      this.bank_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_contact_info() {
      this.contact_info = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    close_document_info() {
      this.documents = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },

    can(per) {
      return true;
      let u = this.$auth.user;
      return (
        (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
      );
    },

    getBankInfo() {
      this.$axios.get(`bankinfo/${this.$route.params.id}`).then(({ data }) => {
        this.BankInfo = {
          ...data
        };
        this.loading = false;
      });
    },
    getPassportInfo() {
      this.$axios.get(`passport/${this.$route.params.id}`).then(({ data }) => {
        // this.passport_list = data;
        this.passport_list = {
          ...data
        };
        this.loading = false;
      });
    },

    getPersonalInfo() {
      this.$axios
        .get(`personalinfo/${this.$route.params.id}`)
        .then(({ data }) => {
          this.personalItem = {
            ...data
          };
          // this.personalItem = data;
          this.loading = false;
        });
    },

    getVisaInfo() {
      this.$axios.get(`visa/${this.$route.params.id}`).then(({ data }) => {
        // this.visaItem = data;
        this.visaItem = {
          ...data
        };
        this.loading = false;
      });
    },

    getEmirateInfo() {
      this.$axios.get(`emirate/${this.$route.params.id}`).then(({ data }) => {
        // this.visaItem = data;
        this.emirateItems = {
          ...data
        };
        this.loading = false;
      });
    },

    getDocumentInfo() {
      this.$axios
        .get(`documentinfo/${this.$route.params.id}`)
        .then(({ data }) => {
          this.document_list = data;
          this.loading = false;
        });
    },

    getQualificationInfo() {
      this.$axios
        .get(`qualification/${this.$route.params.id}`)
        .then(({ data }) => {
          this.qualification_list = {
            ...data
          };
          // this.qualification_list = data;
          this.loading = false;
        });
    },

    getEmployeeDetails() {
      this.$axios.get(`employee/${this.$route.params.id}`).then(({ data }) => {
        this.payload = data;
        this.contactItem = data;
        this.setting = data;

        this.loading = false;
      });
    },
    formatted_date(v) {
      let [year, month, date] = v.split("/");
      return `${year}-${month}-${date}`;
    },
    editItem(item) {
      this.$router.push(item);
    },
    delete_document(id) {
      this.$axios
        .delete(`documentinfo/${id}`)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.getDocumentInfo();
            this.close_document_info();
          }
        })
        .catch(e => console.log(e));
    }
  },
  components: { NoAccess, Preloader }
};
</script>

<style scoped>
.text-bold {
  font-weight: 500;
  color: #34444c;
}

.profile-image-badge {
  position: absolute;
  top: 30px;
  left: 112px;
}

.v-badge__badge {
  border-radius: 40px;
  color: #ffffff;
  display: inline-block;
  font-size: 12px;
  height: 25px;
  letter-spacing: 0;
  line-height: 1;
  min-width: 25px;
  padding: 4px 6px;
  pointer-events: auto;
  position: absolute;
  text-align: center;
  text-indent: 0;
  top: auto;
  transition: 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
  white-space: nowrap;
}

.gradient-custom-2 {
  border-top-right-radius: 0.3rem;
  border-bottom-right-radius: 0.3rem;
  border-bottom-left-radius: 0.3rem;
  border-top-left-radius: 0.3rem;
}
</style>
