<template>
    <v-app>
        <nuxt />
    </v-app>
</template>

<style scoped>
body {

    border-radius: 1%;
    border: 3px solid green;
    padding: 10px;
    max-width: 1600px;
}

.fixed {
    min-height: 100%;
    position: fixed;

    /* overflow: hidden; */
    /* width: 24%; */
}

.fixed .fixed-content {
    width: 150%;
}

@media (max-width:1500px) {

    .fixed {
        min-height: 100%;
        position: relative;

        /* overflow: hidden; */
        /* width: 50%; */
    }

    .fixed .fixed-content {
        width: 100% !important;
    }
}

@media (max-width:1000px) {

    .fixed {
        min-height: 100%;
        position: relative;

        /* overflow: hidden; */
        /* width: 50%; */
    }

    .fixed .fixed-content {
        width: 100% !important;
        padding-right: 13%;
    }

}

/* 
@media (max-width:500px) {

    .fixed {
        min-height: 100%;
        position: relative;

         
}

.fixed .fixed-content {
    width: 100% !important;
}
}

*/
</style> 