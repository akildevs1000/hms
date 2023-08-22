<template>
    <v-app>
        <nuxt />
    </v-app>
</template>

<style>
.fixed {
    min-height: 100%;
    position: fixed;

    overflow: hidden;
    width: 24%;
}

@media (max-width:950px) {

    .fixed {
        min-height: 100%;
        position: fixed;

        overflow: hidden;
        width: 50%;
    }
}
</style> 