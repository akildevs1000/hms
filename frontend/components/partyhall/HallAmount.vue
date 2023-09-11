<template>
    <div v-if="can('calendar_create')">
        <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
            {{ snackbar_response }}
        </v-snackbar>
        <v-card flat>
            <v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>

                    <v-row style="background-color: #e3e3e3;">
                        <v-col md="3" cols="12"><label class="text-h6">Description</label>
                        </v-col>
                        <v-col md="3" cols="12"><label class="text-h6">Unit Cost</label>
                        </v-col>
                        <v-col md="3" cols="12"><label class="text-h6">Qty</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            Total
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>


                    <v-row>
                        <v-col md="3" cols="12"><label>Hall rent</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>{{ getPriceFormat(hallRentPerHour) }} Per Hour</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>{{ durationInHours }} Hours</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(hallRentTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Electricity (EB) Charges</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>{{ getPriceFormat(electricityTotalAmount) }}</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>1 Day</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(electricityTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col md="3" cols="12"><label>Sound System </label>
                        </v-col>
                        <v-col md="3" cols="12"><label>{{ getPriceFormat(audioTotalAmount) }}</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>1 Day</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(audioTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label> Projector</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>{{ getPriceFormat(projecterTotalAmount) }}</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>1 Day</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(projecterTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Setting arrangment & Cleaning charges</label>
                        </v-col>
                        <v-col md="3" cols="12"><label> {{ getPriceFormat(cleaningTotalAmount) }}</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>1 Day</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(cleaningTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="3" cols="12"><label>Food Total</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>---</label>
                        </v-col>
                        <v-col md="3" cols="12"><label>----</label>
                        </v-col>
                        <v-col md="2" cols="12" class="text-right">

                            {{ getPriceFormat(foodTotalAmount) }}
                        </v-col>
                        <v-col md="1" cols="12" class="text-right">


                        </v-col>
                    </v-row>

                    <v-row>
                        <h4>Other Charges</h4>
                        <v-col cols="12">
                            <v-form ref="form" v-model="valid" lazy-validation>

                                <v-row v-for="(d, index) in ExtraCharges.items" :key="index">

                                    <v-col cols="3">
                                        <label class="col-form-label"> Name </label>
                                        <v-text-field dense outlined v-model="d.name" required
                                            :rules="nameRules"></v-text-field>

                                    </v-col>
                                    <v-col cols="3">
                                        <label class="col-form-label">Price </label>
                                        <v-text-field type="number" dense outlined v-model="d.price_per_item" required
                                            :rules="nameRules" @input="calculateGrandPrice()"></v-text-field>


                                    </v-col>
                                    <v-col cols="3">
                                        <label class="col-form-label">Qty/Pax </label>
                                        <v-text-field type="number" @input="calculateGrandPrice()" dense outlined
                                            v-model="d.qty" required :rules="nameRules"></v-text-field>


                                    </v-col>
                                    <v-col cols="2">
                                        <label class="col-form-label">Total </label>
                                        <v-text-field disabled dense outlined v-model="d.total" style="text-align:right"
                                            class="text-box-right-input bold"></v-text-field>


                                    </v-col>
                                    <v-col cols="1">
                                        <v-icon size="30" class="error--text mt-7"
                                            @click="removeItem(index)">mdi-delete</v-icon>
                                        <v-icon size="30" v-if="ExtraCharges.items.length == (index + 1)" fill
                                            class="primary--text mt-7  " @click="addDocumentInfo()">mdi-plus-box</v-icon>

                                    </v-col>


                                </v-row>
                                <v-row>

                                    <hr />
                                    <v-col cols="9" class="text-right bold  "
                                        style="font-weight:bold;text-decoration: :underline; ">
                                        Total


                                    </v-col>
                                    <v-col cols="2" class="text-right bold ;text-decoration: :underline;">
                                        {{ hallTaxableTotalAmount }}
                                    </v-col>
                                </v-row>


                                <v-row>


                                    <v-col cols="9" class="text-right bold  " style="font-weight:bold;color:red">
                                        Tax


                                    </v-col>
                                    <v-col cols="2" class="text-right bold ;color:red">
                                        {{ room_tax_amount }}
                                    </v-col>
                                </v-row>
                                <v-row>


                                    <v-col cols="9" class="text-right bold  " style="font-weight:bold;color:red">
                                        Discount


                                    </v-col>
                                    <v-col cols="2" class="text-right bold ;color:red">
                                        -{{ discount }}
                                    </v-col>
                                </v-row>
                                <v-row>


                                    <v-col cols="9" class="text-right bold" style="font-weight:bold">
                                        Grand Total


                                    </v-col>
                                    <v-col cols="2" class="text-right bold">
                                        {{ AmountGrandTotal }}
                                    </v-col>
                                </v-row>
                                <v-row>


                                </v-row>
                            </v-form>
                        </v-col>






                    </v-row>

                    <v-row>
                        <v-col cols="1" class="text-left bold" style="font-weight:bold">
                            Discount


                        </v-col>
                        <v-col cols="1" class="text-left bold" style="font-weight:bold">
                            <v-text-field dense type="number" outlined v-model="discount" style="text-align:left"
                                class="text-box-left-input bold"></v-text-field>


                        </v-col>


                    </v-row>



                </v-form>

                <v-spacer></v-spacer>
                <v-row>
                    <v-col cols="12" class=" text-right">



                        <v-btn color="primary" fill dense @click="finalCalculation">
                            Confirm Booking </v-btn>
                    </v-col>
                </v-row>







            </v-card-text>
        </v-card>
    </div>
    <NoAccess v-else />
</template>
<script>

export default {
    props: ["nextTabTrigger"],
    data() {
        return {
            discount: 0,
            hallRentPerHour: 0,
            projecterTotalAmount: 0,
            cleaningTotalAmount: 0,
            electricityTotalAmount: 0,
            audioTotalAmount: 0,
            tax_percentage: 0,
            room_tax_amount: 0,


            durationInHours: 1,
            foodTotalAmount: 0,


            hallRentTotalAmount: 0,

            AmountGrandTotal: 0,
            hallTaxableTotalAmount: 0,
            ExtraCharges: {
                items: [{ name: "", price_per_item: "", qty: "", total: "" }],
            },
            valid: true,

            nameRules: [
                v => !!v || 'Required',

            ],

            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],


            activeTab: 0,
            vertical: false,

            snackbar_response: '',
            snackbar: false,
            customer: { contact_person_name: '', company_name: '' }

        };
    },
    watch: {

        discount() {
            this.calculateGrandTotalAmount();

        },
        nextTabTrigger() {

            this.calculateGrandTotalAmount();

            // let amount = {
            //     'hallRentTotalAmount': this.hallRentTotalAmount
            //     , 'foodTotalAmount': this.foodTotalAmount
            //     , 'cleaningTotalAmount': this.cleaningTotalAmount
            //     , 'electricityTotalAmount': this.electricityTotalAmount
            //     , 'audioTotalAmount': this.audioTotalAmount
            // };

            // // this.$store.commit("partyHallBookingAmount", amount);
            // // this.$store.commit("partyHallBookingExtra", this.ExtraCharges);

            // let obj = { ...amount };
            // Object.assign({}, obj)
            // obj = JSON.parse(JSON.stringify(obj));
            // this.$store.commit("partyHallBookingAmount", amount);

            // let obj2 = { ...this.ExtraCharges };
            // Object.assign({}, obj2)
            // obj = JSON.parse(JSON.stringify(obj2));
            // this.$store.commit("partyHallBookingExtra", obj2);
        },

    },
    created() {

        this.getHallPriceDetails();



    },
    methods: {
        getHallPriceDetails() {

            let partyHallBookingEvents = this.$store.state.partyHallBookingEvents;
            let payload = {
                params: {
                    company_id: this.$auth.user.company.id,
                    start: partyHallBookingEvents.date,
                },
            };
            this.$axios.get(`hall/get_hall_prices`, payload).then(({ data }) => {

                this.hallRentPerHour = parseFloat(data[0].price);
                this.cleaningTotalAmount = parseFloat(data[0].cleaning_charges);

                this.electricityTotalAmount = parseFloat(data[0].electricity_charges);

                this.audioTotalAmount = parseFloat(data[0].audio_charges);

                this.projecterTotalAmount = parseFloat(data[0].projector_charges);
                this.tax_percentage = parseFloat(data[0].tax);

                this.calculateGrandTotalAmount();
            });
        },
        calculateGrandTotalAmount() {


            this.foodTotalAmount = 0;


            let partyHallBookingEvents = this.$store.state.partyHallBookingEvents;
            if (partyHallBookingEvents.start_time) {
                let start_time = partyHallBookingEvents.start_time;
                let end_time = partyHallBookingEvents.end_time;
                this.durationInHours = parseInt(end_time) - parseInt(start_time);
                this.hallRentTotalAmount = this.durationInHours * this.hallRentPerHour;

            }

            if (partyHallBookingEvents.audio_system != 1) {
                this.audioTotalAmount = 0;
            }
            if (partyHallBookingEvents.projector != 1) {
                this.projecterTotalAmount = 0;
            }

            //food 

            let obj = this.$store.state.partyHallBookingFood;
            var partyHallBookingFood = Object.keys(obj).map(function (key) { return obj[key]; });


            console.log("partyHallBookingFood", partyHallBookingFood);
            if ((partyHallBookingFood)) {
                partyHallBookingFood[0].forEach(item => {
                    if (parseInt(item.qty) > 0 && parseInt(item.price_per_item)) {
                        item.total = parseInt(item.qty) * parseInt(item.price_per_item);
                        this.foodTotalAmount += item.total;

                    }
                });
            }

            this.calculateGrandPrice();

        },
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },
        validate() {
            return this.$refs.form.validate()
        },
        // reset() {
        //     this.$refs.form.reset()
        // },
        // resetValidation() {
        //     this.$refs.form.resetValidation()
        // },
        calculateGrandPrice() {

            let AmountGrandTotalTemp = this.hallRentTotalAmount + this.foodTotalAmount + this.cleaningTotalAmount + this.electricityTotalAmount + this.audioTotalAmount + this.projecterTotalAmount;


            this.ExtraCharges.items.forEach(item => {
                if (parseInt(item.qty) > 0 && parseInt(item.price_per_item)) {
                    item.total = parseInt(item.qty) * parseInt(item.price_per_item);
                    AmountGrandTotalTemp += item.total;

                }
                else {
                    item.total = 0;
                }

            });
            this.hallTaxableTotalAmount = AmountGrandTotalTemp;

            this.room_tax_amount = AmountGrandTotalTemp * this.tax_percentage / 100;

            this.AmountGrandTotal = AmountGrandTotalTemp + this.room_tax_amount - this.discount;



        },
        addDocumentInfo() {

            this.ExtraCharges.items.unshift({ name: "", price_per_item: "", qty: "", total: "" });
        },
        removeItem(index) {


            this.ExtraCharges.items.splice(index, 1);
        },
        finalCalculation() {


            if (this.validate()) {

                let amount = {
                    'hallRentTotalAmount': this.hallRentTotalAmount
                    , 'foodTotalAmount': this.foodTotalAmount
                    , 'cleaningTotalAmount': this.cleaningTotalAmount
                    , 'electricityTotalAmount': this.electricityTotalAmount
                    , 'audioTotalAmount': this.audioTotalAmount
                    , 'projecterTotalAmount': this.projecterTotalAmount
                    , 'hall_rent_amount_per_hour': this.hallRentPerHour
                    , 'discount': this.discount

                    , 'AmountGrandTotalWithFood': this.AmountGrandTotal
                    , 'AmountGrandTotalWithOutFood': this.AmountGrandTotal - this.foodTotalAmount
                    , 'AmountRoomPriceWithTax_before_discount': this.hallTaxableTotalAmount + this.room_tax_amount + this.discount


                };
                let obj = { ...amount };
                Object.assign({}, obj)
                obj = JSON.parse(JSON.stringify(obj));
                this.$store.commit("partyHallBookingAmount", amount);

                let obj2 = { ...this.ExtraCharges };
                Object.assign({}, obj2)
                obj = JSON.parse(JSON.stringify(obj2));
                this.$store.commit("partyHallBookingExtra", obj2);

                this.$emit("c-next-tab", "UpdateAmount");


                this.storeHallInformation();

            } else {


            }

        },

        storeHallInformation() {

            let totlRoomAmount = this.hallRentTotalAmount + this.cleaningTotalAmount + this.electricityTotalAmount + this.audioTotalAmount + this.projecterTotalAmount;

            let room_discount = this.discount;
            let tax = this.tax_percentage;
            let totlRoomAmount_after_discount_with_tax = totlRoomAmount - room_discount + tax;




            let partyHallBookingCustomer = { ...this.$store.state.partyHallBookingCustomer };
            let partyHallBookingEvents = { ...this.$store.state.partyHallBookingEvents };
            let partyHallBookingFood = { ...this.$store.state.partyHallBookingFood };
            let partyHallBookingAmount = { ...this.$store.state.partyHallBookingAmount };
            let partyHallBookingExtra = { ...this.$store.state.partyHallBookingExtra };
            let payload = {
                partyHallBookingCustomer: partyHallBookingCustomer
                , partyHallBookingEvents: partyHallBookingEvents
                , partyHallBookingFood: partyHallBookingFood
                , partyHallBookingAmount: partyHallBookingAmount
                , partyHallBookingExtra: partyHallBookingExtra
                , hallRoomNumber: 900
                , hallRoomId: 900
                , company_id: this.$auth.user.company.id
                , user_id: this.$auth.user.id
                , discount: room_discount
            };

            let total_amount_with_food_after_discount_with_tax = partyHallBookingAmount.AmountGrandTotalWithFood;;
            let AmountRoomPriceWithTax_before_discount = partyHallBookingAmount.AmountGrandTotalWithFood;;

            let total_amount_without_food_after_discount_with_tax = parseFloat(total_amount_with_food_after_discount_with_tax) + parseFloat(room_discount) - parseFloat(this.foodTotalAmount);

            this.subLoad = true;
            console.log('payload', payload);
            let roomNumber = 900;
            let roomNumberId = 102;

            let Payload2 = {
                "customer_type": partyHallBookingCustomer.customer_type,
                "customer_status": "",
                "room_category_type": "Hall",
                "all_room_Total_amount": total_amount_with_food_after_discount_with_tax,
                "total_extra": 0,
                "type": partyHallBookingCustomer.customer_type,
                "source": partyHallBookingCustomer.source_type,
                "agent_name": "",
                "booking_status": 1,
                "check_in": partyHallBookingEvents.date,
                "check_out": partyHallBookingEvents.date,
                "discount": room_discount,
                "reference_number": "",
                "advance_price": 0,
                "payment_mode_id": 1,
                "total_days": 1,
                "sub_total": total_amount_with_food_after_discount_with_tax,
                "after_discount": 0,
                "sales_tax": 0,
                "total_price": total_amount_with_food_after_discount_with_tax,
                "remaining_price": total_amount_with_food_after_discount_with_tax,
                "request": "",
                "company_id": this.$auth.user.company.id,
                "remark": "",
                "rooms": roomNumber,
                "reference_no": partyHallBookingAmount.reference_no,
                "paid_by": "",
                "purpose": "",
                "allFoods": [],
                "selectedRooms": [
                    {
                        "room_no": roomNumber,
                        "room_type": "Hall",
                        "room_category_type": "Hall",
                        "room_id": roomNumberId,
                        "price": total_amount_with_food_after_discount_with_tax,
                        "days": 1,
                        "sgst": 0,
                        "cgst": 0,
                        "check_in": partyHallBookingEvents.date + ' ' + partyHallBookingEvents.start_time + ':00',
                        "check_out": partyHallBookingEvents.date + ' ' + partyHallBookingEvents.end_time + ':00',
                        "bed_amount": 0,
                        "room_extra_amount": 0,
                        "extra_amount_reason": "",
                        "room_discount": room_discount,
                        "after_discount": total_amount_with_food_after_discount_with_tax,
                        "room_tax": tax,
                        "total_with_tax": 0,
                        "total": total_amount_with_food_after_discount_with_tax,
                        "grand_total": total_amount_with_food_after_discount_with_tax,
                        "company_id": this.$auth.user.company.id,
                        "no_of_adult": 1,
                        "no_of_child": 0,
                        "no_of_baby": 0,
                        "tot_adult_food": this.foodTotalAmount,
                        "tot_child_food": 0,
                        "discount_reason": "",
                        "priceList": [
                            {
                                "date": partyHallBookingEvents.date,
                                "price": total_amount_without_food_after_discount_with_tax,
                                "day_type": "weekday",
                                "day": "Sun",
                                "tax": tax,
                                "room_price": total_amount_without_food_after_discount_with_tax,
                                "discount": room_discount
                            }
                        ],
                        "meal": "--- | --- | ---"
                    }
                ],
                "id": 1717,
                "title": partyHallBookingCustomer.title,
                "name": null,
                "first_name": partyHallBookingCustomer.first_name,
                "last_name": partyHallBookingCustomer.last_name,
                "contact_no": partyHallBookingCustomer.contact_no,
                "whatsapp": partyHallBookingCustomer.whatsapp,
                "email": partyHallBookingCustomer.email,
                "nationality": partyHallBookingCustomer.nationality,
                "image": null,
                "id_card_type_id": partyHallBookingCustomer.id_card_type_id,
                "id_card_no": partyHallBookingCustomer.id_card_no,
                "car_no": partyHallBookingCustomer.car_no,
                "no_of_adult": partyHallBookingEvents.pax,
                "no_of_child": "0",
                "no_of_baby": "0",
                "address": partyHallBookingCustomer.address,
                "dob": partyHallBookingCustomer.dob,
                "document": partyHallBookingCustomer.document,

                "passport_expiration": partyHallBookingCustomer.passport_expiration,
                "gst_number": partyHallBookingCustomer.gst_number,
                "full_name": partyHallBookingCustomer.full_name,
                "document_name": partyHallBookingCustomer.document_name,
                "customer_id": partyHallBookingCustomer.customer_id,
                "user_id": this.$auth.user.id,
                "merge_food_in_room_price": ""
            };





            this.$axios.post(`hall-booking`, { Payload1: payload, Payload2: Payload2 }).then(({ data }) => {


                //this.store_document(data.data);
                this.snackbar = true;
                this.snackbar_response = "Event Booking completed Succesfully";

                setTimeout(() => {
                    this.subLoad = false;
                    // this.$router.push('../hall/upcoming');
                }, 1000);


            });
        },
        getPriceFormat(price) {

            return parseFloat(price).toLocaleString('en-IN', {
                maximumFractionDigits: 2,
                minimumFractionDigits: 2,
            });
        },
        store_document(id) {
            let payload = new FormData();
            // payload.append("document", partyHallBookingCustomer.document);
            //payload.append("image", partyHallBookingCustomer.image);

            payload.append("document", (this.$store.state.customerImage));
            payload.append("image", (this.$store.state.customerImage));

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

    },
};
</script>

