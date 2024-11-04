<template>
  <span>
    <style>
      @keyframes pulse {
        0% {
          /* transform: scale(1); */
          box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.7);
        }
        70% {
          /* transform: scale(1.2); */
          box-shadow: 0 0 0 7px rgba(33, 150, 243, 0);
        }
        100% {
          /* transform: scale(1); */
          box-shadow: 0 0 0 0 rgba(33, 150, 243, 0);
        }
      }

      .recording-active {
        border-radius: 50px;
        animation: pulse 1s infinite;
      }
    </style>
    <v-icon @click="startRecording" color="primary" v-if="!isRecording"
      >mdi-microphone-outline</v-icon
    >

    <v-icon
      :class="`${isRecording ? 'recording-active' : ''}`"
      @click="stopRecording"
      color="primary"
      v-if="isRecording"
      >mdi-microphone-outline</v-icon
    >

    <v-icon @click="playRecording" v-if="recordedBlob" color="success"
      >mdi-play-circle-outline</v-icon
    >
    <audio
      style="display: none"
      ref="audioPlayer"
      :src="audioUrl"
      controls
    ></audio>
  </span>
</template>

<script>
export default {
  layout: "login",
  data() {
    return {
      isRecording: false,
      mediaRecorder: null,
      audioChunks: [],
      recordedBlob: null,
      audioUrl: null,
    };
  },
  methods: {
    async blobToBase64(blob) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob); // Read as Data URL (Base64)
      });
    },

    async startRecording() {
      this.recordedBlob = null;
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          audio: true,
        });
        this.mediaRecorder = new MediaRecorder(stream);
        this.mediaRecorder.start();
        this.isRecording = true;
        this.audioChunks = [];

        this.mediaRecorder.addEventListener("dataavailable", (event) => {
          this.audioChunks.push(event.data);
        });

        this.mediaRecorder.addEventListener("stop", async () => {
          this.recordedBlob = new Blob(this.audioChunks, {
            type: "audio/mpeg",
          });
          this.audioUrl = URL.createObjectURL(this.recordedBlob);
          this.$emit(`voice-note`, await this.blobToBase64(this.recordedBlob));
        });
      } catch (error) {
        console.error("Error accessing the microphone", error);
      }
    },
    async stopRecording() {
      if (this.mediaRecorder) {
        this.mediaRecorder.stop();
        this.isRecording = false;
      }
    },
    playRecording() {
      const audioPlayer = this.$refs.audioPlayer;
      if (audioPlayer && this.audioUrl) {
        audioPlayer.play();
      }
    },
  },
};
</script>
