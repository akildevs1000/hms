<template>
    <div v-if="can('calendar_create')">

        <v-row>
            <v-col md="12">
                <v-tabs v-model="activeTab" :vertical="vertical" background-color="primary" dark show-arrows>
                    <div class="py-3" style="background-color: #1259a7">
                        <span class="mx-2">Party Hall booking </span>
                    </div>
                    <v-col cols="6" class="mx-2 text-center">
                        <h2>Party Hall Booking Information </h2>
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
                                    <v-row>
                                        <v-col md="2" cols="12">

                                            <v-row>
                                                <v-col md="12">
                                                    <v-img class="guest-avatar" :src="showImage"> </v-img>
                                                </v-col>
                                                <v-col md="12">
                                                    <div class="d-flex justify-space-between">
                                                        <v-btn v-if="booking && booking.document" style="width: 50%" small
                                                            dark class="primary pt-4 pb-4 mt-4 mr-1 w-50 ipad-preview"
                                                            @click="preview(booking && booking.customer.document)">
                                                            ID
                                                            <v-icon right dark>mdi-file</v-icon>
                                                        </v-btn>
                                                        <v-btn style="width: 50%" small dark
                                                            class="primary pt-4 pb-4 mt-4 w-50"
                                                            @click="process_grc(booking.id)">
                                                            GRC
                                                            <v-icon right dark>mdi-file</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-col>
                                                <v-col md="12" class="pr-0 mr-0">
                                                    <div class="text-box-amt">
                                                        <table>
                                                            <tr class="bg-white amt-border-full">
                                                                <td>Hall rent
                                                                    :</td>
                                                                <td class="text-right">

                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_rent_amount)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border">
                                                                <td>Electricity (EB) Charges
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_electricity_amount)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border ">
                                                                <td>Sound System
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_electricity_amount)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  ">
                                                                <td>Projector :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_projector_amount)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  ">
                                                                <td>Arrangments & charges
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_cleaning_charges)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  ">
                                                                <td>Food Total
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.food_total_amount)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  ">
                                                                <td>Extra Billings
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.hall_extra_amounts_total)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  ">
                                                                <td>Total Tax
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.inv_total_tax)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-white amt-border  " style="font-weight:bold">
                                                                <td>Total
                                                                    :</td>
                                                                <td class="text-right">
                                                                    {{ $auth.user.company.currency }}

                                                                    {{
                                                                        numFormat(booking.inv_total)
                                                                    }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                        </v-col>

                                        <v-col md="9" cols="12">
                                            <v-row class="mt-4">
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Guest Name</h6>
                                                        <p>
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.title) ||
                                                                ""
                                                            }}.
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.full_name) ||
                                                                "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Mobile</h6>
                                                        <p>
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.contact_no) ||
                                                                "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Whatsapp</h6>
                                                        <p>
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.whatsapp) ||
                                                                "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>
                                            <v-row class="my-0 py-0">
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Reservation No</h6>
                                                        <p>
                                                            {{ (booking && booking.reservation_no) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Start and End Time </h6>
                                                        <p>

                                                            {{ getHours(booking.event_start_time) }}
                                                            To {{ getHours(booking.event_end_time) }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Booking Date</h6>
                                                        <p>
                                                            {{ (booking && booking.booking_date) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>

                                            <v-row class="my-0 py-0">
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Room</h6>
                                                        <p>{{ (booking && booking.room_number) || "---" }}</p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Customer Request</h6>
                                                        <p>
                                                            {{ (booking && booking.customer_request &&
                                                                booking.customer_request != 0) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6> </h6>
                                                        <p>

                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>

                                            <v-row class="my-0 py-0">
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Pay Type</h6>
                                                        <p>
                                                            {{
                                                                (booking && booking.paid_by == 2
                                                                    ? "Paid By Agent"
                                                                    : "Paid By Guest") || "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Source</h6>
                                                        <p>
                                                            {{ (booking && booking.source) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="4">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Reference Number</h6>
                                                        <p>
                                                            {{ (booking && booking.reference_no) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>

                                            <v-row class="my-0 py-0">
                                                <v-col md="6">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Guest Request</h6>
                                                        <p>
                                                            {{ (booking && booking.request) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="6">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Purpose</h6>
                                                        <p>
                                                            {{ (booking && booking.purpose) || "---" }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>

                                            <v-row class="my-0 py-0">
                                                <v-col md="6">
                                                    <div class="text-box" style="float: left">
                                                        <h6>GST</h6>
                                                        <p>
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.gst_number) ||
                                                                "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                                <v-col md="6">
                                                    <div class="text-box" style="float: left">
                                                        <h6>Address</h6>
                                                        <p>
                                                            {{
                                                                (booking &&
                                                                    booking.customer &&
                                                                    booking.customer.address) ||
                                                                "---"
                                                            }}
                                                        </p>
                                                    </div>
                                                </v-col>
                                            </v-row>


                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>

                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">

                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">

                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                    <v-tab-item>
                        <v-card flat>
                            <v-card-text>
                                <v-col cols="12">

                                </v-col>
                            </v-card-text>
                        </v-card>
                    </v-tab-item>
                </v-tabs>
            </v-col>








        </v-row>

    </div>
    <NoAccess v-else />
</template>
<script>

export default {
    components: {


    },
    data() {
        return {
            showImage: "",
            booking: [],
            activeTab: 0,
            vertical: false,
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
        };
    },

    created() {
        this.getBookingDetails();
    },
    methods: {

        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },

        getBookingDetails() {

            let id = this.$route.params.id;
            this.$axios.get(`hall-booking-details/${id}`).then(({ data }) => {
                //assign booking

                this.booking = data;

            });
        },
        numFormat(num) {

            try {
                if (!num) return "0";

                const number = num;
                const res = number.toFixed(2);
                return res;
            } catch (e) {
                return num;
            }
            const formatted = number.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
            return formatted;
        },

        getHours(time) {
            let arry = this.hours.find(hour => hour.id === time);
            if (arry) return arry.name;
            else return '';
        },

    },
};
</script>


<style scoped src="@/assets/custom.css"></style>

<style scoped>
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


