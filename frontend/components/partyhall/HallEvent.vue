<template>
    <div v-if="can('calendar_create')">
        <v-card flat>
            <v-card-text>

                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row>


                        <v-col md="4" cols="12">
                            <v-menu v-model="date_menu" :close-on-content-click="false" :nudge-right="40"
                                transition="scale-transition" offset-y min-width="auto">
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field v-model="event.date" readonly label="Date Of event *" v-on="on"
                                        v-bind="attrs" :hide-details="true" dense outlined required :rules="nameRules"
                                        append-icon="mdi-calendar-month"></v-text-field>
                                </template>
                                <v-date-picker style="height:470px" v-model="event.date"
                                    @input="date_menu = false"></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col md="4" cols="12">

                            <v-select v-model="event.event_type" :items="event_type_names" item-text="name" item-value="id"
                                label="Type of Event *" append-icon="mdi-party-popper" dense flat outlined required
                                :rules="nameRules">
                            </v-select>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Number of Pax" outlined type="number" v-model="event.pax" required
                                :rules="nameRules" append-icon="mdi-account-details-outline"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-select v-model="event.start_time" :items="hours" item-text="name" item-value="id"
                                label="Start Time" append-icon="mdi-clock-outline" dense flat outlined required
                                :rules="nameRules">
                            </v-select>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-select v-model="event.end_time" :items="hours" item-text="name" item-value="id" item
                                label="End Time" append-icon="mdi-clock-outline" :min="event.start_time" dense flat outlined
                                required :rules="nameRules">
                            </v-select>
                        </v-col>



                        <v-col md="4" cols="12">
                            <v-text-field dense label="Special Setup" outlined type="text" v-model="event.special_setup"
                                append-icon="mdi-folder-star"></v-text-field>
                        </v-col>

                        <v-col md="4" cols="12">
                            <h4> Audio System</h4> <v-radio-group v-model="event.audio_system" lable="Test" outlined row>
                                <v-radio label="Yes" value="1">
                                    <template v-slot:label>
                                        <div> <strong class="success--text">Yes </strong></div>
                                    </template>
                                </v-radio>
                                <v-radio label="No" value="0">
                                    <template v-slot:label>
                                        <div> <strong class="error--text">No </strong></div>
                                    </template></v-radio>
                            </v-radio-group>
                            <!-- <v-select v-model="event.audio_system" label="Audio System" :items="YesOrNO" item-text="name"
                                item-value="id" dense flat outlined required :rules="nameRules"
                                append-icon="mdi-surround-sound">
                            </v-select> -->
                        </v-col>
                        <v-col md="4" cols="12">
                            <h4> Projector</h4> <v-radio-group v-model="event.projector" lable="Test" outlined row>
                                <v-radio label="Yes" value="1">
                                    <template v-slot:label>
                                        <div> <strong class="success--text">Yes </strong></div>
                                    </template>
                                </v-radio>
                                <v-radio label="No" value="0">
                                    <template v-slot:label>
                                        <div> <strong class="error--text">No </strong></div>
                                    </template></v-radio>
                            </v-radio-group>

                            <!-- <v-select v-model="event.projector" label="Projector" :items="YesOrNO" item-text="name"
                                item-value="id" append-icon="mdi-projector" dense flat outlined required :rules="nameRules">
                            </v-select> -->
                        </v-col>
                        <v-col md="4" cols="12">
                            <h4>Stage Decoration</h4> <v-radio-group v-model="event.stage_decoration" lable="Test" outlined
                                row>
                                <v-radio label="Yes" value="1">
                                    <template v-slot:label>
                                        <div> <strong class="success--text">Yes </strong></div>
                                    </template>
                                </v-radio>
                                <v-radio label="No" value="0">
                                    <template v-slot:label>
                                        <div> <strong class="error--text">No </strong></div>
                                    </template></v-radio>
                            </v-radio-group>
                            <!-- <v-select v-model="event.stage_decoration" label="Stage Decoration" :items="YesOrNO"
                                item-text="name" append-icon="mdi-postage-stamp" item-value="id" dense flat outlined
                                required :rules="nameRules">
                            </v-select> -->
                        </v-col>




                    </v-row>
                </v-form>

                <v-spacer></v-spacer>
                <v-row>
                    <v-col cols="12" class=" text-right">



                        <v-btn color="primary" fill dense @click="nextTab()">
                            Next
                        </v-btn>
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
            date_menu: false,
            valid: true,

            nameRules: [
                v => v !== undefined && v !== null && v !== '' ? true : 'Required',

            ],

            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],


            activeTab: 0,
            vertical: false,


            event: {},
            event_type_names: [{ id: 1, name: "Marriage" }
                , { id: 2, name: "Reception" }
                , { id: 3, name: "Office Meeting" }],
            YesOrNO: [{ id: 1, name: "Yes" }, { id: 0, name: "No" }],
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
    watch: {


        // event() {
        //     let obj = { ...this.event };
        //     Object.assign({}, obj)
        //     obj = JSON.parse(JSON.stringify(obj))

        //     this.$store.commit("partyHallBookingEvents", obj);

        // },
        nextTabTrigger() {
            let obj = { ...this.event };
            Object.assign({}, obj)
            obj = JSON.parse(JSON.stringify(obj))

            this.$store.commit("partyHallBookingEvents", obj);

        },

    },
    created() {

        let booking_payload = this.$store.state.booking_payload;

        if (booking_payload)

            if (booking_payload.params.checkin) {
                this.event.date = booking_payload.params.checkin;
                this.room_no = booking_payload.params.room_no;
            }



    },
    methods: {
        nextTab() {



            if (this.validate()) {

                let obj = { ...this.event };
                Object.assign({}, obj)
                obj = JSON.parse(JSON.stringify(obj))

                this.$store.commit("partyHallBookingEvents", obj);
                this.$emit("c-next-tab", null);

            }

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
    },
};
</script>

