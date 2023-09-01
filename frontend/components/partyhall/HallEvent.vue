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
                                    <v-text-field v-model="event.date" readonly label="Date Of event" v-on="on"
                                        v-bind="attrs" :hide-details="true" dense outlined required
                                        :rules="nameRules"></v-text-field>
                                </template>
                                <v-date-picker v-model="event.date" @input="date_menu = false"></v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Type of Event" outlined type="text" v-model="event.event_type"
                                required :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Number of Pax" outlined type="text" v-model="event.pax" required
                                :rules="nameRules"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-select v-model="event.start_time" :items="hours" item-text="name" item-value="id" item
                                label="Start Time" dense flat outlined required :rules="nameRules">
                            </v-select>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-select v-model="event.end_time" :items="hours" item-text="name" item-value="id" item
                                label="End Time" dense flat outlined required :rules="nameRules">
                            </v-select>
                        </v-col>



                        <v-col md="4" cols="12">
                            <v-text-field dense label="Special Setup" outlined type="text"
                                v-model="event.special_setup"></v-text-field>
                        </v-col>

                        <v-col md="4" cols="12">
                            <v-text-field dense label="Audio System" outlined type="text"
                                v-model="event.audio_system"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Projector" outlined type="text"
                                v-model="event.projector"></v-text-field>
                        </v-col>
                        <v-col md="4" cols="12">
                            <v-text-field dense label="Stage Decoration" outlined type="text"
                                v-model="event.stage_decoration"></v-text-field>
                        </v-col>




                    </v-row>
                </v-form>

                <v-spacer></v-spacer>
                <v-row>
                    <v-col cols="12" class=" text-right">



                        <v-btn color="primary" fill dense @click="validate">
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

    data() {
        return {
            date_menu: false,
            valid: true,

            nameRules: [
                v => !!v || 'Name is required',

            ],

            emailRules: [
                v => !!v || 'E-mail is required',
                v => /.+@.+\..+/.test(v) || 'E-mail must be valid',
            ],


            activeTab: 0,
            vertical: false,


            event: { date: '', event_type: '', start_time: '', end_time: '' },
            hours: [
                { id: 10, name: "10AM" }
                , { id: 11, name: "11AM" }
                , { id: 12, name: "12PM" }
                , { id: 13, name: "01PM" }
                , { id: 14, name: "02PM" }
                , { id: 15, name: "03PM" }
                , { id: 16, name: "04PM" }
                , { id: 17, name: "05PM" }
                , { id: 18, name: "06PM" }
                , { id: 19, name: "07PM" }
                , { id: 20, name: "08PM" }
                , { id: 21, name: "09PM" }
                , { id: 22, name: "010PM" }
                , { id: 23, name: "011PM" }
                , { id: 0, name: "12AM" }
                , { id: 1, name: "01AM" }
                , { id: 2, name: "02AM" }
                , { id: 3, name: "03AM" }
                , { id: 4, name: "04AM" }
                , { id: 5, name: "05AM" }
                , { id: 6, name: "06AM" }
                , { id: 7, name: "07AM" }
                , { id: 8, name: "08AM" }
                , { id: 9, name: "09AM" }

            ],

        };
    },
    created() {

    },
    methods: {
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
            );
        },
        validate() {
            this.$refs.form.validate()
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

