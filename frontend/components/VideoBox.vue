<template>
  <span>
    <canvas ref="captureCanvasFront" style="display: none"></canvas>
    <canvas ref="captureCanvasBack" style="display: none"></canvas>
    <div style="position: relative">
      <video
        ref="videoElement"
        autoplay
        style="width: 640px; height: 480px"
      ></video>
      <div
        ref="box"
        style="
          position: absolute;
          border: 2px solid red;
          width: 350px;
          height: 220px;
          top: 100px;
          left: 120px;
        "
      ></div>
    </div>
    <v-row no-gutters>
      <v-col cols="12" class="text-center">
        <v-btn class="blue white--text" @click="captureFrontImage"
          >Capture Front</v-btn
        >
        <v-btn class="blue white--text" @click="captureBackImage"
          >Capture Back</v-btn
        >
      </v-col>
    </v-row>
  </span>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    frontSrc: null,
    backSrc: null,
    videoStream: null,
  }),
  methods: {
    close() {
      // if (this.videoStream) {
      //   this.videoStream.getTracks().forEach((track) => track.stop());
      // }
      this.$emit("close");
    },
    captureFrontImage() {
      const video = this.$refs.videoElement;
      const canvas = this.$refs.captureCanvasFront;
      const box = this.$refs.box;

      const context = canvas.getContext("2d");
      const boxRect = box.getBoundingClientRect();
      const videoRect = video.getBoundingClientRect();

      const sx = boxRect.left - videoRect.left;
      const sy = boxRect.top - videoRect.top;
      const sWidth = boxRect.width;
      const sHeight = boxRect.height;

      canvas.width = sWidth;
      canvas.height = sHeight;

      context.drawImage(video, sx, sy, sWidth, sHeight, 0, 0, sWidth, sHeight);

      let src = canvas.toDataURL("image/png");
      this.frontSrc = src;
      this.$emit("frontSrc", src);
    },
    captureBackImage() {
      const video = this.$refs.videoElement;
      const canvas = this.$refs.captureCanvasBack;
      const box = this.$refs.box;

      const context = canvas.getContext("2d");
      const boxRect = box.getBoundingClientRect();
      const videoRect = video.getBoundingClientRect();

      const sx = boxRect.left - videoRect.left;
      const sy = boxRect.top - videoRect.top;
      const sWidth = boxRect.width;
      const sHeight = boxRect.height;

      canvas.width = sWidth;
      canvas.height = sHeight;

      // context.drawImage(video, sx, sy, sWidth, sHeight, 0, 0, sWidth, sHeight);
      context.drawImage(video, sx, sy, sWidth, sHeight, 0, 0, sWidth, sHeight);

      let src = canvas.toDataURL("image/png");
      this.backSrc = src;
      this.$emit("backSrc", src);
    },
    setupCamera() {
      const video = this.$refs.videoElement;
      navigator.mediaDevices
        .getUserMedia({ video: true })
        .then((stream) => {
          video.srcObject = stream;
          this.videoStream = stream;
        })
        .catch((err) => {
          console.error("Error accessing the camera: " + err);
        });
    },
  },
  mounted() {
    this.setupCamera();
  },
  // beforeDestroy() {
  //     if (this.videoStream) {
  //       this.videoStream.getTracks().forEach((track) => track.stop());
  //     }
  //   },
};
</script>
