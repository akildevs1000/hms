<template>
    <div>
      <div id="qr-reader" style="width: 100%; height: 400px;"></div>
      <div v-if="result">
        <p>Scanned Result: {{ result }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import { Html5Qrcode } from "html5-qrcode";
  
  export default {
    data() {
      return {
        result: null,
        qrCodeScanner: null,
      };
    },
    mounted() {
      this.initializeScanner();
    },
    beforeDestroy() {
      if (this.qrCodeScanner) {
        this.qrCodeScanner.stop().catch((err) => {
          console.error("Error stopping QR code scanner", err);
        });
      }
    },
    methods: {
      initializeScanner() {
        this.qrCodeScanner = new Html5Qrcode("qr-reader");
  
        this.qrCodeScanner.start(
          { facingMode: "environment" },
          {
            fps: 10, // optional, frame per second for the scanning
            qrbox: 250, // optional, scanning box size
          },
          (decodedText, decodedResult) => {
            // Handle the result here
            this.result = decodedText;
            this.qrCodeScanner.stop().catch((err) => {
              console.error("Error stopping QR code scanner", err);
            });
          },
          (error) => {
            console.warn(`QR code scan error: ${error}`);
          }
        ).catch((err) => {
          console.error("Error starting QR code scanner", err);
        });
      },
    },
  };
  </script>  