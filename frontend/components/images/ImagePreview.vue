<template>
  <div class="text-right">
    <v-icon class="float-right" @click="downloadImage()" fill color="primary" size="50">mdi-cloud-download</v-icon>
    <v-img :src="src" class="grey darken-4" v-if="isImg"></v-img>
    <embed v-else-if="isPdf" :src="src" width="100%" height="800px" />
  </div>
</template>

<script>
export default {
  props: ["docObj"],
  data() {
    return {
      imgView: false,
      isImg: false,
      isPdf: false,
      src: false,
    };
  },
  watch: {
    docObj() {
      this.docView();
    },
  },
  mounted() {
    this.docView();
  },

  methods: {
    docView() {









      if (this.docObj.fileExtension == "pdf") {
        this.isPdf = true;
        this.isImg = false;
      } else {
        this.isPdf = false;
        this.isImg = true;
      }
      this.src = this.docObj.file;









    },

    downloadImage() {

      fetch(this.src, {
        mode: 'no-cors',
      })
        .then(response => response.blob())
        .then(blob => {
          let blobUrl = window.URL.createObjectURL(blob);

          let a = document.createElement('a');
          a.download = 'download.jpg';//.replace(/^.*[\\\/]/, '');
          a.href = blobUrl;
          document.body.appendChild(a);
          a.click();
          a.remove();
        });
    },
  },
};
</script>

<style scoped></style>
