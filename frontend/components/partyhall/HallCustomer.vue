<template>
    <div v-if="can('calendar_create')">
        <v-row>
            <v-dialog v-model="documentDialog" max-width="30%">
                <v-card>
                    <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
                        <span>Add ID</span>
                        <v-spacer></v-spacer>
                        <v-icon dark class="pa-0" @click="documentDialog = false">mdi-close</v-icon>
                    </v-toolbar>
                    <v-container class="pa-5">
                        <v-row>
                            <v-col md="12" sm="12" cols="12" dense>
                                <v-select v-model="customer.id_card_type_id" :items="idCards" dense label="ID Card Type"
                                    outlined item-text="name" item-value="id"
                                    :hide-details="errors && !errors.id_card_type_id"
                                    :error="errors && errors.id_card_type_id" :error-messages="errors && errors.id_card_type_id
                                        ? errors.id_card_type_id[0]
                                        : ''
                                        "></v-select>
                            </v-col>
                            <v-col md="12" cols="12" sm="12">
                                <v-text-field dense label="ID Card" outlined type="text" v-model="customer.id_card_no"
                                    :hide-details="errors && !errors.id_card_no" :error="errors && errors.id_card_no"
                                    :error-messages="errors && errors.id_card_no ? errors.id_card_no[0] : ''
                                        "></v-text-field>
                            </v-col>
                            <v-col md="12" cols="12" sm="12">
                                <v-menu v-model="customer.passport_expiration_menu" :close-on-content-click="false"
                                    :nudge-right="40" transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="customer.passport_expiration" readonly label="Expired"
                                            v-on="on" v-bind="attrs" :hide-details="true" dense outlined></v-text-field>
                                    </template>
                                    <v-date-picker v-model="customer.passport_expiration"
                                        @input="customer.passport_expiration_menu = false"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="12">
                                <v-file-input v-model="customer.document" color="primary" counter
                                    placeholder="Select your files" outlined :show-size="1000">
                                    <template v-slot:selection="{ index, text }">
                                        <v-chip v-if="index < 2" color="primary" dark label small>
                                            {{ text }}
                                        </v-chip>

                                        <span v-else-if="index === 2" class="text-overline grey--text text--darken-3 mx-2">
                                            +{{ customer.document.length - 2 }} File(s)
                                        </span>
                                    </template>
                                </v-file-input>
                            </v-col>
                            <v-divider></v-divider>
                            <v-col md="12">
                                <!-- @click="documentDialog = false" -->

                                <v-btn small dark class="float-right primary pt-4 pb-4" @click="store_document_new()">
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
                    <v-toolbar class="rounded-md" color="grey lighten-3" dense flat>
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
            <v-col md="12">

                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-card-text>

                        <v-row>

                            <v-col md="2" cols="12">
                                <v-img @click="onpick_attachment" style="
                          width: 150px;
                          height: 150px;
                          margin: 0 auto;
                          border-radius: 50%;
                        " :src="showImage"></v-img>
                                <input required type="file" @change="attachment" style="display: none" accept="image/*"
                                    ref="attachment_input" />
                                <span v-if="errors && errors.image" class="red--text mt-2">
                                    {{ errors.image[0] }}</span>

                                <v-row>
                                    <v-col cols="6">


                                        <v-btn v-if="customer.document" small dark class="primary  "
                                            @click="preview(customer.document)">
                                            View
                                            <v-icon right dark>mdi-file</v-icon>
                                        </v-btn>

                                    </v-col>
                                    <v-col cols="6">
                                        <v-btn small dark class="primary " @click="documentDialog = true">
                                            ID
                                            <v-icon right dark>mdi-cloud-upload</v-icon>
                                        </v-btn>
                                    </v-col>
                                </v-row>


                            </v-col>
                            <v-col md="10" cols="12">
                                <v-row>
                                    <v-col md="2" class="mt-0">
                                        <v-btn color="primary" @click="searchDialog = true">
                                            Search
                                            <v-icon right dark>mdi mdi-magnify</v-icon>
                                        </v-btn>
                                    </v-col>
                                    <v-col md="7" dense> </v-col>
                                    <v-col md="3" dense>
                                        <v-select label="Type" v-model="customer.customer_type"
                                            :items="['Company', 'Regular', 'Corporate']" dense item-text="name"
                                            item-value="id" outlined :hide-details="true"></v-select>
                                    </v-col>
                                    <v-col md="3" cols="12" sm="12">
                                        <v-select v-model="customer.title" :items="titleItems" label="Title *" dense
                                            item-text="name" item-value="name" :hide-details="errors && !errors.title"
                                            :error="errors && errors.title" :error-messages="errors && errors.title ? errors.title[0] : ''
                                                " outlined required :rules="nameRules"></v-select>
                                    </v-col>
                                    <v-col md="3" cols="12" sm="12">
                                        <v-text-field label="First Name *" dense outlined type="text"
                                            v-model="customer.first_name" :hide-details="errors && !errors.first_name"
                                            :error="errors && errors.first_name" :error-messages="errors && errors.first_name
                                                ? errors.first_name[0]
                                                : ''
                                                " required :rules="nameRules"></v-text-field>
                                    </v-col>
                                    <v-col md="3" cols="12" sm="12">
                                        <v-text-field label="Last Name" dense :hide-details="true" outlined type="text"
                                            v-model="customer.last_name" required :rules="nameRules"></v-text-field>
                                    </v-col>
                                    <v-col md="3" cols="12" sm="12">
                                        <v-text-field dense label="Contact No *" outlined type="number"
                                            v-model="customer.contact_no" :hide-details="errors && !errors.contact_no"
                                            :error="errors && errors.contact_no" :error-messages="errors && errors.contact_no
                                                ? errors.contact_no[0]
                                                : ''
                                                " @keyup="mergeContact" required :rules="nameRules"></v-text-field>
                                    </v-col>
                                    <v-col md="3" cols="12" sm="12">
                                        <v-text-field dense label="Whatsapp No *" outlined type="number"
                                            v-model="customer.whatsapp" :hide-details="errors && !errors.whatsapp"
                                            :error="errors && errors.whatsapp" :error-messages="errors && errors.whatsapp ? errors.whatsapp[0] : ''
                                                " required :rules="nameRules"></v-text-field>
                                    </v-col>

                                    <v-col md="3" cols="12" sm="12">
                                        <v-text-field dense label="Email *" outlined type="email" v-model="customer.email"
                                            :hide-details="errors && !errors.email" :error="errors && errors.email"
                                            :error-messages="errors && errors.email ? errors.email[0] : ''
                                                " required :rules="nameRules"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-col>


                        </v-row>
                        <v-row>
                            <v-col md="3" cols="12" sm="12">
                                <v-select v-model="customer.nationality" :items="countryList" label="Nationality *"
                                    item-text="name" item-value="name" :hide-details="errors && !errors.nationality"
                                    :error="errors && errors.nationality" :error-messages="errors && errors.nationality
                                        ? errors.nationality[0]
                                        : ''
                                        " dense outlined required :rules="nameRules"></v-select>
                            </v-col>
                            <v-col md="3" cols="12" sm="12">
                                <v-menu v-model="customer.dob_menu" :close-on-content-click="false" :nudge-right="40"
                                    transition="scale-transition" offset-y min-width="auto">
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="customer.dob" readonly label="DOB" v-on="on" v-bind="attrs"
                                            :hide-details="true" dense outlined></v-text-field>
                                    </template>
                                    <v-date-picker v-model="customer.dob"
                                        @input="customer.dob_menu = false"></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="3">
                                <v-select label="Purpose" v-model="customer.purpose" :items="purposes" dense
                                    :hide-details="true" outlined></v-select>
                            </v-col>
                            <v-col md="3" cols="12" sm="12">
                                <v-text-field dense label="Car Number" outlined :hide-details="true" type="text"
                                    v-model="customer.car_no"></v-text-field>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col md="3" sm="12" cols="12" dense>
                                <v-select v-model="customer.id_card_type_id" :items="idCards" dense label="ID Card Type *"
                                    outlined item-text="name" item-value="id"
                                    :hide-details="errors && !errors.id_card_type_id"
                                    :error="errors && errors.id_card_type_id" :error-messages="errors && errors.id_card_type_id
                                        ? errors.id_card_type_id[0]
                                        : ''
                                        " required :rules="nameRules"></v-select>
                            </v-col>
                            <v-col md="3" cols="12" sm="12">
                                <v-text-field dense label="ID Card *" outlined type="text" v-model="customer.id_card_no"
                                    :hide-details="errors && !errors.id_card_no" :error="errors && errors.id_card_no"
                                    :error-messages="errors && errors.id_card_no ? errors.id_card_no[0] : ''
                                        " required :rules="nameRules"></v-text-field>
                            </v-col>
                            <v-col md="3" cols="12" sm="12">
                                <v-text-field dense outlined label="GST" type="text" v-model="customer.gst_number"
                                    :hide-details="errors && !errors.gst_number" :error="errors && errors.gst_number"
                                    :error-messages="errors && errors.gst_number ? errors.gst_number[0] : ''
                                        "></v-text-field>
                            </v-col>

                        </v-row>
                        <v-row>
                            <v-col md="6" cols="12" sm="12">
                                <v-textarea rows="3" label="Address *" v-model="customer.address" outlined required
                                    :rules="nameRules"></v-textarea>
                            </v-col>
                            <v-col md="6">
                                <v-textarea rows="3" label="Customer Request" v-model="customer.request"
                                    :hide-details="true" outlined></v-textarea>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col md="3" sm="12" cols="12" dense>
                                <v-select v-model="customer.source_type" label="Source Type *" :items="types" dense outlined
                                    @change="getType(customer.source_type)" :hide-details="errors && !errors.type"
                                    :error="errors && errors.type" :error-messages="errors && errors.type ? errors.type[0] : ''
                                        " required :rules="nameRules"></v-select>
                            </v-col>
                            <v-col md="3" cols="12" sm="12" v-if="isAgent">
                                <v-select dense label="Agent Name" outlined :items="agentList" type="text"
                                    @change="get_gst(customer.source, 'agent')" item-value="name" item-text="name"
                                    v-model="customer.source" :hide-details="errors && !errors.source"
                                    :error="errors && errors.source" :error-messages="errors && errors.source ? errors.source[0] : ''
                                        "></v-select>
                            </v-col>
                            <v-col md="3" sm="12" cols="12" dense v-if="isOnline">
                                <v-select v-model="customer.source" label="Source" :items="sources" dense
                                    @change="get_gst(customer.source, 'online')" outlined item-value="name" item-text="name"
                                    :hide-details="errors && !errors.source" :error="errors && errors.source"
                                    :error-messages="errors && errors.source ? errors.source[0] : ''
                                        "></v-select>
                            </v-col>
                            <v-col md="3" sm="12" cols="12" dense v-if="isCorporate">
                                <v-select v-model="customer.source" label="Corporate" :items="CorporateList" dense outlined
                                    @change="get_gst(customer.source, 'corporate')" item-value="name" item-text="name"
                                    :hide-details="errors && !errors.source" :error="errors && errors.source"
                                    :error-messages="errors && errors.source ? errors.source[0] : ''
                                        "></v-select>
                            </v-col>
                            <v-col md="3" cols="12" sm="12" v-if="isAgent || isOnline || isCorporate">
                                <v-text-field label="Reference Number" dense outlined type="text"
                                    v-model="customer.reference_no" :hide-details="errors && !errors.reference_no"
                                    :error="errors && errors.reference_no" :error-messages="errors && errors.reference_no
                                        ? errors.reference_no[0]
                                        : ''
                                        "></v-text-field>
                            </v-col>
                            <v-col md="3" sm="12" cols="12" dense v-if="isAgent || isOnline || isCorporate">
                                <v-select v-model="customer.paid_by" label="Paid Type" :items="[
                                    { name: 'Paid at Hotel', value: '1' },
                                    { name: 'Paid by Agents', value: '2' },
                                ]" dense outlined item-value="value" item-text="name"
                                    :hide-details="errors && !errors.paid_by" :error="errors && errors.paid_by"
                                    :error-messages="errors && errors.paid_by ? errors.paid_by[0] : ''
                                        "></v-select>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="12" class="text-right">
                                <v-btn @click="nextTab" color="primary">Next</v-btn>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-form>
            </v-col>


        </v-row>

        <!---------------------------------------------------------------->

        <v-dialog v-model="searchDialog" width="500">
            <v-card>
                <v-card-title class="text-h5 grey lighten-2"> Customer </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col md="12" cols="12" sm="12">
                            <label class="col-form-label">Search By Mobile Number
                                <span class="error--text">*</span>
                            </label>
                            <v-text-field dense outlined type="text" v-model="search.mobile"
                                :hide-details="true"></v-text-field>
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


    </div>
    <NoAccess v-else />
</template>
<script>

import ImagePreview from "../../components/images/ImagePreview.vue";

export default {
    components: {
        ImagePreview,
    },
    props: ["nextTabTrigger", "booking_id"],
    data() {
        return {

            valid: true,

            nameRules: [
                v => v !== undefined && v !== null && v !== '' ? true : 'Required',


            ],

            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],





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
                breakfast: "",
                lunch: "",
                dinner: "",
                tot_adult_food: 0,
                tot_child_food: 0,
                discount_reason: "",
                priceList: [],


            },
            merge_food_in_room_price: '',
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
        };
    },
    watch: {
        nextTabTrigger() {
            let obj = { ...this.customer };
            Object.assign({}, obj)
            obj = JSON.parse(JSON.stringify(obj))

            this.$store.commit("partyHallBookingCustomer", obj);



        },
        booking_id(val) {

            this.store_document(val);


        },
        // requestUploadBookingId() {

        //     console.log(val);
        //     this.store_document(val);
        // }

    },
    created() {
        // this.get_food_price();
        // this.get_reservation();
        // this.get_room_types();
        this.get_id_cards();
        // this.runAllFunctions();
        this.get_countries();
        this.get_agents();
        this.get_online();
        this.get_Corporate();

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
        validate() {
            return this.$refs.form.validate();
        },
        updateDiscount(temp) {



            let finalDisplayPrice = parseFloat(temp.price) +
                parseFloat(-temp.room_discount);
            this.$axios.get('get_re_calculate_price/' + finalDisplayPrice, null).then(({ data }) => {

                this.gst_calculation.recal_basePrice = data.basePrice;
                this.gst_calculation.recal_final = data.basePrice + data.gstAmount;
                this.gst_calculation.recal_gst_total = data.gstAmount;
                this.gst_calculation.recal_gst_percentage = data.tax;
            });

        },
        nextTab() {




            if (this.validate()) {

                if (
                    this.customer.document == null ||
                    this.customer.document == undefined
                ) {
                    this.alert("Missing!", "Select document", "error");
                    this.subLoad = false;
                    return;
                }

                console.log(this.customer.source_type);

                if (this.customer.source_type == '' || !this.customer.source_type) {
                    this.alert(
                        "oops",
                        "Select Source Type",
                        "error"
                    );

                    return false;
                }
                // this.activeTab += 1;
                let obj = {
                    ...this.customer
                };
                Object.assign({}, obj)
                obj = JSON.parse(JSON.stringify(obj))

                this.$store.commit("partyHallBookingCustomer", obj);
                // const document = this._arrayBufferToBase64(this.customer.document);
                // const image = this._arrayBufferToBase64(this.customer.image);

                // Pass it to the state
                this.$store.commit('customerImage', this.customer.document);
                this.$store.commit('customerDocument', this.customer.image);
                this.$store.commit("partyHallBookingCustomer", obj);

                //this.store_document("");


                this.$emit("c-next-tab", null);

            }

        },

        _arrayBufferToBase64(buffer) {
            var binary = '';
            var bytes = new Uint8Array(buffer);
            var len = bytes.byteLength;
            for (var i = 0; i < len; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return window.btoa(binary);
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
            let ci = new Date(this.temp.check_in);
            let co = new Date(this.temp.check_out);
            let Difference_In_Time = co.getTime() - ci.getTime();
            let days = Difference_In_Time / (1000 * 3600 * 24);
            if (days > 0) {
                return (this.room.total_days = days);
            }
        },

        get_reservation() {
            // this.reservation = this.$store.state.reservation;
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

        // get_food_price() {
        //     let payload = {
        //         params: {
        //             company_id: this.$auth.user.company.id,
        //         },
        //     };
        //     this.$axios.get(`get_food_prices`, payload).then(({ data }) => {
        //         this.foodPriceList = data;
        //         this.get_food_price_cal("adult", 1);
        //     });
        // },

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

        // get_food_price_cal(person_type, person_qty) {
        //     if (this.foodPriceList.length == 0) {
        //         return;
        //     }
        //     let person = this.foodPriceList.find((e) => e.type == person_type);

        //     person.qty = person_qty;

        //     let index = this.person_type_arr.findIndex((e) => e.type == person_type);

        //     if (index == -1) {
        //         this.person_type_arr.push(person);
        //     } else {
        //         this.person_type_arr.splice(index, 1, person);
        //     }
        // },

        meal_cal(meal_type) {
            this.person_type_arr.find((e) => {
                if (e.type == "adult" || meal_type == "adult") {
                    this.get_adult_cal(e);
                }
                if (e.type == "child" || meal_type == "child") {
                    this.get_child_cal(e);
                }
                if (e.type == "baby" || meal_type == "baby") {
                    this.get_baby_cal(e);
                }
            });
        },
        reCalFood(temp) {
            this.meal_cal(temp);

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

        add_room() {
            if (this.temp.room_no == "") {
                this.alert("Missing!", "Select room", "error");
                return;
            }

            let roomDiscount = parseFloat(
                this.temp.room_discount == "" ? 0 : this.temp.room_discount
            );
            let roomExtraAmount = parseFloat(
                this.temp.room_extra_amount == "" ? 0 : this.temp.room_extra_amount
            );

            this.temp.after_discount =
                parseFloat(this.temp.price) - roomDiscount + roomExtraAmount;

            this.temp.days = this.getDays();

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
                parseFloat(this.temp.tot_adult_food) +
                parseFloat(this.temp.after_discount) +
                parseFloat(this.temp.tot_child_food);
            // parseFloat(this.temp.total_with_tax) +

            this.temp.grand_total = parseFloat(this.temp.total);

            this.room.check_in = this.temp.check_in;
            this.room.check_out = this.temp.check_out;

            this.temp.room_discount =
                this.temp.room_discount == "" ? 0 : this.temp.room_discount;

            this.temp.meal = `${this.temp.breakfast || "---"} | ${this.temp.lunch || "---"
                } | ${this.temp.dinner || "---"}`;

            delete this.temp.check_in_menu;
            delete this.temp.check_out_menu;
            delete this.temp.breakfast;
            delete this.temp.lunch;
            delete this.temp.dinner;
            this.selectedRooms.push(this.temp);

            this.get_total_amounts();
            this.runAllFunctions();

            this.allFood.push({ breakfast: this.breakfast });
            this.allFood.push({ lunch: this.lunch });
            this.allFood.push({ dinner: this.dinner });

            this.breakfast = {};
            this.lunch = {};
            this.dinner = {};

            this.clear_add_room();
            this.alert("Success!", "success selected room", "success");
            this.isSelectRoom = false;
            return;
        },

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
                room_extra_amount: 0,
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

        // can(per) {
        //   let u = this.$auth.user;
        //   console.log(u);
        //   if (!u.permissions) return false;
        //   return (
        //     (u && u.permissions.some((e) => e.name == per || per == "/")) ||
        //     u.is_master
        //   );
        // },

        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },

        store() {
            if (this.room.advance_price == "") {
                this.room.advance_price = 0;
            }

            console.log(this.reservation.booking_status);
            console.log(this.customer.document);
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
                // allFoods: this.breakfast,
                // qty_lunch: this.lunch,
                // qty_dinner: this.dinner,
                allFoods: this.allFood,
                selectedRooms: this.selectedRooms,
                ...this.customer,
                user_id: this.$auth.user.id,
                merge_food_in_room_price: this.merge_food_in_room_price,
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
                            this.$router.push('/reservation/in_house');
                            return '';
                        }
                        this.$router.push('/');
                    }
                })
                .catch((e) => console.log(e));
        },

        store_document(id) {
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
  