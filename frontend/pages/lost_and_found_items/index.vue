<template>
    <div v-if="can('lost_and_found_access') && can('lost_and_found_view')">
        <div class="text-center ma-2">
            <v-snackbar v-model="snackbar" top="top" :color="snackbarColor" elevation="24">
                {{ response }}
            </v-snackbar>
        </div>

        <div>
            <v-dialog v-model="newItem" max-width="60%">
                <template>
                    <v-card>
                        <v-tabs desne small v-model="tab" background-color="rgb(43 52 56)" right dark icons-and-text>
                            <v-tabs-slider></v-tabs-slider>

                            <v-tab href="#tab-1" desne small>
                                Guest
                                <v-icon>mdi-human-male-female-child</v-icon>
                            </v-tab>
                            <v-tab href="#tab-2" :disabled="disableNextProcess" desne small>
                                Lost
                                <v-icon>mdi-magnify-minus</v-icon>
                            </v-tab>
                            <v-tab href="#tab-3" :disabled="editedIndex == -1" desne small>
                                Found
                                <v-icon>mdi-magnify-plus</v-icon>
                            </v-tab>

                            <v-tab href="#tab-4" :disabled="editedIndex == -1" desne small>
                                Return
                                <v-icon>mdi-emoticon-happy-outline</v-icon>
                            </v-tab>
                            <v-tab @click="newItem = false">
                                <v-icon dark class="pa-0">mdi mdi-close-box</v-icon>
                            </v-tab>
                        </v-tabs>
                        <v-tabs-items v-model="tab">
                            <v-tab-item value="tab-1">
                                <v-card>
                                    <!-- <v-card-title v-if="editedIndex == -1">Search Booking Id</v-card-title>
                                    <v-card-title v-else>Guest Details</v-card-title> -->
                                    <v-card-text>
                                        <v-row class="mb-2 mt=4" v-if="editedIndex == -1">
                                            <v-col md="3" cols="12">

                                                <v-text-field type="number" v-model="reservation_no"
                                                    placeholder="Enter Booking ID" outlined :hide-details="true" dense
                                                    clearable @click:clear="clearForm()"></v-text-field>
                                            </v-col>
                                            <v-col md="3" cols="12">

                                                <v-btn :loading="loading" class="primary" @click="searchBookingId()">Get
                                                    Reservation Details</v-btn>

                                            </v-col>
                                            <v-col md="6" cols="12">
                                                <span style="color:red;text-align:left" class="mr-5">
                                                    {{ response }}
                                                </span>


                                                <span v-if="bookingData && bookingData.id && editedItem && editedItem.id"
                                                    style="color:red;text-align:left" class="mr-5">
                                                    Lost Item name(s):
                                                    {{ editedItem && editedItem.item_name ? editedItem.item_name : '' }}

                                                </span>
                                            </v-col>
                                        </v-row>

                                        <v-row class="mb-2 mt=4">
                                            <v-col md="3" cols="12" style="height:100px">
                                                <v-img v-if="bookingData && bookingData.customer.image" style="
                                                    width: 250px;
                                                    height: 250px;
                                                    margin: 0 auto;
                                                    border-radius: 60%;
                                                " :src="bookingData && bookingData.customer.image"></v-img>
                                                <v-img v-else style="border-radius: 50%;                                                
                                                width: 250px !important;
                                            " src="/no-profile-image.jpg"></v-img>
                                            </v-col>
                                            <v-col md="9" cols="12"><v-row>

                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Rev.No</h6>
                                                            <p> {{ bookingData && bookingData.reservation_no ?
                                                                bookingData.reservation_no : '---' }}
                                                            </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Room Number</h6>
                                                            <p> {{ bookingData && bookingData.rooms ? bookingData.rooms :
                                                                '---' }} </p>
                                                        </div>
                                                    </v-col>


                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Guest Name</h6>
                                                            <p> {{ bookingData && bookingData.customer ?
                                                                bookingData.customer.full_name :
                                                                '---' }} </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Contact Number</h6>
                                                            <p> {{ bookingData && bookingData.customer ?
                                                                bookingData.customer.contact_no :
                                                                '---' }}
                                                            </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Check in Date</h6>
                                                            <p> {{ bookingData && bookingData.check_in ?
                                                                bookingData.check_in : '---' }} </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Check Out Date</h6>
                                                            <p> {{ bookingData && bookingData.check_out ?
                                                                bookingData.check_out : '---' }} </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Type</h6>
                                                            <p> {{ bookingData && bookingData.type ? bookingData.type :
                                                                '---'
                                                            }} </p>
                                                        </div>
                                                    </v-col>
                                                    <v-col md="3">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Source</h6>
                                                            <p> {{ bookingData && bookingData.source ? bookingData.source :
                                                                '---' }} </p>
                                                        </div>
                                                    </v-col>


                                                    <v-col md="12">
                                                        <div class="text-box" style="float: left">
                                                            <h6>Address</h6>
                                                            <p> {{ bookingData && bookingData.customer &&
                                                                bookingData.customer.address ?
                                                                bookingData.customer.address : '---' }}
                                                            </p>
                                                        </div>
                                                    </v-col>


                                                </v-row>
                                            </v-col>
                                        </v-row>

                                    </v-card-text>

                                </v-card>

                            </v-tab-item>
                            <v-tab-item value="tab-2">
                                <v-card>
                                    <v-card-title>Lost Item Details</v-card-title>
                                    <v-card-text>
                                        <v-row>

                                            <v-col md="12" cols="12">

                                                <strong>Lost Item name(s)*</strong>
                                                <v-text-field :disabled="viewMode" v-model="editedItem.item_name" small
                                                    dense outlined></v-text-field>
                                                <span dense v-if="errors && errors.item_name" class="error--text">{{
                                                    errors.item_name[0]
                                                }}</span>
                                            </v-col>
                                            <v-col md="3" cols="12">
                                                <strong>Missing Date*</strong>
                                                <v-menu v-model="menu1" :close-on-content-click="false" :nudge-right="40"
                                                    transition="scale-transition" offset-y min-width="auto">
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field :disabled="viewMode" dense small outlined
                                                            v-model="missing_date" readonly v-bind="attrs"
                                                            v-on="on"></v-text-field>
                                                    </template>
                                                    <v-date-picker style="height:400px" no-title v-model="missing_date"
                                                        @input="menu1 = false"></v-date-picker>
                                                </v-menu>
                                                <span dense v-if="errors && errors.missing_datetime" class="error--text">{{
                                                    errors.missing_datetime[0]
                                                }}</span>
                                            </v-col>
                                            <v-col md="3" cols="12">
                                                <strong>Missing Time</strong>
                                                <v-menu ref="menu" v-model="menu2" :close-on-content-click="false"
                                                    :nudge-right="40" :return-value.sync="missing_time"
                                                    transition="scale-transition" offset-y max-width="290px"
                                                    min-width="290px">
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field :disabled="viewMode" dense small outlined
                                                            v-model="missing_time" readonly v-bind="attrs"
                                                            v-on="on"></v-text-field>
                                                    </template>
                                                    <v-time-picker no-title v-if="menu2" v-model="missing_time" full-width
                                                        @click:minute="$refs.menu.save(missing_time)"></v-time-picker>
                                                </v-menu>
                                                <span dense v-if="errors && errors.missing_datetime" class="error--text">{{
                                                    errors.missing_datetime[0]
                                                }}</span>
                                            </v-col>
                                            <v-col md="6" cols="12">
                                                <strong>Remarks</strong>
                                                <v-text-field :disabled="viewMode" v-model="editedItem.missing_remarks"
                                                    small dense outlined></v-text-field>

                                            </v-col>
                                            <v-col md="12" cols="12">

                                                <v-textarea :disabled="viewMode" v-model="editedItem.missing_notes" outlined
                                                    name="input-7-4" label="Notes" value=" "></v-textarea>

                                            </v-col>
                                        </v-row>

                                        <v-card-actions v-if="!viewMode">
                                            <v-spacer></v-spacer>
                                            <v-btn small :loading="loading" :disabled="disableNextProcess"
                                                class="primary   " @click="saveMissingItems()">
                                                Save Lost Items

                                            </v-btn>
                                        </v-card-actions>

                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item value="tab-3">
                                <v-card flat>
                                    <v-card-title>Found Item Details</v-card-title>
                                    <v-card-text>
                                        <v-row>
                                            <v-col md="4" cols="12" style="height:200px;text-align:right">

                                                <v-img @click="!viewMode && $refs.attachment_input.click()" style="
                                                height: 100%;
                                                
                                                margin: 0 auto;
                                                border-radius: 10%;
                                            " :src="showFoundImage"></v-img>
                                                <input required type="file" @change="attachment" style="display: none"
                                                    accept="image/*" ref="attachment_input" />

                                                <a v-if="viewMode" download style="    margin-top: -22px;
    
    position: absolute;" target="_blank" :href="showFoundImage"><v-icon color="primary" desne fill
                                                        dark>mdi-download-box</v-icon></a>
                                            </v-col>
                                            <v-col md="8" cols="12">
                                                <v-row>
                                                    <v-col md="3" cols="12">
                                                        <strong>Found Date</strong>
                                                        <v-menu v-model="menu3" :close-on-content-click="false"
                                                            :nudge-right="40" transition="scale-transition" offset-y
                                                            min-width="auto">
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field :disabled="viewMode" dense small outlined
                                                                    v-model="found_date" readonly v-bind="attrs"
                                                                    v-on="on"></v-text-field>
                                                            </template>

                                                            <v-date-picker style="height:450px" no-title
                                                                v-model="found_date" @input="menu3 = false"></v-date-picker>
                                                        </v-menu>
                                                    </v-col>
                                                    <v-col md="3" cols="12">
                                                        <strong>Found Time</strong>
                                                        <v-menu ref="menu4" v-model="menu4" :close-on-content-click="false"
                                                            :nudge-right="40" :return-value.sync="found_time"
                                                            transition="scale-transition" offset-y max-width="290px"
                                                            min-width="290px">
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field :disabled="viewMode" dense small outlined
                                                                    v-model="found_time" readonly v-bind="attrs"
                                                                    v-on="on"></v-text-field>
                                                            </template>
                                                            <v-time-picker no-title v-if="menu4" v-model="found_time"
                                                                full-width
                                                                @click:minute="$refs.menu4.save(found_time)"></v-time-picker>
                                                        </v-menu>
                                                    </v-col>
                                                </v-row>
                                                <v-row>
                                                    <v-col md="4" cols="12">

                                                        <strong> Found By </strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.found_person_name" small dense
                                                            outlined></v-text-field>

                                                    </v-col>
                                                    <v-col md="4" cols="12">
                                                        <strong>Found Location</strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.found_location" small dense
                                                            outlined></v-text-field>
                                                    </v-col>
                                                    <v-col md="4" cols="12">
                                                        <strong> Remarks</strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.found_remarks" small dense
                                                            outlined></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-col>

                                        </v-row>
                                        <v-row>
                                            <v-col md="12" cols="12" class="mt-3">

                                                <v-textarea v-model="editedItem.found_notes" outlined label="Notes"
                                                    value=" "></v-textarea>
                                            </v-col>
                                        </v-row>
                                        <v-card-actions v-if="!viewMode">
                                            <v-spacer></v-spacer>
                                            <v-btn small :loading="loading" :disabled="disableNextProcess"
                                                class="primary   " @click="saveFoundDetails">
                                                Save Found Details
                                            </v-btn>
                                        </v-card-actions>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item value="tab-4">
                                <v-card flat>
                                    <v-card-title>Return Item Details</v-card-title>
                                    <v-card-text>
                                        <v-row>
                                            <v-col md="4" cols="12" style="height:200px;text-align:right">

                                                <v-img @click="!viewMode && $refs.returned_attachment_input.click()" style="
                                                height: 100%;

                                                margin: 0 auto;
                                                border-radius: 10%;
                                                
                                            " :src="showRetunedImage"></v-img>

                                                <input v-nodel="input_returned_image" required type="file"
                                                    @change="return_attachment" style="display: none" accept="image/*"
                                                    ref="returned_attachment_input" />


                                                <a download v-if="viewMode" style="    margin-top: -22px;
    
    position: absolute;" target="_blank" :href="showRetunedImage"><v-icon color="primary" desne fill
                                                        dark>mdi-download-box</v-icon></a>
                                            </v-col>
                                            <v-col md="8" cols="12">

                                                <v-row>
                                                    <v-col md="3" cols="12">
                                                        <strong>Retuned Date</strong>
                                                        <v-menu v-model="menu5" :close-on-content-click="false"
                                                            :nudge-right="40" transition="scale-transition" offset-y
                                                            min-width="auto">
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field :disabled="viewMode" dense small outlined
                                                                    v-model="returned_date" readonly v-bind="attrs"
                                                                    v-on="on"></v-text-field>
                                                            </template>
                                                            <v-date-picker style="height:400px" no-title
                                                                v-model="returned_date"
                                                                @input="menu5 = false"></v-date-picker>
                                                        </v-menu>
                                                    </v-col>
                                                    <v-col md="3" cols="12">
                                                        <strong>Retuned Time</strong>
                                                        <v-menu ref="menu6" v-model="menu6" :close-on-content-click="false"
                                                            :nudge-right="40" :return-value.sync="returned_time"
                                                            transition="scale-transition" offset-y max-width="290px"
                                                            min-width="290px">
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field :disabled="viewMode" dense small outlined
                                                                    v-model="returned_time" readonly v-bind="attrs"
                                                                    v-on="on"></v-text-field>
                                                            </template>
                                                            <v-time-picker no-title v-if="menu6" v-model="returned_time"
                                                                full-width
                                                                @click:minute="$refs.menu6.save(returned_time)"></v-time-picker>
                                                        </v-menu>
                                                    </v-col>
                                                </v-row>
                                                <v-row>
                                                    <v-col md="4" cols="12">

                                                        <strong> Returned By </strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.returned_person_name" small dense
                                                            outlined></v-text-field>

                                                    </v-col>
                                                    <v-col md="4" cols="12">
                                                        <strong>Approved By</strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.approved_person_name" small dense
                                                            outlined></v-text-field>
                                                    </v-col>
                                                    <v-col md="4" cols="12">
                                                        <strong> Remarks</strong>
                                                        <v-text-field :disabled="viewMode"
                                                            v-model="editedItem.returned_remarks" small dense
                                                            outlined></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-col>

                                        </v-row>
                                        <v-row>
                                            <v-col md="12" cols="12" class="mt-4">
                                                <v-textarea :disabled="viewMode" v-model="editedItem.returned_notes"
                                                    outlined label="Notes" value=" "></v-textarea>
                                            </v-col>
                                        </v-row>
                                        <v-card-actions v-if="!viewMode">
                                            <v-spacer></v-spacer>
                                            <v-btn small @click="saveReturnedDetails()" :loading="loading"
                                                :disabled="disableNextProcess" class="primary   ">
                                                Save Return Details

                                            </v-btn>
                                        </v-card-actions>
                                    </v-card-text>
                                </v-card>
                            </v-tab-item>

                        </v-tabs-items>

                    </v-card>
                </template>

            </v-dialog>

            <v-dialog v-model="viewDialog" max-width="80%">
                <v-card>
                    <v-card-title dense class=" primary  white--text background">
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

            <v-card class="mb-5" elevation="0">
                <v-toolbar class="rounded-md mb-2 white--text" color="background" dense flat>


                    <v-toolbar-title><span> Lost & Found</span></v-toolbar-title>


                    <v-tooltip top color="primary">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn dense class="ma-0 px-0" x-small :ripple="false" text v-bind="attrs" v-on="on">
                                <v-icon color="white" class="ml-2" @click="reload()" dark>mdi
                                    mdi-reload</v-icon>
                            </v-btn>
                        </template>
                        <span>Reload</span>
                    </v-tooltip>
                    <v-tooltip top color="primary">
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="toggleFilter">
                                <v-icon white color="#FFF">mdi-filter</v-icon>
                            </v-btn>
                        </template>
                        <span>Filter</span>
                    </v-tooltip>
                    <v-spacer></v-spacer>

                    <v-tooltip top color="primary">
                        <template v-if="can('lost_and_found_create')" v-slot:activator="{ on, attrs }">
                            <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="openNewRecord()">
                                <v-icon color="white" dark white>mdi-plus-circle</v-icon>
                            </v-btn>
                        </template>
                        <span>Add New Record</span>
                    </v-tooltip>

                </v-toolbar>
                <v-row>
                    <v-col cols="12">

                        <v-data-table dense :headers="headers_table" :items="data" :loading="loading"
                            :options.sync="options" :footer-props="{
                                itemsPerPageOptions: [10, 20, 50, 100, 500, 1000],
                            }" class="elevation-1" :server-items-length="totalRowsCount" @page-change="updateIndex">
                            <template v-slot:header="{ props: { headers } }">
                                <tr v-if="isFilter">
                                    <td v-for="header in headers" :key="header.text">
                                        <v-text-field clearable :hide-details="true"
                                            v-if="header.filterable && !header.filterSpecial" v-model="filters[header.key]"
                                            :id="header.value" @input="applyFilters(header.key, $event)" outlined dense
                                            autocomplete="off"></v-text-field>
                                        <v-autocomplete v-else-if="header.filterable && header.value == 'status'" clearable
                                            @click:clear="filters[header.value] = ''; applyFilters()" :hide-details="true"
                                            @change="applyFilters('status', $event)" item-value="value" item-text="title"
                                            v-model="filters[header.value]" outlined dense :items="[
                                                { value: '', title: 'All' },
                                                { value: '0', title: 'Not Found' },
                                                {
                                                    value: '1',
                                                    title: 'Found',
                                                },
                                                { value: '2', title: 'Returned' },
                                            ]" placeholder="Status"></v-autocomplete>

                                        <v-menu v-if="header.filterSpecial && header.value == 'found_datetime'"
                                            ref="from_menu_filter" v-model="from_menu_filter"
                                            :close-on-content-click="false" transition="scale-transition" offset-y
                                            min-width="auto">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field clearable
                                                    @click:clear="filters[header.value] = ''; applyFilters()"
                                                    :hide-details="!from_date_filter" outlined dense
                                                    v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                                                    placeholder="Select Date"></v-text-field>
                                            </template>
                                            <v-date-picker style="height: 400px" v-model="filters[header.value]" no-title
                                                scrollable @input="applyFilters()">
                                                <v-spacer></v-spacer>

                                                <v-btn text color="primary"
                                                    @click="filters[header.value] = ''; from_menu_filter = false; applyFilters()">
                                                    Clear
                                                </v-btn>
                                            </v-date-picker>
                                        </v-menu>
                                        <v-menu v-if="header.filterSpecial && header.value == 'returned_datetime'"
                                            ref="to_menu_filter" v-model="to_menu_filter" :close-on-content-click="false"
                                            transition="scale-transition" offset-y min-width="auto">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field clearable
                                                    @click:clear="filters[header.value] = ''; applyFilters()"
                                                    :hide-details="!to_date_filter" outlined dense
                                                    v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                                                    placeholder="Select Date"></v-text-field>
                                            </template>
                                            <v-date-picker style="height: 400px" v-model="filters[header.value]" no-title
                                                scrollable @input="applyFilters()">
                                                <v-spacer></v-spacer>

                                                <v-btn text color="primary"
                                                    @click="filters[header.value] = ''; to_menu_filter = false; applyFilters()">
                                                    Clear
                                                </v-btn>
                                            </v-date-picker>
                                        </v-menu>
                                        <v-menu v-if="header.filterSpecial && header.value == 'created_at'"
                                            ref="to_menu_filter1" v-model="to_menu_filter1" :close-on-content-click="false"
                                            transition="scale-transition" offset-y min-width="auto">
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field clearable
                                                    @click:clear="filters[header.value] = ''; applyFilters()"
                                                    :hide-details="!to_date_filter" outlined dense
                                                    v-model="filters[header.value]" readonly v-bind="attrs" v-on="on"
                                                    placeholder="Select Date"></v-text-field>
                                            </template>
                                            <v-date-picker style="height: 400px" v-model="filters[header.value]" no-title
                                                scrollable @input="applyFilters()">
                                                <v-spacer></v-spacer>

                                                <v-btn text color="primary"
                                                    @click="filters[header.value] = ''; to_menu_filter1 = false; applyFilters()">
                                                    Clear
                                                </v-btn>
                                            </v-date-picker>
                                        </v-menu>
                                    </td>
                                </tr>


                            </template>
                            <template v-slot:item.sno="{ item, index }">
                                {{ currentPage ? ((currentPage - 1) * perPage) + (cumulativeIndex + itemIndex(item)) : '' }}
                            </template>
                            <template v-slot:item.booking.reservation_no="{ item }">
                                {{ item.booking.reservation_no }}
                            </template>
                            <template v-slot:item.bookings.rooms="{ item }">
                                {{ item.booking.rooms }}
                            </template>

                            <template v-slot:item.customer.name="{ item }">
                                {{ item.booking.customer.title }} {{ item.booking.customer.first_name }} {{
                                    item.booking.customer.last_name }}
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
                                {{ formatDate(item.created_at) }}
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
                                <v-menu bottom left
                                    v-if="can('lost_and_found_create') || can('lost_and_found_edit') || can('lost_and_found_delete')">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn dark-2 icon v-bind="attrs" v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>
                                    <v-list width="120" dense>
                                        <v-list-item v-if="can('lost_and_found_create')" @click="viewItem(item.id)">
                                            <v-list-item-title style="cursor: pointer">
                                                <v-icon color="primary" small> mdi-eye </v-icon>
                                                View
                                            </v-list-item-title>
                                        </v-list-item>

                                        <v-list-item v-if="can('lost_and_found_edit')" @click="editItem(item, false)">
                                            <v-list-item-title style="cursor: pointer">
                                                <v-icon color="secondary" small> mdi-pencil </v-icon>
                                                Edit
                                            </v-list-item-title>
                                        </v-list-item>
                                        <v-list-item v-if="can('lost_and_found_delete')" @click="deleteItem(item)">
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
                key: '',
                filterable: false,
                value: "sno",
            },
            {
                text: "Rev.No",
                align: "left",
                sortable: true,
                key: 'reservation_no',
                filterable: true,
                value: "booking.reservation_no",
            },
            {
                text: "Room",
                align: "left",
                sortable: true,
                key: 'bookings_rooms',
                filterable: true,
                value: "bookings.rooms",
            },
            {
                text: "Guest Name",
                align: "left",
                sortable: false,
                key: 'customer_name',
                filterable: true,
                value: "customer.name",
            },
            {
                text: "Items",
                align: "left",
                sortable: true,
                key: 'item_name',
                filterable: true,
                value: "item_name",
            },
            {
                text: "Date Found",
                align: "left",
                sortable: true,
                key: 'date_found',
                filterable: true,
                value: "found_datetime",
                filterSpecial: true,
            },
            {
                text: "Returned Date ",
                align: "left",
                sortable: true,
                key: 'date_returned',
                filterable: true,
                value: "returned_datetime",
                filterSpecial: true,
            },
            {
                text: "Retuned Remarks",
                align: "left",
                sortable: true,
                key: 'returned_remarks',
                filterable: true,
                value: "returned_remarks",
            },
            {
                text: "Created",
                align: "left",
                sortable: true,
                key: 'created',
                filterable: true,
                value: "created_at",
                filterSpecial: true,
            },
            {
                text: "Status",
                align: "left",
                sortable: true,
                key: 'status',
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

        filter_returned_datetime: '',
        filter_found_datetime: '',
        from_menu_filter: '',
        from_date_filter: '',
        to_date_filter: '',
        to_menu_filter: '',
        to_menu_filter1: '',
        missing: {},
        found: {},
        missing_date: '',
        missing_time: '',
        found_date: '',
        found_time: '',
        returned_date: '',
        returned_time: '',
        menu1: false,
        menu2: false,
        menu3: false,
        menu4: false,
        menu5: false,
        menu6: false,
        date: '',
        disableNextProcess: true,
        bookingId: '',
        reservation_no: '',
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
        lost_item_id: '',
    }),

    computed: {

        formTitle() {
            return this.editedIndex === -1 ? "New" : "Edit";
        },
        showFoundImage() {



            if (!this.editedItem.found_image && !this.previewImage) {
                return "/no-image-display.png";
            } else if (this.previewImage) {
                return this.previewImage;
            }

            return this.editedItem.found_image;


        },
        showRetunedImage() {
            if (!this.editedItem.recovered_image && !this.previewRetunedImage) {
                return "/no-image-display.png";
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

            this.editedItem.missing_datetime = this.missing_date + ' ' + this.missing_time;//+ ':00';

        },
        missing_time(val) {

            this.editedItem.missing_datetime = this.missing_date + ' ' + this.missing_time;//+ ':00';

        },
        found_date(val) {

            this.editedItem.found_datetime = this.found_date + ' ' + this.found_time;//+ ':00';

        },
        found_time(val) {

            this.editedItem.found_datetime = this.found_date + ' ' + this.found_time;// + ':00';

        },
        returned_date(val) {

            this.editedItem.returned_datetime = this.returned_date + ' ' + this.returned_time;// + ':00';

        },
        returned_time(val) {

            this.editedItem.returned_datetime = this.returned_date + ' ' + this.returned_time;// + ':00';

        },

    },

    created() {
        this.loading = true;
        this.getDataFromApi();


    },

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

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
            this.response = '';
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

            this.response = '';
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

            this.$axios.get(`${this.endpoint}/${lostItemId}`, options).then(({ data }) => {
                this.loading = false;
                this.disableNextProcess = false;
                if (data == '') {
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
            this.response = '';
            //this.bookingId = '';
            this.editedIndex = -1;
            this.bookingData = { customer: {} };
            this.editedItem = this.defaultItem;

            this.missing_date = '';
            this.missing_time = '';
            this.found_time = '';
            this.found_date = '';

            this.returned_time = '';
            this.returned_date = '';
        },
        getStatus(status) {
            if (status == 0) return 'Not Found';
            else if (status == 1) return 'Item Found';
            else if (status == 2) return 'Returned';
        },
        searchBookingId(url = this.endpoint) {
            this.disableNextProcess = true;



            this.response = "";
            if (this.reservation_no == '') {
                this.clearForm();
                return false;
            }
            let lostItemId = '';
            this.loading = true;
            this.bookingData = { customer: {} };
            let options = {
                params: {
                    company_id: this.$auth.user.company.id,
                    lostItemId: lostItemId,
                },
            };

            this.$axios.post(`${url}/search_by_reference/${this.reservation_no}`, options.params).then(({ data }) => {

                this.clearForm();
                this.loading = false;
                if (!data || data == '') {
                    this.clearForm();
                    return false;
                } else {

                    this.bookingData = data.booking;
                    this.bookingId = data.booking.id
                    this.disableNextProcess = false;

                    if (this.bookingData && this.bookingData.booking_status != 1) {

                    }

                    if (this.bookingData) {
                        if (this.bookingData.booking_status < 0) {
                            this.snackbarColor = "red";
                            this.disableNextProcess = true;
                            this.snackbar = true;
                            this.response = " Check-in is not avaialle. You can not add Missing Item details";
                        }

                    }
                    else {
                        this.disableNextProcess = true;
                        this.snackbarColor = "red";
                        this.snackbar = true;
                        this.response = "Booking Details are not avaialbe";
                    }


                    // this.editedIndex = this.data.id;
                    // if (this.bookingData.booking_status == 1)
                    //     this.disableNextProcess = false;
                    //this.loadLostItemDetails();



                }
            });

        },
        loadLostItemDetails(missingItemData) {
            if (missingItemData) {
                this.editedItem = missingItemData;
                this.editedIndex = missingItemData.id;

                this.missing_date = this.editedItem.missing_datetime ? this.editedItem.missing_datetime.split(' ')[0] : '';
                this.missing_time = this.editedItem.missing_datetime ? this.editedItem.missing_datetime.split(' ')[1] : '';
                this.found_date = this.editedItem.found_datetime ? this.editedItem.found_datetime.split(' ')[0] : '';
                this.found_time = this.editedItem.found_datetime ? this.editedItem.found_datetime.split(' ')[1] : '';
                this.returned_date = this.editedItem.returned_datetime ? this.editedItem.returned_datetime.split(' ')[0] : '';
                this.returned_time = this.editedItem.returned_datetime ? this.editedItem.returned_datetime.split(' ')[1] : '';

            }
            else {
                this.editedIndex = -1;
                this.editedItem = this.defaultItem;
            }




        },
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
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
                    ...this.editedItem
                },
            };

            if (this.editedIndex > -1) {
                this.$axios.put(url + "/" + this.editedIndex, options.params).then(({ data }) => {
                    this.loading = false;
                    if (!data.status) {
                        this.errors = data.errors;

                        return false;
                    }
                    else {
                        this.getDataFromApi();
                        this.newItem = false;
                    }
                    this.snackbarColor = "primary";
                    this.snackbar = true;
                    this.response = 'Detais are Updated Successfully';

                });
            } else { //New
                this.$axios.post(`${url}`, options.params).then(({ data }) => {

                    this.loading = false;
                    if (data.errors) {
                        this.errors = data.errors;

                        return false;
                    }
                    else {
                        this.getDataFromApi();
                        this.newItem = false;
                    }
                    this.snackbarColor = "primary";
                    this.snackbar = true;
                    this.response = 'Detais are saved Successfully';
                }).catch((res) => console.log(res));
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
                    ...this.editedItem
                },
            };
            if (this.editedIndex > -1) {
                this.$axios.put(url + "/" + this.editedIndex, options.params).then(({ data }) => {


                    this.uploadFoundFile(this.editedIndex);
                    this.loading = false;
                    this.getDataFromApi();


                    this.snackbarColor = "primary";
                    this.snackbar = true;
                    this.response = 'Detais are saved Successfully';
                });
            }
            else {
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
            this.$axios.post(this.endpoint + "/file_found/" + editedIndex, payload).then(({ data }) => {


            });
        },
        saveReturnedDetails() {
            let url = this.endpoint;
            this.loading = true;

            let options = {
                params: {
                    company_id: this.$auth.user.company.id,
                    booking_id: this.bookingId,
                    update_type: "return",
                    ...this.editedItem
                },
            };
            if (this.editedIndex > -1) {
                this.$axios.put(url + "/" + this.editedIndex, options.params).then(({ data }) => {
                    this.uploadReturnedFile(this.editedIndex);
                    this.loading = false;
                    this.getDataFromApi();
                    // this.newItem = false;

                    this.snackbarColor = "primary";
                    this.snackbar = true;
                    this.response = 'Detais are saved Successfully';
                });
            }
            else {
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

            this.$axios.post(this.endpoint + "/file_returned/" + editedIndex, payload).then(({ data }) => {


            });
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
            this.$set(this.options, 'page', 1);
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
                    ...this.filters,
                },
            };

            this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
                this.data = data.data;
                this.pagination.current = data.current_page;
                this.pagination.total = data.last_page;
                this.loading = false;
                this.totalRowsCount = data.total;
            });
        },
        // searchIt(e) {
        //     let s = e.toLowerCase();
        //     this.getDataFromApi();
        //     return;
        // },

        // editItem(item) {
        //     this.editedIndex = this.data.indexOf(item);
        //     this.editedItem = Object.assign({}, item);


        //     this.roomTypeDialog = true;
        // },

        // delteteSelectedRecords() {
        //     let just_ids = this.ids.map((e) => e.id);
        //     confirm(
        //         "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
        //     ) &&
        //         this.$axios
        //             .post(`${ this.endpoint } / delete /selected`, {
        //                 ids: just_ids,
        //             })
        //             .then((res) => {
        //                 if (!res.data.status) {
        //                     this.errors = res.data.errors;
        //                 } else {
        //                     this.getDataFromApi();
        //                     this.snackbar = res.data.status;
        //                     this.ids = [];
        //                     this.response = "Selected records has been deleted";
        //                 }
        //             })
        //             .catch((err) => console.log(err));
        // },

        deleteItem(item) {
            confirm(
                "Are you sure you wish to delete?"
            ) &&
                this.$axios
                    .delete(this.endpoint + "/" + item.id)
                    .then(({ data }) => {
                        this.getDataFromApi();
                        this.snackbar = data.status;
                        this.response = data.message;
                    })
                    .catch((err) => console.log(err));
        },

        // close() {
        //     this.roomTypeDialog = false;

        //     // setTimeout(() => {
        //     this.editedItem = Object.assign({}, this.defaultItem);
        //     this.editedIndex = -1;
        //     // }, 300);
        // },

        // save() {
        //     let payload = {
        //         name: this.editedItem.name,
        //         price: this.editedItem.price,
        //         adult: this.editedItem.adult,
        //         child: this.editedItem.child,
        //         baby: this.editedItem.baby,
        //         company_id: this.$auth.user.company.id,
        //     };
        //     if (this.editedIndex > -1) {
        //         this.$axios
        //             .put('room_types' + "/" + this.editedItem.id, payload)
        //             .then(({ data }) => {
        //                 console.log(data);
        //                 if (!data.status) {
        //                     this.errors = data.errors;
        //                 } else {
        //                     console.log('suc');
        //                     this.getDataFromApi();
        //                     this.snackbar = data.status;
        //                     this.response = data.message;
        //                     this.close();
        //                 }
        //             })
        //             .catch((err) => console.log(err));
        //     } else {
        //         this.$axios
        //             .post('room_types', payload)
        //             .then(({ data }) => {
        //                 if (!data.status) {
        //                     this.errors = data.errors;
        //                 } else {
        //                     this.getDataFromApi();
        //                     this.snackbar = data.status;
        //                     this.response = data.message;
        //                     this.close();
        //                     this.errors = [];
        //                     this.search = "";
        //                 }
        //             })
        //             .catch((res) => console.log(res));
        //     }
        // },
    },
};
</script> 

 
<style scoped >
.no-bg {
    background-color: white !important;
}

.guest-avatar {
    max-width: 200px !important;
    height: 200px !important;
    float: left;
    margin: 0 auto;
    border-radius: 50%;
}

.text-box {
    border: 1px solid rgb(215, 211, 211);
    padding: 10px 0px 0px 10px;
    margin: 10px 20px;
    position: relative;
    border-radius: 5px;
    width: 100%;
}

.text-box-amt {
    border: 0px solid rgb(215, 211, 211);
    padding: 0px 0px 0px 0px;
    margin: 0px 00px;
    position: relative;
    border-radius: 5px;
    width: 100%;
}

.amt-border {
    border-bottom: 1px solid;
}

.amt-border-full {
    border-bottom: 1px solid;
    border-top: 1px solid;
}

.text-box p {
    margin: 5px;
}

h6 {
    position: absolute;
    top: -12px;
    left: 20px;
    background-color: white;
    padding: 0 10px;
    color: rgb(154, 152, 152);
    margin: 0;
    font-size: 15px;
}

table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td,
th {
    text-align: left;
    padding: 8px;
    /* border: 1px solid black !important; */
}

tr:nth-child(even) {
    background-color: white;
}

.custom-text-box {
    border-radius: 2px !important;
    border: 1px solid #dbdddf !important;
}

input[type="text"]:focus.custom-text-box {
    border: 2px solid #5fafa3 !important;
}

select.custom-text-box {
    border: 2px solid #5fafa3 !important;
}

select:focus {
    outline: none !important;
    border-color: #5fafa3;
    box-shadow: 0 0 0px #5fafa3;
}

.table-header-text {
    font-size: 12px;
}
</style>  