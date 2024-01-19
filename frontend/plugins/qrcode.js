import QRCode from "qrcode";

export default ({ app }, inject) => {
  inject("qrcode", {
    generate: (text, options) => {
      return new Promise((resolve, reject) => {
        QRCode.toDataURL(text, options, (error, dataURL) => {
          if (error) {
            reject(error);
          } else {
            resolve(dataURL);
          }
        });
      });
    },
  });
};
