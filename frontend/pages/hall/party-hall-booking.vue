<template>
    <div v-if="can('calendar_create')">
        <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
            {{ snackbar_response }}
        </v-snackbar>
        <v-row>
            <v-col md="12">{{ booking_id }}
                <v-tabs v-model="activeTab" :vertical="vertical" background-color="primary" dark show-arrows>
                    <div class="py-3" style="background-color: #1259a7">
                        <span class="mx-2">Party Hall booking </span>
                    </div>
                    <v-col cols="6" class="  text-center">
                        <h2>{{ tabName[activeTab] }} Information - {{ room_no }}</h2>
                    </v-col>

                    <v-spacer></v-spacer>


                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-account-tie </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-party-popper </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-chef-hat </v-icon>
                    </v-tab>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-cash-multiple </v-icon>
                    </v-tab>
                    <v-icon dark class="pa-2">
                        mdi mdi-close-box
                    </v-icon>
                    <v-tabs-slider color="#1259a7"></v-tabs-slider>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallCustomer @c-next-tab="nextTab" :nextTabTrigger="activeTab" :key1="componentKey"
                                        :booking_id="booking_id" />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>

                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallEvent @c-next-tab="nextTab" :nextTabTrigger="activeTab" :key1="componentKey" />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallFood @c-next-tab="nextTab" :nextTabTrigger="activeTab" :key1="componentKey" />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">
                                    <HallAmount @c-next-tab="nextTab" @bookingIdUpdated="bookingIdUpdated"
                                        :nextTabTrigger="activeTab" :key1="componentKey" />
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                </v-tabs>
            </v-col>

            <!-- <v-col md="4">
                <v-tabs color="primary" :vertical="vertical" background-color="primary" dark show-arrows>
                    <v-tab active-class="active-link">
                        <v-icon> mdi mdi-list-box-outline </v-icon>
                    </v-tab>
                    <v-tabs-slider color="#1259a7"></v-tabs-slider>
                    <v-tab-item>
                        <v-card flat>
                            <v-divider class="px-5 py-0"></v-divider>

                            <section class="payment-section pt-0 mt-1">

                                <div class="input-group input-group-sm px-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        Event Date and Time
                                    </span>
                                    <div type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        {{ eventDateTime }}
                                    </div>
                                </div>
                                <div class="input-group input-group-sm px-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        Pax
                                    </span>
                                    <div type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        {{ eventPax }}
                                    </div>
                                </div>
                                <div class="input-group input-group-sm px-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        Food Amount
                                    </span>
                                    <div type="text" class="form-control" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        {{ eventFoodTotal }}
                                    </div>
                                </div>
                                
                                <div class="input-group input-group-sm px-5 mb-5">
                                    <span class="input-group-text" id="inputGroup-sizing-sm">
                                        <strong>Advance Payment </strong>
                                    </span>
                                    <div type="text" class="form-control red--text" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm" disabled>
                                        <strong> {{ eventAdvanceAmount }}</strong>
                                    </div>
                                </div>
                                
                                <div class="input-group input-group-sm px-3 mb-5">
                                    
                                    <v-btn style="background-color: #5fafa3" width="50%" height="40"
                                        @click="storeHallInformation" :loading="subLoad" dark>Book</v-btn>
                                </div>
                            </section>
                        </v-card>
                    </v-tab-item>
                </v-tabs>

            </v-col> -->






        </v-row>

    </div>
    <NoAccess v-else />
</template>
<script>
import HallCustomer from "../../components/partyhall/HallCustomer.vue"
import HallEvent from "../../components/partyhall/HallEvent.vue";
import HallFood from "../../components/partyhall/HallFood.vue";
import HallAmount from "../../components/partyhall/HallAmount.vue";
export default {
    components: {
        HallCustomer,
        HallEvent,
        HallFood,
        HallAmount,

    },
    data() {
        return {
            booking_id: 0,
            eventAdvanceAmount: 0,
            eventBalanceAmount: 0,

            eventFoodTotal: 0,
            eventGrandTotal: 0,


            eventPax: 0,
            eventDateTime: '',

            componentKey: 0,
            subLoad: false,
            valid: true,
            name: '',
            nameRules: [
                v => v !== undefined && v !== null && v !== '' ? true : 'Required',


            ],
            email: '',
            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],
            select: null,
            items: [
                'Item 1',
                'Item 2',
                'Item 3',
                'Item 4',
            ],
            checkbox: false,
            tabName: ['Guest', 'Event', 'Food ', 'Price List'],
            hours: [
                { id: 9, name: "09 AM" }
                , { id: 10, name: "10 AM" }
                , { id: 11, name: "11 AM" }
                , { id: 12, name: "12 PM" }
                , { id: 13, name: "01 PM" }
                , { id: 14, name: "02 PM" }
                , { id: 15, name: "03 PM" }
                , { id: 16, name: "04 PM" }
                , { id: 17, name: "05 PM" }
                , { id: 18, name: "06 PM" }
                , { id: 19, name: "07 PM" }
                , { id: 20, name: "08 PM" }
                , { id: 21, name: "09 PM" }
                , { id: 22, name: "10 PM" }
                , { id: 23, name: "11 PM" }
                // , { id: 0, name: "12 AM" }
                // , { id: 1, name: "01 AM" }
                // , { id: 2, name: "02 AM" }
                // , { id: 3, name: "03 AM" }
                // , { id: 4, name: "04 AM" }
                // , { id: 5, name: "05 AM" }
                // , { id: 6, name: "06 AM" }
                // , { id: 7, name: "07 AM" }
                // , { id: 8, name: "08 AM" }


            ],

            activeTab: 0,
            vertical: false,
            customer: {},
            snackbar_response: '',
            snackbar: false,
            room_no: '',
        };
    },
    watch: {
        activeTab() {
            this.componentKey++;

            this.rightContentUpdate();

        },

    },
    created() {
        let booking_payload = this.$store.state.booking_payload;


        if (booking_payload)
            if (booking_payload.params.room_no) {

                this.room_no = booking_payload.params.room_no;
            }
        this.$store.commit("partyHallBookingCustomer", {});
        this.$store.commit("partyHallBookingEvents", {});
        this.$store.commit("partyHallBookingFood", {});
        this.$store.commit("partyHallBookingAmount", {});
        this.$store.commit("partyHallBookingExtra", {});


    },
    methods: {

        bookingIdUpdated(data) {
            this.booking_id = data;
        },
        rightContentUpdate() {
            this.eventFoodTotal = 0;
            this.eventGrandTotal = 0;
            this.eventAdvanceAmount = 0;
            this.eventBalanceAmount = this.eventGrandTotal - this.eventAdvanceAmount;

            let partyHallBookingEvents = this.$store.state.partyHallBookingEvents;
            if (partyHallBookingEvents.start_time) {
                let start_time = this.hours.find(hour => hour.id === partyHallBookingEvents.start_time);;
                let end_time = this.hours.find(hour => hour.id === partyHallBookingEvents.end_time);;

                this.eventDateTime = partyHallBookingEvents.date + ' ' + start_time.name + ' - ' + end_time.name;

                this.eventPax = partyHallBookingEvents.pax;

            }
            //food 

            let obj = this.$store.state.partyHallBookingFood;
            var partyHallBookingFood = Object.keys(obj).map(function (key) { return obj[key]; });

            if ((partyHallBookingFood)) {
                partyHallBookingFood.forEach(item => {
                    if (parseInt(item.qty) > 0 && parseInt(item.price_per_item)) {
                        item.total = parseInt(item.qty) * parseInt(item.price_per_item);
                        this.eventFoodTotal += item.total;

                    }
                });
            }


            this.eventGrandTotal = 0;
        },
        reRender() {
            this.componentKey++;
        },
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },
        validate() {
            this.$refs.form.validate()
        },
        reset() {
            this.$refs.form.reset()
        },
        resetValidation() {
            this.$refs.form.resetValidation()
        },
        storeHallInformation() {

            // let partyHallBookingCustomer = { ...this.$store.state.partyHallBookingCustomer };
            // let partyHallBookingEvents = { ...this.$store.state.partyHallBookingEvents };
            // let partyHallBookingFood = { ...this.$store.state.partyHallBookingFood };
            // let partyHallBookingAmount = { ...this.$store.state.partyHallBookingAmount };
            // let partyHallBookingExtra = { ...this.$store.state.partyHallBookingExtra };
            // let payload1 = {
            //     partyHallBookingCustomer: partyHallBookingCustomer
            //     , partyHallBookingEvents: partyHallBookingEvents
            //     , partyHallBookingFood: partyHallBookingFood
            //     , partyHallBookingAmount: partyHallBookingAmount
            //     , partyHallBookingExtra: partyHallBookingExtra
            //     , hallRoomNumber: 900
            //     , hallRoomId: 900
            //     , company_id: this.$auth.user.company.id,
            //     user_id: this.$auth.user.id,
            // };
            // this.subLoad = true;
            // console.log("payload1", payload1);
            // let roomNumber = 900;
            // let roomNumberId = 20;
            // let discount = 500;
            // let tax = 500;
            // let Payload2 = {
            //     "customer_type": partyHallBookingCustomer.customer_type,
            //     "customer_status": "",
            //     "all_room_Total_amount": partyHallBookingAmount.inv_total,
            //     "total_extra": 0,
            //     "type": partyHallBookingCustomer.customer_type,
            //     "source": partyHallBookingCustomer.source_type,
            //     "agent_name": partyHallBookingCustomer.agent_name,
            //     "booking_status": 1,
            //     "check_in": partyHallBookingEvents.event_date,
            //     "check_out": partyHallBookingEvents.event_date,
            //     "discount": 0,
            //     "reference_number": partyHallBookingCustomer.reference_number,
            //     "advance_price": 0,
            //     "payment_mode_id": 1,
            //     "total_days": 1,
            //     "sub_total": partyHallBookingAmount.inv_total,
            //     "after_discount": 0,
            //     "sales_tax": 0,
            //     "total_price": partyHallBookingAmount.inv_total,
            //     "remaining_price": partyHallBookingAmount.inv_total,
            //     "request": partyHallBookingAmount.inv_total,
            //     "company_id": this.$auth.user.company.id,
            //     "remark": "",
            //     "rooms": roomNumber,
            //     "reference_no": partyHallBookingAmount.reference_no,
            //     "paid_by": "",
            //     "purpose": partyHallBookingAmount.purpose,
            //     "allFoods": [],
            //     "selectedRooms": [
            //         {
            //             "room_no": roomNumber,
            //             "room_type": "Hall",
            //             "room_id": roomNumberId,
            //             "price": partyHallBookingAmount.inv_total,
            //             "days": 1,
            //             "sgst": 0,
            //             "cgst": 0,
            //             "check_in": partyHallBookingEvents.event_date + ' ' + partyHallBookingEvents.event_start_time + ':00',
            //             "check_out": partyHallBookingEvents.event_date + ' ' + partyHallBookingEvents.event_end_time + ':00',
            //             "bed_amount": 0,
            //             "room_extra_amount": 0,
            //             "extra_amount_reason": "",
            //             "room_discount": discount,
            //             "after_discount": partyHallBookingAmount.inv_total,
            //             "room_tax": tax,
            //             "total_with_tax": 0,
            //             "total": partyHallBookingAmount.inv_total,
            //             "grand_total": partyHallBookingAmount.inv_total,
            //             "company_id": this.$auth.user.company.id,
            //             "no_of_adult": 1,
            //             "no_of_child": 0,
            //             "no_of_baby": 0,
            //             "tot_adult_food": 0,
            //             "tot_child_food": 0,
            //             "discount_reason": "",
            //             "priceList": [
            //                 {
            //                     "date": "2023-09-10",
            //                     "price": partyHallBookingAmount.inv_total,
            //                     "day_type": "weekday",
            //                     "day": "Sun",
            //                     "tax": tax,
            //                     "room_price": partyHallBookingAmount.inv_total,
            //                     "discount": discount
            //                 }
            //             ],
            //             "meal": "--- | --- | ---"
            //         }
            //     ],
            //     "id": 1717,
            //     "title": partyHallBookingCustomer.title,
            //     "name": null,
            //     "first_name": partyHallBookingCustomer.first_name,
            //     "last_name": partyHallBookingCustomer.last_name,
            //     "contact_no": partyHallBookingCustomer.contact_no,
            //     "whatsapp": partyHallBookingCustomer.whatsapp,
            //     "email": partyHallBookingCustomer.email,
            //     "nationality": partyHallBookingCustomer.nationality,
            //     "image": null,
            //     "id_card_type_id": partyHallBookingCustomer.id_card_type_id,
            //     "id_card_no": partyHallBookingCustomer.id_card_no,
            //     "car_no": partyHallBookingCustomer.car_no,
            //     "no_of_adult": "1",
            //     "no_of_child": "0",
            //     "no_of_baby": "0",
            //     "address": partyHallBookingCustomer.address,
            //     "dob": partyHallBookingCustomer.dob,
            //     "document": partyHallBookingCustomer.document,

            //     "passport_expiration": partyHallBookingCustomer.passport_expiration,
            //     "gst_number": partyHallBookingCustomer.gst_number,
            //     "full_name": partyHallBookingCustomer.full_name,
            //     "document_name": partyHallBookingCustomer.document_name,
            //     "customer_id": partyHallBookingCustomer.customer_id,
            //     "user_id": partyHallBookingCustomer.user_id,
            //     "merge_food_in_room_price": ""
            // };
            // this.$axios.post(`hall-booking`, Payload2).then(({ data }) => {

            //     this.snackbar = true;
            //     this.snackbar_response = "Event Booking completed Succesfully";




            //     setTimeout(() => {
            //         this.subLoad = false;
            //         this.$router.push('../hall/upcoming');
            //     }, 1000);


            // });
        },

        nextTab(data) {

            if (data) {
                if (data == "UpdateAmount") {


                    //get all components data 
                    try {
                        let partyHallBookingCustomer = JSON.parse(JSON.stringify(this.$store.state.partyHallBookingCustomer));
                        let partyHallBookingEvents = JSON.parse(JSON.stringify(this.$store.state.partyHallBookingEvents));
                        let partyHallBookingFood = JSON.parse(JSON.stringify(this.$store.state.partyHallBookingFood));
                        let partyHallBookingAmount = JSON.parse(JSON.stringify(this.$store.state.partyHallBookingAmount));
                        let partyHallBookingExtra = JSON.parse(JSON.stringify(this.$store.state.partyHallBookingExtra));





                    } catch (e) { console.log(e) }
                }
            }
            else {
                this.activeTab += 1;
            }


        },
    },
};
</script>

