
<template>
    <div v-if="can('email_access') && can('email_view')">

        <v-tabs background-color="background" class="rounded-t-sm" dark show-arrows>
            <v-spacer></v-spacer>
            <v-tab active-class="active-link"> Emails </v-tab>
            <v-tab active-class="active-link"> Report Types </v-tab>

            <v-tabs-slider color="#1259a7"></v-tabs-slider>
            <!-- today checkin -->

            <v-tab-item>
                <v-card flat>
                    <v-card-text>
                        <client-only>
                            <EmailNotifications />
                        </client-only>
                    </v-card-text>
                </v-card>


            </v-tab-item>
            <v-tab-item>
                <v-card flat>
                    <v-card-text>
                        <client-only>
                            <NotificationReports />
                        </client-only>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>


    </div>
    <NoAccess v-else />
</template>
<script>
export default {

    methods: {
        can(per) {
            let u = this.$auth.user;
            return (
                (u && u.permissions.some(e => e == per || per == "/")) || u.is_master
            );
        },
    }
}

</script>